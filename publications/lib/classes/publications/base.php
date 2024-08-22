<?php

	namespace Publications;
	
	
	class Base {
		
		
		protected $errors;
		
		
		function add_error($error){
		    $this->errors[] = $error;
		}
		
		
		
		function fail(){
			$errors = implode("<br/>", $this->errors);
			$data['errors'] = $errors;
			$this->_mvc->render(\MHS\Env::ERROR_VIEW, $data);
			exit();
		}

	}