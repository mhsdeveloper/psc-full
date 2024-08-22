<?php


	namespace API\Controllers;

	class Name extends Controller {

		function index(){
		}


		public function get(){
			$husc = $this->_mvc->segment(2);

			$name = new \API\Models\NameModel();
			$result = $name->get($husc);

			if(!$result){
				$this->fail("Unable to find name for " . $husc);
			}

			$this->JSONRespond($result);
		}
	}