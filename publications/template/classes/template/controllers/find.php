<?php


	namespace Template\Controllers;


	require_once("mhs_global_env.php");


	class Find {

		//need to provide the index to limit results to just this set
		const SOLR_INDEX =  \MHS\Env::SOLR_INDEX;
		const SOLR_CORE = \MHS\Env::SOLR_CORE;
		

		function index(){
			$this->_mvc->render("find.php");
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
