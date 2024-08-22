<?php

	namespace Template\Controllers;


	class Document extends \Publications\DocumentLoader {

		//which segment of the URL (index.php being 0) holds the retrieval string
		const RETRIEVAL_STRING_URL_SEGMENT = 2;
		const VIEW_MODE_URL_SEGMENT = 3;

		private $loggedin = false;
		private $proofView = false;
		private $xmlView = false;


		function __construct(){
			$this->data = [];

			if(isset($_GET['proof'])){
				$this->proofView = true;
				$this->xsltPath = \MHS\Env::getProofXSLT();
				return;
			}

			else if(isset($_GET['xml'])){
				$this->xmlView = true;
				$this->skipTransform = true;
			}				

			// xslt to ready tei for modern browser display: some TEI but some HTML
			$this->xsltPath = \MHS\Env::getDisplayXSLT();
		}


		/* overriding original intent of iterpreting params;
			now, just a single id is used to figure out path:
				id is assumed to be filename, unless it has one of these subtypes:
					--entry : split on this, [0] part being the filename, the whole being the fragment xml:id

		*/
		protected function getParams(){
			//get URL segments
			$seg = $this->_mvc->segment(self::DOC_FILENAME_URL_SEGMENT);
			if(strpos($seg, ",") !== false) $seg = explode(",", $seg)[0];
			$this->xmlid = $seg;
			$this->filename = $seg;

			if(strpos($seg, "--entry") !== false){
				$parts = explode("--entry", $seg);
				$this->filename = $parts[0];
			}
			
			$this->filename .= ".xml";

			$temp = $this->_mvc->segment(self::VIEW_MODE_URL_SEGMENT);
			if(!empty($temp)) {
			    $this->determineViewMode($temp);
			}
		}


		/* override to check for published, logged in, etc*/
		function render($view = ""){
			$view = \MHS\Env::getDocumentView();

			if(\Publications\StaffUser::isLoggedin()) $this->loggedin = true;

			if($this->proofView && $this->loggedin){
				parent::render(\MHS\Env::getProofView());
				return;
			}

			if($this->xmlView && $this->loggedin){
				$xml = $this->xml;

				//syntax highlighting
				$xml = str_replace(["&", "<", ">"], ["&amp;", "&lt;", "&gt;"], $xml);
				//need to do this in separate pass to avoid the &gt removing our <code> closing >
				$xml = str_replace(["&lt;", "&gt;"], ["<code>&lt;", "&gt;</code>"], $xml);
				
//				print "<pre>" . $xml . "</pre>";

				$this->_mvc->render("document-xml.php", ["xml" => $xml]);
				return;
			}


			if(!$this->loggedin && $this->doc->isPublished()) parent::render($view);
			else if($this->loggedin) parent::render($view);
			else parent::render("unpublished.php");
		}


		public function loadTimePeriods(){
			$file = \MHS\Env::APP_INSTALL_DIR . "support-files/timeperiods.xml";
			if(is_readable($file)){
				$xml = file_get_contents($file);

				//remove xml declaration
				$xml = preg_replace("/<\?.*\?>/U", "", $xml);

				return $xml;
			}
		}

	} // class
