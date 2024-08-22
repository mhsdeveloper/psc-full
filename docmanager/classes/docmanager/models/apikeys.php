<?php 


 namespace DocManager\Models;



 class ApiKeys {

	public static function CheckKey($key){

		$path = $_SERVER["DOCUMENT_ROOT"] . PATH_ABOVE_WEBROOT . "customize/apikeys.php";
		if(!is_readable($path)) {
			return false;
		}

		include($path);
		if(!isset($APIKEYS[$key])) return false;
		return $APIKEYS[$key];
	}

 }