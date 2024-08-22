<?php


	namespace DocManager\Controllers;



	class Document extends AjaxController {

		private $filePath = "";
		private $filename = "";

		function __construct(){
			parent::__construct();
			if(!\Customize\User::isAtLeastXMLEditor()) {$this->fail("You must be an editor to do this."); return;}
		}




		function upload(){
			$filename = $this->physicallyUploadFile();

			//find file record
			$doc = new \DocManager\Models\Document();

			//check if we have that document in the system
			if($doc->getFromFilename($filename)){
				$this->fail("That file is already in the system. Please use the checkin feature found at the file's listing.");
			}

			$this->storeUploadedFile($filename);
			$this->checkSchema($doc, $filename);


			// CREATE RECORD IN DB
			$docid = $doc->addNew($filename, \Customize\User::username());
			if(!$docid){
				$this->fail("Unable to create a record for that file. Please see the web developer.");
			}

			$Steps = new \DocManager\Models\Steps();
			$rows = $Steps->getFromProject();
			$ids = [];
			foreach($rows as $row) $ids[] = $row['id'];

			if(count($rows)) if(!$doc->addSteps($docid, $ids)){
				$this->fail("Unable to add workflow steps for that file. Please see the web developer.");
			}

			
			$this->updateInSOLR($filename);

			//look for hooks from our customizations
			foreach(\DocManager\Hooks::getInstance()->postUploadHooks as $hook){
				if(method_exists($hook['class'], $hook['method'])){
					$HookInstance = new $hook['class'];
					$method = $hook['method'];
					$success = $HookInstance->$method(\MHS\Env::SOURCE_FOLDER . $filename);
					if(false === $success){
						$this->fail($HookInstance->errors);
					}
				}
			}

			$this->AR->statusOk();
			$this->AR->data("id", $docid);
			$this->AR->data("filename", $filename);
			$this->AR->respond();
		}



		function physicallyUploadFile(){
			$this->uploader = new \Publications\Uploader();
			$this->uploader->whiteList = \MHS\Env::FILE_UPLOAD_EXT_WHITELIST;
			$this->uploader->setDestFolder(\MHS\Env::SOURCE_FOLDER);

			
			//get file from the request stream
			//check return from upload
			if(false === $this->uploader->upload(false)) {
				$this->fail("Unable to upload the file: \n" . $this->uploader->getError());
			}
			$this->filePath = $this->uploader->getPreMovePath();
			$filename = $this->uploader->getFilename();

			//filename changing hooks
			foreach(\DocManager\Hooks::getInstance()->filenameChangesHooks as $hook){
				if(method_exists($hook['class'], $hook['method'])){
					$HookInstance = new $hook['class'];
					$method = $hook['method'];
					$newname = $HookInstance->$method($this->filePath, $filename);
					//check if filename fails hooks requirements
					if(false === $newname){
						$this->fail($HookInstance->errors);
					}
					
					$filename = $newname;
				}
			}
	
			//check if file matches projects filename regex
			preg_match(\MHS\Env::FILENAME_PATTERN, $filename, $matches);
			if(!isset($matches[0]) || $filename != $matches[0]){
				$this->fail("The filename " . $filename . " is not allowed according to the project's filenaming rules.");
			}

			return $filename;
		}


		function storeUploadedFile($filename){
			//move the file to actual source location
			if($this->uploader->moveToDest($filename)){
				$this->filename = $filename;
			} else {
				$this->fail("Unable to store the uploaded file.");
			}
		}


		function checkSchema($doc, $filename){
			if(isset($_POST['checkSchema']) && $_POST['checkSchema'] == "1" && !$doc->checkSchema($filename)){
				$this->fail("The schema failed: " . $doc->getErrors());
			}
		}



		public function downloadXML(){
			$filename = $_GET['filename'];
			$fullpath = \MHS\Env::SOURCE_FOLDER . $filename;

			$xml = file_get_contents($fullpath);
			if(false == $xml){
				$this->fail("Unable to retrieve file " . $fullpath);
			}

			$this->AR->statusOk();
			$this->AR->data("fileContent", $xml);
			$this->AR->respond();
		}





		function checkin(){
			$filename = $this->physicallyUploadFile();

			//find file record
			$doc = new \DocManager\Models\Document();

			if(!$doc->getFromFilename($filename)){
				$this->fail("The file you uploaded is not in the system. Please check that the filenames of the document you checked out and the the one you're uploading match, and that the xml:id matches the filename (less the .xml extension).");
			}

			if($doc->checked_outin_by != \Customize\User::username()){
				$this->fail("That file is checked out by another user: " . $doc->checked_outin_by);		
			}

			$this->storeUploadedFile($filename);
			$this->checkSchema($doc, $filename);

			if(!$doc->checkin($doc->id)){
				$this->fail("Unable to flag the file as checked in the database.");
			}
			
			$this->updateInSOLR($filename);

			//look for hooks from our customizations
			foreach(\DocManager\Hooks::getInstance()->postUploadHooks as $hook){
				if(method_exists($hook['class'], $hook['method'])){
					$HookInstance = new $hook['class'];
					$method = $hook['method'];
					$success = $HookInstance->$method(\MHS\Env::SOURCE_FOLDER . $filename);
					if(false === $success){
						$this->fail($HookInstance->errors);
					}
				}
			}

			$this->AR->statusOk();
			$this->AR->data("id", $doc->id);
			$this->AR->data("filename", $filename);
			$this->AR->respond();
		}



		function undoCheckout(){
			$docid = filter_var($this->_mvc->segment(2), FILTER_SANITIZE_NUMBER_INT);

			//find file record
			$doc = new \DocManager\Models\Document();

			//first be sure this document belongs to this project
			if(!$doc->getFromID($docid)){
				$this->fail("Bad doc id, or the document with id " .$docid. " does not belong to this project.");
			}
			
			if($doc->checked_outin_by != \Customize\User::username()){
				$this->fail("That file is checked out by another user: " . $doc->checked_outin_by);		
			}

			if(!$doc->checkin($doc->id)){
				$this->fail("Unable to flag the file as checked in the database.");
			}

			$this->AR->statusOk();
			$this->AR->data("id", $doc->id);
			$this->AR->respond();
		}



		function checkout(){
			$username = \Customize\User::username();

			$doc = new \DocManager\Models\Document();
			$docid = filter_var($this->_mvc->segment(2), FILTER_SANITIZE_NUMBER_INT);

			//first be sure this document belongs to this project
			if(!$doc->getFromID($docid)){
				$this->fail("Bad doc id, or the document with id " .$docid. " does not belong to this project.");
			}

			if(!$doc->checkoutID($docid, $username)){
				$this->fail("Document model returned an error checking out docid " . $docid);
			}

			$this->AR->statusOk();
			$this->AR->data("id", $doc->id);
			$this->AR->respond();
		}





		function updateInSOLR($filename = ""){
			if(empty($filename)){
				$filename = $this->fileParts['basename'];
			}


			//filename changing hooks
			foreach(\DocManager\Hooks::getInstance()->solrIndexHooks as $hook){
				if(method_exists($hook['class'], $hook['method'])){
					$HookInstance = new $hook['class'];
					$method = $hook['method'];
					$success = $HookInstance->$method(\MHS\Env::SOURCE_FOLDER . $filename);
					if(false === $success){
						$this->fail($HookInstance->errors);
					}
				}
			}
			
			
			$Indexer = new \Publications\IndexXML(\MHS\Env::getInstance());
			if(false === $Indexer->processFile($filename)){
				$this->fail("Unable to run XSLT for SOLR.");
			}

			if(false === $Indexer->callSolr($filename)){
				$this->fail("Unable to index in SOLR");
			}
			return true;
		}


		function delete(){
			if(\Customize\User::role() != "administrator") {
				$this->fail("You must be an administrator to delete.");
			}	
			
			$filename = $this->_mvc->segment(2);

			$Indexer = new \Publications\IndexXML(\MHS\Env::getInstance());
			$Indexer->removeSolrDoc($filename);

			if(false === $Indexer->callSolr($filename)){
				$this->fail("Unable to unindex in SOLR");
			}

			$doc = new \DocManager\Models\Document();
			if(!$doc->getFromFilename($filename)){
				$this->AR->errors("Can't find that file in the DB.");
			}			

			if(!$doc->delete($filename)){
				$this->fail("unable to delete that file.");
			}

			$this->AR->statusOk();
			$this->AR->respond();
		}




		function publish(){
			$filename = $_GET['filename'];

			$fullPath = \MHS\Env::SOURCE_FOLDER . $filename;

			//filename changing hooks
			foreach(\DocManager\Hooks::getInstance()->publishHooks as $hook){
				if(method_exists($hook['class'], $hook['method'])){
					$HookInstance = new $hook['class'];
					$method = $hook['method'];
					$success = $HookInstance->$method($fullPath);
					if(false === $success){
						$this->fail($HookInstance->errors);
					}
				}
			}

			//find file record
			$doc = new \DocManager\Models\Document();

			if(!$doc->publish($filename)){
				$this->fail("SQL error publishing the file. The XML was published, however. Contact the web developer.");
			}

			if(!$this->setSolrPublishStatus("published")){
				$this->fail("Unable to set solr publish status for Ids: " . $_GET['ids']);
			}
			

			$this->AR->statusOk();
			$this->AR->respond();
		}



		function unpublish(){
			$filename = $_GET['filename'];
			$fullPath = \MHS\Env::SOURCE_FOLDER . $filename;

			//filename changing hooks
			foreach(\DocManager\Hooks::getInstance()->unPublishHooks as $hook){
				if(method_exists($hook['class'], $hook['method'])){
					$HookInstance = new $hook['class'];
					$method = $hook['method'];
					$success = $HookInstance->$method($fullPath);
					if(false === $success){
						$this->fail($HookInstance->errors);
					}
				}
			}

			//find file record
			$doc = new \DocManager\Models\Document();

			if(!$doc->unPublish($filename)){
				$this->fail("SQL error publishing the file. The XML was published, however. Contact the web developer.");
			}

			if(!$this->setSolrPublishStatus("staffonly")){
				$this->fail("Unable to set solr publish status for Ids: " . $_GET['ids']);
			}

			$this->AR->statusOk();
			$this->AR->respond();
		}




		function setSolrPublishStatus($status = "staffonly"){
			//update SOLR record so searches can find publish doc
			$Indexer = new \Publications\IndexXML(\MHS\Env::getInstance());


			$ids = $_GET['ids'];
			if(strpos($ids, ";")){
				$ids = explode(";", $ids);
			} else {
				$ids = [$ids];
			}

			$xml = "<add>";
			foreach($ids as $id){
				$xml .="<doc><field name=\"id\">{$id}</field><field name=\"status\" update=\"set\">{$status}</field></doc>";
			}
			$xml .= "</add>";


			if(false === $Indexer->feedSolrXML($xml)){
				return false;
			}		

			return true;
		}


	}