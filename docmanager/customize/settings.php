<?php

	//Title for the whole app
	define("DM_MANAGER_TITLE", "XML FILE MANAGER");

	//Name for the main content chunk: are we dealing with "files", or "pages" or "records"
	// you label it
	define("DM_FILE_NAME_LABEL", "File");

	//set this true to allow uploading content as files
	define("DM_ALLOW_UPLOAD", true);

	//set this to true to enable workflow controls. Note: hiding only hides the controls, the data remains
	define("DM_ENABLE_WORKFLOW", false);

	// set true to enable the checkout/in interface
	define("DM_ENABLE_CHECKOUT", true);

	// set true to enable the edit option, and be sure to setup appEventListeners for
	// editDocument / releaseDocument in your customize-frontend/scripts.js
	define("DM_ENABLE_EDIT", false);

		/* This only appears if somehow the user circumvents the clientside auth, so rarely if ever seen
		 use this to define the language describing checking out or editing a document. 
		for example: the default is for the checking out/in paradigm:
			"That document is currently checked out to "
	*/
	define("DM_CHECKEDOUT_TO", "That document is currently checked out to ");



	/* these are xpaths that are checked to find subjects to be indexed
	*/

	define("SUBJECTS_XPATHS", [
		"/tei:TEI/tei:teiHeader[1]//tei:keywords[1]//tei:item",
		"/tei:TEI/tei:text/tei:body//tei:subject"
	]);


	
/*

	EDIT THE VARIABLES ABOVE, NOT BELOW




*/
