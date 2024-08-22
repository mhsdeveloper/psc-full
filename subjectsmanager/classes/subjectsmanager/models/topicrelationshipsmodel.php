<?php
	namespace SubjectsManager\Models;

	class TopicRelationshipsModel extends DataModel {
		public $data;
		public $errors;

		public function __construct(){
			parent::__construct();	
		}

		public function getData(){
			return $this->data;
		}
	
		public function getErrors(){
			return $this-> errors;
		}

		function test(){

			$this->_mvc->segment(1) ;// url segments: 1 is first in route

			try {
				$query = "SELECT * FROM topics WHERE name LIKE :term";

				$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
				$this->DB->prepQuery($query);
				$this->DB->setParam(":term", $term);
				// $this->DB->setParam(":project_id", \MHS\Env::PROJECT_ID);

				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				// print_r($rows);
				return $rows;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}

		}

		//Get a topic's related topics by scope(narrow or broadens)
		function getTopicRelationshipsByScope($topicId, $scope){

			if (strlen($topicId) == 0 || strlen($scope) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "A topic name and relationship scope is required to get relationships";
				return false;
			}

			switch($scope){
				case "broaden":{
					$query = "SELECT * FROM topic_relationships
							JOIN Topic ON topic_relationships.related_topic = Topic.:topic_id
							WHERE topic_relationships.:topic_id = '$topicId' AND topic_relationships.relationship = 'broadensTo'";
					break;
			
				} case "narrow": {
					$query = "SELECT * FROM topic_relationships
							JOIN Topic ON topic_relationships.:topic_id = Topic.:topic_id
							WHERE topic_relationships.related_topic = '$topicId' AND topic_relationships.relationship = 'broadensTo'";
					break;
				} default: {
					$this->errors["status"] = 400;
					$this->errors["message"] = "Invalid scope. Please use 'broaden' or 'narrow'";
					return false;
				}
			}

			try{		
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topic_id", $topicId);

				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				// print_r($rows);
				$this->data = $rows;

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}

			return true;
		}

		//Get a topic's see also topics
		function getTopicSeeAlso(){
			$topicId = $_GET['topicId'];

			if (strlen($topicId) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "A topic name is required to get see also";
				return false;
			}

			$query = "SELECT * FROM topic_relationships
            		JOIN Topic ON topic_relationships.related_topic = Topic.:topic_id
            		WHERE topic_relationships.:topic_id = :topicId AND topic_relationships.relationship = 'seeAlso'";
			
			try{
		
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topic_id", $topicId);


				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				// print_r($rows);
				$this->data = $rows;

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}

			return true;
		}

		function createTopicRelationship($topicId, $relationship, $relatedTopicId){

			if (strlen($topicId) == 0 || strlen($relatedTopicId) == 0 || strlen($relationship) == 0 ){
				$this->errors["status"] = 400;
				$this->errors["message"] = "A topic, related_topic and relationship is required create a topic-topic relationship";
				return false;
			}

			// if ((strcmp( $relationship, "broadensTo") !== 0) || (strcmp( $relationship, "seeAlso" ) !== 0)){
			// 	echo($relationship . "broadensTo");
			// 	$this->errors["status"] = 400;
			// 	$this->errors["message"] = "The relationship must be 'broadensTo' or 'seeAlso'";
			// 	return false;
			// }

			$existingQuery = "SELECT COUNT(*) FROM topic_relationships WHERE topic_id = :topicId AND relationship = :relationship AND related_topic_id = :relatedTopicId";
			$this->DB->prepQuery($existingQuery);
			$this->DB->setParam(":topicId", $topicId);
			$this->DB->setParam(":relationship", $relationship);
			$this->DB->setParam(":relatedTopicId", $relatedTopicId);
			$existingResult = $this->DB->runQuery();
			$rows = $this->DB->getAllRows();
			$this->data = $rows;

			if (isset($rows) && $rows[0]["COUNT(*)"] > 0) {
				$this->errors["status"] = 400;
				$this->errors["message"] = "The relationship already exists";
				return false;
			}

			$query = "INSERT INTO topic_relationships (topic_id, relationship, related_topic_id) VALUES (:topicId, :relationship, :relatedTopicId)";
			
			// try{
		
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicId", (int) $topicId);
				$this->DB->setParam(":relationship", $relationship);
				$this->DB->setParam(":relatedTopicId", (int) $relatedTopicId);

				$this->DB->runQuery();

			// } catch(Exception $e){
			// 	$this->errors = $e->getMessage();
			// 	if(count($errors)){
			// 		\MHS\Sniff::print_r($errors);
			// 	}
			// 	return false;
			// }

			return true;
		}



		//QUESTION: If topic, related_topic and relationship are all keys, what should the update feature do? Is deleting and adding a new relationship a better approach than updating?
		function updateTopicRelationship(){
			
		}

		function deleteTopicRelationship($topicId, $relationship, $relatedTopicId){
			if (strlen($topicId) == 0 || strlen($relatedTopicId) == 0 || strlen($relationship) == 0  ){
				$this->errors["status"] = 400;
				$this->errors["message"] = "A topic, related_topic and relationship is required delete a topic-topic relationship";
				return false;
			}

			$query = "DELETE FROM topic_relationships WHERE topic_id=':topicId' AND related_topic_id=':relatedTopicId' AND relationship=':relationship'";
			
			try{
		
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicId", (int) $topicId);
				$this->DB->setParam(":relationship", $relationship);
				$this->DB->setParam(":relatedTopicId", (int) $relatedTopicId);

				$this->DB->runQuery();

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}

			return true;
		}



		function getAllRelationships($topicId){

			$query = "SELECT relationship, topic_id, related_topic_id FROM topic_relationships WHERE (topic_id = :id OR related_topic_id = :id)
				";//	GROUP BY relationship, topic_id, related_topic_id";

			try {

				$this->DB->prepQuery($query);
				$this->DB->setParam(":id", $topicId);

				$this->DB->runQuery();
				$this->data = $this->DB->getAllRows();
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