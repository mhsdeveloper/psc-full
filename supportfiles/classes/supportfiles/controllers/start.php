<?php


	namespace SupportFiles\Controllers;




	class Start {
	
		function __construct(){
			$this->AR = new \Publications\AjaxResponder();
			$this->files = new \SupportFiles\Models\Files();
			$this->files->setPath(\MHS\Env::SUPPORT_FILES_PATH);

		}

		function index(){
			$this->_mvc->render("supportfiles.php");
		}



		function dir(){
			$list = $this->files->dir();
			$this->AR->data($list);
			$this->AR->respond();
		}



		function upload(){
			if(!$this->files->physicallyUploadFile()){
				$this->AR->statusFailed();
				$this->AR->errors($this->files->getErrors());
			} else {
				$this->AR->data("uploaded " . $this->files->uploadedFilename);
			}

			$this->AR->respond();
		}



		function getFile(){
			$file = $this->sanitizeFilename($_GET['name']);
			$txt = $this->files->getFileContent($file);
			if(false === $txt){
				$this->AR->statusFailed();
			} else {
				$this->AR->data(["text"=> $txt]);
			}
		
			$this->AR->respond();
		}


		function saveFile(){
			if(!isset($_POST['filename'])){
				$this->AR->statusFailed();
				$this->AR->errors("Error saving: no filename provided.");
			} else if(!isset($_POST['text'])){
				$this->AR->statusFailed();
				$this->AR->errors("Error saving: no content provided.");
			} else {
				$filename = $this->sanitizeFilename($_POST['filename']);
				$text = $this->sanitizeHTML($_POST['text']);
				$text = $this->removeLightPs($text);

				
				if(!$this->files->saveFileContent($filename, $text)){
					$this->AR->statusFailed();
					$this->AR->errors($this->files->getErrors());
				}
			}
			$this->AR->data(["message" => "file saved"]);
			$this->AR->respond();
		}


		function deleteFile(){
			$file = $this->sanitizeFilename($_GET['name']);
			$success = $this->files->deleteFile($file);
			if(false === $success){
				$this->AR->statusFailed();
			} else {
				$this->AR->data($file . " deleted.");
			}
		
			$this->AR->respond();
		}


		public function download(){
			$filename = $_GET['filename'];
			$fullpath = \MHS\Env::LIVE_INSTALL_DIR . "/support-files/" . $filename;

			$xml = file_get_contents($fullpath);
			if(false == $xml){
				$this->fail("Unable to retrieve file " . $fullpath);
			}

			$this->AR->statusOk();
			$this->AR->data("fileContent", $xml);
			$this->AR->respond();
		}



		function sanitizeFilename($str){
			$filename = str_replace(["../", "./"], "", $str);
			return $filename;
		}


		function sanitizeHTML($html){
			$html = urldecode($html);
			//bad strings:
			/*
				if any of these, must remove all tags, because an attack could do something like:
						<<scriptscript> if you are just removing "<script", then it would result in:
							<script>
			*/
			$bads = [
				"<script",
				"<embed",
				"<iframe"
			];

			foreach($bads as $bad){
				if(strpos($html, $bad)){
					$html = strip_tags($html);
					break;
				}
			}

			return $html;
		}


		function removeLightPs($text){
			$text = str_replace(["\r","\n"], "", $text);
			//remove paragraphs that folks added for spacing
			$text = preg_replace("/<p>\s*<br>\s*<\/p>/U", "", $text);

			return $text;
		}

	}