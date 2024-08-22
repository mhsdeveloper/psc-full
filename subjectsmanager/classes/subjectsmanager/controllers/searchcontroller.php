<?php
	namespace SubjectsManager\Controllers;

	class SearchController extends SMController{
        private $topicsModel;
		
		public function __construct(){
            parent::__construct();
			$this->model = new \SubjectsManager\Models\TopicsSearchModel();
		}



		/**
		 * This function should call model's methods for searching only by topic name
		 */
		public function index(){
			$terms = "A";
			if(isset($_GET['terms'])) $terms = preg_replace("/;\:\)\(\.\'\"/", "", $_GET['terms']);
			
			$project = "";
			if(isset($_GET['project'])) $project = preg_replace("//", "", $_GET['project']);
			
			$leadingOnly = strlen($terms) < 2 ? true : false;

			$this->model->findTopicInDB($terms, "", $leadingOnly);
			
			$data = $this->model->data;

			$this->subjectsManagerView->respondWithData(200, $data);
		}



		public function getAllFromDB(){
			if(isset($_GET['order']) && $_GET['order'] == 'id'){
				$order = "id ASC";
			} else {
				$order = "topic_name ASC";
			}
			$this->model->getAllFromDB($order);
			$data = $this->model->getData();
			$this->subjectsManagerView->respondWithData(200, $data);
		}



		/**
		 * This method for searches including descriptions/public notes
		 */
		public function fullSearch(){
			$this->model->fullSearch($terms, $project);
		}



		public function getCompleteTopic(){
			$name =  preg_replace("/;\:\+\%\#\@\!\"\'/", "", urldecode($_GET['name']));
			//first get main table columns
			$model = new \SubjectsManager\Models\TopicsModel();

			$model->getAllTopicData($name);
			$data = $model->data;
			//then get project descriptions
			if(isset($data[0])){
				//lastly, get relationships
				$return = $this->getRelationshipsCore($data[0]["id"]);
				//join all data before replay
				$return["id"] = $data[0]["id"];
				$return['consensusDefinition'] = $data[0]['consensusDefinition'];
				$return['see_id'] = $data[0]['see_id'];
			} else {
				$return = $data;
			}

			$this->subjectsManagerView->respondWithData(200, $return);
		}

		

		public function getAllInSOLR(){
			$project = "*";
			if(isset($_GET['project'])) $project = preg_replace("/[^0-9a-z]/", "", strtolower($_GET['project']));

			$this->model->findInSOLR("", $project);
			$data = $this->model->data;
			$this->subjectsManagerView->respondWithData(200, $data);
		}


		public function getInSOLR(){
			$project = "*";
			$terms = "";
			if(isset($_GET['project'])) $project = preg_replace("/[^0-9a-z]/", "", strtolower($_GET['project']));
			if(isset($_GET['terms'])) $terms = preg_replace("/[\+\(\)\[\]\$\#\@\!\=\-\']/", " ", strtolower($_GET['terms']));

			$this->model->findInSOLR($terms, $project);
			$data = $this->model->data;
			$this->subjectsManagerView->respondWithData(200, $data);
		}


		/**
		 * This is the responder to direct Relationships queries
		 */
		public function getRelationships(){
			$id = preg_replace("/[^0-9]/", "", $_GET['id']);
			$return = $this->getRelationshipsCore($id);
			$this->subjectsManagerView->respondWithData(200, $return);
		}



		protected function getRelationshipsCore($id){
			$model = new \SubjectsManager\Models\TopicRelationshipsModel();
			$model->getAllRelationships($id);
			$data = $model->data;

			$return = ["relationships" => $data];

			//now get all the ids and fleshout the topics for them
			$ids = [];
			foreach($data as $row){
				if(!in_array($row['topic_id'], $ids)) $ids[] = $row['topic_id'];
				if(!in_array($row['related_topic_id'], $ids)) $ids[] = $row['related_topic_id'];
			}


			$model = new \SubjectsManager\Models\TopicsModel();
			$model->getTopicsInSet($ids);
			$return["topics"] = $model->data;

			return $return;
		}
    }