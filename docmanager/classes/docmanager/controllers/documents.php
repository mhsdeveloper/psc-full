<?php


	namespace DocManager\Controllers;


	/* class for retrieving
	*/

	class Documents extends AjaxController {

		private $order = "filename";
		private $dir = "ASC";
		private $params = [];
		private $start = 0;
		private $count = 20;
		private $total = 0;

		function index(){
			//this responds to API calls, not the docmanage GUI!!
			if(isset($_GET['f'])) {
				return $this->docsFromFilenames();
			} 

			$this->parseReq();

			$docs = new \DocManager\Models\Documents();

			$rows = $docs->getProjectDocuments($this->params, $this->start, $this->count, $this->order);
			$this->total = $docs->getLastQueryCount();


			if(false === $rows){
				$this->fail($docs->getErrors());
			}

			return $this->respondWithWorkflow($rows);
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
			}
			
			if(isset($_GET['start'])){
				$start = preg_replace("/[^0-9]/", "", $_GET['start']);
				$this->start = $start;
			}

			if(isset($_GET['user'])){
				$user = preg_replace("/[^a-zA-Z0-9\-\_\.\@\+]/", "", $_GET['user']);
				if(!empty($user)) $this->params['checked_outin_by'] = $user;
			}

			if(isset($_GET['filename'])){
				$filename = preg_replace("/[^a-zA-Z0-9\-\_\.]/", "", $_GET['filename']);
				if(!empty($filename)) $this->params['filename'] = $filename;
			}

			if(isset($_GET['count']) && is_numeric($_GET['count'])){
				$this->count = $_GET['count'];
			}
		}




		function docsFromFilenames(){
			$filenames = $_GET['f'];
		
			$filenames = preg_replace(\MHS\Env::FILENAME_SEQ_REGEX, "", $filenames);
			$filenames = explode("|", $filenames);
			$docs = new \DocManager\Models\Documents();

			$rows = $docs->getDocumentsFromFilenames($filenames);

			$this->total = $docs->getLastQueryCount();
			
			if(false === $rows){
				$this->fail($docs->getErrors());
			}

			return $this->respondWithWorkflow($rows);
		}





		function respondWithWorkflow($rows){
			//collate ids
			$ids = [];
			foreach($rows as $row){
				$ids[] = $row["id"];
			}

			
			$docs = new \DocManager\Models\Documents();
			$stepsRows = $docs->getDocumentsSteps($ids);


			//merge data
			foreach($rows as $key => $row){
				$rows[$key]['steps'] = [];
				foreach($stepsRows as $step){
					if($step["document_id"] == $row['id']){
						$rows[$key]['steps'][] = $step;
					}
				}
			}

			$this->AR->statusOk();
			$this->AR->data("total", $this->total);
			$this->AR->data("start", $this->start);
			$this->AR->data("docs", $rows);
			$this->AR->respond();
		}




		
		function reindex($files = []){
			if(empty($files)){
				if(!isset($_GET['f'])){
					$this->fail("No filenames specified");
				}
				$files = explode(";", $_GET['f']);
			}


			
			$outlist = [];
			$Indexer = new \Publications\IndexXML(\MHS\Env::getInstance());

			$doc = new \DocManager\Controllers\Document();

			foreach($files as $filename){
				$doc->updateInSOLR($filename);
				$outlist[] = $filename;
			}


			$msg = "Update search engine with latest versions of: " . implode(", ", $outlist);
			$this->AR->messages($msg);
			$this->AR->respond();
		}





		function reindexAll(){
			$role = \Customize\User::role();

			if($role != "administrator"){
					$this->fail("You must be an administrator to reindex all the documents");
			}

			$docs = new \DocManager\Models\Documents();
			$list = $docs->getProjectDocuments([], 0, 9999);
			if(false === $list){
					$this->fail("Error trying to get list of all documents.");
			}

			$files = [];
			foreach($list as $doc){
					$files[] = $doc['filename'];
			}

			$this->AR->data($files);
			$this->AR->respond();
			return;
	}





		function resync(){
			$docs = new \DocManager\Models\Documents();
			$doc = new \DocManager\Models\Document();
			$username = \Customize\User::username();

			//first delete all files' records for project; this includes document_step but not the step definitions
			if(false === $docs->deleteAll()){
				$this->fail("Unable to delete existing document records.");
			}

			$files = scandir(\MHS\Env::SOURCE_FOLDER);

			foreach($files as $file){
				//limit to xml to avoid those zip files etc that creep into source folder
				if(!stripos($file, ".xml")) continue;

				//read if it's published or not
				$xml = file_get_contents(\MHS\Env::SOURCE_FOLDER . $file);

				//this should be enough, but maybe use regex or (gulp) actually load w/ DOMDocument
				if(strpos($xml, ' published="yes"')){
					$published = 1;
					$publish_date = date("Y-m-d H:i:s");
				} else {
					$published = 0;
					$publish_date = "2000-01-01 00:00:00";
				}

				if(false === $doc->addNew($file, $username, $published, $publish_date)){
					$this->fail("There was an error adding the document " . $file);
				}

			}

			$this->AR->data(["message", "All set!"]);
			$this->AR->respond();
			return;

		}

	}