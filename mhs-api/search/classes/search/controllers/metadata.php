<?php

	namespace Api\Controllers;

	include("mhs_global_env.php");


	class SolrSearch {

		//need to provide the index to limit results to just this set
		const SOLR_INDEX = "*";
		const SOLR_CORE = "publications";

		function index(){
			//check config
			$confFile = $_SERVER['DOCUMENT_ROOT'] . $_GET['configURL'];
			$confFile = str_replace("..", "", $confFile);
			$conf = file_get_contents($confFile);

			$this->SOLR = new \Publications\SOLR\SOLR(self::SOLR_CORE, "http", SOLR_IP);
			$this->SOLR->responder($conf);
		}

		function preSearchHook(){
		}

	} //class
