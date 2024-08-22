<?php


	/*
	 * functions for parsing XML via DOMDocument to load XML from file, grab fragments of a larger document,
	 * and traverse the doc looking for page breaks, etc.
	 *
	 * This assumes the XML is TEI but also with our <mhs:doc> addition that identifies our chunks, that is,
	 * the granularity of the content, our letters, diary entries, etc.
	 */




	namespace Publications;



	class TeiDocument {

		use TeiHeader, TeiXslt;

		// xslt to ready tei for modern browser display: some TEI but some HTML; OVERRIDED in template/document
		const XSLTPATH = SERVER_WWW_ROOT . "html/publications/lib/xsl/tei-fragment.xsl";
		
		// xslt to display output for single document xml fragment; removes notes/links/ things we don't share; OVERRIDED in template/document
		const XSLTPATH_MARKUP_ONLY = SERVER_WWW_ROOT . "html/publications/lib/xsl/tei-fragment-xml-out.xsl";


		public $fullDoc;	// the DOMDocument from the xml file
		protected $fragment; // the document, an entry, letter, paper, etc. Usually a <div>
		protected $node; // the node version of the fragment retrieved
		protected $errors;
		protected $docElementName = "";


		
		public function getFullDoc(){ return $this->fullDoc;}
		public function getFragment(){ return $this->fragment;}
		public function getNode(){ return $this->node;}


		public function __construct(){
			$this->fullDoc = new \DOMDocument('1.0', 'utf-8');
			$this->fragment = new \DOMDocument('1.0', 'utf-8');
		}


		public function load($fullpath){
			return $this->open($fullpath);
		}
		public function open($fullpath){
			if(@$this->fullDoc->load($fullpath)) return true;
			else {
				$errors[] = "Unable to load {$fullpath}";
				return false;
			}
		}


		/* Wrapper for common case of getting a doc by ID
		 * retrieve document based on doc ID
		 * returns doc as xml text, not DOMdoc obj
		 *
		 */
		function getDocFromID($id){
			//get xpath based on ID
			$xpath = "//*[@xml:id=\"{$id}\" or @old-id=\"{$id}\"]"; //ids
			//first, add any leading pb
			$this->prependPrecedingPage($id);

			//get the fragment of XML that is our "document" via the id
			$docXML = $this->getDocument($xpath);

			return $docXML;
		}



		function prependPrecedingPage($id){
			$xpath = "//*[@xml:id=\"{$id}\" or @old-id=\"{$id}\"]/preceding-sibling::*[1][name() = 'pb']";
			$xpathObj = new \DOMXpath($this->fullDoc);

			$elements = $xpathObj->query($xpath);

			//doesn't have a preceding pb
			if ($elements->length > 0) {
				$node1 = $elements->item(0);
				$node = $this->fragment->importNode($node1, true);
				$this->fragment->appendChild($node);
			}

			return;
		}



		/* wrapper for this common case
		 * follows xpath and stores the element that that points to in this->fragment (type DOMDocument)
		 *
		 * returns the XML code for the fragment
		 */

		public function getDocument($xpath){
			if(!$this->loadXMLfragment($xpath)) return false;
			$docXML = $this->fragment->saveXML();
//NEED ERROR REPORTING
			return $docXML;
		}




		/* grab a fragment of XML and load into our fragment prop, which is itself a DOMdoc
		 * We assume the passed xpath resolves to a single node. This is a must, because
		 * we will keep this node, as $this->node, for further xpaths from that starting point
		 */
		public function loadXMLfragment($xpath){
			$xpathObj = new \DOMXpath($this->fullDoc);
			$elements = $xpathObj->query($xpath);

			if ($elements->length == 0) {
				$this->errors[] = "Xpath {$xpath} found no elements.";
				return false;

			} else {
				$this->node = $elements->item(0);
				$node = $this->fragment->importNode($this->node, true);
				$this->fragment->appendChild($node);
				return true;
			}
		}



		public function getDocObj(){
			return $this->fullDoc;
		}


		/*
		 * function for finding the next <mhs:doc> in the file, after our current doc
		 *
		 * assumes we've retrieve the fragment for current doc, thus we have a $this->node
		 * from which we can run further xpath
		 *
		 * returns the document as a node object
		 */

		function findNextDoc(){
			$xpath = new \DOMXpath($this->getDocObj());

			$nextNodes = $xpath->query("./following::{$this->docElementName}[1]", $this->node);
			if($nextNodes && $nextNodes->item(0)){
				return $nextNodes->item(0);
			}
			else return false;
		}


		function findPrevDoc(){
			$xpath = new \DOMXpath($this->getDocObj());

			$nextNodes = $xpath->query("./preceding::{$this->docElementName}[1]", $this->node);
			if($nextNodes && $nextNodes->item(0)){
				return $nextNodes->item(0);
			}
			else return false;
		}


		function getTeaser(){
			$xpath = new \DOMXpath($this->fullDoc);
			$xpath = XmlProcessor::registerNamespaces($xpath);

			$raw = "";
			$tempNodes = $xpath->query("/tei:TEI/tei:text[1]/tei:body[1]//tei:p[1]");
			if($tempNodes->length){
				$raw = $tempNodes->item(0)->nodeValue;
			} 

			return \MHS\MHSTextHelper::word_trim($raw, 250);
		}

		function getXPathNode($xpathQuery){
			$xpath = new \DOMXpath($this->fullDoc);
			$xpath = XmlProcessor::registerNamespaces($xpath);

			$tempNodes = $xpath->query($xpathQuery);
			if(false === $tempNodes){
				trigger_error($e->getMessage());
				return false;
			}
			return $tempNodes;
		}


		function getPersRefs(){
			$xpath = new \DOMXpath($this->fullDoc);
			$xpath = XmlProcessor::registerNamespaces($xpath);

			$persRefs = [];
			$tempNodes = $xpath->query("/tei:TEI/tei:text[1]/tei:body[1]//tei:persRef[1]/@ref");
			foreach($tempNodes as $node){
				$this->stringToPersRefs($persRefs, $node->nodeValue);
			} 
			$tempNodes = $xpath->query("/tei:TEI/tei:text[1]/tei:body[1]//tei:persRef[1]/@key");
			foreach($tempNodes as $node){
				$this->stringToPersRefs($persRefs, $node->nodeValue);
			} 
			return $persRefs;
		}


		function isPublished(){
			$xpath = new \DOMXpath($this->fullDoc);
			$xpath = XmlProcessor::registerNamespaces($xpath);

			$tempNodes = $xpath->query("/tei:TEI/@published");
			if($tempNodes->length === 0) return false;
			$value = $tempNodes->item(0)->nodeValue;
			if($value == "true" || $value == "yes" || $value == "1") return true;
			return false;
		}


		function stringToPersRefs(&$array, $raw){
			if(strpos($raw, ";")){
				$parts = explode(";", $raw);
				foreach($parts as $part) {
					$ref = trim($part);
					if(!in_array($ref, $array)) $array[] = $ref;
				}
			} else {
				$ref = trim($raw);
				if(!in_array($ref, $array)) $array[] = $ref;
			}
		}


	} //class
