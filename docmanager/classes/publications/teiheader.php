<?php


	namespace Publications;


	trait TeiHeader {


		/* gather metadata for preview of a doc
		 * so, author, head, title, recipient, that sort of thing
		 */
		public function gleanMeta($contextNode){
			$xpath = new \DOMXpath($this->fullDoc);
			$xpath = XmlProcessor::registerNamespaces($xpath);

			$metadata = [
				"xmlid" => "",
				"authors" => [],
				"recipients" => [],
				"date_from" => "",
				"date_to" => "",
				"date_from_pretty" => "",
				"date_to_pretty" => "",
				"persRefs" => [],
				"title" => ""
			];
			
			$tempNodes = $xpath->query("./@xml:id", $contextNode);
			if($tempNodes->length){
				$metadata["xmlid"] = $tempNodes->item(0)->nodeValue;
			}

			//head strings
			$tempNodes = $xpath->query("/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:bibl/tei:title[1]", $contextNode);
			if($tempNodes->length){
				$metadata['title'] = $tempNodes->item(0)->nodeValue;
			}

			//author
			$tempNodes = $xpath->query("/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:bibl/tei:author", $contextNode);
			if($tempNodes->length){
				foreach($tempNodes as $node){
					$metadata['authors'][] = $node->nodeValue;
				}
			}

			//recipient
			$tempNodes = $xpath->query("/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:bibl/tei:recipient", $contextNode);
			if($tempNodes->length){
				foreach($tempNodes as $node){
					$metadata['recipients'][] = $node->nodeValue;
				}
			}
			
			//date
			$tempNodes = $xpath->query("/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:bibl/tei:date[1]/@when", $contextNode);
			if($tempNodes->length){
				$metadata['date_from'] = $tempNodes->item(0)->nodeValue;
				$metadata['date_to'] = $tempNodes->item(0)->nodeValue;
				$metadata['date_from_pretty'] = \Publications\DateFormatter::ISOtoMHS($tempNodes->item(0)->nodeValue);
				$metadata['date_to_pretty'] = \Publications\DateFormatter::ISOtoMHS($tempNodes->item(0)->nodeValue);
			} else {
				//look for from to values

			}

			return $metadata;
		}





		function getRevisionDescProps(){
			$xpath = new \DOMXpath($this->fullDoc);
			$xpath = XmlProcessor::registerNamespaces($xpath);

			$props = [];

			$path = "/tei:TEI/tei:teiHeader[1]/tei:revisionDesc[1]/tei:listChange[1]/tei:change";

			$tempNodes = $xpath->query($path);
			foreach($tempNodes as $node){
				$type = $node->getAttribute("type");				
				$segs = $xpath->query("tei:seg", $node);

				if($segs->length){
					$props[$type] = [];
					
					foreach($segs as $seg){
						$subtype = $seg->getAttribute("type");
						$status = $seg->getAttribute("status");

						$props[$type][] = $subtype . ": " . $status;
					}
				}
			} 

			$path = "/tei:TEI/tei:teiHeader[1]/tei:revisionDesc[1]/tei:listChange[1]/tei:change[@type='xmlReview']";
			$xmlReviewEl = $xpath->query($path);
			if($xmlReviewEl->length){
				$props['xmlReview'] = $xmlReviewEl[0]->getAttribute('status');
			}

			$path = "/tei:TEI/tei:teiHeader[1]/tei:revisionDesc[1]/tei:listChange[1]/tei:change[@type='OK-to-Publish']";
			$el = $xpath->query($path);
			if($el->length){
				$props['OK-to-Publish'] = $el[0]->getAttribute('status');
			}

			return $props;
		}
	}