<?php
	namespace SubjectsManager\Models;

	class ProjectTopicDataModel extends DataModel {
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
	
		private function setData($dataIn){
			return $this->data = $dataIn;
		}

		//change the hide flag within a project topic data relationship
		function setHidden($projectName, $topicId, $hiddenBooleanValue){
			if (strlen($projectName) == 0 | strlen($topicId) == 0 | !is_bool($hiddenBooleanValue)){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Project name, topic id and value is required to change the hidden status of a topic from project's display";
				return false;
			}
			
			$query = "UPDATE project_topic_data SET hide=:hide WHERE topic_id=:topicId AND project_sitename=:projectName";

			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicId", $topicId);
				$this->DB->setParam(":projectName", $projectName);
				$this->DB->setParam(":hide", $hiddenBooleanValue);

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


		// Get all topics and related data that a project has
		function getTopics($projectName){
			if (strlen($projectName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Project name is required to get topics";
				return false;
			}


			$query = "SELECT * FROM project_topic_data
            		JOIN topics ON project_topic_data.topic_id = topics.id
            		WHERE project_topic_data.project_sitename = :projectName";

			try{
		
				$this->DB->prepQuery($query);
				$this->DB->setParam(":projectName", $projectName);

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


		// Get all topics and related data that a project has
		function getTopicNames($projectName){
			if (strlen($projectName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Project name is required to get topics";
				return false;
			}
			
			$query = "SELECT DISTINCT t.topic_name 
					FROM project_topic_data pt,topics t 
            		WHERE pt.topic_id = t.id AND pt.project_sitename = :projectName";

			try{
		
				$this->DB->prepQuery($query);
				$this->DB->setParam(":projectName", $projectName);

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

		// Get all projects with a given topic
		function getProjects($topicId){
			if (strlen($topicId) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Topic name is required to get all projects with that given topic";
				return false;
			}

			//QUESTION: What kind of project data would we want to return when listing all projects that have a given topic?
			$query = "SELECT * FROM project_topic_data
            		JOIN Project ON project_topic_data.project_sitename = Project.project_sitename
           			WHERE project_topic_data.topic_id = :topicId";

			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicId", $topicId);

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

		function getRecentlyEditedTopicNames($projectName){
			if (strlen($projectName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Project required to get all recently edit topics for that project";
				return false;
			}

			$query = "SELECT t.topic_name
					FROM project_topic_data pd, topics t
					WHERE pd.topic_id = t.id AND pd.project_sitename=:projectName
					ORDER BY last_update DESC
					LIMIT 20";

			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":projectName", $projectName);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();

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

		function createProjectTopicRelationship($topicId, $projectName, $internalNote, $publicNote){
			if (strlen($topicId) == 0 || strlen($projectName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Topic name and project name are required to create a project-topic relationship";
				return false;
			}
			
			$query = "INSERT INTO project_topic_data (topic_id, project_sitename, internalNote, publicNote) VALUES (:topicId, :projectName, :internalNote, :publicNote)";

			try{
				$this->DB->prepQuery($query);
				
				$this->DB->setParam(":topicId", $topicId);
				$this->DB->setParam(":projectName", $projectName);

				if (strlen($internalNote) > 0) {
					$this->DB->setParam(":internalNote", $internalNote);
				} else {
					$this->DB->setParam(":internalNote", NULL);
				}

				if (strlen($publicNote) > 0) {
					$this->DB->setParam(":publicNote", $publicNote);
				} else {
					$this->DB->setParam(":publicNote", NULL);
				}

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

		function getProjectTopicData($topicId, $projectName){
			if (strlen($topicId) == 0 || strlen($projectName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Topic name and project name are required to get a project-topic relationship";
				return false;
			}

			$query = "SELECT *, t.topic_name
					FROM project_topic_data pd, topics t
					WHERE t.id=:topicId AND pd.topic_id=t.id AND pd.project_sitename=:projectName";

			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicId", $topicId);
				$this->DB->setParam(":projectName", $projectName);

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

		function updateProjectTopicRelationship($topicId, $projectName, $internalNote, $publicNote){

			//TODO: Assess if description is the only thing that can be updated(Should we identify topics by id and allow names to be mutable?)
			if (strlen($topicId) == 0 || strlen($projectName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Topic name and project name are required to update a project-topic relationship";
				return false;
			}

			$query = "UPDATE project_topic_data SET internalNote=:internalNote, publicNote=:publicNote WHERE topic_id=:topicId AND project_sitename=:projectName";

			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicId", $topicId);
				$this->DB->setParam(":projectName", $projectName);
				

				if (strlen($internalNote) > 0){
					$this->DB->setParam(":internalNote", $internalNote);
				}else{
					$this->DB->setParam(":internalNote", NULL);
				}
					
				
				if (strlen($publicNote) > 0){
					$this->DB->setParam(":publicNote", $publicNote);
				}else{
					$this->DB->setParam(":publicNote", NULL);
				}

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

		function deleteProjectTopicRelationship($topicId, $projectName){

			if (strlen($topicId) == 0 || strlen($projectName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Topic name and project name are required to delete a project-topic relationship";
				return false;
			}

			$query = "DELETE FROM project_topic_data WHERE topic_id=:topicId AND project_sitename=:projectName";
			
			try{
				$this->DB->prepQuery($query);
				$this->DB->setParam(":topicId", $topicId);
				$this->DB->setParam(":projectName", $projectName);


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




		function getProjectTopicsInIdList($topicIds = [], $projectName = ""){
			if (count($topicIds) == 0 || strlen($projectName) == 0){
				$this->errors["status"] = 400;
				$this->errors["message"] = "Topic name and project name are required to check for topics in a project.";
				return false;
			}

			$inClause = implode(",", $topicIds);
			$query = "SELECT * FROM project_topic_data WHERE topic_id IN(" . $inClause . ") AND project_sitename=:projectName";
			
			try{
				//getProject topics
				$this->DB->prepQuery($query);
				$this->DB->setParam(":projectName", $projectName);
				$this->DB->runQuery();
				$projectRows = $this->DB->getAllRows();

				//get rest of topics info
				$query = "SELECT * FROM topics WHERE id IN(" . $inClause . ")";
				$this->DB->prepQuery($query);
				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();

				//merge 'em
				$out = [];
				foreach($projectRows as $p){
					$out[$p['topic_id']] = $p;
				}

				foreach($rows as $r){
					if(isset($out[$r['id']])){
						foreach($r as $field => $value){
							$out[$r['id']][$field] = $value;
						}
					}
				}

				$this->data = $out;

			} catch(Exception $e){
				$this->errors = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}

			return true;
		}


		// Get all topics and related data that a project has
		function getAllTopicRelationships(){
			$query = "SELECT * FROM project_topic_data order by topic_id ASC, project_sitename ASC";

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

	}

