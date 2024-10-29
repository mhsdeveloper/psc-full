<?php

	namespace MHS;


	class Env extends \Publications\Environment {

		const DEBUGGING_MODE_ON = false;

		/* this is used for the folder name and THUS ALSO THE URL, for the SOLR index prefix */
		const PROJECT_SHORTNAME = "coop";

		const PROJECT_ID = 0;

		//these items come from the xml config file

		/*::FROM-CONFIG */
		const PROJECT_FULLNAME = "[[EDIT-THIS-COOP-NAME]]";
		const PROJECT_HTML_TITLE = "[[EDIT-THIS-COOP-NAME]]";
		const CSS_FILENAME = "example.css";

		/*::END-FROM-CONFIG */

		const FILENAME_PATTERN = "/(CMS[0-9]{4}-[0-9]{2}-[0-9]{2}-[a-zA-Z0-9\-]+\.xml)/";

		
		protected function __construct(){
			$this->constructContinued();
		}
	}
