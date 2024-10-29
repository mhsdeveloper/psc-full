<?php

	namespace MHS;


	class Env extends \Publications\Environment {

		const DEBUGGING_MODE_ON = false;

		/* this is used for the folder name and THUS ALSO THE URL, for the SOLR index prefix */
		const PROJECT_SHORTNAME = "[[SHORT-NAME-HERE]]";

		const PROJECT_ID = 1;

		//these items come from the xml config file

		/*::FROM-CONFIG */
		const PROJECT_FULLNAME = "[[FULL-NAME-HERE]]";
		const PROJECT_HTML_TITLE = "[[FULL-NAME-HERE]]";
		const CSS_FILENAME = "example.css";
		/*::END-FROM-CONFIG */

		const FILENAME_PATTERN = "/[a-zA-Z0-9\-]+\.xml/";

		/* uncomment to override standard f(): use this for a project that requires a different schema */
		// public static function getSchemaFilename(){
		// 	return  SERVER_WWW_ROOT . "html/publications/pub/schema/codem-0.3-djqa.rng";
		// }

		/*  uncomment to override standard f(): should return relative path to alterative document.php view file */
		// public static function getDocumentView(){
		// 	return "/projects/jqa/views/document.php";
		// }
		

		/* override to change the context search range in months */
		// public static function getContextSearchRange(){
		// 	return 6;
		// }

		
		protected function __construct(){
			$this->constructContinued();

			/* you can overrride SOLR xslt here */
			//$this->configSOLR["indexXSLT"] = SERVER_WWW_ROOT . "html/projects/jqa/xslt/jqa-to-solr.xsl";
			//$this->configSOLR["deleteXSLT"] = SERVER_WWW_ROOT . "html/publications/lib/xsl/solr-delete-psc.xsl";

		}
	}
