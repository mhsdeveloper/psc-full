<?php
	namespace SubjectsManager\Controllers;

	class ProjectTopicDataController extends SMController{
        private $projectTopicDataModel;
		
		public function __construct(){
            parent::__construct();
			$this->projectTopicDataModel = new \SubjectsManager\Models\ProjectTopicDataModel();
		}

         //Get all topics and related data for a given project
         public function getTopics() {
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['project'])){
                $projectName = $_GET['project'];

				if(!$this->projectTopicDataModel->getTopics($projectName)){
					$errors = $this->projectTopicDataModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->projectTopicDataModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }


		public function getAllTopicRelationships(){
			if(!$this->projectTopicDataModel->getAllTopicRelationships()){
				$errors = $this->projectTopicDataModel->getErrors();
				$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
				exit();
			}

			$data = $this->projectTopicDataModel->getData();
			$this->subjectsManagerView->respondWithData(200, $data);
			exit;
		}


        //Get all topic names for a given project
        public function getTopicNames() {
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['project'])){
                $projectName = $_GET['project'];

                if(!$this->projectTopicDataModel->getTopicNames($projectName)){
                    $errors = $this->projectTopicDataModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $data = $this->projectTopicDataModel->getData();
                $this->subjectsManagerView->respondWithData(200, $data);
                exit;
        }

        $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
                }

        // Get all projects with a given topicgit 
        public function getProjects(){
            $this->setHeaders('GET');
            $this->enablePreflight();

            if (isset($_GET['topic_id'])){
                $topicId = $_GET['topic_id'];
                if(!$this->projectTopicDataModel->getProjects($topicId)){
                    $errors = $this->projectTopicDataModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $data = $this->projectTopicDataModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        //Post new project-topic relationship
        public function postProjectTopicRelationship(){
            $this->setHeaders('POST');
            $this->enablePreflight();

           if (isset($_GET['topic_id']) && isset($_GET['project'])){
                $topicId = $_GET['topic_id'];
                $projectName = $_GET['project'];
                $internalNote = $_GET['internalNote'];
                $publicNote = $_GET['publicNote'];

                if(!$this->projectTopicDataModel->createProjectTopicRelationship($topicId, $projectName, $internalNote, $publicNote)){
                    $errors = $this->projectTopicDataModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(201);
                exit();
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        public function getRecentlyEditedTopics(){
            $this->setHeaders('GET');
            $this->enablePreflight();

            if (isset($_GET['project'])){
                $projectName = $_GET['project'];
                
                if(!$this->projectTopicDataModel->getRecentlyEditedTopicNames($projectName)){
                    $errors = $this->projectTopicDataModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $data = $this->projectTopicDataModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);

        }

        public function getProjectTopicRelationship(){
            $this->setHeaders('GET');
            $this->enablePreflight();

            if (isset($_GET['topic_id']) && isset($_GET['project'])){
                $topicId = $_GET['topic_id'];
                $projectName = $_GET['project'];
                
                if(!$this->projectTopicDataModel->getProjectTopicData($topicId, $projectName)){
                    $errors = $this->projectTopicDataModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $data = $this->projectTopicDataModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        

        public function putProjectTopicRelationship(){
            $this->setHeaders('POST');
            $this->enablePreflight();

            if (isset($_GET['topic_id']) && isset($_GET['project'])){
                $topicId = $_GET['topic_id'];
                $projectName = $_GET['project'];
                $internalNote = $_GET['internalNote'];
                $publicNote = $_GET['publicNote'];

                if(!$this->projectTopicDataModel->updateProjectTopicRelationship($topicId, $projectName, $internalNote, $publicNote)){
                    $errors = $this->projectTopicDataModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(200);
                exit();
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);

        }

        //Delete existing topic-topic relationship
        public function delTopicRelationship(){
            $this->setHeaders('DELETE');
            $this->enablePreflight();

            if (isset($_GET['topic_id']) && isset($_GET['project'])){
                $topicId = $_GET['topic_id'];
                $projectName = $_GET['project'];

                if(!$this->projectTopicDataModel->deleteProjectTopicRelationship($topicId, $projectName)){
                    $errors = $this->projectTopicDataModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(200);
                exit();
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);

        }
    }