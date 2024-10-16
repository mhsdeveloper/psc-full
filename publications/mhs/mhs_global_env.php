<?php



	/*
		This file alters various constants depending on whether the context is a live server
		or a testing machine.

	*/



	if(COOP_SINGLE_INSTALL) $serverAddr  = "SINGLE_INSTALL";
	else $serverAddr = $_SERVER['SERVER_ADDR'];


	if($serverAddr == COOP_TEST_IP){

		//test server
		define("APP_DOMAIN", COOP_TEST_IP);

		define("SOLR_BASEURL", 'http://'. APP_DOMAIN . ':8983/solr/');

		define("SOLR_IP", APP_DOMAIN);

		define("API_URL", '//' . APP_DOMAIN . '/mhs-api/v1/');
		define("ALTAPI_URL", '//'. APP_DOMAIN . '/mhs-api/u1/');

		define ("MHS_DEBUG", false);

		if(!defined("JAVA_BIN")) define("JAVA_BIN", ALT_JAVA_PATH);
	}
			
	//some sort of wii fi test setup!
	else if(strpos($serverAddr, "192.168.") !== false){

		define("SOLR_BASEURL", 'http://' . $serverAddr . ':8983/solr/');
		define("API_URL", '//' . $serverAddr .'/mhs-api/v1/');
		define("ALTAPI_URL", '//'. $serverAddr .'/mhs-api/u1/');
		define("SOLR_IP", $serverAddr);

		define ("MHS_DEBUG", true);

		if(!defined("JAVA_BIN")) define("JAVA_BIN", "java");
	}

	else {
		define("APP_DOMAIN", COOP_LIVE_DOMAIN);

		define("SOLR_BASEURL", 'http://'. APP_DOMAIN . ':8983/solr/');

		define("SOLR_IP", APP_DOMAIN);

		define("API_URL", '//' . APP_DOMAIN . '/mhs-api/v1/');
		define("ALTAPI_URL", '//'. APP_DOMAIN . '/mhs-api/u1/');

		define ("MHS_DEBUG", false);

		if(!defined("JAVA_BIN")) define("JAVA_BIN", ALT_JAVA_PATH);
	}

