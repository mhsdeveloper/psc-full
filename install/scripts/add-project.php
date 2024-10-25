<?php

	if(!isset($argv)){
		die("This script must be run from the command line");
	}

	echo "\n";
	echo "This tool will create the file structure for a new coop project/edition.";
	echo "\n";



	// $line = readline("Enter your mysql username: ");
	// readline_add_history($line);
	// $username = readline_list_history()[0];

	// $line = readline("Enter your mysql password: ");
	// readline_add_history($line);
	// $password = readline_list_history()[0];


	echo "\n";
	$line = readline("Enter the full project/edition name: ");
	readline_add_history($line);
	$fullname = readline_list_history()[0];

	echo "\n";
	echo "Enter the abbreviation for the new project,";
	$line = readline("lowercase letters only, no spaces or other characters: ");
	readline_add_history($line);
	$abbr = readline_list_history()[0];


	$cmd = 'sudo mysql -e "USE psccore; INSERT INTO projects SET name= "' . $fullname . '", project_sitename = "' . $abbr . '";';
