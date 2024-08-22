<?php


	namespace API\Controllers;

	class ExtNames extends Controller {


		
		public const NAMES_FIELDS = [
			"family_name", 
			"given_name", 
			"maiden_name", 
			"middle_name", 
			"suffix",
			"title", 
			"professions", 
			"variants", 
			"name_key",
			"date_of_birth",
			"birth_ca",
			"birth_era",
			"date_of_death",
			"death_ca",
			"death_era",
			"identifier",
			"verified",
			"sort_birth",
			"sort_name",
			"created_at",
			"updated_at"
		];


		public function listFields(){
			$this->JSONRespond(self::NAMES_FIELDS);			
		}

		public function help(){
?>
<h1>Help with API format</h1>

<h2>Some sample requests:</h2>

<p>[api url]/names?huscs=adams-john;adams-abigail</p>
<blockquote>
	Separate huscs with semi-colons. Limited to 100 per request.
</blockquote>

<p>[api url]/names?huscs=adams-john;adams-abigail&fields=family_name;given_name;maiden_name;middle_name</p>
<blockquote>
	Leaving out the fields param will retrieve all fields.
</blockquote>


<p>[api url]/names/fields</p>
<blockquote>
			Lists available fields to specific.
</blockquote>

<?
			die();
		}


		public function namesFromHuscs(){
			

			if(!isset($_GET['huscs'])){
				$this->fail("No HUSCS sent to check.");
			}
			$raw = $_GET['huscs'];
			$refs = preg_replace("/[^a-zA-Z0-9\-\;]/", "", $raw);
			$refs = explode(";", $refs);


			//get fields
			$fields = "name_key";
			if(isset($_GET['fields']) && !empty($_GET['fields'])){
				$fields = preg_replace("/[^a-zA-Z0-9_\-\;]/", "", $_GET['fields']);
				$fields = str_replace(";", ",", $fields);
				$fields .= ",name_key";
			} else {
				$fields = implode(",", self::NAMES_FIELDS);
			}


			$names = new \API\Models\NamesModel();
			$set = $names->retrieveNamesFromHuscs($refs, $fields);
			if($set === false){
				$this->fail("Error trying to audit huscs.");
			}		

			$rows = $names->getRows();

			$output = [];
			foreach($rows as $row){
				$husc = $row['name_key'];
				$output[$husc] = $row;
			}


			$this->JSONRespond($output);
		}









		function publicSearch(){
			$sortA = Names::parseSort();
			$fields = Names::parseFields($_GET);

			$fields['public'] = 1;
			$pagination =Names::parsePagination();
			$offset = ($pagination['page'] - 1) * $pagination['count'];

			$names = new \API\Models\NamesModel();

			if(isset($_GET['project'])){
				$projectId = preg_replace("/[^0-9]/", "", $_GET['project']);
				$names->setProjectId($projectId);				
			}

			$names->search($fields, $sortA['field'], $sortA['direction'], $offset, $pagination['count']);
			$names->getPublicNotes();

			$rows = $names->getRows();
			$this->JSONRespond($rows);
		}

		public function JSONRespond($payload = [], $other = false){
			$Aj = new \Publications\AjaxResponder();
			$Aj->data($payload);
			$Aj->respond();
		}

	}