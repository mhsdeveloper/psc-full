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


	}