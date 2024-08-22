<?php


	namespace API\Controllers;



	class Controller {

		public function JSONRespond($payload = [], $other = false){
			$Aj = new \Publications\AjaxResponder2();
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