<?php


	namespace Customize;


	$classLoader = new \SplClassLoader('API', SERVER_WWW_ROOT . 'html/mhs-api/u1/classes');
	$classLoader->register();



	class CoopSubjectChecks extends \DocManager\Controllers\AjaxController {


		private $subjects = [];


		public function ingest(){
			if(!isset($_GET['f'])){
				$this->fail("No filename specified for checking subjects.");
			}
			$filename = preg_replace("/[^0-9a-zA-Z\-\._]/", "", $_GET['f']);
			$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

			$subjects = $this->getSubjects($filename);

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




		function getSubjects($filename){
	
			$doc = new \Publications\TEIDocument();

			if(!$doc->load($filename)){
				$this->fail("Unable to load " . $filename);
			}
			$subjects = [];

			foreach(SUBJECTS_XPATHS as $xpath){
				$nodes = $doc->getXPathNode($xpath);
				if($nodes->length) {
					foreach($nodes as $node){
						$ref = $node->textContent;
						$subjects[] = $ref;
					}
				}
			}


			// $nodes = $doc->getXPathNode("//tei:TEI/tei:text[1]/tei:body[1]//tei:bibl[1]/tei:subject");
			// if($nodes->length) {
			// 	foreach($nodes as $node){
			// 		$ref = $node->textContent;
			// 		$subjects[] = $ref;
			// 	}
			// }

			return $subjects;
		}

	}