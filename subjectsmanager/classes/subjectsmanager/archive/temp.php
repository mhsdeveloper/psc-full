<?php

	namespace SubjectsManager\Controllers;

	class SubjectsManagerController {
        private $topicsModel;
		private $topicRelationshipsModel;
		private $projectTopicDataModel;
		private $subjectsManagerView;
		
		public function __construct(){
			$this->topicsModel = new \SubjectsManager\Models\TopicsModel();
			$this->topicRelationshipModel  = new \SubjectsManager\Models\TopicRelationshipModel();
			$this->projectTopicDataModel = new \SubjectsManager\Models\ProjectTopicDataModel();
			$this->$subjectsManagerView = new \SubjectsManager\Views\SubjectsManagerView();
		}

        public function enablePreflight(){
            //Enable Preflight to return success
			if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
				header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE');
				header('Access-Control-Allow-Headers: *');
				header('Access-Control-Max-Age: 86400');
				header('Content-Length: 0');
				header('Content-Type: text/plain');
				die();
			}
        }

		public function handleRequests() {
			header('Content-Type: application/json');
			//TODO: limit this to origin 
			header("Access-Control-Allow-Origin: *");
			header("Access-Control-Allow-Methods: GET, POST, PUT,  OPTIONS, DELETE");
			header("Access-Control-Allow-Headers: *");

            $this->enablePreflight();

			switch($_SERVER['REQUEST_METHOD']){
				case "GET":{
					$this->handleGet();
					break;
				}
				case "POST": {
					//post new project-topic relationship and description
					if (isset($_GET['topic']) && isset($_GET['projects'])){
						if(!$this->projectTopicDataModel->createProjectTopicRelationship()){
							$errors = $this->projectTopicDataModel->getErrors();
							$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
							exit();
						}
	
						$this->subjectsManagerView->respond(201);
						exit();
					}
				}
	
				case "PUT": {
					//update existing project-topic relationship and description
					if (isset($_GET['topic']) && isset($_GET['projects'])){
						if(!$this->projectTopicDataModel->updateProjectTopicRelationship()){
							$errors = $this->projectTopicDataModel->getErrors();
							$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
							exit();
						}

						$this->subjectsManagerView->respond(201);
						exit();
                    }
				}
	
				case "DELETE": {
					//delete existing project-topic relationship and description
					if (isset($_GET['topic']) && isset($_GET['projects'])){
						if(!$this->projectTopicDataModel->deleteProjectTopicRelationship()){
							$errors = $this->projectTopicDataModel->getErrors();
							$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
							exit();
						}

						$this->subjectsManagerView->respond(201);
						exit();}
				}

				default:{
					$this->subjectsManagerView->respondWithErrors(400);
				}
			}
		}

		private function handleGet(){
			//Get all topics for a given project
		     if (isset($_GET['project']) && isset($_GET['topics'])){
				if(!$this->projectTopicDataModel->getTopics()){
					$errors = $this->projectTopicDataModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->projectTopicDataModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit();

			//Get all projects that have a given topic
			} else if (isset($_GET['topic']) && isset($_GET['projects'])){
				if(!$this->projectTopicDataModel->getProjects()){
					$errors = $this->projectTopicDataModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->projectTopicDataModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit();
			}
		}
	}
