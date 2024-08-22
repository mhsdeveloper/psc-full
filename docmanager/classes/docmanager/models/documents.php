<?php


	namespace DocManager\Models;


	/* class for retrieving
	*/

	class Documents extends \Customize\DataModel {

		private $count = 0;
		private $queryTotal = -1;

		public function getDocumentsFromFilenames($filenames = []){
			try {
				$ins = implode("','", $filenames);
				$query = "SELECT * FROM documents WHERE filename in ('" . $ins . "')";
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);

				$DB->runQuery();
				$rows = $DB->getAllRows();
				return $rows;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}



		public function getProjectDocumentCount(){
			try {
				$query = "SELECT COUNT(*) FROM documents  WHERE project_id = " . \MHS\Env::PROJECT_ID;

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);

				$DB->runQuery();
				$rows = $DB->getAllRows();

				if(count($rows)) {
					return $rows[0]["COUNT(*)"];
				}

				throw(new \Exception("Error getting doc count"));
				
			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}


		public function getLastQueryCount(){
			return $this->queryTotal;
		}



		public function getProjectDocuments($params = [], $start = 0, $count = 40, $order = "checked_outin_date DESC"){
			try {
				$wheres = [];
				foreach($params as $col => $val){
					if($col == "filename" || $col == "checked_outin_by"){
						$wheres[] = $col . " LIKE '%" . $val . "%'"; 
					} else {
						$wheres[] = $col . " = " . $val;
					}
				}

				if(count($wheres)){
					$where = " AND " . implode(" AND ", $wheres);				
				} else $where = "";

				$query = "SELECT * FROM documents WHERE project_id = " . \MHS\Env::PROJECT_ID . " " . $where . " ORDER BY " . $order . " LIMIT " . $start . "," . $count;
				$countQuery = "SELECT COUNT(*) FROM documents WHERE project_id = " . \MHS\Env::PROJECT_ID . " " . $where;
//print $query . "\n\n";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);

				$DB->runQuery();
				$rows = $DB->getAllRows();

				$DB->prepQuery($countQuery);
				$DB->runQuery();
				$countrows = $DB->getAllRows();
				$this->queryTotal = $countrows[0]["COUNT(*)"];
				return $rows;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}





		public function getDocumentsSteps($id = []){
			try {
				$ins = implode(",", $id);

				if(empty($id)) return [];

				$query = "SELECT 
					document_step.id as document_step_id,
					steps.name, 
					steps.short_name,
					steps.order, 
					steps.description,
					steps.color,
					steps.share_requires,
					document_step.status,
					document_step.username,
					document_step.document_id
					 FROM document_step, steps WHERE document_id IN (" . $ins . ") AND steps.id = document_step.step_id ORDER BY document_id, steps.order";
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		

				$DB->prepQuery($query);
				$DB->runQuery();
				$rows = $DB->getAllRows();

				return $rows;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}





		public function getChangedDocuments($dateOfChange, $start = 0, $count = 100, $order = "checked_outin_date DESC", $project_id = 0){
			try {
				$projectClause = "";
				if($project_id){
					$projectClause = "AND project_id = " . $project_id;
				}


				//first get count
				$query = "SELECT COUNT(*) FROM documents WHERE (updated_at >= :dateOfChange OR publish_date >= :dateOfChange) AND published = 1 " . $projectClause . " ORDER BY " . $order;

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);
				$DB->setParam(":dateOfChange", $dateOfChange);		
				$DB->prepQuery($query);

				$DB->runQuery();
				$rows = $DB->getAllRows();

				$this->count = array_pop($rows[0]);

				$query = "SELECT * FROM documents WHERE (updated_at >= :dateOfChange OR publish_date >= :dateOfChange) AND published = 1 " . $projectClause . " ORDER BY " . $order . " LIMIT " . $start . "," . $count;

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);
				$DB->setParam(":dateOfChange", $dateOfChange);		
				$DB->prepQuery($query);

				$DB->runQuery();
				$rows = $DB->getAllRows();
				return $rows;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}


		function getCount(){
			return $this->count;
		}


		//delete all files' records for project; this includes document_step but not the step definitions
		function deleteAll(){
			try {
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);

				$query = "SELECT id FROM documents WHERE project_id = " . \MHS\Env::PROJECT_ID;
				$DB->prepQuery($query);
				$DB->runQuery();
				$rows = $DB->getAllRows();

				$ids = [];
				foreach($rows as $row) $ids[] = $row["id"];
				
				//nothing to delete
				if(count($ids) === 0) return true;

				$ins = implode(",", $ids);


				$query = "DELETE FROM document_step WHERE document_id IN (" . $ins . ")";
				$DB->prepQuery($query);
				$DB->runQuery();

				$query = "DELETE FROM documents WHERE project_id = " . \MHS\Env::PROJECT_ID;
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
	}