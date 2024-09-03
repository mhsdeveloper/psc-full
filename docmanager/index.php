<?php

/*
	This is the master router index for our app

*/
	session_start();	

	define("ROLE_READ_ONLY", "contributor"); //all read only
	define("ROLE_XML_EDITOR", "xml_editor"); //read only names db, but checkout docs
	define("ROLE_EDITOR", "editor"); //full access, less publishing and delete
	define("ROLE_ADMIN", "administrator"); //full access

	define("PATH_ABOVE_WEBROOT", str_replace("index.php", "",$_SERVER['SCRIPT_NAME']));
	define("PATH_ABOVE_WEBROOT_TO_SOLR_JS", "/solr/solr.js");

	define("UPLOAD_URL", $_SERVER['SCRIPT_NAME'] . "/upload");


	//use our autoloader
	require_once "autoloader.php";
	require_once("mhs_global_env.php");

	ini_set('error_log', "errors.txt");
	define("SNIFF_OUT_FILE", "sniff_out.txt");


	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/docmanager/classes');
	$classLoader->register();

	$temploader = new SplClassLoader('Solr', SERVER_WWW_ROOT . 'html');
	$temploader->register();

	//this project's classes
	$projectLoader = new SplClassLoader('DocManager', SERVER_WWW_ROOT . 'html/docmanager/classes');
	$projectLoader->register();
	
	//customization classes
	$customLoader = new SplClassLoader("Customize", SERVER_WWW_ROOT . "html/docmanager");
	$customLoader->register();

	$pubsLoader = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/docmanager/classes');
	$pubsLoader->register();

	//load our mvc framework
	require "mhsmvc2.php";
	$mvc = new MHSmvc();

	

	/***************************************************
	 IMPLIMENTATION

		create a datamodel root class that has the db
		credentials:

					namespace Customize;

					class DataModel {
						const DATABASE = "docmanager";
						const USER = "shmide";
						const PASSWORD = "shmeelee";
					}
	
	****************************************************/
	require_once("customize/datamodel.php");



	//API calls supercede rest of setup
	if(strpos($_SERVER['REQUEST_URI'], "/api/") !== false){
		//API ROUTES NEED API KEY
		$mvc->route("/api/documents", "DocManager\Controllers\Api");
		$mvc->route("/api", "DocManager\Controllers\Api");

		//start!
		$mvc->run();
		die();
	}


	//SECURITY CHECK

	/***************************************************
	 IMPLIMENTATION

	 your customize/access.php file should:
	 1) die or otherwise handle if not logged in. 
	 
	 2) create a \Customize\User class extends
	 	\DocManager\Models\BaseUser
	    with static f()s:
	    ::role() returns one of thes roles (see above)
		::username() returns username
	 
	****************************************************/

	require_once("customize/settings.php");
	require_once("customize/access.php");





	
	/***************************************************
	 IMPLIMENTATION

	 your environmentloader.php needs to include
	 an environment file that extends the publications one:
	 
	    class Env extends \Publications\Environment {}
	
	****************************************************/
	require_once("customize/environmentloader.php");



	/***************************************************
	 IMPLIMENTATION

	 add you hooks to the customize/hooks.php file

	 see classes/docmanager/hooks.php for more info

	****************************************************/
	require_once("customize/hooks.php");




	/**********************************************
	 * ROUTING
	 *********************************************/
	$mvc->route("/search", "DocManager\Controllers\Search");

	$mvc->route("/documentfiles", "DocManager\Controllers\DocumentFiles");
	$mvc->route("/documents/reindexall", "DocManager\Controllers\Documents@reindexAll");
	$mvc->route("/documents/reindex", "DocManager\Controllers\Documents@reindex");
	$mvc->route("/documents", "DocManager\Controllers\Documents");
	$mvc->route("/documents/resync", "DocManager\Controllers\Documents@resync");

	$mvc->route("/project/steps", "DocManager\Controllers\Steps@getFromProject");
	$mvc->route("/project/newstep", "DocManager\Controllers\Steps@newStep");
	$mvc->route("/project/updatestep", "DocManager\Controllers\Steps@updateStep");
	$mvc->route("/project/deletestep", "DocManager\Controllers\Steps@deleteStep");
	$mvc->route("/steps", "DocManager\Controllers\Start@steps");
	$mvc->route("/update-document-step", "DocManager\Controllers\Steps@updateDocumentStep");

	$mvc->route("/upload", "DocManager\Controllers\Document@upload");
	$mvc->route("/checkout", "DocManager\Controllers\Document@checkout");
	$mvc->route("/undo-checkout", "DocManager\Controllers\Document@undoCheckout");
	$mvc->route("/checkin", "DocManager\Controllers\Document@checkin");

	$mvc->route("/zip", "DocManager\Controllers\Zip");
	$mvc->route("/downloadXML", "DocManager\Controllers\Document@downloadXML");
	$mvc->route("/delete", "DocManager\Controllers\Document@delete");

	$mvc->route("/unpublish", "DocManager\Controllers\Document@unPublish");
	$mvc->route("/publish", "DocManager\Controllers\Document@publish");

	$mvc->route("/meta", "DocManager\Controllers\Document@testMeta");
	$mvc->route("/", "DocManager\Controllers\Start");

	
	/***************************************************
	 IMPLIMENTATION

	 you may add other routes here
	 
	****************************************************/
	require_once("customize/routes.php");

	//start!
	$mvc->run();
