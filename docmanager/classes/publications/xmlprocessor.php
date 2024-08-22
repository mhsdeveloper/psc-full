<?php


	/* some helper static functions for XML
	
	
	*/
	
	
	namespace Publications;
	
	
	
	class XmlProcessor {
	
		
		/* pass in a DOMDocument and an xpath expresstion (string)
		 * this returns string (or typed) content as best as can be.
		 * It'll concatenate (with line breaks) the text content for multiple matches
		 */
		
		static function xpathString($doc, $expression, $contextNode = null){
			return self::xpathData($doc, $expression, $contextNode);
		}
		
		
		/* always return data as unkeyed/simple array, even if only a single item
		 */
		static function xpathArray($doc, $expression, $contextNode){
			
			$results = self::xpathData($doc, $expression, $contextNode, true);
			
			if(is_string($results)) return [$results];

			else return $results;
		}
		
		
		static function registerNamespaces($xpath,
			$namespaces = ["tei" => "http://www.tei-c.org/ns/1.0", "mhs" => "http://www.masshist.org/ns/1.0"]
		){
			foreach($namespaces as $prefix => $ns){
				$xpath->registerNamespace($prefix, $ns);
			}
			return $xpath;
		}
	
		
		
		static function xpathData($doc, $expression, $contextNode = null, $itemsAsArray = false){
			$xpath = new \DOMXPath($doc);
			
			$xpath = self::registerNamespaces($xpath);
			
			if($contextNode != null) $result = $xpath->evaluate($expression, $contextNode);
			else $result = $xpath->evaluate($expression);
			
			// DOMNODELIST
			if(is_object($result)) {
				if($result->length == 1) $text = $result->item(0)->nodeValue;
				
				else {
					$text = [];
					for($x=0; $x < $result->length; $x++) {
						$text[] = $result->item($x)->nodeValue;
					}
					
					//is not returning all items as array, implode to string
					if(false == $itemsAsArray) $text = implode("\n", $text);
				}
			}
			//place typed value
			else $text = $result;
			
			return $text;
		}
	
	}