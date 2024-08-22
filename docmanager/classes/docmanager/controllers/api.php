<?php


	namespace DocManager\Controllers;


	/* class for retrieving
	*/

	class Api extends AjaxController {

		private $order = "filename";
		private $dir = "ASC";
		private $params = [];
		private $start = 0;
		private $count = 20;
		private $total = 0;
		private $projectMetadata = [];
		private $outputCols = ["filename", "updated_at", "publish_date", "published"];
		const LOG_FILE = "access.log";

		function __construct(){
			parent::__construct();
		}

		function index(){
			$headers = getallheaders();
			if(!isset($headers['Authorization'])) $this->fail("No 'Authorization' token provided.", 401);
			
			$key = trim(str_ireplace("Bearer", "", $headers['Authorization']));

			$this->keySettings = \DocManager\Models\ApiKeys::CheckKey($key);

			if(false === $this->keySettings){
				$this->fail("Authorization failed.", 401);
			}


			//route on verbs/methods here

			//only route right now
			$this->docsByDate();
		}




		private function docsByDate(){
			$this->loadProjectMetadata();

			$this->parseReq();

			if(isset($_GET['dateOfChange'])) {
				$dateOfChange = urldecode($_GET['dateOfChange']);
			} else if(isset($_GET['dateofchange'])) {
				$dateOfChange = urldecode($_GET['dateofchange']);
			} else {
				$dateOfChange = "2000-01-01 00:00:00";
			}

			if(strlen($dateOfChange) == 10){
				$dateOfChange .= " 00:00:00";
			}


			preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/", $dateOfChange, $matches);
			if(!isset($matches[0]) || $matches[0] != $dateOfChange){
				$this->fail("Please use ISO YYYY-MM-DD HH:MM:SS format for dateOfChange.", 400);
			}

			if($this->count > $this->keySettings["docsPerRequest"]){
				$this->count = $this->keySettings["docsPerRequest"];
				$this->AR->messages("Limiting request size to " . $this->count . " documents");
			}

			if(!empty($this->params['edition'])){
				$project_id = $this->projectMetadata["nameToID"][$this->params['edition']];
			} else $project_id = 0;

			$docs = new \DocManager\Models\Documents();
			$rows = $docs->getChangedDocuments($dateOfChange, $this->start, $this->count, $this->order, $project_id);
			$count = $docs->getCount();

			if(false === $rows){
				$this->fail("Error retrieving docs from db");
			}

			//enhance data; flesh out projects and URLs
			$output = [];
			foreach($rows as $key => $doc){
				$outDoc = [];
				foreach($this->outputCols as $colname){
					$outDoc[$colname] = $doc[$colname];
				}
				// $url = $this->projectMetadata[$doc['project_id']]['abbr'] . "/xml/" . $doc['filename'];
				// $outDoc['url'] = $url;
				$outDoc['project_abbr'] =  $this->projectMetadata[$doc['project_id']]['abbr'];
				$output[] = $outDoc;
			}	

			$returnedCount = count($output);

			$json = json_encode($output);

//			$this->logAccess(['queryDate' => $dateOfChange, 'start' => $this->start, 'count' => $count, 'returned' => $returnedCount]);
		
			$this->AR->response['urlScheme'] = '///' . PROJECTS_FOLDER . '/${project_abbr}/xml/${filename}';
			$this->AR->data("documents", $output);
			$this->AR->data("total", $count);
			$this->AR->data('count', $returnedCount);
			$this->AR->statusOk();
			$this->AR->respond();
		}


		function logAccess($metrics){
			$date = date("Y-m-d H:i:s");
			$out = $date . " ----- ";
			foreach($metrics as $key => $val){
				$out .= " | " . $key . ": " . $val;
			}
			$out .= "\n";
			
			$headers = getallheaders();
			foreach($headers as $key => $val){
				$out .= "\n-- " . $key . ": " . $val;
			}

			$out .= "\n";

			@file_put_contents(self::LOG_FILE, $out, FILE_APPEND);
		}




		function loadProjectMetadata(){
			$json = file_get_contents(SERVER_WWW_ROOT . 'projects.json');
			$this->projectMetadata = json_decode($json, true);
		}



		function parseReq(){
			if(isset($_GET['order'])){
				switch($_GET['order']){
					case "checked_outin_date";
					case "checkout":
					case "check_outin_date": $this->order = "checked_outin_date DESC"; break;

					case "published": $this->order = "published DESC"; break;

					case "unpublished": $this->order = "published ASC"; break;

					case "checked_outin_by":
					case "checkedby":
					case "check_outin_by": $this->order = "checked_outin_by ASC"; break;

					case "filename": $this->order = "filename ASC"; break;
					case "filenameRev": $this->order = "filename DESC"; break;
				}
			} else $this->order = "updated_at DESC, publish_date DESC, filename ASC";
			
			if(isset($_GET['start'])){
				$start = preg_replace("/[^0-9]/", "", $_GET['start']);
				$this->start = $start;
			}

			if(isset($_GET['count'])){
				$count = preg_replace("/[^0-9]/", "", $_GET['count']);
				$this->count = $count;
			}

			if(isset($_GET['user'])){
				$user = preg_replace("/[^a-zA-Z0-9\-\_\.\@\+]/", "", $_GET['user']);
				if(!empty($user)) $this->params['checked_outin_by'] = $user;
			}

			if(isset($_GET['filename'])){
				$filename = preg_replace("/[^a-zA-Z0-9\-\_\.]/", "", $_GET['filename']);
				if(!empty($filename)) $this->params['filename'] = $filename;
			}

			if(isset($_GET['edition'])){
				$edition = preg_replace("/[^a-z]/", "", strtolower($_GET['edition']));
				$this->params['edition'] = $edition;
			}

		}

	}