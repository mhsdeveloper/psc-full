<?php

	namespace MHS\TxtProcessor;
	
	
	class Template {
	
		//original loaded version
		private $template = "";
		
		//the working version
		private $completed = "";
		
	
		public function load($string){
		
			$this->template = $string;
			$this->completed = $string;
		}
	
	
	
		public function fillIn($marker, $with){
			
			$this->completed = str_replace("{{" . $marker . "}}", $with, $this->completed);
		}
	
		
		public function getOutput(){
			return $this->completed;
		}
		
		public function reset(){
			$this->completed = $this->template;
		}
	}