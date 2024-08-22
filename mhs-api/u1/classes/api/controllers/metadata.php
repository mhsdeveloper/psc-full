<?php


	namespace API\Controllers;


	/*
		?index=cms&person_keyword=adams-john&date_year=*
		&facets=person_keyword,date_year,subject


		PERSON DOCUMENTS:
		person_keyword=[HUSC]
		&facets=subject,date_year


		Full parameter set:

		project=cms						shortname for a project; without this, all projects searched

		person_keyword=[HUSC HUSC]		single or space-separated list of huscs to query
		date_year=[4 digit year]		
		subject=[url encoded subject]	single subject to search on

		&raw=[0|1] 						0: the default, groups the return into predictable lists?
										1: returns the raw SOLR output
	*/


	class Metadata {

		//need to provide the index to limit results to just this set
		//const SOLR_INDEX =  \MHS\Env::SOLR_INDEX;
		const SOLR_CORE = "publications";


		function __construct(){
		}

		function index(){
			//load some config
			$confFile = SERVER_WWW_ROOT . "html/mhs-api/u1/configs/solr-metadata.json";
			$conf = file_get_contents($confFile);
			$conf = json_decode($conf, true);
			// //replace conf's url to match the one that got us here
			// $conf['url'] = "/" . \MHS\Env::PROJECT_SHORTNAME . "/test";

			if(isset($_GET['project'])){
				$conf['fields'][] = [
					"name" => "index",
					"param" => "project",
					"type"=> "text",
					"required" => true
				];
			}


			if(isset($_GET['person_keyword'])){
				$conf['fields'][] = [
					"name" => "person_keyword",
					"param" => "person_keyword",
					"type"=> "text",
					"required" => true
				];
			}

			if(isset($_GET['subject'])){
				$conf['fields'][] = [
					"name" => "subject",
					"param" => "subject",
					"phraseLike" => true,
					"type"=> "text",
					"required" => true
				];
			}



			$this->SOLR = new \Solr\SOLR(self::SOLR_CORE, "http", SOLR_IP);
			$this->SOLR->respondtoArray($conf);
		}

	} //class
