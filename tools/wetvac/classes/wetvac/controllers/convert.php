<?php

	/*
	 */

	namespace Wetvac\Controllers;



	class Convert extends Controller {

		function __construct(){
			ini_set('display_errors', 0);
			ini_set('error_log', "errors.txt");
		}


		function index(){
			$this->_mvc->render("word-to-tei.php");
		}




		function upload(){
			$this->uploader = new \MHS\Uploader();
			$this->uploader->whiteList = [];
			$this->uploader->addAllowedFileType(".docx");
			$this->uploader->setDestFolder(\MHS\Env::CONVERT_UPLOAD_DIR);

			//check return from upload
			if(false === $this->uploader->upload()) {
				return $this->ajaxError($this->uploader->getError());
			}

			$fullFileName = $this->uploader->getFullFilePath();

			$filenameNoExt = pathinfo($fullFileName)['filename'];
			
			$filenameChecker  = new \Publications\Filenames();
			if(false === $filenameChecker->check($filenameNoExt)){
				//attempt to rename it
				//$newFilenamePart = str_replace([".", "*", "^", "~", "`", "+", "|"], "-", $filenameNoExt);
				$newFilenamePart = preg_replace("/[^a-zA-Z0-9\-]/", "-", $filenameNoExt);
				$newFullName = str_replace($filenameNoExt, $newFilenamePart, $fullFileName);
				if(false == rename($fullFileName, $newFullName)){
					$allErrors = $filenameChecker->getErrors();
					array_unshift($allErrors,  "Unable to rename $filenameNoExt to an allowed name:" . $newFullName);
					$this->response = ["filename" => $fullFileName, "errors" => $allErrors];
					$this->ajaxResponse();
					return;
				}
				$fullFileName = $newFullName;
				$filenameNoExt = $newFilenamePart;
			}

			if(false === $filenameChecker->check($filenameNoExt)){
			    $this->response = ["filename" => $filenameNoExt, "message" => "Errors with $filenameNoExt.", "errors" => $filenameChecker->getErrors()];
			    $this->ajaxResponse();
			    return;
			}
				
			$this->response = ["filename" => $filenameNoExt . "." . pathinfo($fullFileName)["extension"], "message" => "Uploaded $filenameNoExt."];
			$this->ajaxResponse();
		}




		function process(){

			$filename = str_replace(["..", "/"], "", $_GET['filename']);
			$fullpath = \MHS\Env::CONVERT_UPLOAD_DIR . $filename;
			if(!is_readable($fullpath)){
				return $this->ajaxError("Unable to read $fullpath in upload dir");
			}
			$filenameNoExt = pathinfo($fullpath)['filename'];
			
			$Vac = new \Wetvac\Models\WordToXML();

			if(false === $Vac->setFile(($fullpath))) {
				return $this->ajaxError("Word to XML script could not read $fullpath.");
			}



			if(false === $Vac->process()){
				$errors = $Vac->getErrors();
				return $this->ajaxError($errors);
			}

			$fullpath = $Vac->getFullOutputPath();
			$this->text = file_get_contents($fullpath);

//			$this->runPreWETxslt();
file_put_contents($fullpath . "-vac-prewet.xml", $this->text);

			$this->checker = new \Wetvac\Models\WetChecker();
			$this->checker->loadRawTEI($this->text);

			if(false === $this->checker->fullCheck()){
			    $errors = $this->checker->getErrors();
			    return $this->ajaxError($errors);
			}

			$idRoot = $this->setXMLID();

			$T = new \Wetvac\Models\VacToTei2();
			$T->setIdRoot($idRoot);
			$T->setText($this->text);
			$T->separateDocParts();
			$T->processMarkers();
			$T->rejoinParts();


	//		die($T->getText());


			$T->formOutput();

			//look for GET params to override milestone markers
			$paramKeys = ['transcriptionMilestone', 'persrefsMilestone', 'subjectsMilestone', 'annotationsMilestone'];
			$queryParams = [];
			foreach($paramKeys as $param){
				if(isset($_GET[$param]) && $_GET[$param] != "x") $queryParams[$param] = $_GET[$param];
			}

			$params = $T->getSetMilestones($queryParams);
			$this->text = $T->getText();

			file_put_contents($fullpath . "-ox-postwet.xml", $this->text);

			$this->runPostWETxslt($params);

			//send text back to model
			$T->setText($this->text);
			$T->numberNotes();
			$this->text = $T->getText();

			//and get FINAL version from model
//			$this->text = $T->text();
			$this->finalFormatting();

			//save
			file_put_contents(\MHS\Env::CONVERT_UPLOAD_DIR . $idRoot . ".xml", $this->text);
			$this->response["message"] = "Ready to download TEI";
			$this->response['status'] = "download";
			$this->response['filename'] = $idRoot . ".xml";
			$this->response['fileContent'] = $this->text;

			$this->ajaxResponse();
		}





		private function setXMLID(){
			$idMatches = [];
			preg_match("/\{\{XMLID\}\}\s*([A-Z]{3}[0-9]{5,6})/", $this->text, $idMatches);

			if(!isset($idMatches[1])){
				return $this->ajaxError("Make sure the id after your {{XMLID}} marker is formatted: LLLNNNNN (that's 3 uppercase letters and 5 numerals, no spaces)");
			}
			//remove the XML ID line
			$this->text = preg_replace("/\{\{XMLID\}\}\s*([A-Z]{3}[0-9]{5,6})/", "", $this->text);

			//set the tei-id that was placeholdered in the first XSLT pass
			$this->text = str_replace("XMLIDPLACEHOLDER", $idMatches[1], $this->text);

			return $idMatches[1];
		}



		private function runPostWETxslt($params){
			$Prep = new \Wetvac\Models\XSLT1();

			if(false == $Prep->loadXSLT(\MHS\Env::POST_WET_XSLT)){
				$error = $Prep->errorMsg;
				return $this->ajaxError("Error loading the POST_WET_XSLT: " . \MHS\Env::POST_WET_XSLT );
			}

			$params["wetvacDate"] = date("Y-m-d");
			if(false == $Prep->runTransform($this->text, $params)){
				$error = $Prep->errorMsg;
				return $this->ajaxError("XSLT post-processing the WET output failed for this reason: " . $error);
			}

			$this->text = $Prep->getOutput();
		}





		public function finalFormatting(){

			// add line breaks at specific tags, both open and close, except for p
			$endTags = [ "Desc>", "Stmt>", "Info>", "Change>", "text>", "body>", "</p>", "<lb/>", "</change>", "<seg"];
			$endTagsModded = [ "Desc>\n", "Stmt>\n", "Info>\n", "Change>\n", "text>\n", "body>\n", "</p>\n", "<lb/>\n", "\n</change>\n", "\n<seg"];
			$this->text = str_replace($endTags, $endTagsModded, $this->text);

			//remove these
			$this->text = str_replace(' xml:space="preserve"', "", $this->text);

			//simple replacements
			$this->text = str_replace(["<p><lb/>", "<p/>"], ["<p>", ""], $this->text);

			//clean spaces after <p>
			$this->text = preg_replace("/<p>\s+/", "<p>", $this->text);
		}

	} //class
