<?php
	namespace SubjectsManager\Controllers;

	class TopicsController extends SMController{
        private $topicsModel;
		
		public function __construct(){
            parent::__construct();
			$this->topicsModel = new \SubjectsManager\Models\TopicsModel();
		}

        public function getProject(){
            $this->setHeaders('GET');
            $this->enablePreflight();

            if(!$this->topicsModel->getProjectName()){
                $errors = $this->topicsModel->getErrors();
                $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                exit();
            }

            $data = $this->topicsModel->getData();
            $this->subjectsManagerView->respondWithData(200, $data);
            exit;
        }


        public function getId(){
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['topic'])){
                $topicName = $_GET['topic'];

				if(!$this->topicsModel->getIdByName($topicName)){
					$errors = $this->topicsModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->topicsModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }


        public function getName(){
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['id'])){
                $topidId = $_GET['id'];

				if(!$this->topicsModel->getNameById($topidId)){
					$errors = $this->topicsModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->topicsModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        public function getAllUmbrellas(){
            $this->setHeaders('GET');
            $this->enablePreflight();
            if(!$this->topicsModel->getAllUmbrellas()){
                $errors = $this->topicsModel->getErrors();
                $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                exit();
            }

            $data = $this->topicsModel->getData();
            $this->subjectsManagerView->respondWithData(200, $data);
            exit;

        }

        public function getSubTopics(){
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['topic'])){
                $topicName = $_GET['topic'];

				if(!$this->topicsModel->getAllSubtopics($topicName)){
					$errors = $this->topicsModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->topicsModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        public function getBroaderTopics(){
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['topic'])){
                $topicName = $_GET['topic'];

				if(!$this->topicsModel->getBroaderTopics($topicName)){
					$errors = $this->topicsModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->topicsModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        public function searchTopics(){
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['search'])){
                $search = $_GET['search'];

				if(!$this->topicsModel->searchTopics($search)){
					$errors = $this->topicsModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->topicsModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        public function getAllTopicData(){
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['topic'])){
                $topicName = $_GET['topic'];

				if(!$this->topicsModel->getAllTopicData($topicName)){
					$errors = $this->topicsModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->topicsModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        public function postTopic(){
            $this->setHeaders('POST');
            $this->enablePreflight();

            //decode and then sanitize each $_GET; remove uncess characters (*, ;, =, )
            if (isset($_GET['topic'])){
                $topicName = $_GET['topic'];
                $see = urldecode($_GET['see_id']);
                $consensusDefinition = $_GET['consensusDefinition'];

                if(!$this->topicsModel->createTopic($topicName, $see, $consensusDefinition)){
                    $errors = $this->topicsModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(201);
                exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        public function putTopic(){
            $this->setHeaders('PUT');
            $this->enablePreflight();

            if (isset($_GET['topic_id'])){
                $topicId = $_GET['topic_id'];
                $topicName = $_GET['topic'];
                $newSee = $_GET['see'];
                $consensusDefinition = $_GET['consensusDefinition'];
                if(!$this->topicsModel->updateTopic($topicId, $topicName, $newSee, $consensusDefinition)){
                    $errors = $this->topicsModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(200);
                exit();
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        public function postUmbrella(){
            $this->setHeaders('POST');
            $this->enablePreflight();

            if (isset($_GET['topic_name']) && isset($_GET['is_umbrella'])){
                $topicName = $_GET['topic_name'];
                $isUmbrella = $_GET['is_umbrella'];
                if(!$this->topicsModel->updateTopicUmbrellaStatus($topicName, $isUmbrella)){
                    $errors = $this->topicsModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(200);
                exit();
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }



        public function delTopic(){
            $this->setHeaders('DELETE');
            $this->enablePreflight();

            if (isset($_GET['topic_id'])){
                $topicId = $_GET['topic_id'];

                if(!$this->topicsModel->deleteTopic($topicId)){
                    $errors = $this->topicsModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(201);
                exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }


		public function getTopicsByName(){
			$raw = $_GET['t'];
			$topics = preg_replace("/[:\?$\/]/", "", $raw);
			$topics = explode(";", $topics);

			$project = preg_replace("/[^0-9a-zA-Z]/", "", $_GET['project']);


			if(!$this->topicsModel->getTopicsByName($topics)){
				$errors = $this->topicsModel->getErrors();
				$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
				exit();
			}

			$foundTopics = $this->topicsModel->getData();
			$ids = [];
			foreach($foundTopics as $topic){
				$ids[] = $topic["id"];
			}

			$ptmodel = new \SubjectsManager\Models\ProjectTopicDataModel();

			if(!$ptmodel->getProjectTopicsInIdList($ids, $project)){
				$errors = $this->topicsModel->getErrors();
				$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
				exit();
			}

			$assigned = $ptmodel->getData();

			$data = ["topics" => $foundTopics, "assigned" => $assigned];

			//match these topics with
			$this->subjectsManagerView->respondWithData(200, $data);
			
		}



        public function csvtodb(){

// die("DISABLED IN topicscontroller.php so i don't accidentally run again");


            $reader =  new \SubjectsManager\Helpers\CSVtoDB('/data.csv', '$');
            $reader->process();
        }
    
    }