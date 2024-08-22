<?php

	/* Class that extend this class Must implement the DocInterface;
	 * see docinterface.php for details
	 */



	namespace Publications;


	class Document extends Base {


		const TEMPLATE_OVERRIDE_PATH = "";



		public function __construct(){
			$this->data = [];
		}


		public function index(){

			//parse input
			$mode = "document";

			//some backward compat
			if(isset($_GET['id'])) $id = $_GET['id'];
			else $id = $this->_mvc->segment(\MHS\Env::DOC_LINK_URL_SEGMENT);


			if(strpos($id, "front-") !== false) {
				$mode = "document";
				$temp = explode("front-", $id);
				$id = $temp[1];
			}

			else if(strpos($id, "p") !== false){
				$mode = "page";
			}

			else if(isset($_GET['mode']) and $_GET['mode'] == 'p') {
				$mode = "page";
			}

			//add data point for qualifier to page no.
			if($this->_mvc->segment(\MHS\Env::DOC_LINK_URL_SEGMENT + 1)){
				$this->data['pageNoQualifier'] = $this->_mvc->segment(\MHS\Env::DOC_LINK_URL_SEGMENT + 1);
			}

			$this->setID($id);

			if($mode == "document") {

				$this->displayDocument();

			} else {

				//look for FGEA-style pb ids
				if(strpos($id, "-pb-") > 0){
					$separator = "-pb-";
				} else $separator = "p";

				$temp = explode($separator, $id);
				$vol = $temp[0];

				//fix tons of errors from web queries that are somehow sending IDs with "p" but no page no
				if(!isset($temp[1])) return;

				$pageNo = $temp[1];
				while($pageNo[0] == "0") $pageNo = substr($pageNo, 1);

				//remove allowed chars and check that it's OK
				$temp = str_ireplace($this->getAllowedVolChars(), "", $vol);
				$temp = "1" . $temp;
				if(!is_numeric($temp)){
					$this->add_error("Sorry, that volume is invalid.");
					$this->fail();
				}

				$this->displayPage($pageNo);
			}
		}



		public function meetMVC($mvc){
			$this->_mvc = $mvc;
		}



		public function setID($id){
			$this->id = $id;
			$this->data['id'] = $this->id;
		}


		public function pageFromPageID($id){

			//many projects use "[series]p[Pageno]"
			if(strpos($id, "p") !== false){
				$temp = explode("p", $id);
				return $temp[1];

			} else return $id;
		}




		public function loadFile(){
			//choose the right XML file based on series/vol;
			$this->filepath = $this::find($this->id);

			if($this->filepath === false) {
				$this->add_error("Sorry, can't find an XML file that matches your request");
				$this->fail();
			}

			// open the file
			$this->doc = new \Publications\TeiDocument();

			if(!$this->doc->open($this->filepath)) {
				$this->add_error("Sorry, unable to open that XML file.");
				$this->fail();
			}
		}





		public function displayDocument(){

			//sanitized: if we look at id without the allowed chars, it should all be digits
			$temp = str_ireplace($this->getAllowedIdChars(), "", $this->id);
			//add leading 1 to make it a real number
			$temp = "1" . $temp;

			//it has to be numeric or there's something fishing going on
			if(!is_numeric($temp)){
				$this->add_error("Sorry, don't know what to make of the id provided [{$temp}].");
				$this->fail();
			}

			$this->loadFile();

			$docXML = $this->doc->getDocFromID($this->id);

			if(isset($_GET['markup'])) {
				$docXML = $this->transform(\Publications\TeiDocument::XSLTPATH_MARKUP_ONLY);
				print $docXML;
				exit();
			}

			//transform
			// plus add a URI flag to bypass this
			if(!isset($_GET['skip']) && !isset($_GET['markup'])) {
				$docXML = $this->transform();
			}
			$this->data['viewTitle'] = "document " . $this->id;
			$this->data['bodyClass'] = "document";
			$this->data['pageNo'] = 0;

			//build next prev nav
			if($nextDocNode = $this->doc->findNextDoc()){
				$this->data['nextDoc'] = $this->doc->gleenBiblMeta($nextDocNode);
			} else $this->data['nextDoc'] = array();

			if($prevDocNode = $this->doc->findPrevDoc()){
				$this->data['prevDoc'] = $this->doc->gleenBiblMeta($prevDocNode);
			} else $this->data['prevDoc'] = array();


			$this->callView($docXML);
		}




		public function displayPage($page){

			$this->loadFile();

			$docXML = $this->doc->getPageFragment($this->id);

			//transform
			// plus add a URI flag to bypass this
			if(!isset($_GET['skip'])) {
				$docXML = $this->transform();
			}

			//at this stage, if page is roman, it will have leading R
			if($page[0] == "R"){
				$page = \MHS\Helpers\RomanNumerals::fromDecimal(substr($page, 1));
			}


			$this->data['viewTitle'] = "documents near page " . $page;
			$this->data['bodyClass'] = "page";
			$this->data['pageNo'] = $page;
			$this->data['prevDoc'] = array();
			$this->data['nextDoc'] = array();

			$this->callView($docXML);
		}





		public function callView($docXML){

			//Check that we got something
			if($docXML === false) {
				$this->add_error("Sorry, unable to open that XML file, it might be malformed?");
				$this->fail();
			}

			//call the view
			$this->data['docXML'] = "<div id='document'>\n" . $docXML . "\n</div>\n";

			$this->data['viewVol'] = $this::volumeFromID($this->id);
			$this->_mvc->render("document", $this->data);


		} //index



		/* transforms the doc, first looking for any override xslt full of templates
		 *  to append to main xslt
		 */
		function transform($xsltPath = \Publications\TeiDocument::XSLTPATH){

			//parameters to pass to XSLT
			$params = array(

 				"docLinkPrefix" => \MHS\Env::DOC_LINK_PREFIX,
				"stLinkPrefix" => \MHS\Env::SHORT_TITLE_LINK_PREFIX,

			);


			if($this->doc->loadXSLT($xsltPath)) {

				//if we have a usable override path
				if(is_readable($this::TEMPLATE_OVERRIDE_PATH)){

					//load
					$importXSL = new \DOMDocument;
					$importXSL->load($this::TEMPLATE_OVERRIDE_PATH);

					//get ref to main xslt
					$rootEl = $this->doc->xslt->getElementsByTagNameNS("http://www.w3.org/1999/XSL/Transform","stylesheet")->item(0);

					//grab each template and append to main XSLT
					$set = $importXSL->getElementsByTagNameNS("http://www.w3.org/1999/XSL/Transform", "template");
					foreach($set as $el){
						$newnode = $this->doc->xslt->importNode($el, true);
						$rootEl->appendChild($newnode);
					}
				}

				if($this->doc->transformFragment($params)){
					$docXML = $this->doc->getXSLToutput();

					return $docXML;
				}
			}

			$this->add_error("Sorry, unable to transform the document.");
			$this->fail();
		}



	} //class
