<?php
	namespace SubjectsManager\Controllers;

	class SMController {
		protected $subjectsManagerView;
        const MISSING_PARAM_ERROR = array("status"=>422, "message"=>"Missing paramater(s)");
		
		public function __construct(){
			$this->subjectsManagerView = new \SubjectsManager\Views\SubjectsManagerView();
		}

        protected function setHeaders($methods = null){
            if (null === $methods){
                $methods = 'GET, POST, PUT,  OPTIONS, DELETE';
            }

            header('Content-Type: application/json');
			//TODO: limit this to origin 
			header("Access-Control-Allow-Origin: *");
			//fix: AK PHP doesn't use these quotes: ` `
			header("Access-Control-Allow-Methods: {$methods}");
			header("Access-Control-Allow-Headers: *");
        }

        protected function enablePreflight(){
            //Enable Preflight to return success
			if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
				header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE');
				header('Access-Control-Allow-Headers: *');
				header('Access-Control-Max-Age: 86400');
				header('Content-Length: 0');
				header('Content-Type: text/plain');
				die();
			}
        }


		//decode and then sanitize each $_GET; remove uncess characters (*, ;, =, )
    }
