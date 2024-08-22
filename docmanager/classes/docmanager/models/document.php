<?php

	namespace DocManager\Models;



	class Document extends \Customize\DataModel {

		public $persRefs = [];

		private $row;
		public $id;
		public $checked_outin_by;
		public $filename;
		public $fullpath;

		public $errors = [];


		// public function matchXMLID($filename = ""){
		// 	$xmlid = $this->getXMLIDFromFile($filename);

		// 	$fileparts = pathinfo($filename);

		// 	if($xmlid == $fileparts['filename']) return true;
		// 	return false;
		// }




		// public function getXMLIDFromFile($filename = ""){
		// 	if(empty($filename)){
		// 		$filename = $this->filename;
		// 	}
		// 	$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

		// 	$doc = new \Publications\TEIDocument();

		// 	if(!$doc->load($filename)){
		// 		$this->errors[] = "Unable to load " . $filename;
		// 		return false;
		// 	}
		// 	$nodes = $doc->getXPathNode("/tei:TEI/@xml:id");
		// 	if($nodes->length == 0) return false;

		// 	$xmlid = $nodes->item(0)->value;
		// 	return $xmlid;
		// }




		public function checkSchema($filename = ""){
			if(empty($filename)){
				$filename = $this->filename;
			}
			$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

			$doc = new \DOMDocument();
			if(!$doc->load($filename)){
				$this->errors[] = "Unable to load " . $filename;
				return false;
			}

			$schemaFile = \MHS\Env::getSchemaFilename();

			$cmd = "java -jar " . \MHS\Env::JING_FULL_PATH . " " . $schemaFile . " " . $filename;
			$bash = new \MHS\ShellCmd();
			$out = $bash->runCmd($cmd);
			if($out == "1" || $out == 1){
				$error = $bash->getStderr();
				$error .= $bash->getStdout();

				$pretty = "";
				//format
				$lines = explode("\n", $error);
				foreach($lines as $line) {
					if(strlen($line) < 5) continue;
					//get better msg aprts
					preg_match("/\.xml\:([0-9]).+\: error\:(.+)/", $line, $parts);
					$pretty = isset($parts[1]) ? "XML error line " . $parts[1] : "Error";
					$pretty .= isset($parts[2]) ? ": " . $parts[2] : "";
					$this->errors[] = $pretty;
				}

				return false;
			}

			return true;
		}





		public function getFromFilename($filename){

			try {
				$query = "SELECT * FROM documents WHERE filename = :filename AND project_id = :project_id LIMIT 1";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":filename", $filename);
				$DB->setParam(":project_id", \MHS\Env::PROJECT_ID);

				$DB->runQuery();
				$rows = $DB->getAllRows();
				if(count($rows)){
					$this->row = $rows[0];
					$this->fleshout();
					return true;
				}
				return false;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}





		public function getFromID($id){
			try {
				$query = "SELECT * FROM documents WHERE id = :id AND project_id = :project_id LIMIT 1";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":id", $id);
				$DB->setParam(":project_id", \MHS\Env::PROJECT_ID);

				$DB->runQuery();
				$rows = $DB->getAllRows();
				if(count($rows)){
					$this->row = $rows[0];
					$this->id = $this->row['id'];
					$this->fleshout();
					return true;
				}
				return false;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}





		public function getRowData(){
			return $this->row;
		}




		protected function fleshout(){
			$this->id = $this->row['id'];
			$this->checked_outin_by = $this->row['checked_outin_by'];
			$this->filename = $this->row['filename'];
		}




		public function addNew($filename, $username, $published = 0, $publish_date = "2000-01-01 00:00:00"){

			try {
				$query = "INSERT INTO documents set filename = :filename, checked_outin_by = :user, checked_outin_date = :created_at, created_at = :created_at, updated_at = :updated_at, publish_date = :publish_date, published = :published, project_id = :project_id";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":filename", $filename);
				$DB->setParam(":user", $username);
				$DB->setParam(":created_at", date("Y-m-d H:i:s"));
				$DB->setParam(":updated_at", date("Y-m-d H:i:s"));
				$DB->setParam(":published", $published);
				$DB->setParam(":publish_date", $publish_date);
				$DB->setParam(":project_id", \MHS\Env::PROJECT_ID);

				$DB->runQuery();
				$id = $DB->getLastInsertId();
				$this->id = $id;
				return $id;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r(date("Y-m-d H:i:s") . " -- user: " . $username);
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}




		public function addSteps($docid, $stepIds){
			try {
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	

				$query = "INSERT INTO document_step(document_id, step_id) VALUES ";
				$values = [];
				foreach($stepIds as $stepId){
					$values[] = "(" . $docid . "," . $stepId . ")";
				}
				$query .= implode(", ", $values);

				$DB->prepQuery($query);
				$DB->runQuery();

				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}




		public function checkin($id){
			try {
				$query = "UPDATE documents set updated_at = :updated_at, checked_outin_by = :user, checked_outin_date = :checked_outin_date, checked_out = 0 WHERE id = :id LIMIT 1";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->setParam(":id", $id);	
				$DB->setParam(":user", \Customize\User::username());
				$DB->setParam(":updated_at", date("Y-m-d H:i:s"));
				$DB->setParam(":checked_outin_date", date("Y-m-d H:i:s"));
				$DB->prepQuery($query);
				$DB->runQuery();

				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}



		

		public function checkoutID($id, $username){
			try {
				$query = "UPDATE documents set checked_outin_date = :checked_outin_date, checked_outin_by = :checked_outin_by, checked_out = 1 WHERE id = :id LIMIT 1";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->setParam(":id", $id);	
				$DB->setParam(":checked_outin_by", $username);
				$DB->setParam(":checked_outin_date", date("Y-m-d H:i:s"));
				$DB->prepQuery($query);
				$DB->runQuery();

				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}



		public function delete($filename){
			try {
				$query = "delete FROM documents WHERE id = :id AND project_id = :project_id LIMIT 1";
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":id", $this->id);
				$DB->setParam(":project_id", \MHS\Env::PROJECT_ID);
				$DB->runQuery();

				$query = "delete FROM document_step WHERE document_id = :id";
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":id", $this->id);
				$DB->runQuery();
				$rows = $DB->getAllRows();

				//remove actual file
				$fullpath = \MHS\Env::SOURCE_FOLDER . $filename;
				if(is_readable($fullpath) && !unlink($fullpath)){
					return false;
				}

				return true;
			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}





		public function publish($filename){
			try {
				$query = "UPDATE documents SET published = 1, publish_date = :date WHERE filename = :filename LIMIT 1";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->setParam(":filename", $filename);	
				$DB->setParam(":date", date("Y-m-d H:i:s"));
				$DB->prepQuery($query);
				$DB->runQuery();

				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}



		public function unPublish($filename){
			try {
				$query = "UPDATE documents SET published = 0 WHERE filename = :filename LIMIT 1";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->setParam(":filename", $filename);	
				$DB->prepQuery($query);
				$DB->runQuery();

				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}




		/* update general columns, useful for installs that have extended the document model with
			new colums in the db.

			Logic here will prevent changing the cols in $notThere
		*/
		public function update($filename, $colsValues = []){

			//prune notallow cols
			$notThese = ["id", "filename", "project_id", "created_at"];

			for($i=0; i< count($notThese); $i++){
				if(in_array($notThese[$i], $colsValues)){
					array_splice($colsValues, $i, 1);
				}
			}

			$setClauses = [];				
			foreach($colsValues as $colName => $value){
				$setClauses[] = $colName . " = :" . $colName;
			}
			$setClause = implode(", ", $setClauses);

			try {
				
				$query = "UPDATE documents SET " . $setClause . " WHERE filename = :filename LIMIT 1";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->setParam(":filename", $filename);	
				foreach($colsValues as $colName => $value){
					$DB->setParam(":" . $colName, $value);
				}
				$DB->prepQuery($query);
				$DB->runQuery();

				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}





		public function getErrors(){
			return implode("; ", $this->errors);
		}

	}