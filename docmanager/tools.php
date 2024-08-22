<?php

	//	command line tools
	$isCMD = php_sapi_name();
	if(strpos($isCMD, "cli")=== false) die("CMD LINE ONLY");



	function showUsage(){
		print"Please enter a command group:
			apikey [generate]
		
		";

		die();
	}


	function apiKeys($mode = "generate"){
		switch($mode){
			case "generate":
				print bin2hex(random_bytes(32));
				die();

		}
	}



	if(!isset($argv[1]) || !isset($argv[2])){
		showUsage();
	}


	if($argv[1] == "apikey") apiKeys($argv[2]);
