<?php 


	namespace API\Models;


	/*
		CONCERNS: 
			translate user input into actual fields (REFACTOR? another class handle this mapping?)
			build SQL
			build resulting data array

	*/
	
	class NameModel extends DataModel {

		function get($husc){

			try {
				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$sql = "SELECT * FROM names WHERE name_key = :husc";
				$this->DB->setParam(":husc", $husc);
				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				return $rows;
			
			} catch(\Exception $e) {
				error_log($e->getMessage());
				return false;
			}

		}


		function getProjectMetadata($id){

			try {
				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$sql = "SELECT * FROM project_metadata WHERE name_id = :id";
				$this->DB->setParam(":id", $id);
				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				return $rows;
			
			} catch(\Exception $e) {
				error_log($e->getMessage());
				return false;
			}

		}


		function getLinks($id){
			try {
				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$sql = "SELECT * FROM links WHERE linkable_id = :id order by authority desc, display_title asc";
				$this->DB->setParam(":id", $id);
				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				return $rows;
			
			} catch(\Exception $e) {
				error_log($e->getMessage());
				return false;
			}
		}



		function updateField($id, $field, $value){
			try {
				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$sql = "UPDATE names SET {$field} = :value WHERE id = :id LIMIT 1";
				$this->DB->setParam(":value", $value);
				$this->DB->setParam(":id", $id);
				$this->DB->prepQuery($sql);
				$this->DB->runQuery();
				return true;
			
			} catch(\Exception $e) {
				error_log($e->getMessage());
				return false;
			}
		}
	}