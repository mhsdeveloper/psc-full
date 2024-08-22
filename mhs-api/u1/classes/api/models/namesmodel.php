<?php 


	namespace API\Models;


	/*
		CONCERNS: 
			translate user input into actual fields (REFACTOR? another class handle this mapping?)
			build SQL
			build resulting data array

	*/
	
	class NamesModel extends DataModel {

		private $debugSQL = false;
		private $rows;
		public $staffNotes = [];
		public $inProjectIds = [];
		public $publicNotes = [];
		public $count = 0;
		private $ids = []; //the final rsults set ids, used to get notes and descriptions
		private $errors = [];
		private $insertid = 0;
		private $whereClauses = [];
		private $project_id = 0;

		public const NAMES_FIELDS = [
			"family_name", 
			"given_name", 
			"maiden_name", 
			"middle_name", 
			"variants"
		];

		public const ANY_FIELDS = [
			"family_name", 
			"given_name", 
			"maiden_name", 
			"middle_name", 
			"professions", 
			"variants", 
			"title", 
			"name_key",
			"date_of_birth",
			"date_of_death",
			"identifier",
			"verified"
		];

		public const SPECIFIC_FIELDS = [
			"family_name", 
			"given_name", 
			"maiden_name", 
			"middle_name", 
			"professions", 
			"variants", 
			"title", 
			"name_key",
			"date_of_birth",
			"date_of_death",
			"identifier",
			"verified"
		];


		/*
			$fields is an array, keys are "fields" that user understands, values are the, uh, values:
				[
					"any" => "adams-charles2"  // this should translate to all fields
				]

1) FIND RECORDS
if only :any use union => ID set
if fields other than :notes and not :notes, simply query
if fields other than :notes AND :notes:: get ids, limit to notes => new ID set
if just :notes/ descriptions

2) RETRIEVE RECORDS
ID set => get full records and notes and descriptions



		*/
		public function search($fields, $sort, $dir = "ASC", $offset = 0, $count = 25){
			//some preliminary setup
			$ids = [];
			$sort = $this->parseSort($sort);
			$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	

			//setup situation flags
			$any = isset($fields['any']) ? true : false;
			$hasName = isset($fields['name']) ? true: false;
			$hasNotes = isset($fields['notes']) ? true : false;
			//if going for public only, that is stored in descriptions.public, so need this flag
			$hasDescriptions = (isset($fields['descriptions']) || isset($fields['public'])) ? true : false;
			$other = false;
			foreach(self::SPECIFIC_FIELDS as $name) {
				if(isset($fields[$name])){
					$other = true;
					break;
				}
			}

			//getting all fields
			if(empty($fields)) {
				return $this->allNames($sort, $dir, $offset, $count);
			}

			//no other tables to search, so simple where clauses in main table
			if(!$hasNotes && !$hasDescriptions){
				if($any) $this->buildAnyWhere();
				if($hasName) $this->namesWhere($fields);
				$ids = $this->idsFromMainTable($fields);

			} else {
				if($any || $hasName || $other){
					if($any) $this->buildAnyWhere();
					if($hasName) $this->namesWhere($fields);
					$ids = $this->idsFromMainTable($fields);
					if(!empty($ids)) $ids = $this->idsForNotesMetadata($fields, $ids);
				} else {
					$ids = $this->idsForNotesMetadata($fields);
				}
			}


			//get full records based on the set of ids
			return $this->retrieveNames($ids, $sort, $dir, $offset, $count);
		}





		protected function idsFromMainTable($fields){
			try {
				//main sql: see if we need any fields from the main names
				foreach(self::SPECIFIC_FIELDS as $field){
					if(isset($fields[$field])){
						$this->whereClauses[] = $field . " LIKE :" . $field . "Term";
					}
				}
				$whereClause = implode(" AND ", $this->whereClauses);

				if($this->project_id > 0){
					$sql = "SELECT names.id as id FROM names, name_project WHERE names.id = name_project.name_id AND project_id = :project_id AND " . $whereClause;
					$this->DB->setParam(":project_id", $this->project_id);
				} else{ 
					$sql = "SELECT id FROM names WHERE " . $whereClause;
				}

				if($this->debugSQL) print "idsFromMainTable()\n\n" . $sql . "\n\n";
				//matchup PDO bindings with user input
				foreach($fields as $name => $value){
					if(!in_array($name, self::ANY_FIELDS)) continue;
					if($name == "family_name" || $name == "given_name" || $name == "middle_name" || $name == "maiden_name") {
						$this->DB->setParam(":" . $name . "Term", $value . "%");
					} else {
						$this->DB->setParam(":" . $name . "Term", "%" . $value . "%");
					}
				}

				//prep "name" terms
				// if "name" was parsed in to last, first because of comma, name would have been unset, so this is safe
				if(isset($fields['name'])) $this->DB->setParam(":nameTerm", "%". $fields['name'] . "%");
				if(isset($fields['any'])) $this->DB->setParam(":anyTerm", "%". $fields['any'] . "%");
				

				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();

				$ids = [];
				foreach($rows as $row) $ids[] = $row["id"];
				return $ids;
				
			} catch(\Exception $e){
				$this->errors[] = $e->getMessage();

				return false;				
			}
		}




		protected function idsForNotesMetadata($fields, $ids = []){
			$sql = $notesUnion = $descriptionsUnion = $inClause = "";
			$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);

			if(!empty($ids)){
				$inClause = " AND name_id IN(" . implode(", ", $ids) . ")";
			}

			if(isset($fields['public'])){
				$descriptionsUnion = "SELECT name_id as id FROM project_metadata WHERE public = 1 " . $inClause;
			}

			else if(isset($fields['notes'])) {
				$notesUnion = "SELECT name_id as id FROM notes WHERE notes LIKE :notesTerm" . $inClause;
			}
			if(isset($fields['descriptions'])) {
				$descriptionsUnion = "SELECT name_id as id FROM project_metadata WHERE notes LIKE :descriptionsTerm" . $inClause;
			}

			if(!empty($notesUnion) && empty($descriptionsUnion)) $sql .= $notesUnion;
			else if(!empty($descriptionsUnion) && empty($notesUnion)) $sql .= $descriptionsUnion;
			else if(!empty($descriptionsUnion) && !empty($notesUnion)) $sql .= $notesUnion . " UNION " . $descriptionsUnion;
			if($this->debugSQL) print "idsForNOTES()\n\n" . $sql . "\n\n";

			try {
				if(isset($fields['notes'])) $this->DB->setParam(":notesTerm", "%" . $fields['notes'] . "%");
				if(isset($fields['descriptions'])) $this->DB->setParam(":descriptionsTerm", "%" . $fields['descriptions'] . "%");
				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();

				$ids = [];
				foreach($rows as $row) $ids[] = $row["id"];
				return $ids;

			} catch(\Exception $e){
				$this->errors[] = $e->getMessage();
//print_r($this->errors);
				return false;				
			}
		}



		/*
			$ids can be HUSCS or ids, but the must all be one type or the other
		*/
		function retrieveNames($ids, $sort = "", $dir = "ASC", $offset = 0, $count = 100){
			try { 
				//set count
				$this->count = count($ids);
				if($this->count == 0) return false;

				if(is_numeric($ids[0])){
					$inClause = " WHERE id IN (";
				 	$joiner =  ", ";
					$end = ")";
				} else {
					$inClause =" WHERE name_key IN(\"";
					$joiner = "\", \"";
					$end = "\")";
				}
				
				$inClause .= implode($joiner , $ids) . $end;

				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);

				//get main rows, sorted, and paginated
				$sql = "SELECT * FROM names " . $inClause;
				if(!empty($sort)) $sql .= " ORDER BY " . $sort . " " . $dir;
				$sql .= " LIMIT " . $count . " OFFSET " . $offset;
				if($this->debugSQL) print "retrieveNames()\n\n" . $sql . "\n\n";

				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$this->rows = $this->DB->getAllRows();

				//store results ids
				foreach($this->rows as $row){
					$this->ids[] = $row["id"];
				}
				return true;

			} catch (\Exception $e){
				$this->errors[] = $e->getMessage();

				return false;
			}
		}





				/*
			$ids can be HUSCS or ids, but the must all be one type or the other
		*/
		function retrieveNamesFromHuscs($huscs, $fields, $offset = 0, $count = 100){
			try { 
				//set count
				$this->count = count($huscs);
				if($this->count == 0) return false;

				$inClause =" WHERE name_key IN(\"";
				$joiner = "\", \"";
				$end = "\")";
				
				$inClause .= implode($joiner, $huscs) . $end;

				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);

				//get main rows, sorted, and paginated
				$sql = "SELECT " . $fields . " FROM names " . $inClause;
				$sql .= " LIMIT " . $count . " OFFSET " . $offset;

				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$this->rows = $this->DB->getAllRows();
				return true;

			} catch (\Exception $e){
				$this->errors[] = $e->getMessage();

				return false;
			}
		}






		public function setProjectId($id){
			if(!is_numeric($id)) $this->project_id = 0;
			else $this->project_id = $id;
		}



		
		protected function allNames($sort, $dir, $offset, $count){
			try { 
				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);

				//get count
				if($this->project_id) $sql ='SELECT COUNT(id) FROM name_project WHERE project_id = ' . $this->project_id;
				else $sql = 'select COUNT(id) from names';
				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$this->rows = $this->DB->getAllRows();

				if(!isset($this->rows[0])) throw(new \Exception("No count of rows found."));
				if(!isset($this->rows[0]["COUNT(id)"])) throw(new \Exception("No count(id) col found"));

				$this->count = $this->rows[0]["COUNT(id)"];


				//get main rows, sorted, and paginated
				if($this->project_id){
					$sql = "SELECT * FROM names, name_project 
								WHERE names.id = name_project.name_id 
								AND name_project.project_id  = " . $this->project_id . 
								" ORDER BY " . $sort . " " . $dir . " LIMIT " . $count . " OFFSET " . $offset;
				} else {
					$sql = "SELECT * FROM names ORDER BY " . $sort . " " . $dir . " LIMIT " . $count . " OFFSET " . $offset;
				}
				if($this->debugSQL) print "allNames()\n\n" . $sql . "\n\n";
				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$this->rows = $this->DB->getAllRows();

				//store results ids
				foreach($this->rows as $row){
					$this->ids[] = $row["id"];
				}
				return true;

			} catch (\Exception $e){
				$this->errors[] = $e->getMessage();
				return false;
			}
		}


		public function getStaffNotes(){
			$inClause = "IN(" . implode(", ", $this->ids) . ")";
			try {
				$sql = "select * from notes where name_id " . $inClause . " ORDER BY name_id, project_id";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->prepQuery($sql);
				$DB->runQuery();
				$this->staffNotes = $DB->getAllRows();
				return true;
			} catch(\Exception $e){
				$this->errors[] = $e->getMessage();
				return false;
			}
		}




		public function getPublicNotes(){
			$inClause = "IN(" . implode(", ", $this->ids) . ")";
			try {
				$sql = "select * from project_metadata where name_id " . $inClause . " ORDER BY name_id, project_id";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->prepQuery($sql);
				$DB->runQuery();
				$this->publicNotes = $DB->getAllRows();
				return true;
			} catch(\Exception $e){
				$this->errors[] = $e->getMessage();
				return false;
			}
		}



		public function getInProjectFlags(){
			//if we searched on project id, then all ids are of coursed flagged in projects
			if($this->project_id){
				$this->inProjectIds = $this->ids;
				return true;
			}

			$inClause = "IN (" . implode(", ", $this->ids) . ")";
			try {
				$sql = "select * from name_project where name_id " . $inClause . " AND project_id = :project_id ORDER BY name_id, project_id";
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->setParam(":project_id", \MHS\Env::PROJECT_ID);
				$DB->prepQuery($sql);
				$DB->runQuery();
				$rows = $DB->getAllRows();
				foreach($rows as $row){
					$this->inProjectIds[] = $row["name_id"];
				}
				return true;
			} catch(\Exception $e){
				$this->errors[] = $e->getMessage();
				return false;
			}
		}



		/* look for patterns to split "name=xyz,john" into proper parts
		*/
		protected function namesWhere(&$fields){
			if(!isset($fields['name'])) return;

			$raw = $fields['name'];
			$parts = explode(",", trim($raw));

			//last, first situation
			if(isset($parts[1])){
				$fields['family_name'] = trim($parts[0]);
				$fields['given_name'] = trim($parts[1]);
				//remove name psuedo field to avoid uneven parameter binding
				unset($fields['name']);
			//just checking all names-like fields
			} else {
				$where = [];
				foreach(self::NAMES_FIELDS as $field){
					$where[] = $field . " LIKE :nameTerm ";
				}
				$this->whereClauses[] = " (" . implode(" OR ", $where) . ") ";
			}
		}
		


		protected function parseSort($sort){
			if($sort == "date") return "sort_birth";
			else if($sort == "verified") return "verified";
			return "sort_name";
		} 


		/* NOTE: this deals with main names table; 
			searching the notes field is added in main search via the UNION of selects */
		protected function buildAnyWhere(){
			$where = [];
			foreach(self::ANY_FIELDS as $field){
				$where[] = $field . " LIKE :anyTerm ";
			}
			
			$this->whereClauses[] = " (" . implode(" OR ", $where) . ") ";
		}



		
		function auditHuscs($huscsArray){
			$inClause = "name_key IN (\"" . implode('", "', $huscsArray) . "\")";
			try {
				$sql = "SELECT name_key FROM names WHERE " . $inClause;
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->prepQuery($sql);
				$DB->runQuery();
				$rows = $DB->getAllRows();
				//gather the huscs not found
				$missingHuscs = [];
				$flatHuscs = [];

				foreach($rows as $row){
					$flatHuscs[] = $row['name_key'];
				}

				foreach($huscsArray as $husc){
					if(!in_array($husc, $flatHuscs)){
						$missingHuscs[] = $husc;
					}
				}

				return $missingHuscs;
			} catch(\Exception $e){
				$this->errors[] = $e->getMessage();
				return false;
			}
		}





		function getRowsFromFieldStr($field, $strMatch){
			try {
				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$sql = "SELECT * FROM names WHERE {$field} LIKE :strMatch order by id";
				$this->DB->setParam(":strMatch", $strMatch);
				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				return $rows;
			
			} catch(\Exception $e) {
				error_log($e->getMessage());
				return false;
			}
		}








		public function getRows(){
			return $this->rows;
		}

		public function getLastID(){
			return $this->insertid;
		}


		public function getErrors(){
			return $this->errors;
		}

	}