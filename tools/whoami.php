<?php 


	session_start();	


	//use our autoloader
	require "autoloader.php";

	ini_set('error_log', "errors.txt");
	define("SNIFF_OUT_FILE", "sniff_out.txt");


	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/publications/mhs/classes');
	$classLoader->register();

	$pubsLoader = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$pubsLoader->register();


	$data = \Publications\StaffUser::currentUser();
	$AR = new \Publications\AjaxResponder2();
	$AR->addData($data);
	$AR->respond();

