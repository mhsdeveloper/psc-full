<?php

	namespace Template\Controllers;


	class Helper {


		static function getWPPage($slug){

			$url = "http://" . $_SERVER['SERVER_NAME'] . "/" . \MHS\Env::PROJECT_SHORTNAME . "/" . $slug;

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, false);//true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$content = curl_exec($ch);

			$info = curl_getinfo($ch);
			curl_close($ch);

			if($info['http_code'] != 200) return "";

			return $content;
		}


	}

