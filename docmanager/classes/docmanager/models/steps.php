<?php


	namespace DocManager\Models;

	

	class Steps extends \Customize\DataModel {


		public function getFromProject(){
			try {
				$query = "SELECT * FROM steps WHERE project_id = :project_id ORDER BY `order` ASC, id DESC";
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":project_id", \MHS\Env::PROJECT_ID);

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


		public function newStep($params){
			try {
				$query = "INSERT INTO steps SET 
					project_id = :project_id, 
					name = :name, 
					short_name = :short_name, 
					description = :description, 
					`order` = :order, 
					share_requires = :share_requires,
					created_at = :created_at";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":project_id", \MHS\Env::PROJECT_ID);
				$DB->setParam(":name", $params['name']);
				$DB->setParam(":short_name", $params['short_name']);
				$DB->setParam(":description", $params['description']);
				$DB->setParam(":order", $params['order']);
				$DB->setParam(":share_requires", $params['share_requires']);
				$DB->setParam(":created_at", date("Y-m-d H:i:s"));

				$DB->runQuery();

				return $DB->getLastInsertId();

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::out("Error in Controller/Steps/newStep()");
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}



		public function linkDocuments($stepId){
			try{
				$query = "SELECT id FROM documents WHERE project_id = :project_id";


				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->prepQuery($query);
				$DB->setParam(":project_id", \MHS\Env::PROJECT_ID);
				$DB->runQuery();

				$rows = $DB->getAllRows();

				$query = "INSERT INTO document_step(document_id, step_id) VALUES ";
				$values = [];
				foreach($rows as $row){
					$values[] = "(" . $row['id'] . "," . $stepId . ")";
				}
				$query .= implode(", ", $values);

				$DB->prepQuery($query);
				$DB->runQuery();


			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::out("Error in Controller/Steps/linkDocuments()");
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}



		public function update($params){
			try {
				$query = "UPDATE steps SET 
					name = :name, 
					short_name = :short_name, 
					description = :description, 
					`order` = :order, 
					color = :color,
					share_requires = :share_requires,
					updated_at = :updated_at
					WHERE id = :id LIMIT 1";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":name", $params['name']);
				$DB->setParam(":short_name", $params['short_name']);
				$DB->setParam(":description", $params['description']);
				$DB->setParam(":order", $params['order']);
				$DB->setParam(":color", $params['color']);
				$DB->setParam(":share_requires", $params['share_requires']);
				$DB->setParam(":updated_at", date("Y-m-d H:i:s"));
				$DB->setParam(":id", $params['id']);

				$DB->runQuery();
				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::out("Error in Controller/Steps/update()");
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}



		


		public function updateDocumentStep($document_step_id, $status){
			try {
				$query = "UPDATE document_step SET status = :status WHERE id = :document_step_id LIMIT 1";
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":status", $status);
				$DB->setParam(":document_step_id", $document_step_id);
				$DB->runQuery();
				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::out("Error in Controller/Steps/updateDocumentStep()");
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}

		public function delete($params){
			try {
				$query = "DELETE FROM steps WHERE id = :id LIMIT 1";
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$DB->prepQuery($query);
				$DB->setParam(":id", $params['id']);

				$DB->runQuery();

				$query = "DELETE FROM document_step WHERE step_id = :id LIMIT 1";
				$DB->prepQuery($query);
				$DB->setParam(":id", $params['id']);

				$DB->runQuery();


				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::out("Error in Controller/Steps/delete()");
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}





		public function fixOrder(){
			$steps = $this->getFromProject();

			$order = [];
			$reorder = false;

			// //find all orders
			// foreach($steps as $step){
			// 	//are ther duplicates?
			// 	if(in_array($step['order'], $order)){
			// 		$reorder = true;
			// 		break;
			// 	}
			// 	$order[] = $step['order'];
			// }

			// if($reorder == false) return;

			try {
				//reorder: the steps are already properly sorted
				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);

				$order = 0;
				foreach($steps as $step){
					$order++;
					$query = "UPDATE steps SET `order` = :order WHERE id = :id LIMIT 1";
					$DB->prepQuery($query);
					$DB->setParam(":order", $order);
					$DB->setParam(":id", $step['id']);
					$DB->runQuery();
				}

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::out("Error in Controller/Steps/fixOrder()");
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}	
			
			
		}

	}