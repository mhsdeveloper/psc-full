<?php


	namespace Customize;


	$classLoader = new \SplClassLoader('API', SERVER_WWW_ROOT . 'html/mhs-api/u1/classes');
	$classLoader->register();



	class CoopDocChecks extends \DocManager\Controllers\AjaxController {


		public function auditHuscs(){
			if(!isset($_GET['f'])){
				$this->fail("No filename specified for checking persrefs.");
			}
			$filename = preg_replace("/[^0-9a-zA-Z\-\._]/", "", $_GET['f']);
			$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

			$refs = $this->getPersrefs($filename);

			$names = new \API\Models\NamesModel();
			$missing = $names->auditHuscs($refs);
			if($missing === false){
				$this->fail("Error trying to audit huscs.");
			}		


			//also try to add any that are not associated with out project
			$project = new \API\Models\ProjectModel();

			if(!$project->auditHuscsInProject($refs)){
				$this->fail("Unable to add HUSCs to project");	
			} else {
				$this->AR->messages("Associated existing HUSCS with your project.");
			}

			$this->AR->data($missing);
			$this->AR->respond();
		}




		public function checkHuscs(){
			if(!isset($_GET['f'])){
				$this->fail("No filename specified for checking persrefs.");
			}
			$filename = preg_replace("/[^0-9a-zA-Z\-\._]/", "", $_GET['f']);
			$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

			$refs = $this->getPersrefs($filename);

			$names = new \API\Models\NamesModel();
			$missing = $names->auditHuscs($refs);
			if($missing === false){
				$this->fail("Error trying to audit huscs.");
			}		

			$this->AR->data($missing);
			$this->AR->respond();
		}






		public function checkRevDesc(){
			if(!isset($_GET['f'])){
				$this->fail("No filename specified for checking revision description.");
			}
			$filename = preg_replace("/[^0-9a-zA-Z\-\._]/", "", $_GET['f']);
			$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

			$doc = new \Publications\TEIDocument();
			if(!$doc->load($filename)){
				$this->fail("Unable to load " . $filename);
			}

			$props = $doc->getRevisionDescProps();

			$statuses = [];


			$this->AR->data($props);
			$this->AR->respond();
		}





		public function makeNamesPublic(){
			if(!isset($_GET['f'])){
				$this->fail("No filename specified for checking persrefs.");
			}
			$filename = preg_replace("/[^0-9a-zA-Z\-\._]/", "", $_GET['f']);
			$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

			$refs = $this->getPersrefs($filename);

			$project = new \API\Models\ProjectModel();

			if(!$project->makeNamesPublic($refs)){
				$this->fail("Unable to flag HUSCS as publicly searchable.");
			}
			$this->AR->data(["messages" => "Huscs marked as public for searches."]);
			$this->AR->respond();
		}




		public function getPersrefs($filename){
			$doc = new \Publications\TEIDocument();

			if(!$doc->load($filename)){
				$this->fail("Unable to load " . $filename);
			}
			$refs = [];

			$nodes = $doc->getXPathNode("//tei:persRef/@ref");
			if($nodes->length) {
				foreach($nodes as $node){
					$ref = $node->value;
					$this->parseRef($refs, $ref);
				}
			}

			$nodes = $doc->getXPathNode("//tei:persRef/@key");
			if($nodes->length){
				foreach($nodes as $node){
					$ref = $node->value;
					$this->parseRef($refs, $ref);
				}
			}
			return $refs;
		}

	


		private function parseRef(&$refs, $ref){
			$ref = preg_replace("/[^a-zA-Z0-9\-\;]/", "", $ref);
			if(strpos($ref, ";") !== false){
				$parts = explode(";", $ref);
				foreach($parts as $part){
					if(!in_array($part, $refs)) $refs[] = $part;
				}
			}
			else if(!in_array($ref, $refs)) $refs[] = $ref;
		}



		public function getSubjects(){
			$doc = new \Publications\TEIDocument();

			if(!isset($_GET['f'])){
				$this->fail("No filename specified.");
			}
			$filename = preg_replace("/[^0-9a-zA-Z\-\._]/", "", $_GET['f']);
			$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

			if(!$doc->load($filename)){
				$this->fail("Unable to load " . $filename);
			}

			$subjects = [];


			foreach(SUBJECTS_XPATHS as $xpath){
				$nodes = $doc->getXPathNode($xpath);
				if($nodes->length) {
					foreach($nodes as $node){
						$subject = trim(preg_replace("/\s\s+/", " ", $node->nodeValue));
						if(!in_array($subject, $subjects)){
							$subjects[] = $subject;
						}
					}
				}
			}

			$this->AR->data($subjects);
			$this->AR->respond();
		}

	}