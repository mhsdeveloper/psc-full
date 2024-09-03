<?php

	require("../index_core.php");

	//this project's classes
	$projectLoader = new SplClassLoader('Tools', SERVER_WWW_ROOT . 'html/tools/tools/classes');
	$projectLoader->register();


	//load our mvc framework
	require "mhsmvc2.php";
	$mvc = new MHSmvc();

	/**********************************************
	 * ROUTING
	 *********************************************/
	$mvc->route("/storemenu", "Tools\Controllers\Start@storeMenu");
	$mvc->route("/updateschema", "Tools\Controllers\Start@generateUmbrellaList");
	$mvc->route("/storefooter", "Tools\Controllers\Start@storeFooter");
	$mvc->route("/storecss", "Tools\Controllers\Start@storeCss");
	$mvc->route("/", "Tools\Controllers\Start");

	//start!
	$mvc->run();
