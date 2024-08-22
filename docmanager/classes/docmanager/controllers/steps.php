<?php


	namespace DocManager\Controllers;

	//extends \Publications\Metadata\Responder;

	class Steps {

		private $filePath = "";

		function __construct(){
			$this->AR = new \Publications\AjaxResponder();
		}


		public function getFromProject(){
			$Steps = new \DocManager\Models\Steps();

			$projectSteps = $Steps->getFromProject();

			if(false === $projectSteps){
				$this->AR->statusFailed();
				$this->AR->errors("Unable to retrieve steps. Please see the web developer.");
				$this->AR->respond();
				return;
			}

			$this->AR->statusOk();
			$this->AR->data("steps", $projectSteps);
			$this->AR->respond();
			return;
		}


		public function newStep(){
			if(!\Customize\User::isAtLeastXMLEditor()) {$this->fail("You must be an editor to do this."); return;}

			$params = $this->paramsFromPost();

			$Steps = new \DocManager\Models\Steps();
		
			$newStepId = $Steps->newStep($params);

			if($newStepId === false){
				$this->AR->statusFailed();
				$this->AR->errors("Unable to add that step. Please see the web developer.");
				$this->AR->respond();
				return;
			}

			//insert new association of this step with each existing doc
			$Steps->linkDocuments($newStepId);
			$Steps->fixOrder();
			$this->getFromProject();
		}


		public function updateStep(){
			if(!\Customize\User::isAtLeastXMLEditor()) {$this->fail("You must be an editor to do this."); return;}

			$params = $this->paramsFromPost();

			$Steps = new \DocManager\Models\Steps();
		
			if(false === $Steps->update($params)){
				$this->AR->statusFailed();
				$this->AR->errors("Unable to update the step. Please see the web developer.");
				$this->AR->respond();
				return;
			}

			$Steps->fixOrder();
			$this->getFromProject();
		}


		
		public function updateDocumentStep(){
			if(!\Customize\User::isAtLeastXMLEditor()) {$this->fail("You must be an editor to do this."); return;}

			$document_step_id = filter_var($_GET['document_step_id'], FILTER_SANITIZE_NUMBER_INT);
			$status = filter_var($_GET['status'], FILTER_SANITIZE_NUMBER_INT);
			
			$Steps = new \DocManager\Models\Steps();
		
			if(false === $Steps->updateDocumentStep($document_step_id, $status)){
				$this->AR->statusFailed();
				$this->AR->errors("Unable to update the step. Please see the web developer.");
				$this->AR->respond();
				return;
			}
		}
		


		public function deleteStep(){
			if(!\Customize\User::isAtLeastXMLEditor()) {$this->fail("You must be an editor to do this."); return;}

			$params['id'] = $_POST['id'];

			$Steps = new \DocManager\Models\Steps();
		
			if(false === $Steps->delete($params)){
				$this->AR->statusFailed();
				$this->AR->errors("Unable to delete the step. Please see the web developer.");
				$this->AR->respond();
				return;
			}

			$Steps->fixOrder();
			$this->getFromProject();
		}


		private function paramsFromPost(){
			$params['name'] = $_POST['name'];
			$params['short_name'] = $_POST['short_name'];
			$params['description'] = $_POST['description'];
			$params['order'] = $_POST['order'];
			$params['color'] = $_POST['color'];
			if($_POST['share_requires'] == "true") $params['share_requires'] = 1;
			else $params['share_requires'] = 0;

			if(isset($_POST['id'])) $params['id'] = $_POST['id'];

			return $params;
		}
	}
