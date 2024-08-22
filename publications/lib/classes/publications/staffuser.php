<?php


namespace Publications;


class StaffUser {

	const ROLE_READ_ONLY = "contributor"; //all read only
	const ROLE_XML_EDITOR = "xml_editor"; //read only names db, but checkout docs
	const ROLE_NAMES_EDITOR = "names_editor";  //read only XML doc manager, but can edit names
	const ROLE_EDITOR = "editor"; //full access, less publishing and delete
	const ROLE_ADMIN = "administrator"; //full access
	const PSC_PROJECTS_PATH = PSC_PROJECTS_PATH;

	const BLACKLIST = [];

	//in maintenance mode, only allow whitelisted usernames
	const MAINTENANCE = false;
	const WHITELIST = ["webdev"];


	public static function currentUser(){
		if(PHP_SESSION_NONE === session_status()) session_start();
		
		$data = ["username" => false, "role" => false, "sitename" => "", "display_name" => "", "project_id" => 0];

		if(isset($_SESSION['PSC_USER'])){
			$data['username'] = $_SESSION['PSC_USER'];
		}

		if(isset($_SESSION['PSC_ROLE'])){
			$data['role'] = $_SESSION['PSC_ROLE'];
		}

		if(isset($_SESSION['PSC_NAME'])){
		    $data['display_name'] = $_SESSION['PSC_NAME'];
		}
		
		if(isset($_SESSION['PSC_SITE'])){
			$data['sitename'] = $_SESSION['PSC_SITE'];
		}
			
		if(isset($_SESSION['PSC_PROJECT_ID'])){
			$data['project_id'] = $_SESSION['PSC_PROJECT_ID'];
		}

		return $data;
	}


	public static function isLoggedin(){
		if(PHP_SESSION_NONE === session_status()) session_start();

		if(!isset($_SESSION['PSC_USER'])) return false;

		if(in_array($_SESSION['PSC_USER'], self::BLACKLIST)) return false;

		if(self::MAINTENANCE){
			if(!in_array($_SESSION['PSC_USER'], self::WHITELIST)) return false;
		}

		if(!isset($_SESSION['PSC_ROLE'])) return false;
		return true;		
	}


	public static function getProjectID(){
		if(PHP_SESSION_NONE === session_status()) session_start();
		if(!isset($_SESSION['PSC_PROJECT_ID'])) return false;
		return $_SESSION['PSC_PROJECT_ID'];		
	}

	/* returns true if the user is an editor or greater
	*/
	public static function isAtLeastEditor(){
		if(PHP_SESSION_NONE === session_status()) session_start();

		if(!isset($_SESSION['PSC_ROLE'])) return false;
		if($_SESSION['PSC_ROLE'] == self::ROLE_ADMIN || $_SESSION['PSC_ROLE'] == self::ROLE_EDITOR) return true;
		return false;
	}

	public static function isAtLeastXMLEditor(){
		if(PHP_SESSION_NONE === session_status()) session_start();
		if(!isset($_SESSION['PSC_ROLE'])) return false;
		if($_SESSION['PSC_ROLE'] == self::ROLE_ADMIN || $_SESSION['PSC_ROLE'] == self::ROLE_EDITOR || $_SESSION['PSC_ROLE'] == self::ROLE_XML_EDITOR) return true;
		return false;
	}

	public static function isAtLeastNamesEditor(){
		if(PHP_SESSION_NONE === session_status()) session_start();
		if(!isset($_SESSION['PSC_ROLE'])) return false;
		if($_SESSION['PSC_ROLE'] == self::ROLE_ADMIN || $_SESSION['PSC_ROLE'] == self::ROLE_EDITOR || $_SESSION['PSC_ROLE'] == self::ROLE_NAMES_EDITOR) return true;
		return false;
	}


	public static function isAdmin(){
		if(PHP_SESSION_NONE === session_status()) session_start();

		if(!isset($_SESSION['PSC_ROLE'])) return false;
		if($_SESSION['PSC_ROLE'] == self::ROLE_ADMIN) return true;
		return false;
	}


	public static function isSuperAdmin(){
		if(!in_array($_SESSION['PSC_USER'], self::WHITELIST)) return false;
		return true;
	}


	public static function username(){
		if(PHP_SESSION_NONE === session_status()) session_start();
		return $_SESSION['PSC_USER'];
	}

	public static function fullname(){
		if(PHP_SESSION_NONE === session_status()) session_start();
		return $_SESSION['PSC_NAME'];
	}

	public static function role(){
		if(PHP_SESSION_NONE === session_status()) session_start();
		return $_SESSION['PSC_ROLE'];
	}

	public static function level(){
		if(!isset($_SESSION['PSC_ROLE'])) return 0;
		if($_SESSION['PSC_ROLE'] == self::ROLE_READ_ONLY) return 1;
		if($_SESSION['PSC_ROLE'] == self::ROLE_NAMES_EDITOR) return 2;
		if($_SESSION['PSC_ROLE'] == self::ROLE_XML_EDITOR) return 2;
		if($_SESSION['PSC_ROLE'] == self::ROLE_EDITOR) return 3;
		if($_SESSION['PSC_ROLE'] == self::ROLE_ADMIN) return 4;
		return 0;
	}


	public static function getProjectEnvFile(){
		if(PHP_SESSION_NONE === session_status()) session_start();
		if(!isset($_SESSION['PSC_SITE'])) return false;
		if(empty($_SESSION['PSC_SITE'])) return false;
		return self::PSC_PROJECTS_PATH . $_SESSION['PSC_SITE'] . "/environment.php";
	}

}