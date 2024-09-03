<?php

	define("TEST_MODE", false);


	/*
		This is the master router index for our app

	*/
	
	require(SERVER_WWW_ROOT . "/environment.php");

	//use our autoloader
	require "autoloader.php";

	session_start();	

	// if(!isset($_SESSION['alpha']) && isset($_GET['alpha'])){
	// 	$_SESSION['alpha'] = true;
	// } 

	ini_set('error_log', "errors.txt");
	define("SNIFF_OUT_FILE", "sniff_out.txt");


	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/publications/mhs/classes');
	$classLoader->register();

	// $fogLoader = new SplClassLoader('Foghorn', SERVER_WWW_ROOT . 'html/publications/lib/foghorn/classes');
	// $fogLoader->register();

	$pubsLoader = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$pubsLoader->register();

	//this project's classes
	$projectLoader = new SplClassLoader('Wetvac', SERVER_WWW_ROOT . 'html/tools/wetvac/classes');
	$projectLoader->register();

	//load settings
	// if(isset($_SESSION['alpha'])){
	// 	include("classes/environment-test.php");
	// } else {
	
	include("classes/environment.php");
	


	//load our mvc framework
	require "mhsmvc2.php";
	$mvc = new MHSmvc();




	/**********************************************
	 * ROUTING
	 *********************************************/

	$mvc->route("/convert/upload", "Wetvac\Controllers\Convert@upload");

	$mvc->route("/convert/process", "Wetvac\Controllers\Convert@process");


	$mvc->route("/", "\Wetvac\Controllers\Convert");


	//start!
	$mvc->run();
