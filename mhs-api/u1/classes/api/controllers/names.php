<?php


	namespace API\Controllers;

	class Names extends Controller {

		public const sortFields = ["name", "notes", "date", "verified"];
		public const searchFields = ["any", "name", "notes", "descriptions", "date_of_birth", "born_after", "born_before",
			"name_key", "family_name", "given_name", "maiden_name", "middle_name", "variants", "professions", "verified"
		];


		function index(){
		}


		/* CONCERNS: sanitization of user input
					JSON response
		*/

		function search(){
			$sortA = self::parseSort();
			$fields = self::parseFields($_GET);
			$pagination = self::parsePagination();
			$offset = ($pagination['page'] - 1) * $pagination['count'];

			$names = new \API\Models\NamesModel();
			if(isset($_GET['projectonly'])){
				$names->setProjectId(\MHS\Env::PROJECT_ID);				
			}
			$names->search($fields, $sortA['field'], $sortA['direction'], $offset, $pagination['count']);
			$names->getPublicNotes();
			$names->getStaffNotes();
			$names->getInProjectFlags();

			$rows = $names->getRows();
			$other = [];
			$other['publicNotes'] = $names->publicNotes;
			$other['staffNotes'] = $names->staffNotes;
			$other['count'] = $names->count;
			$other['inProjectIds'] = $names->inProjectIds;

			$this->JSONRespond($rows, $other);
		}




		public static function parseSort(){
			$sort = isset($_GET['sort']) ? $_GET['sort'] : "name";
			$parts = explode(",", $sort);
			$sortField = $parts[0];

			//sanitize
			if(!in_array($sortField, self::sortFields)) $sortField = "name";

			//dir if any
			if(isset($parts[1])){
				$dir = $parts[1];
				if(!in_array($dir, ["asc", "desc"])) $dir = "asc";
			} else $dir = "asc";

			return ["field" => $sortField, "direction" => $dir];
		}




		public static function parseFields($fields){
			$availableFields = self::searchFields;
			$outfields = [];

			foreach($fields as $name => $value){
				//skip 
				if(!in_array($name, $availableFields)){
					continue;
				}

				$value = trim(urldecode($value));
				//remove spaces after commas 
				$value = preg_replace("/,\s+/", ",", $value);
				//convert some other common wildcards to MYSQL wildcards
				$value = str_replace(['*', ".", "?"], ["%", "_", "_"], $value);
				// regularize spaces, and convert to wildcard
				$value = preg_replace("/\s\s+/", " ", $value);
				$value = str_replace(" ", "%", $value);

				if(empty($value)) continue;

				//lastly get rid of other symbols
				$outfields[$name] = preg_replace("/[;:\"'\.\<\>\!\@\#\$\^\&\*\(\)\+\=\?\/]/", "", $value);
			}
			return $outfields;
		}


		

		public static function parsePagination(){
			$page = isset($_GET['page']) ? preg_replace("/[^0-9]/", "", $_GET['page']) : 1;
			$per_page = isset($_GET['per_page']) ? preg_replace("/[^0-9]/", "", $_GET['per_page']) : 25;
			$per_page = intval($per_page);
			if($per_page > 100) $per_page = 100;
			return ["page" => $page, "count" => $per_page];
		}


		public function get(){
			$id = $this->_mvc->segment(2);

			$names = new \API\Models\NamesModel();
		}



		public function auditHuscs(){
			
			if(!isset($_GET['h'])){
				$this->fail("No persrefs sent to check.");
			}
			$raw = $_GET['h'];
			$refs = preg_replace("/[^a-zA-Z0-9\-\;]/", "", $raw);
			$refs = explode(";", $refs);

			$names = new \API\Models\NamesModel();
			$missing = $names->auditHuscs($refs);
			if($missing === false){
				$this->fail("Error trying to audit huscs.");
			}		

			$this->JSONRespond($missing);
		}




		function fixStrayPunctuation($saveChanges = false){

			$field = "title";// "variants";
			
			//get set of bad punctuations
			$names = new \API\Models\NamesModel();

			$rows = $names->getRowsFromFieldStr($field, "; %");

			if(false === $rows) die("Error in mysql?");
			
			print count($rows);
			print "<br/><table>";

			$name = new \API\Models\NameModel();

			foreach($rows as $row){
				print "<tr>";
				print "<td>" . $row['name_key'] . "</td><td>" . $row['id'] . "</td>";
				print "<td>" . $row[$field] . "</td><td>===>";

				$str = $row[$field];
				//fix missing empty cells from the orig spreadsheet
				$str = str_replace(["; ;", ";;"], ";", $str);

				$str = preg_replace("/\s\s+/", " ", $str);
				if($str[0] == ";") $str = substr($str, 1);

				$str = trim($str);

				print $str . "</td>";

				//only update if the field actually changed
				if($str != $row[$field] && $saveChanges){
					if(!$name->updateField($row['id'], $field, $str)){
						print "<br>/n!!!!!ERROR: unable to save record id " . $row['id'] . " w/ value " . $str . "<br/>\n";
					}
				}


				print "</tr>\n";
			}

			print "</table>";
		}
	}