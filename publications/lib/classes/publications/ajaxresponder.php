<?php

	namespace Publications;


	class AjaxResponder {

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




		/* data() is a setter/getters:
		 * passing arrays will merge that with the existing array;
		 * passing a string will expect the second argument to be the value, the first is the key;
		 * passing nothing will return the whole array
		 */

		public function data($data = false, $value = false){

			if(false == $data) return $this->response["data"];

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

		public function dataRaw($data = false, $value = false){

			if(false == $data) return $this->response;

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



		public function statusOk(){
			$this->dataRaw("status", "OK");
		}

		public function statusFailed(){
			$this->dataRaw("status", "FAILED");
		}



		/* errors()
		 * passing arrays will merge that with the existing array;
		 * passing a string will a string will add it as an array member;
		 * passing nothing will return the whole array
		 */

		public function errors($data = false){

			if(false == $data) return $this->response["errors"];

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
				$this->errors($errors);
			}
		}



		/* messages()
 		 * passing arrays will merge that with the existing array;
		 * passing a string will a string will add it as an array member;
		 * passing nothing will return the whole array
		 */


		public function messages($data = false){

			if(false == $data) return $this->response["messages"];

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
		 * passing nothing will return the whole string
		 */


		public function html($html = false) {
			if(false == $html) return $this->response["html"];

			if(is_string($html)) {
				$this->response['html'] .= $html;
				return true;
			}

			else return false;
		}



		public function respond(){

			header('Content-Type: application/json');
			print json_encode($this->response);
		}
	}
