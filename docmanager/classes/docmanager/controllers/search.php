<?php

	namespace DocManager\Controllers;



	class Search extends AjaxController {

		//need to provide the index to limit results to just this set
		const SOLR_INDEX = "*";
		const SOLR_CORE = "publications";


		
		function index(){
			//check config
			$confFile = \MHS\Env::getSolrConfig();
			$confFile = str_replace("..", "", $confFile);
			$conf = file_get_contents($confFile);

			if($conf === false) {
				$this->fail("Can't load the solr config json");
			}

			$conf = json_decode($conf, true);

			//find index field and add our project name
			foreach($conf['fields'] as $key => $field){
				if($field['name'] == "index"){
					$conf['fields'][$key]['value'] = \MHS\Env::PROJECT_SHORTNAME;
					break;
				}
			}

			$grouping = false;
			if(isset($_GET['groupField'])){
				$grouping = true;
				$groupField = preg_replace("/[^a-zA-Z_\-0-9]/", "", $_GET['groupField']);
			}


			$this->SOLR = new \Solr\SOLR(self::SOLR_CORE, "http", SOLR_IP);
			if($grouping){
				$this->SOLR->groupBy($groupField);
			}
			$this->SOLR->respondToArray($conf);
		}

		function preSearchHook(){

		}


	} //class
