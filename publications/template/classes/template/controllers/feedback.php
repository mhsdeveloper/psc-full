<?php

	namespace Template\Controllers;


	class Feedback {

		function save(){

			$model = new \Template\Models\Feedback();

			$note = $_POST['note'];
			$url = $_POST['url'];

			if(!$model->feedme($note, $url)){

			}

		}


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