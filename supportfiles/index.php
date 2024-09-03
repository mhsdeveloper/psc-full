<?php

/*
	This is the master router index for our app

*/
	session_start();	

	define("PATH_ABOVE_WEBROOT", str_replace("index.php", "",$_SERVER['SCRIPT_NAME']));

	define("UPLOAD_URL", PATH_ABOVE_WEBROOT . "/upload");

	//use our autoloader
	require_once "autoloader.php";
	require_once("mhs_global_env.php");

	ini_set('error_log', "errors.txt");
	define("SNIFF_OUT_FILE", "sniff_out.txt");

	require(SERVER_WWW_ROOT . "/environment.php");

	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/publications/mhs/classes');
	$classLoader->register();

	$classLoader2 = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$classLoader2->register();

	//this project's classes
	$projectLoader = new SplClassLoader('SupportFiles', SERVER_WWW_ROOT . 'html/supportfiles/classes');
	$projectLoader->register();

	//no autoloader needed, we just have this one file
	include(SERVER_WWW_ROOT . "html/publications/lib/classes/publications/staffuser.php");
	

	if(\Publications\StaffUser::isLoggedin() == false) die("Please login.");
	

	$user = \Publications\StaffUser::currentUser();

	$envFile = \Publications\StaffUser::getProjectEnvFile();

	if(false == $envFile){
		die("Check that the project " . $_SESSION['PSC_SITE'] . " folder has been setup and is readable. Are you an Admin? Be sure to go to a specific project's dashboard first.");
	}
	require($envFile);

	$_SESSION['PROJECT_ID'] = \MHS\Env::PROJECT_ID;



	//load our mvc framework
	require "mhsmvc2.php";
	$mvc = new MHSmvc();

	



	/**********************************************
	 * ROUTING
	 *********************************************/


	$mvc->route("/upload", "SupportFiles\Controllers\Start@upload");
	$mvc->route("/getFile", "SupportFiles\Controllers\Start@getFile");
	$mvc->route("/deleteFile", "SupportFiles\Controllers\Start@deleteFile");
	$mvc->route("/saveFile", "SupportFiles\Controllers\Start@saveFile");
	$mvc->route("/download", "SupportFiles\Controllers\Start@download");

	$mvc->route("/zip", "DocManager\Controllers\Zip");

	$mvc->route("/dir", "SupportFiles\Controllers\Start@dir");
	$mvc->route("/", "SupportFiles\Controllers\Start");

	

	//start!
	$mvc->run();
