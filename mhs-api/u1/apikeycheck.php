<?php

	function checkAPIKeys(){

//we're not really using this, but keeping incase we want to reinstate it

return true;

		/*
			enter domains without the scheme (drop the http:// or https://)
		*/
		$allowedDomains[] = "main.d1ku7bru4acedi.amplifyapp.com/";
		$allowedDomains[] = "192.168.56.57";
		$allowedDomains[] = "masshist.org";
		$allowedDomains[] = "www.masshist.org";
		$allowedDomains[] = "primarysourcecoop.org";
		$allowedDomains[] = "www.primarysourcecoop.org";


		$headers = getallheaders();

		if(!isset($headers['Referer'])) die("ERROR: no referer set");

		$referer = $headers["Referer"];

		foreach($allowedDomains as $domain){
			$test = "http://" . $domain;
			if(strpos($referer, $test) === 0) return true;

			$test = "https://" . $domain;
			if(strpos($referer, $test) === 0) return true;
		}

		return false;
	}

	if(!checkAPIKeys()) die("Your app does not have access.");