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
		 * passing a string will add it as an array member;
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



		public function respond($code = "200"){
			header('Content-Type: application/json');
			switch($code){
				case 200: header("HTTP/1.0 200 OK"); break; 
				case 400: header("HTTP/1.0 400 Bad Request"); break;
				case 401: header("HTTP/1.0 401 Unauthorized"); break;
				case 404: header("HTTP/1.0 404 Not Found"); break;
				default: header("HTTP/1.0 500 Internal Server Error");
			}
			print json_encode($this->response);
		}
	}
