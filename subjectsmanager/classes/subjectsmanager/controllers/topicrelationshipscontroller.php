<?php
	namespace SubjectsManager\Controllers;

	class TopicRelationshipsController extends SMController{
        private $topicRelationshipsModel;
		
		public function __construct(){
            parent::__construct();
			$this->topicRelationshipsModel = new \SubjectsManager\Models\TopicRelationshipsModel();
		}

        //Get a topic's related topics by scope(narrow or broadens)
        public function getTopicRelationshipsByScope(){
            $this->setHeaders('GET');
            $this->enablePreflight();
            if (isset($_GET['topic_id']) && isset($_GET['scope'])){
                $topicId = $_GET['topicId'];
                $scope =  $_GET['scope'];

				if(!$this->topicRelationshipsModel->getTopicRelationshipsByScope($topicId, $scope)){
					$errors = $this->topicRelationshipsModel->getErrors();
					$this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
					exit();
				}

				$data = $this->topicRelationshipsModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        //Get a topic's see also topics
        public function getTopicSeeAlso(){
            $this->setHeaders('GET');
            $this->enablePreflight();

            if (isset($_GET['topic_id'])){
                $topicId = $_GET['topic_id'];

                if(!$this->topicRelationshipsModel->getTopicSeeAlso($topicId)){
                    $errors = $this->topicRelationshipsModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $data = $this->topicRelationshipsModel->getData();
				$this->subjectsManagerView->respondWithData(200, $data);
				exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        //Post new topic-topic relationship
        public function postTopicRelationship(){
            $this->setHeaders('POST');
            $this->enablePreflight();

           if (isset($_GET['topic_id']) && isset($_GET['related_topic_id']) && isset($_GET['relationship'])){
                $topicId = $_GET['topic_id'];
                $relationship = $_GET['relationship'];
                $relatedTopicId = $_GET['related_topic_id'];

                if(!$this->topicRelationshipsModel->createTopicRelationship($topicId, $relationship, $relatedTopicId)){
                    $errors = $this->topicRelationshipsModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(200);
                exit();
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        //QUESTION: If topic, related_topic and relationship are all keys, what should the update feature do? Is deleting and adding a new relationship a better approach than updating?
        public function putTopicRelationship(){
        //     $this->setHeaders('PUT');
        //     $this->enablePreflight();

        //    if (isset($_GET['topic_id']) && isset($_GET['related_topic']) && isset($_GET['relationship'])){
        //         $topicId = $_GET['topic_id'];
        //         $relationship = $_GET['relationship'];
        //         $relatedTopicId = $_GET['related_topic'];

        //         if(!$this->topicRelationshipsModel->createTopicRelationship($topicId, $relationship, $relatedTopicId)){
        //             $errors = $this->topicRelationshipsModel->getErrors();
        //             $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
        //             exit();
        //         }

        //         $this->subjectsManagerView->respond(200);
        //         exit();
        //     }

        //     $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);
        }

        //Delete existing topic-topic relationship
        public function delTopicRelationship(){
            $this->setHeaders('DELETE');
            $this->enablePreflight();

            if (isset($_GET['topic_id']) && isset($_GET['relationship']) && isset($_GET['related_topic_id'])){
                $topicId = $_GET['topic_id'];
                $relationship = $_GET['relationship'];
                $relatedTopicId = $_GET['related_topic_id'];

                if(!$this->topicRelationshipsModel->deleteTopicRelationship($topicId, $relationship, $relatedTopicId)){
                    $errors = $this->topicRelationshipsModel->getErrors();
                    $this->subjectsManagerView->respondWithErrors($errors["status"], $errors["message"]);
                    exit();
                }

                $this->subjectsManagerView->respond(201);
                exit;
            }

            $this->subjectsManagerView->respondWithErrors(SMController::MISSING_PARAM_ERROR["status"], SMController::MISSING_PARAM_ERROR["message"]);

        }
    }