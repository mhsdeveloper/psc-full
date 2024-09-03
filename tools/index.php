<?php

/* this script simply passes along the REQUEST_URI to the project subfolder and it's
 *	index.php, which should act as if it was called directly from nginx.
 */
(function(){

	//remove leading slash
	if($_SERVER['REQUEST_URI'][0] == "/") $request = substr($_SERVER['REQUEST_URI'], 1);
	else $request = $_SERVER['REQUEST_URI'];
	
	//uri segments
	$segments = explode("/", $request);

	//popoff "publications"
	array_shift($segments);

	//project is next element
	$project = array_shift($segments);

	//pretend we were already in the subfolder for the project
	chdir($project);

	//and let's also lie to the project's router and make it think it was called directly:
	$_SERVER["SCRIPT_NAME"] = "/tools/" . $project . "/index.php";


	require($project . "/index.php");

})();
