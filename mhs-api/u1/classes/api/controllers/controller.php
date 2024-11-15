<?php


	namespace API\Controllers;



	class Controller {

		public function JSONRespond($payload = [], $other = false){
			$Aj = new \Publications\AjaxResponder2();

			// if(strpos(ALLOWED_CORS_ORIGINS, $_SERVER['REMOTE_ADDR']) !== false){
			// 	$url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['REMOTE_ADDR'];
			// 	$Aj->addHeader("Access-Control-Allow-Origin: " . $url);
			// }
			$Aj->addHeader("Access-Control-Allow-Origin: *");

			$Aj->addData($payload);
			if($other !== false) $Aj->addDataRaw($other);
			$Aj->respond();
		}


			
		protected function fail($msg){
			$AR = new \Publications\AjaxResponder2();
			$AR->statusFailed();
			$AR->addError($msg);
			$AR->respond();
			exit();
		}

	}