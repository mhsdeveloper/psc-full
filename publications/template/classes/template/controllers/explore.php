<?php


	namespace Template\Controllers;


	require_once("mhs_global_env.php");


	class Explore {

		//need to provide the index to limit results to just this set
		const SOLR_INDEX =  \MHS\Env::SOLR_INDEX;
		const SOLR_CORE = \MHS\Env::SOLR_CORE;
		

		function index(){
			$data['projectShortName'] = \MHS\Env::PROJECT_SHORTNAME;
			$data['husc'] = "";
			$data['topic'] = "";

			//segment 2 is "explore", 3 might be "person" or "topic
			$type = $this->_mvc->segment(3);
			$value = $this->_mvc->segment(4);
			if(empty($type) || empty($value)){
				header("Location: /" . \MHS\Env::PROJECT_SHORTNAME . "/explore");
				die();
				return;
			} else if($type == "person") {
				$data['husc'] = $value;
				$this->_mvc->render("explore-person.php", $data);
			} else if($type == "topic") {
				$data['topic'] = $value;
				$this->_mvc->render("explore.php", $data);
			}
		}




		function ajaxQuery(){
			//load some config
			$confFile = \MHS\Env::getSolrSearchConfig();
			$conf = file_get_contents($confFile);
			$conf = json_decode($conf, true);
			//replace conf's url to match the one that got us here
			$conf['url'] = "/" . \MHS\Env::PROJECT_SHORTNAME . "/searchQuery";

			//add the index of our project
			$conf['fields'][] = [
				"name" => "index",
				"value" => \MHS\Env::PROJECT_SHORTNAME,
				"type"=> "text",
				"required" => true
			];

			$this->SOLR = new \Solr\SOLR(self::SOLR_CORE, "http", SOLR_IP);
			$this->SOLR->respondtoArray($conf);
		}


	} //class
