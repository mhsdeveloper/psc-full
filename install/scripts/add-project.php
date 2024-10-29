<?php

	if(!isset($argv)){
		die("This script must be run from the command line");
	}

	echo "\n";
	echo "This tool will create the file structure for a new coop project/edition.";
	echo "\n";


	include(SERVER_WWW_ROOT . "/environment.php");
	$dirs = [];
	$scan = scandir(PSC_PROJECTS_PATH);
	foreach($scan as $file){
		if(is_dir(PSC_PROJECTS_PATH . "/".  $file) && $file != "." && $file != ".."){
			$dirs[] = $file;
		}
	}


	echo "\n";
	$fullname = readline("Enter the full project/edition name: ");

	echo "\n";
	echo "Choose an abbreviation for the new project. This will also be the subpath of the project's URL, for example:\n";
	echo "a project with the abbreviation 'gma' will have a home page at 'www.mydomain.org/gma'\n";
	echo "Use 3-6 lowercase letters only, no spaces or other characters.\n";
	echo "The abbreviation must be unique. Currently the following are in use:\n\n";
	echo implode(" | ", $dirs);
	echo "\n\n";



	for($i = 0; $i<10; $i++){

		$abbr = readline("Enter abbreviation: ");

		if(!preg_match("/^[a-z]{3,6}$/", $abbr)){
			echo "\nThat abbreviation is not valid.\n";
			continue;
		}

		if(in_array($abbr, $dirs)){
			echo "\nThat abbreviation is already in use.\n";
			continue;
		}

		if($i == 9){
			echo "\nToo many attempts. Please run the script again after getting help for this step from the MHS.\n";
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


	$destPath = PSC_PROJECTS_PATH . "/" . $abbr;

	mkdir($destPath);
	mkdir($destPath . "/customize");
	mkdir($destPath . "/images");
	mkdir($destPath . "/includes");
	mkdir($destPath . "/solrtemp");
	mkdir($destPath . "/support-files");
	mkdir($destPath . "/xml");

	echo "\nCreating individual project environment file...\n";

	$env = file_get_contents(SERVER_WWW_ROOT . "html/install/projects/newproject/environment.php");
	$env = str_replace("[[FULL-NAME-HERE]]", $fullname, $env);
	$env = str_replace("[[SHORT-NAME-HERE]]", $abbr, $env);
	file_put_contents($destPath . "/environment.php", $env);
	
	copy(SERVER_WWW_ROOT . "html/projects/coop/customize/custom.js", $destPath . "/customize/custom.js");
	copy(SERVER_WWW_ROOT . "html/projects/coop/customize/metadata.json", $destPath . "/customize/metadata.json");
	copy(SERVER_WWW_ROOT . "html/projects/coop/customize/name-metadata-template.html", $destPath . "/customize/name-metadata-template.html");

	echo "\n\nCreated new folder and files for project at: $destPath\n\n";


	//load projects.json
	$json = json_decode(file_get_contents(SERVER_WWW_ROOT . "projects.json"), true);

	$json[$insertID] = ["name" => $fullname, "abbr" => $abbr, "docListShowDate" => 1];
	$json['nameToID'][$abbr] = $insertID;

	$text = json_encode($json);
	$text = str_replace(":{", ":\n\t{", $text);
	$text = str_replace(":\"", ": \"", $text);
	$text = str_replace("}", "}\n", $text);
	file_put_contents(SERVER_WWW_ROOT . "projects.json", $text);