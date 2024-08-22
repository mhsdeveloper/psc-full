<?php

	namespace Publications;


	class AjaxResponder2 {

		public $response = [
			"data" => [],
			"errors" => [],
			"messages" => [],
			"html" => ""
		];


		public function reset(){
			$this->response = [
				"data" => [],
				"errors" => [],
				"messages" => [],
				"html" => ""
			];
		}


		/* 
		 * passing arrays will merge that with the existing array;
		 * passing a string will expect the second argument to be the value, the first is the key;
		 */

		public function addData($data = false, $value = false){
			if(is_string($data)){
				$this->response['data'][$data] = $value;
				return true;
			}
			if(is_numeric($data)){
				$this->response['data'][$data] = $value;
				return true;
			}
			if(is_array($data)) {
				$this->response['data'] = array_merge($this->response['data'], $data);
				return true;
			}
			else return false;
		}

		/* works like ->data() except at the top level of the data array;
		*/

		public function addDataRaw($data = false, $value = false){
			if(is_string($data)){
				$this->response[$data] = $value;
				return true;
			}
			if(is_array($data)) {
				$this->response = array_merge($this->response, $data);
				return true;
			}
			else return false;
		}


		public function statusOK(){
			$this->setStatusOk();
		}
		public function setStatusOk(){
			$this->addDataRaw("status", "OK");
		}

		public function statusFailed(){
			$this->setStatusFailed();
		}
		public function setStatusFailed(){
			$this->addDataRaw("status", "FAILED");
		}



		/* errors()
		 * passing arrays will merge that with the existing array;
		 * passing a string will a string will add it as an array member;
		 */

		public function addError($data = false){
			if(is_string($data)){
				$this->response['errors'][] = $data;
				return true;
			}
			if(is_array($data)) {
				$this->response['errors'] = array_merge($this->response['errors'], $data);
				return true;
			}
			else return false;
		}


		/*gather errors from the global Config messenger object
		*/

		function gatherErrors($purge = false){
			$errors = \MHS\Env::getInstance()->Config->Messenger->getUserErrors();
			if(count($errors) > 0){
				if($purge) $this->Config->Messenger->purgeUserErrors();
				$this->addError($errors);
			}
		}



		/* messages()
 		 * passing arrays will merge that with the existing array;
		 * passing a string will a string will add it as an array member;
		 */


		public function addMessage($data = false){
			if(is_string($data)){
				$this->response['messages'][] = $data;
				return true;
			}
			if(is_array($data)) {
				$this->response['messages'] = array_merge($this->response['messages'], $data);
				return true;
			}
			else return false;
		}



		/* html()
		 * passing a string will concat string to end of ->html;
		 */


		public function html($html = false) {
			if(is_string($html)) {
				$this->response['html'] .= $html;
				return true;
			}
			else return false;
		}



		public function respond($die = true){
			header('Content-Type: application/json');
			print json_encode($this->response);
			if($die) die();
		}
	}
