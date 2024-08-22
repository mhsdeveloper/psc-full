<?php


	namespace DocManager\Controllers;



	class AjaxController {
		function __construct(){
			$this->AR = new \Publications\AjaxResponder();
		}


		
		protected function fail($msg, $code = "200"){
			$this->AR->statusFailed();
			$this->AR->errors($msg);
			$this->AR->respond($code);
			exit();
		}

	}