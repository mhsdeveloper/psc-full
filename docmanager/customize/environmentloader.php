<?php


	require(SERVER_WWW_ROOT . "/environment.php");

	$envFile = \Publications\StaffUser::getProjectEnvFile();

	if(false == $envFile){
		die("Check that the project " . $_SESSION['PSC_SITE'] . " folder has been setup and is readable. Are you an Admin? Be sure to go to a specific project's dashboard first.");
	}
	require($envFile);

	$_SESSION['PROJECT_ID'] = \MHS\Env::PROJECT_ID;

