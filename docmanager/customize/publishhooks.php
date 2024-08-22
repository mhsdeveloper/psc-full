<?php 


	namespace Customize;


	class PublishHooks {

		public $errors = "";



		function publish($filePath){
			$doc = new \Publications\TEIDocument();

			if(!$doc->load($filePath)){
				$this->errors = "Unable to load " . $filePath . " for publishing; contact web developer.";
				return false;
			}

			//check XML has been reviewed
			$nodes = $doc->getXPathNode("/tei:TEI/tei:teiHeader[1]/tei:revisionDesc[1]/tei:listChange[1]/tei:change[@type='xmlReview']");
			if($nodes->length == 0){
				$this->errors = "xmlReview revision description not found.";
				return false;
			}
			$att = $nodes[0]->getAttribute("status");
			if($att != "complete"){
				$this->errors = "xmlReview status is: " . $att;
				return false;
			}

			//check ok to publish marked
			$nodes = $doc->getXPathNode("/tei:TEI/tei:teiHeader[1]/tei:revisionDesc[1]/tei:listChange[1]/tei:change[@type='OK-to-Publish']");
			if($nodes->length == 0){
				$this->errors = "OK-to-Publish revision description not found.";
				return false;
			}
			$att = $nodes[0]->getAttribute("status");
			if($att != "yes"){
				$this->errors = "OK-to-Publish status is: " . $att;
				return false;
			}

			$nodes = $doc->getXPathNode("/tei:TEI");
			if($nodes->length == 0) return false;

			$nodes[0]->setAttribute("published", "yes");

			$xml = $doc->fullDoc->saveXML();
			if(!file_put_contents($filePath, $xml)){
				$this->errors = "Unable to save file " . $filePath . " with published attribute.";
				return false;
			}

			return true;
		}
		


		function unPublish($filePath){
			$doc = new \Publications\TEIDocument();

			if(!$doc->load($filePath)){
				$this->errors = "Unable to load " . $filePath . " for unpublishing; contact web developer.";
				return false;
			}
			$nodes = $doc->getXPathNode("/tei:TEI");
			if($nodes->length == 0) return false;

			$nodes[0]->setAttribute("published", "no");

			$xml = $doc->fullDoc->saveXML();
			if(!file_put_contents($filePath, $xml)){
				$this->errors = "Unable to save file " . $filePath . " with not published attribute.";
				return false;
			}

			return true;
		}
		



	}