<?php

	namespace API\Controllers;

	class APIKeys {


		private $apiAccounts = [];


		private function getAPIAccounts(){

		}


		public function checkAPIReferer(){

			/*
				enter domains without the scheme (drop the http:// or https://)
			*/
			$allowedDomains[] = "main.d1ku7bru4acedi.amplifyapp.com/";
			$allowedDomains[] = "192.168.56.57";
			$allowedDomains[] = "masshist.org";
			$allowedDomains[] = "www.masshist.org";
			$allowedDomains[] = "primarysourcecoop.org";
			$allowedDomains[] = "www.primarysourcecoop.org";


			$headers = get_headers();

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


		public function CheckAPIToken(){

			$headers =  get_headers();

		}


//		if(!checkAPIKeys()) die("Your app does not have access.");
	}