<?php

/*

TODO



	search for "//error" to find places that need error handling or messages
	
*/

	
	/*
		XML Fragment
		
		a php object that represents a fragment (single element)
		of XML, probably from a larger document, though nothing
		limits this, so it might be the root element
		
	*/
	
	
	namespace Publications;
	
	class XMLfragment {
		
		/*	$pathToXML			: string:	full path to the directory that holds our XML files. there might
		 *									be subdirectories under this,
		 */
		protected $pathToXML = "";
		
		/*	$xmlsource			:	DOMDocument object:	the source XML of the file system file.
		 */
		protected $xmlsource = "";
		
		/*	$fragment			: DOMDocument object: the fragment loaded from within a file
		 */
		protected $fragment;
		
		/* $xpath				:DOMXPath object for evaluating path to fragmnet
		 */
		protected $xpathObj;

		/* $errorLogFile			: string: full path to writable file to append error messages to
		 */
		protected $errorLogFile;
		
		/* $output				: string:	the output of the XSLT transformation
		 */
		protected $output;





		/* setPathToXML()
		 * 
		 *  $path		: string: full path to the directory containing xml files
		 */
		public function setPathToXML($path){
			$this->pathToXML = $path;
		}
	
	
	
		/* loadXMLfile()
		 *
		 * $fullpath	: string: full path to the file to load
		 *
		 * loads the specified file into the $xmlsource property as a DOMDocument
		 * returns true on success, or false on failure
		 */
		public function loadXMLfile($fullpath){

			$this->xmlsource = new \DOMDocument;
			
			//first let's test if file is there and readable
			if(!is_readable($fullpath)) {


//error handling needed				

				return false;
			}
			
			return @$this->xmlsource->load($fullpath);
		}
		
		
		
		
		/* add string as the xml doc
		 */
		public function importStringXML($xml){
			$this->xmlsource = new \DOMDocument;
			return @$this->xmlsource->loadXML($xml);
		}
		
		public function getXMLcode(){
			return $this->xmlsource->saveXML();
		}
		
	
	
	
		/* getXMLfragment()
		 *
		 * $xpath		: string of xpath to retrieve an xml fragment.
		 *
		 * RETURN:	DOMDocument or false if failed
		 */
		public function getXMLfragment($xpath){
			
			if(false == is_object($this->xpathObj)) $this->xpathObj = new \DOMXpath($this->xmlsource);
			
			//->query() always returns nodelist (or false), so more predictable than ->evaluate()
			$elements = $this->xpathObj->query($xpath); 

			if($elements === false) return false;
//error handling needed				
			
			if($elements->length == 0 ) return false;
//error handling needed				
			
			$this->fragment = new \DOMDocument('1.0', 'utf-8');

			$node = $this->fragment->importNode($elements->item(0), true);
			$this->fragment->appendChild($node);
//error handling needed				
			
			return true;
		}
		
		
		
		public function getFragmentSource(){
			
			return $this->fragment->saveXML();
			
		}
		
		
		
		
		public function loadXSLT($fullpath){
			$this->xslt = new \DOMDocument;

			if($this->xslt->load($fullpath)) return true;
			return false;
		}
		
		
		
		
		/* transform()
		 *
		 * $xsltfile	:	string:	fullpath to xslt transform
		 *
		 * $params		:	array: key is parameter name, value is value is value ...
		 *
		 *	returns true on success, false on any number of things that could go wrong!
		 */
		public function transform($params = false){
			$this->transformer = new \XSLTProcessor;
		
			$this->transformer->importStyleSheet($this->xslt);

			if(is_array($params)){
				foreach($params as $param => $value) $this->transformer->setParameter('', $param, $value);
			}
			
			if(!isset($this->fragment)) $this->output = $this->transformer->transformToXML($this->xmlsource);

			else $this->output = $this->transformer->transformToXML($this->fragment);
//error handling needed				
			if($this->output === false) return false;
			else return true;

		}
	
	
	
	
		/* getXSLToutput()
		 *
		 * returns $this->output, from the XSLT transform
		 */
		public function getXSLToutput(){
			
			return $this->output;
			
		}
		
		
		
		/* grab the text value of an xpath
		 * will iterate through the returned nodes and append to output string
			// Returns result of xpath if typed value, or text nodes as string if a node(set)

			//fix_amp fixes a known implimentation of libxml in PHP in which entities are decoded, thus &amp; becomes just the char &,
			//which breaks xml
		 */
		public function getFragmentXPathResultString($xpath, $add_newlines = false, $fix_amp = true){

			//new object only if needed
			$this->xpathObj = new \DOMXpath($this->fragment);
			$results = $this->xpathObj->query($xpath);
			
			//->query() always returns nodelist (or false), so more predictable than ->evaluate()
			$elements = $this->xpathObj->query($xpath); 

			if(is_object($results)){

				if($results->length > 0) {
					$str = '';
					foreach($results as $item){
						$str .= $item->nodeValue;
						if($add_newlines) $str .= "\n ";
						else $str .= " ";
					}

					//Because entities are decoded  to true single characters, any & must be a real ampersand, and needs escaping
					if($fix_amp) $str = str_replace("&", "&amp;", $str);
					$str = trim($str);
					return $str;
				}
				else return ""; //object of zero length is non-matching xpath?

			} else return $results;
		}

	}