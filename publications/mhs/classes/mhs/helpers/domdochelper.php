<?php

	/* helper library for DOMDocuments
	 */
	
	
	
	namespace MHS\Helpers;
	
	
	class DomDocHelper {
		
		
		
		/*
		 *	Important different for the string portion of this output:
		 *	&lt; and &gt; in the code will be output as such. in get_xpath_result_string,
		 *	these are output as < and >.
		 *
		 */

		function get_xpath_result_code($contextnode, $remove_xml_dec = false, $drop_outer_el = false){

			// Returns result of xpath if typed value, or xml code if a node(set)

			// NOTE: because attributes can't exist as nodes without a parent element,
			//      use xpath= "string(//@id)" to simply return the string value of
			//      an atttribute

			if(is_object($contextnode)){
				$xml = new DOMDocument();
				$xml->formatOutput = true;
	
				// get the xpath found fragment (item(0) is first top level node), and import
				// into temp DOM obj
				// look for nodelist
				if($contextnode->length > 0) {

					foreach($contextnode as $item){
						$frag = $xml->importNode($item, true);
						$xml->appendChild($frag);
					}
					$return  = $xml->saveXML();

				} else $return  = ""; //object of zero length is non-matching xpath?

			}
			else return false;
			
			//remove <?xml etc...
			if($remove_xml_dec) $return = trim(preg_replace("/<\?.*\?>/U", '', $return));
			
			//remove outer element
			if($drop_outer_el){
				
				$return = trim($return); //and leading whitespace will mess up our substr-based trimming
				
				if(preg_match('/(<.*>)/U', $return, $matchp)){
					
					//this could drop inner elements! $return = str_replace($matchp[1], '', $return);
					
					//we'll substring it, since the one thing we do know is tag is start of string
					$templ = strlen($matchp[1]);

					$temp = substr($return, $templ);

					//create string for outer el
					
					//starting element might have ns or attributes, so we need just the tag name
					preg_match('/<([^ ]+)[ >]/U', $matchp[1], $endMatch);
					$closingtag ='</' .  $endMatch[1] . ">";

					//again, this is bad, could replace inner matches:		$return = str_replace($closingtag, '', $return);
					$templ = strlen($closingtag);
					$temptot = strlen($temp);
					$return = substr($temp, 0, ($temptot - $templ));
				}
			}
			
			return $return;
		}
		
		
	}
	