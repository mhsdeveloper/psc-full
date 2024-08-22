<?php
	namespace SubjectsManager\Models;

	class TopicsSearchModel extends DataModel {
		public $data;
		public $errors;

		const SOLR_CORE = "publications";

		public function __construct(){
			parent::__construct();	
		}

		public function getData(){
			return $this->data;
		}
	
		public function getErrors(){
			return $this->errors;
		}



		function getAllFromDB($order = "topic_name ASC"){
				//first just query topics
				$query = "SELECT topic_name, id, see_id, is_umbrella, consensusDefinition FROM topics ORDER BY " . $order;

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



		function findInSOLR($searchString, $index = "*"){

			$url = "http://" . SOLR_IP .":8983/solr/" . self::SOLR_CORE ."/select?facet.limit=5000&facet.field=subject&facet.mincount=1&facet=true&fl=id%2C%20index&indent=true&q.op=OR&q=%2Bindex%3A" . $index
			. "%20%2Bstatus%3Apublished&rows=1&facet.sort=index&facet.contains.ignoreCase=true";

			//If not just the leading letter, use facet.contains to limit the list ahead of time
			if(strlen($searchString) > 1){
				$url .= "&facet.contains=" . $searchString;
			}	

			$resp = $this->curl($url);

			if(!isset($resp["facet_counts"]) 
				|| !isset($resp["facet_counts"]["facet_fields"])
				|| !isset($resp["facet_counts"]["facet_fields"]['subject'])){
					$this->errors[]= "SOLR did not return any date facets.";
					return false;
			}
			$data = $this->collateFacets($resp["facet_counts"]["facet_fields"]['subject']);

			//single letter gets all subjects, so filter to just leading letter
			if(strlen($searchString) == 1){
				$this->data = [];
				$searchString = strtolower($searchString);
				foreach($data as $name => $count){
					$initial = strtolower($name[0]);
					if($initial == $searchString) $this->data[$name] = $count;
				}
			}
			else $this->data = $data;
			return true;
		}



		function fullSearch($searchString, $projectName = ""){
			if(!empty($projectName)){
				return $this->searchProjectTopics($searchString, $projectName);
			}

/*
			This query is no good! If a topic hasn't been assigned to any project, it won't have a row in project_topic_data,
			so the join clause fails and it won't appear

			$query = "SELECT topic_name, topics.id, publicNote FROM topics, project_topic_data WHERE ( topic_name LIKE :searchString OR publicNote LIKE :searchString ) AND topics.id = project_topic_data.topic_id " . $projectClause. " ORDER BY topic_name ASC";

			SO: we must do several queries and merge the resultant ids
*/
			//first just query topics
			$query = "SELECT topic_name, topics.id, see_id FROM topics, project_topic_data 
				WHERE (topic_name LIKE :searchString OR consensusDefinition LIKE :searchString)
				AND topics.id = project_topic_data.topic_id 
				ORDER BY topic_name ASC";

			try{
		
				$this->DB->prepQuery($query);
				$this->DB->setParam(":searchString", "%" . $searchString . "%");

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



		function searchProjectTopics($searchString, $projectName){

			$query = "SELECT topics.id, topics.see_id, topic_name
			FROM topics, project_topic_data 
			WHERE ( topic_name LIKE :searchString OR publicNote LIKE :searchString OR consensusDefinition LIKE :searchString) 
			AND topics.id = project_topic_data.topic_id AND project_sitename = :projectName
			ORDER BY topic_name ASC";

			try {
				$this->DB->prepQuery($query);
				$this->DB->setParam(":searchString", "%" . $searchString . "%");
				$this->DB->setParam(":projectName", $projectName);

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
		}


		function findTopicInDB($searchString, $projectName = "", $leadingOnly = false){

			if(!empty($projectName)){
				$query = "SELECT topics.id, see_id, topic_name, consensusDefinition, publicNote, is_umbrella
				FROM topics, project_topic_data 
				WHERE topic_name LIKE :searchString 
				AND topics.id = project_topic_data.topic_id AND project_sitename = :projectName
				ORDER BY topic_name ASC";
			} else {
				$query = "SELECT topics.id, see_id, topic_name, consensusDefinition, is_umbrella
				FROM topics 
				WHERE topic_name LIKE :searchString 
				ORDER BY topic_name ASC";
			}

			try {
				$this->DB->prepQuery($query);
				$leadingWildcard = $leadingOnly ? "" : "%";
				$this->DB->setParam(":searchString", $leadingWildcard . $searchString . "%");
				if(!empty($projectName)) $this->DB->setParam(":projectName", $projectName);

				$this->DB->runQuery();
				$rows = $this->DB->getAllRows();
				$this->data = $rows;

			} catch(\Exception $e){
				$this->errors = $e->getMessage();
				if(count($this->errors)){
					\MHS\Sniff::print_r($this->errors);
				}
				return false;
			}
		}





		function curl($url){
			$ch = curl_init();
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_HTTPGET, true);

			$response = curl_exec($ch);
			if(!$response){
				$this->errors[] = "SOLR is not responding to cURL";
				return false;
			}
			return json_decode($response, true);
		}

		function collateFacets($facets){
			$out = [];
			$len = count($facets);
			for($i=0; $i<$len; $i += 2){
				$out[$facets[$i]] = $facets[$i + 1];
			}

			return $out;
		}


	}