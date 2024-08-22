<?php 


	namespace Customize;


	class UploadHooks {

		public $errors = "";


		function normalizeFilename($uploadTmpFile, $filename){
			$xmlid = $this->getXMLIDFromFile($uploadTmpFile);
			if(false === $xmlid){
				return false;
			}
			return $xmlid . ".xml";
		}



		public function getXMLIDFromFile($filePath){
			$doc = new \Publications\TEIDocument();

			if(!$doc->load($filePath)){
				$this->errors = "Unable to load " . $filePath . "; likely not well-formed xml; please test your files in Oxygen.";
				return false;
			}
			$nodes = $doc->getXPathNode("/tei:TEI/@xml:id");
			if($nodes->length == 0) return false;

			$xmlid = $nodes->item(0)->value;
			return $xmlid;
		}

		
	}