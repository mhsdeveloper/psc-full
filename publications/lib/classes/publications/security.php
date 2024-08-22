<?php



	/* Singleton to handle scurity. Intended to be extended to connect with what
	 * ever 3rd party tool handles logins
	 *
	 */



	namespace Publications;
	
	
	
	class Security {
	
		static private $instance;
	
	
		private function __construct(){
die("use staffuser.php instead");
		}
		
		
		
		public function loggedIn(){
			
			session_start(); //override ims_header

			if(isset($_SESSION['sessref'])) return true;

			else return false;
		}
		
		
		
	
		static public function getInstance(){
			if(self::$instance == NULL){
				self::$instance = new Security();
			}
			
			return self::$instance;
		}
	
	}
