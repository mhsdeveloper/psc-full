<?php


	namespace Customize;


	class CoopImageTools extends \DocManager\Controllers\AjaxController {



		function addPBAttrs(){
			if(!isset($_GET['f'])){
				$this->fail("No filename specified for adding image attributes");
			}
			$filename = preg_replace("/[^0-9a-zA-Z\-\._]/", "", $_GET['f']);
			$filename = \MHS\Env::SOURCE_FOLDER  . $filename;

			$message = "";


			$doc = new \Publications\TEIDocument();
			$doc->load($filename);


			//see if there's a leading pb
			$xpath = new \DOMXpath($doc->fullDoc);
			$xpath = \Publications\XmlProcessor::registerNamespaces($xpath);

			$tempNodes = $xpath->query("/tei:TEI/tei:text[1]/tei:body[1]//tei:div[@type='docbody'][1]");

			if(!$tempNodes->length) $this->fail("Document has no docbody");
			$docbody = $tempNodes->item(0);
			$namespace = $docbody->namespaceURI;

			$firstpb = null;
			$noLeadingPb = true;
			
			for($i=0;$i<$docbody->childNodes->length;$i++){
				//stop and check when we find first element (skipping text nodes)
				if($docbody->childNodes->item($i)->nodeType == 1){
					$el = $docbody->childNodes->item($i);

					//no pb
					if($el->nodeName != "pb"){
						$firstpb = $doc->fullDoc->createElementNS($namespace, "pb");
						$docbody->insertBefore($firstpb, $el);
					} else {
						$noLeadingPb = false;
						$firstpb = $el;
					}
					break;
				}
			}
			$nodes = $doc->fullDoc->getElementsByTagName("pb");

			//we need to set the page number for the new pb we created
			if($noLeadingPb){
				//is there a second pb to glean the pb number from?
				//0 is the pb we created
				if($nodes->item(1)){
					$pageno = $nodes->item(1)->getAttribute("n");
					$pageno = intval($pageno) - 1;
					$firstpb->setAttribute("n", $pageno);
				} else {
					$firstpb->setAttribute("n", "1");
				}
			}

			foreach($nodes as $node){
				$node->setAttribute("facs", "yes");
				$n = $node->getAttribute("n");
				$message .= "set facs='yes' for page $n <br/>";
			}

			$xml = $doc->fullDoc->saveXML();

			$success = file_put_contents($filename, $xml);
			
			if(!$success) $this->fail("Error saving the XML file");

			$this->AR->data(["messages" => $message]);
			$this->AR->respond();
		}

	}

