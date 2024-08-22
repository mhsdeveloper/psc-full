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

	require(SERVER_WWW_ROOT . "/environment.php");

	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/publications/mhs/classes');
	$classLoader->register();

	$pubsLoader = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$pubsLoader->register();

	//MAINTENANCE CHECK
	if(MAINTENANCE_TOOLS !== false){
		print "This app is currently under maintenance. ";
		if(MAINTENANCE_TOOLS !== true){
			 print MAINTENANCE_TOOLS;
		}

		die();
	}

	//SECURITY CHECK
	if(\Publications\StaffUser::isLoggedin() == false) die("Please login.");

	//load settings
	$envFile = \Publications\StaffUser::getProjectEnvFile();
	if(false == $envFile){
		die("Check that the project " . $_SESSION['PSC_SITE'] . " folder has been setup and is readable. Are you an Admin? Be sure to go to a specific project's dashboard first.");
	}
	require($envFile);

	$_SESSION['PROJECT_ID'] = \MHS\Env::PROJECT_ID;

	