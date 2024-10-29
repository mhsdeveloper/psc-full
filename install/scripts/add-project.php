<?php

	if(!isset($argv)){
		die("This script must be run from the command line");
	}

	echo "\n";
	echo "This tool will create the file structure for a new coop project/edition.";
	echo "\n";


	include(SERVER_WWW_ROOT . "/environment.php");
	$dirs = scandir(PSC_PROJECTS_PATH);



	echo "\n";
	$line = readline("Enter the full project/edition name: ");
	readline_add_history($line);
	$fullname = readline_list_history()[0];

	for($i = 0; $i<10; $i++){
		echo "\n";
		echo "Choose an abbreviation for the new project. This will also be the subpath of the project's URL, for example a project with the abbreviation 'gma' will have a home page at 'www.mydomain.org/gma'\n";
		echo "Use3-6 lowercase letters only, no spaces or other characters. The abbreviation must be unique. Currently the following are in use:\n";
		echo implode(" | ", $dirs);

		$line = readline("Enter abbreviation: ");
		readline_add_history($line);
		$abbr = readline_list_history()[0];

		if(!preg_match("/[a-z]{3,6}/")){
			echo "\nThat abbreviation is not valid.\n";
			continue;
		}

		if(in_array($abbr, $dir)){
			echo "\nThat abbreviation is already in use.\n";
			continue;
		}

		if($i == 9){
			echo "\nToo many attempts. Please try again later or get help for this step from the MHS.\n";
			die();
		}

		break;
	}



	$cmd = 'sudo mysql -e "USE psccore; INSERT INTO projects SET name= \'' . $fullname . '\', project_sitename = \'' . $abbr . '\', description = \'\'; SELECT LAST_INSERT_ID();"';

	//run the cmd and get the stndout as string
	ob_start();
	passthru($cmd);
	$output = ob_get_contents();
	ob_end_clean();

	//parse output to get last ID
	$lines = explode("\n", $output);

	$insertID = 0;

	foreach($lines as $index => $line){
		//line after this is the ID
		if($line == "LAST_INSERT_ID()"){
			$insertID = $lines[$index + 1];
			echo "\nFound ID for added project : $insertID ...\n";
		}
	}