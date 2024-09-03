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


	require(SERVER_WWW_ROOT . "/environment.php");

	$pubsLoader = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$pubsLoader->register();

	$thisLoader = new SplClassLoader('API', SERVER_WWW_ROOT . 'html/mhs-api/u1/classes');
	$thisLoader->register();

	//SECURITY CHECK
	if(\Publications\StaffUser::isLoggedin() == false) die("Please login through Wordpress.");


	//load settings
	$envFile = \Publications\StaffUser::getProjectEnvFile();
	if(false == $envFile){
		die("Check that the project " . $_SESSION['PSC_SITE'] . " folder has been setup and is readable. Are you an Admin? Be sure to go to a specific project's dashboard first.");
	}
	require($envFile);

	$_SESSION['PROJECT_ID'] = \MHS\Env::PROJECT_ID;

	

	
	/**********************************************
	 * ROUTING
	 *********************************************/
	//load our mvc framework
	require "mhsmvc2.php";
	$mvc = new MHSmvc();

	$mvc->route("/add-to-project", "API\Controllers\Project@addNames");
	$mvc->route("/remove-from-project", "API\Controllers\Project@removeNames");
	$mvc->route("/add-missing-huscs", "API\Controllers\Project@addMissingHuscs");
	$mvc->route("/huscs/audit", "API\Controllers\Names@auditHuscs");
	$mvc->route("/names/search", "API\Controllers\Names@search");

	$mvc->route("/names/fixpunc", "API\Controllers\Names@fixStrayPunctuation");
	$mvc->route("/name", "API\Controllers\Name@get");

	//start!
	$mvc->run();
