<?php

	/*
		This is the master router index for our app

	*/

	/*
		ISSUES WITH SECURITY, limiting api access:

		not all browser will send referer header, so if we really need to limit this,
		api user will need to first AJAX to a server script that then CURLS over https 
		with an api key.
	*/
	//	require "apikeycheck.php";



	session_start();	

	//use our autoloader
	require "autoloader.php";
	require_once("mhs_global_env.php");

	ini_set('error_log', "errors.txt");
	define("SNIFF_OUT_FILE", "sniff_out.txt");


	require(SERVER_WWW_ROOT . "/environment.php");

	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/publications/mhs/classes');
	$classLoader->register();

	$pubsLoader = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$pubsLoader->register();

	$thisLoader = new SplClassLoader('API', SERVER_WWW_ROOT . 'html/mhs-api/u1/classes');
	$thisLoader->register();


	$solrLoader = new SplClassLoader('Solr', SERVER_WWW_ROOT . 'html');
	$solrLoader->register();

		
	/**********************************************
	 * ROUTING
	 *********************************************/
	//load our mvc framework
	require "mhsmvc2.php";
	$mvc = new MHSmvc();

	$urlStart = "/mhs-api/ext/";

	$mvc->route($urlStart . "names/search", "API\Controllers\ExtNames@publicSearch");
	$mvc->route($urlStart . "names/fields", "API\Controllers\ExtNames@listFields");
	$mvc->route($urlStart . "help", "API\Controllers\ExtNames@help");
	$mvc->route($urlStart . "names", "API\Controllers\ExtNames@namesFromHuscs");
	$mvc->route($urlStart . "name", "API\Controllers\ExtName");
	$mvc->route($urlStart . "metadata", "API\Controllers\Metadata");
	$mvc->route($urlStart . "search", "API\Controllers\Search");

	//start!
	$mvc->run();
