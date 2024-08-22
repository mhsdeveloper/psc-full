<?php


	namespace Template\Controllers;


	require_once("mhs_global_env.php");


	class Search {

		//need to provide the index to limit results to just this set
		const SOLR_INDEX =  \MHS\Env::SOLR_INDEX;
		const SOLR_CORE = \MHS\Env::SOLR_CORE;
		

		function index(){
			$this->_mvc->render("search.php");
		}


		function coopIndex(){
			$this->_mvc->render("coop-search.php");
		}


		function read(){
			$this->_mvc->render("read.php");
		}


		function parsePersonParam(){
			if(!isset($_GET['p'])) return;
			$raw = html_entity_decode($_GET['p']);
			$raw = preg_replace("/\s\s+/", " ", $raw);
			$raw = trim($raw);
			$parts = explode(" ", $raw);
			$mentionedNames = [];
			foreach($parts as $p){
				if(strpos($p, ":")){
					$temp = explode(":", $p);
					if($temp[1] == "a") $_GET['a'] = '"' . $temp[0] . '"';
					else if($temp[1] == "r") $_GET['r'] =  '"' . $temp[0] . '"';
				} else {
					$mentionedNames[] = '"' . $p . '"';
				}
			}

			$_GET['p'] = implode(" ", $mentionedNames);
		}



		

		//search coming from /publications/[projects]/search page
		function ajaxQuery(){

			$this->parsePersonParam();

			//check config
			$confFile = $_SERVER['DOCUMENT_ROOT'] . "/publications/template/configs/" . $_GET['configURL'];
			$confFile = str_replace("..", "", $confFile);

			//load some config
			$conf = file_get_contents($confFile);
			$conf = json_decode($conf, true);
			//replace conf's url to match the one that got us here
			$conf['url'] = "/" . \MHS\Env::PROJECT_SHORTNAME . "/searchQuery";

			//add the index of our project
			if(\MHS\Env::PROJECT_SHORTNAME != "coop"){
				$conf['fields'][] = [
					"name" => "index",
					"value" => \MHS\Env::PROJECT_SHORTNAME,
					"type"=> "text",
					"required" => true
				];
			}

			$this->SOLR = new \Solr\SOLR(self::SOLR_CORE, "http", SOLR_IP);


			if(isset($_GET['']));


			$this->SOLR->respondtoArray($conf);
		}




		function solrDirectProjectSearch(){
			return $this->solrDirect(\MHS\Env::PROJECT_SHORTNAME);
		}



		function solrDirect($projectShortname = ""){
			$rowMax = 20;

			$q = \Solr\SolrDirect::parseQ($_GET['query']);

			//remove any attempt to see unpublished
			$q = preg_replace("/status=\s*:[a-zA-Z]+/", "", $q);

			//add published requirement
			$q .= " +status:published";

			if(!empty($projectShortname)){
				$q .= " +index:" . $projectShortname;
			}

			$q = \Solr\SolrDirect::cleanupQ($q);

			$queryString = \Solr\SolrDirect::addOtherSolrGetParams($q, $rowMax);

			$url = \Solr\SolrDirect::buildURL("publications", SOLR_IP);
			$url .= "?" . $queryString;

			$response = \Solr\SolrDirect::call($url);

			header('Content-Type: application/json');
			print $response;
		}


	} //class
