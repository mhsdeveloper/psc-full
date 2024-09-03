<?php
/* this is the bootloaded for the whole front end

*/


/* this script simply passes along the REQUEST_URI to the project subfolder and it's
 *	index.php, which should act as if it was called directly from nginx.
 */

(function(){

	ini_set('error_log', "errors.txt");
	define("SNIFF_OUT_FILE", "sniff_out.txt");


	chdir(SERVER_WWW_ROOT . "/html/publications/template");

	//use our autoloader
	require "autoloader.php";
	require(SERVER_WWW_ROOT . "/environment.php");

	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/publications/mhs/classes');
	$classLoader->register();

	$classLoader2 = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$classLoader2->register();

	$classLoader3 = new SplClassLoader('Solr', SERVER_WWW_ROOT . 'html');
	$classLoader3->register();

	$projectEnv = \Publications\Environment::getProjectEnv();

	//Coop-wide route names will be interpreted as the project name, so look for those
	$coopRouteSegments = ["search", "searchQuery", "document"];

	if(in_array($projectEnv['projectName'], $coopRouteSegments)){
		$projectName = "coop";
	} else {
		$projectName = $projectEnv['projectName'];
	}

	define("PROJECT_SHORT_NAME", $projectName);

	require(PSC_PROJECTS_PATH . $projectName . "/environment.php");

	//look for template override for project
	$template_path =  PSC_PROJECTS_PATH . $projectName . "/template/";

	if(is_readable($template_path)) {
		$template_path .= "/classes";
	}
	else $template_path =  SERVER_WWW_ROOT . 'html/publications/template/classes';


	$classLoader3 = new SplClassLoader('Template', $template_path);
	$classLoader3->register();

	

	//load mhs env
	require "mhs_global_env.php";

	//load our mvc framework
	require "mhsmvc2.php";
	$mvc = new MHSmvc();


	/**********************************************
	 * ROUTE OUR URI REQUESTS! WOO HOO
	 *********************************************/

	//place the most specific URI requests first!
	$mvc->route("/" . $projectName . "/explore", "Template\Controllers\Explore");
	$mvc->route("/" . $projectName . "/document", 'Template\Controllers\Document');
	$mvc->route("/" . $projectName . "/contextYears", 'Template\Controllers\Context@years');
	$mvc->route("/" . $projectName . "/contextMonths", 'Template\Controllers\Context@months');
	$mvc->route("/" . $projectName . "/contextMonthDocs", 'Template\Controllers\Context@monthDocs');
	$mvc->route("/" . $projectName . "/context", 'Template\Controllers\Context');
	$mvc->route("/" . $projectName . "/finddocbydate", 'Template\Controllers\Context@findDocByDate');
//OLD USE /mhs-api/ext/metadata  	$mvc->route("/" . $projectName . "/metadata", 'Template\Controllers\Metadata');
	$mvc->route("/" . $projectName . "/read", 'Template\Controllers\Search@read');
	$mvc->route("/" . $projectName . "/topic", 'Template\Controllers\Topic');
	$mvc->route("/" . $projectName . "/person", 'Template\Controllers\Person');
	$mvc->route("/" . $projectName . "/searchQuery", "Template\Controllers\Search@ajaxQuery");
	$mvc->route("/" . $projectName . "/searchDirect", "Template\Controllers\Search@solrDirectProjectSearch");
	$mvc->route("/" . $projectName . "/search", 'Template\Controllers\Search');


	//site-wide pages

	//  [domain]/publications/search
	$mvc->route("/searchQuery", 'Template\Controllers\Search@solrDirect');
	$mvc->route("/search", 'Template\Controllers\Search@coopIndex');



	//generic non-project-specific route to metadata
///mhs-api/ext/metadata  	$mvc->route("/projects/metadata", 'Template\Controllers\Metadata');

//	$mvc->route("/" . $projectName . "/test", "Template\Controllers\Search@test");
	
	// //some static pages
    // $mvc->remap("/headnotes", "views/headnotes.php");

	//start!
	$mvc->run();

})();