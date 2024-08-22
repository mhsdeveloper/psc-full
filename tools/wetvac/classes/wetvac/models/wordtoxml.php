<?php

/*

 */



	namespace Wetvac\Models;





	class WordToXML {


		private $errors = [];


		function __construct(){
		}


		public function setFile($filename){

			$this->filename = $filename;

			$parts = pathinfo($this->filename);

			if($parts['extension'] != "docx") {
				$this->error("Please upload a MS Word file with a docx extensions");
				return false;
			}

			$this->outputFilenameOnly = $parts['filename'] . ".xml";
			$this->outputFilename = $parts['dirname'] . "/" . $this->outputFilenameOnly;

			return true;
		}


		public function getFullOutputPath(){
			return $this->outputFilename;
		}


		public function getFilenameOnly(){
			return $this->outputFilenameOnly;
		}


		public function process(){		
			$Unpacker = new \Wetvac\Models\DocxUnpacker();
			$wordXML = $Unpacker->unzip($this->filename);
			$this->filenameNoext = pathinfo($this->filename)["filename"];

			if(false === $wordXML) {
				$this->error("DocxUnpacker was unable to open the docx file");
				return;
			}

//file_put_contents("prevac.xml", $wordXML);
			
			$Prep = new \Wetvac\Models\XSLT1();

			if(false == $Prep->loadXSLT(\MHS\Env::WORD_TO_TEI_XSLT)){
				$error = $Prep->errorMsg;
				return $this->error("Error loading the WORD_TO_TEI_XSLT: " . \MHS\Env::WORD_TO_TEI_XSLT );
			}

			$params = [
				"xmlid" => "XMLIDPLACEHOLDER"
			];

			if(false == $Prep->runTransform($wordXML, $params)){
				$error = $Prep->errorMsg;
				return $this->error("XSLT post-processing the XML output failed for this reason: " . $error);
			}

			$this->text = $Prep->getOutput();
			
			if(!file_put_contents($this->outputFilename, $this->text)){
				return $this->error("WordToXML() was unable to save output file.");
			}

			return true;
		}





		protected function error($msg){
			$this->errors[] = $msg;
			return false;
		}


		public function getErrors(){
			return $this->errors;
		}


	} //class
