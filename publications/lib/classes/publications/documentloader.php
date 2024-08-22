<?php
	/* New class for retrieving TEIDocuments, simply retrieving XML based on filepath and xmlid

	Aug 2019 enhancements:
		new concept of a retrival string: a unique string that is not necessarily an XML:id, nor a filename.
		If an extending child class implements a method named parseRetrievalString, it uses that instead of
		the filename/xmlid URL convention to determine the filename and xmlid, that is: parseRetrievalString() should set:
		$this->filename = "[string file name, no path]";
		$this->xmlid = "[string xml:id]";

	 *
	 * replaces Publications\Document, which was too coupled to being the controller, getting input, etc.
	 *
	 * expected to be used with new metadata json system which directly provides filename and xmlid
	 *
	 * your project's controller should extend this
	 *
	 * 2017
	 */



	namespace Publications;



	class DocumentLoader extends Base {

		public $templateOverridePath = "";

		 /* these props determine which file is loaded and which element xml:id is retrieved,
		  * unless the method parseRetrievalString() exists in a child class, in which
		  * case that method is called and expected to set $this->filename and $this->xmlid
		  * 
		  *   index.php/document/abc-1888-02/
		  */
		const DOC_FILENAME_URL_SEGMENT = 3;
		const DOC_XMLID_URL_SEGMENT = 3;
		const VIEW_MODE_URL_SEGMENT = 4; //view modes are: "dual" (default is having no segment meaning transcription)
		
		/* if this is true, then the xml:id from the URL segment is understood to only be
		 * the last part of the full xml:id, and the full xml:id is built thus:
		 *    [DOC_FILNAME_URL_SEGMENT]-[XMLID_URL_SEGMENT]
		 */
		const XMLID_URL_IS_ABBREVIATED = false;

		//the second URL segment is used as the retrieval string if child class implements parseRetrievalString()
		const RETRIEVAL_STRING_URL_SEGMENT = 2;
		
		const DOCUMENT_VIEW = "document";

		const ERROR_VIEW = "error";
		
		const ERROR_SUBVIEW = "error";

		//reference to a quickdoc object that should extend \Publications\Metadata\Views\QuickDoc
		//this is so that other controllers can just call $Document->QuickDoc->buildRetrievalString($filename, $xmlid)
		// and views could call $this->controller->QuickDoc->buildRetrievalString($filename, $xmlid)
		public $QuickDoc;
		
		
		public $viewMode = "transcription"; // this should be "transcription" or "dual" 
		
		public $filename; //the xml file with the doc!
		
		public $xmlid; // the xml:id of the element that is the doc!
		
		public $doc;
		
		public $xml;

		protected $skipTransform = false;
		

		function __construct(){
			$this->data = [];
			$this->xsltPath = \Publications\TeiDocument::XSLTPATH;
		}


		function meetQuickDoc($obj){
			$this->QuickDoc = $obj;
		}

		//simple single doc, non-ajax view
		function index(){
		    if(method_exists($this, "parseRetrievalString")) {
				$this->parseRetrievalString();
			} else {
				$this->getParams();
			}

			$this->loadFile();
			$this->data['document'] = $this->xml;
			$this->data['bodyClasses'] = "";

			$this->data['contextDocs'] = false;
			
			if($this->viewMode == "dual"){
			    $this->data['bodyClasses'] .= " dual";
			    
			    $PM = new \Publications\PageMaker($this);
			    
			    //new approach: get  all the text appearing on the pages that touch our document
			    $this->data['document'] = $PM->buildPagesXML();
		    
			    //alt approach: only the text from the doc, but split into page divs
			    /* $PM->removeLeadingTrailing();
			    $this->data['document'] = $PM->getDocXMLAsPages();
			    */
			    
			    //actually get the images as an array
			    $this->data['pageImages'] = $PM->getImages();
			}

			$this->render();
		}


		function render($view = ""){
			if(empty($view)) $view = self::DOCUMENT_VIEW;
			$this->_mvc->render($view, $this->data);
		}


		//ajax respond, just the object HTML
		function loadAjax(){
			$this->getParams();
			$this->loadFile();
			print $this->xml;
		}


		protected function getParams(){
			//get URL segments
			$this->filename = $this->_mvc->segment(self::DOC_FILENAME_URL_SEGMENT);
			$this->filename .= ".xml";
			$this->xmlid = $this->_mvc->segment(self::DOC_XMLID_URL_SEGMENT);
			$temp = $this->_mvc->segment(self::VIEW_MODE_URL_SEGMENT);
			if(!empty($temp)) {
			    $this->determineViewMode($temp);
			}
		}
		
		protected function determineViewMode($str){
		    if($str != "dual") $str = "transcription";
		    $this->viewMode = $str;
		}


		protected function loadFile(){
			$fullpath = \MHS\Env::SOURCE_FOLDER . $this->filename;

			$this->xml = $this->load($fullpath, $this->xmlid);
		}


		public function load($filepath, $xmlid = "", $skipTransform = false){

			if(!is_readable($filepath)) {
				$this->add_error("Sorry, can't find an XML file that matches your request for " . $filepath);
				$this->fail();
			}

			$this->doc = new \Publications\TeiDocument();

			if(!$this->doc->open($filepath)) {
				$this->add_error("Sorry, unable to open that XML file.");
				$this->fail();
			}

			if(!empty($xmlid)) $docXML = $this->doc->getDocFromID($xmlid);
			else {
			    $docXML = $this->doc->getFullDoc()->saveXML();
			}

			if($skipTransform === false && $this->skipTransform === false) {
				$docXML = $this->transform();
			}

			if($docXML === false) {
				$this->add_error("Sorry, unable to open that XML file, it might be malformed?");
				$this->fail();
			}

			return $docXML;
		} //load()



		function setXSLTPath($xsltPath){
			$this->xsltPath = $xsltPath;
		}


		function setTemplateOverridePath($overridePath){
			$this->templateOverridePath = $overridePath;
		}



		/* transforms the doc, first looking for any override xslt full of templates
		 *  to append to main xslt
		 */
		function transform(){
			//parameters to pass to XSLT
			$params = array(
				"docLinkPrefix" => \MHS\Env::DOC_VIEW_URL_PREFIX
			);


			if($this->doc->loadXSLT($this->xsltPath)) {
				//if we have a usable override path
				if(!empty($this->templateOverridePath) and is_readable($this->templateOverridePath)){

					//load
					$importXSL = new \DOMDocument;
					$importXSL->load($this->templateOverridePath);

					//get ref to main xslt
					$docXslt = $this->doc->getXslt();
					$rootEl = $docXslt->getElementsByTagNameNS("http://www.w3.org/1999/XSL/Transform","stylesheet")->item(0);

					//grab each template and append to main XSLT
					$set = $importXSL->getElementsByTagNameNS("http://www.w3.org/1999/XSL/Transform", "template");
					foreach($set as $el){
						$newnode = $docXslt->importNode($el, true);
						$rootEl->appendChild($newnode);
					}
				}

				//if no xmlid, we're just showing the whole doc
				if(empty($this->xmlid)){
		            $docXML = $this->doc->transformDoc($params);
			        return $docXML;
				}
				
				else if($this->doc->transformFragment($params)){
					$docXML = $this->doc->getXSLToutput();

					return $docXML;
				}
			} else {
    			$this->add_error("Sorry, unable to load transform " . $this->xsltPath);
    			$this->fail();
			}
		}


		
		function fail(){
		    
		    $errors = implode("<br/>", $this->errors);
		    $data['errors'] = $errors;
		    $data['hasErrors'] = true;
		    $this->_mvc->render(self::ERROR_VIEW, $data);
		    exit();
		}
		

	} //class
