<?php



	/*
		This file alters various constants depending on whether the context is a live server
		or a testing machine.

	*/


	if($_SERVER['SERVER_ADDR'] == COOP_TEST_IP){

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
	else if(strpos($_SERVER['SERVER_ADDR'], "192.168.") !== false){

		define("SOLR_BASEURL", 'http://' . $_SERVER['SERVER_ADDR'] . ':8983/solr/');
		define("API_URL", '//' . $_SERVER['SERVER_ADDR'] .'/mhs-api/v1/');
		define("ALTAPI_URL", '//'. $_SERVER['SERVER_ADDR'] .'/mhs-api/u1/');
		define("SOLR_IP", $_SERVER['SERVER_ADDR']);

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

