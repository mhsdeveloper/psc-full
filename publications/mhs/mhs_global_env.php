<?php



	/*
		This file alters various constants depending on whether the context is a live server
		or a testing machine.

	*/





	if(COOP_SINGLE_INSTALL) $serverAddr  = "SINGLE_INSTALL";
	else $serverAddr = $_SERVER['SERVER_ADDR'];

	switch($serverAddr){

		//main server
		case "SINGLE_INSTALL":
		case COOP_LIVE_IP:
			define("APP_DOMAIN", COOP_LIVE_DOMAIN);

			define("SOLR_BASEURL", 'http://'. APP_DOMAIN . ':8983/solr/');

			define("SOLR_IP", APP_DOMAIN);

			define("API_URL", '//' . APP_DOMAIN . '/mhs-api/v1/');
			define("ALTAPI_URL", '//'. APP_DOMAIN . '/mhs-api/u1/');

			define ("MHS_DEBUG", false);

			if(!defined("JAVA_BIN")) define("JAVA_BIN", ALT_JAVA_PATH);
			break;



		//test server
		case COOP_TEST_IP:
			define("APP_DOMAIN", COOP_TEST_IP);

			define("SOLR_BASEURL", 'http://'. APP_DOMAIN . ':8983/solr/');

			define("SOLR_IP", APP_DOMAIN);

			define("API_URL", '//' . APP_DOMAIN . '/mhs-api/v1/');
			define("ALTAPI_URL", '//'. APP_DOMAIN . '/mhs-api/u1/');

			define ("MHS_DEBUG", false);

			if(!defined("JAVA_BIN")) define("JAVA_BIN", ALT_JAVA_PATH);
			break;

	
		// TEST SERVER CONFIGS
		default:

			define("SOLR_BASEURL", 'http://' .LOCAL_TEST_IP . ':8983/solr/');
			define("API_URL", '//' . LOCAL_TEST_IP .'/mhs-api/v1/');
			define("ALTAPI_URL", '//'. LOCAL_TEST_IP .'/mhs-api/u1/');
			define("SOLR_IP", LOCAL_TEST_IP);

			define ("MHS_DEBUG", true);

			if(!defined("JAVA_BIN")) define("JAVA_BIN", "java");
			break;
	}

