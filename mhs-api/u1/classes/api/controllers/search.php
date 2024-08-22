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


	class Search {

		//need to provide the index to limit results to just this set
		//const SOLR_INDEX =  \MHS\Env::SOLR_INDEX;
		const SOLR_CORE = "publications";


		function __construct(){
		}

		function index(){
			//load some config
			$conf = [
				"fields" => [
					[
						"type" => "intDateRange",
						"startName" => "date_when",
						"endName" => "date_to",
						"startValue" => $startDate,
						"endValue" => $endDate,
					],
					[
						"name" => "status",
						"value" => "published"
					]
				],
				"facetFields" => [],
				"displayFields" => ["id", "filename", "date_when", "title", "doc_beginning", "resource_uri", "status"],
				"sortFields" => [
					["name" => "date_when",	"sort" => "asc" ]
				]
			];

			if(isset($_GET['project'])){
				$conf['fields'][] = [
					"name" => "index",
					"param" => "project",
					"type"=> "text",
					"required" => true
				];
			}

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
