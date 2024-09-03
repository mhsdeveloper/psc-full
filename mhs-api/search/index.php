<?php

	/*
		This is the master router index for our app

	*/
	session_start();	

	//use our autoloader
	require "autoloader.php";
	require_once("mhs_global_env.php");

	ini_set('error_log', "errors.txt");
	define("SNIFF_OUT_FILE", "sniff_out.txt");


	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/publications/mhs/classes');
	$classLoader->register();

	$pubsLoader = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$pubsLoader->register();

	$thisLoader = new SplClassLoader('API', SERVER_WWW_ROOT . 'html/mhs-api/u1/classes');
	$thisLoader->register();


	
	/**********************************************
	 * ROUTING
	 *********************************************/
	//load our mvc framework
	require "mhsmvc2.php";
	$mvc = new MHSmvc();

	$mvc->route("/metadata", "API\Controllers\SolrSearch");

	//start!
	$mvc->run();
