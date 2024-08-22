<?php
	namespace SubjectsManager\Models;

	class TopicsModel extends DataModel {
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



		// function test(){

		// 	$this->_mvc->segment(1) ;// url segments: 1 is first in route

		// 	try {
		// 		$query = "SELECT * FROM topics WHERE name LIKE :term";

		// 		$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);		
		// 		$this->DB->prepQuery($query);
		// 		$this->DB->setParam(":term", $term);
		// 		// $this->DB->setParam(":project_id", \MHS\Env::PROJECT_ID);

		// 		$this->DB->runQuery();
		// 		$rows = $this->DB->getAllRows();
		// 		print_r($rows);
		// 		return $rows;

		// 	} catch(\Exception $e){
		// 		$errors[] = $e->getMessage();
		// 		if(count($errors)){
		// 			\MHS\Sniff::print_r($errors);
		// 		}
		// 		return false;
		// 	}

		// }

		function validateTopicName($inputString) {
			$uncapitalizedArr = array("and", "as", "as if", "as long as", "at", "but", "by", "even if", "for", "from", "if", "if only", "in", "into", "like", "near", "now that", "nor", "of", "off", "on", "on top of", "once", "onto", "or", "out of", "over", "past", "so", "so that", "than", "that", "the", "till", "to", "up", "upon", "v.", "with", "when", "yet");
		
			$parts = preg_split('/\s+| /', $inputString);
			$capitalizedParts = array();
		
			foreach ($parts as $part) {
				if (!in_array($part, $uncapitalizedArr)) {
					$part = ucfirst($part);
				}
				$capitalizedParts[] = $part;
			}
		
			$resultString = implode(' ', $capitalizedParts);
			$resultString = trim($resultString);

			return $resultString;
		}
		

		function test2(){
			http_response_code(201);
			echo json_encode(['hey' => true]);
		}

		function getProjectName(){ 
			$this->data["project"] =  $_SESSION['PSC_SITE'];
			// $this->data["project"] = var_dump($_SESSION);
			if(isset($this->data["project"])){
				return true;
			}

			return false;
		}

		//Helper function to get the id of topic by name for import functionality
		function getIdByName($topicName){
			if (strlen($topicName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "A topic name is required to get id";
				return false;
			}

			$query = "SELECT id FROM topics
            		WHERE topic_name LIKE :topic_name";
			
			$topicName = $topicName . "%";
			
			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topic_name", $topicName);
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

		//Helper function to determine if topic is umberella for import functionality
		function getAllUmbrellas(){
			$query = "SELECT  DISTINCT topic_name 
					FROM topics t, topic_relationships tr
					WHERE t.id = tr.related_topic_id AND tr.relationship = 'broadensTo'
					ORDER BY topic_name ASC";
			
			try{
				$this->DB->prepQuery($query);
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

		function getNameById($topicId) {		
			$query = "SELECT topic_name FROM topics WHERE id = :topic_id";
		
			try {
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topic_id", $topicId);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				$this->data = $rows;
			} catch (Exception $e) {
				$this->errors = $e->getMessage();
				if (count($errors)) {
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		
			return true;
		}

		function getAllSubtopics($umbrellaName){
			$query = "SELECT t.topic_name 
					FROM topics t, topics u, topic_relationships tr
					WHERE u.topic_name = :umbrellaName AND u.id = tr.related_topic_id AND t.id = tr.topic_id AND tr.relationship = 'broadensTo'";
			
			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":umbrellaName", $umbrellaName);
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

		function searchTopics($search){
			$query = "SELECT topic_name 
					FROM topics
					WHERE UPPER(topic_name) LIKE UPPER(:search)";
			
			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":search", $search . "%");
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
		

		
		//Post new topic and see
		function createTopic($topicName, $seeId, $consensusDefinition){
			if (strlen($topicName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "A topic name is required to create a new topic";
				return false;
			}

			$topicName = $this->validateTopicName($topicName);

			$query = "INSERT INTO topics (topic_name, see_id, consensusDefinition) VALUES (:topicName, :see, :consensusDefinition)";
			
			// try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicName", $topicName);

				if (strlen($seeId) > 0) {
					$this->DB->setParam(":see", $seeId);
				}else{
					$this->DB->setParam(":see", null);
				}


				if (strlen($consensusDefinition) > 0) {
					$this->DB->setParam(":consensusDefinition", $consensusDefinition);
				}else{
					$this->DB->setParam(":consensusDefinition", null);
				}
				$this->DB->runQuery();

			// } catch(Exception $e){
			// 	$this->errors = $e->getMessage();
			// 	if(count($this->errors)){
			// 		\MHS\Sniff::print_r($this->errors);
			// 	}
			// 	return false;
			// }

			return true;
		}

		function updateTopicUmbrellaStatus($topicName, $isUmbrella){
			if(strlen($topicName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "topics id is required to update topic";
				return false;
			}

			$query = "UPDATE topics SET is_umbrella = :isUmbrella  WHERE topic_name LIKE :topicName";

			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicName", $topicName);
				$this->DB->setParam(":isUmbrella", $isUmbrella);

				$this->DB->runQuery();

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($this->errors)){
					\MHS\Sniff::print_r($this->errors);
				}
				return false;
			}

			return true;

		}

		function updateTopic($topicId, $topicName, $see, $consensusDefinition){
			if (strlen($topicId) == 0 ){
				$this->errors["status"] = 400;
				$this->errors["message"] = "topic id is required to update topic";
				return false;
			}

			$query = "UPDATE topics SET see_id=':see', consensusDefinition=':consensusDefinition', topic_name=':topicName' WHERE id=':topicId'";

			try{
				$this->DB->prepQuery($query);
				if (strlen($topicName) > 0) $this->DB->setParam(":topicName", $topicName);
				if (strlen($see) > 0) $this->DB->setParam(":see", $see);
				if (strlen($consensusDefinition) > 0) $this->DB->setParam(":consensusDefinition", $consensusDefinition);
				$this->DB->setParam(":topicId", $topicId);


				$this->DB->runQuery();

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($this->errors)){
					\MHS\Sniff::print_r($this->errors);
				}
				return false;
			}

			return true;
		}



		function deleteTopic($topicId){
			$topicName = $_GET['topic'];

			if (strlen($topicName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "topics name is required to delete a topic";
				return false;
			}

			$query = "DELETE from topics WHERE id=:topicId";

			//TODO/QUESTION: delete all rows in topic_relationships and project_topic_data that reference thie topic. (can we make the db take care of this)
			
			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicId", $topicId);

				$this->DB->runQuery();

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($this->errors)){
					\MHS\Sniff::print_r($this->errors);
				}
				return false;
			}

			return true;
		}

		//Get broader topics for when seeAlso = 0
		function getBroaderTopics($topicName){
			$query = "SELECT
						u.topic_name
					FROM
						topics t,
						topics u,
						topic_relationships tr
					WHERE
						t.topic_name = :topicName AND t.id = tr.topic_id AND u.id = tr.related_topic_id AND tr.relationship = 'broadensTo'";
			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicName", $topicName);
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


		//Get all topic data: see, see also, and broadens to
		function getAllTopicData($topicName){


			if(is_numeric($topicName)){
				$query = "SELECT * FROM topics
				WHERE id = :topicName";
			} else {
				$query = "SELECT * FROM topics
				WHERE topic_name = :topicName";
				if (strlen($topicName) == 0){
					$this->errors["status"] = 400;
					$this->errors["message"] = "A topic name is required to get all topic data";
					return false;
				}
			}

			try{	
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicName", $topicName);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				$this->data = $rows;

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($this->errors)){
					\MHS\Sniff::print_r($this->errors);
				}
				return false;
			}

			return true;
		}




		//Get all topic data: see, see also, and broadens to
		function getTopicsInSet($inArray){
			$this->data = [];

			//if empty array passed, just return, having set the data to empty
			if(empty($inArray)) return true;

			$inClause = implode(",", $inArray);

			$query = "SELECT * FROM topics
					WHERE id IN (" . $inClause . ") 
					ORDER BY id";

			try{
		
				$this->DB->prepQuery($query);

				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				$this->data = $rows;

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($this->errors)){
					\MHS\Sniff::print_r($this->errors);
				}
				return false;
			}

			return true;
		}





		//Get all topic data: see, see also, and broadens to
		function getTopicsByName($inArray){
			$this->data = [];

			//if empty array passed, just return, having set the data to empty
			if(empty($inArray)){
				$this->errors = ['message' => 'No topic names received', 'status' => 'ok'];
				return false;
			}

			$inClause = implode('","', $inArray);
			$inClause = '"' . $inClause . '"';

			$query = "SELECT * FROM topics
					WHERE topic_name IN(" . $inClause . ") 
					ORDER BY id";
			try{
		
				$this->DB->prepQuery($query);

				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				$this->data = $rows;

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($this->errors)){
					\MHS\Sniff::print_r($this->errors);
				}
				return false;
			}

			return true;
		}
		

	}