<?php 


	namespace API\Models;


	/*
		CONCERNS: 
			translate user input into actual fields (REFACTOR? another class handle this mapping?)
			build SQL
			build resulting data array

	*/
	
	class ProjectModel extends DataModel {

		private $rows = [];
		private $addedHuscs = [];


		function getRows(){
			return $this->rows;
		}

		function addNames($ids){
			$inNames = implode(",", $ids);
			try {
				//first find any names already in there and don't add, so we don't duplicate associations
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$sql = "SELECT name_id FROM name_project WHERE project_id = ". \MHS\Env::PROJECT_ID . " AND name_id IN(" . $inNames . ")";
				$DB->prepQuery($sql);
				$DB->runQuery();
				$rows = $DB->getAllRows();

				//flatten output
				$existingIds = [];
				foreach($rows as $row){
					$existingIds[] = $row['name_id'];
				}

				$values = [];
				foreach($ids as $id){
					if(in_array($id, $existingIds)) continue;
					$values[] = "(" . $id . ", " . \MHS\Env::PROJECT_ID . ")";
				}

				if(count($values) == 0) return true; //fine, non to add, but not an error

				$values = implode(",", $values);

				$sql = "INSERT INTO name_project (name_id, project_id) VALUES  ". $values;

				$DB->prepQuery($sql);
				$DB->runQuery();
				return true;

			} catch(\Exception $e) {
				error_log($e->getMessage());
				error_log($sql);
				return false;
			}
		}


		
		function removeNames($ids){
			$inClause = implode(",", $ids);
			try {
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$sql = "DELETE FROM name_project WHERE name_id IN(" . $inClause . ") AND project_id = " . \MHS\Env::PROJECT_ID;

				$DB->prepQuery($sql);
				$DB->runQuery();
				return true;
				
			} catch(\Exception $e) {
				error_log($e->getMessage());
				error_log($sql);
				return false;
			}
		}


		function auditHuscsInProject($huscsArray){
			try {
				//find which are not part of project
				$inClause = "name_key IN (\"" . implode('", "', $huscsArray) . "\")";
				$sql = "SELECT name_key FROM names, name_project WHERE " . $inClause . " AND names.id = name_project.name_id AND name_project.project_id = " . \MHS\Env::PROJECT_ID;

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->prepQuery($sql);
				$DB->runQuery();
				$rows = $DB->getAllRows();

				//gather the huscs not found
				$missingHUSCS = [];
				$flatHuscs = [];
				$ids = [];
				foreach($rows as $row){
					$flatHuscs[] = $row['name_key'];
				}

				foreach($huscsArray as $husc){
					if(!in_array($husc, $flatHuscs)){
						$missingHUSCS[] = $husc;
					}
				}

				$this->addedHuscs = $missingHUSCS;

				//get ids for missing huscs
				$inClause = "name_key IN (\"" . implode('", "', $missingHUSCS) . "\")";
				$sql = "SELECT id FROM names WHERE " . $inClause;

				$DB->prepQuery($sql);
				$DB->runQuery();
				$rows = $DB->getAllRows();

				//add ids to project
				$missingIDs = [];
				foreach($rows as $row){
					$missingIDs[] = $row['id'];
				}

				if(count($missingIDs)){
					if(!$this->addNames($missingIDs)) {
						throw(new \Exception("Unable to add names to project"));	
					}
				}

				return true;

			} catch(\Exception $e){
				error_log($e->getMessage());
				error_log($sql);
				return false;
			}

		}


		public function getAddedHuscs(){
			return $this->addedHuscs;
		}





		function makeNamesPublic($huscsArray){
			try {
				//find ids for all the huscs
				$inClause = "name_key IN (\"" . implode('", "', $huscsArray) . "\")";
				$sql = "SELECT id, name_key FROM names WHERE " . $inClause;

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->prepQuery($sql);
				$DB->runQuery();
				$rows = $DB->getAllRows();

				$ids = [];
				foreach($rows as $row){
					$ids[] = $row['id'];
				}


				//get ids for missing huscs
				$inClause = "name_id IN (\"" . implode('", "', $ids) . "\")";
				$sql = "UPDATE project_metadata SET public = 1 WHERE " . $inClause . " AND project_id = " . \MHS\Env::PROJECT_ID;

				$DB->prepQuery($sql);
				$DB->runQuery();
				$rows = $DB->getAllRows();

				return true;

			} catch(\Exception $e){
				error_log($e->getMessage());
				error_log($sql);
				return false;
			}

		}


	}