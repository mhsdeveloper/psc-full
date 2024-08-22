<?php

	namespace MHS\TxtProcessor;




	class Line {

		public $text = "";

		//the search string or pattern
		public $pattern = "";

		
		const KEEP_PATTERN = true;


		public function begins($string){

			if(strpos($this->text, $string) === 0) {
				//keep the pattern for subsequence processes
				$this->pattern = $string;
				return true;
			}

			$this->pattern = "";
			return false;
		}




		public function contains($string){
			if(strpos($this->text, $string) !== false) {
				//keep the pattern for subsequence processes
				$this->pattern = $string;
				return true;
			}

			$this->pattern = "";
			return false;
		}




		public function substr($start, $length = false){

			if(is_numeric($length)) return substr($this->text, $start, $length);
			else return substr($this->text, $start);
		}




		//processing tools

		//remove first occurence of pattern and preceding text
		public function trimLeading(){

			if(empty($this->pattern)) return $this;

			$temp = explode($this->pattern, $this->text);

			//remove first occurence
			if(count($temp) > 1){
				$temp = array_slice($temp, 1);

				//incase there are more parts ( when pattern occured more than once)
				$temp = implode($this->pattern, $temp);
				$this->text = $temp;
			}
			return $this;
		}

		
		
		public function trimTrailingP(){
			if(strpos($this->text, "</p>") !== false) {
				$this->text = str_replace("</p>", "", $this->text);
			}
			return $this;
		}
		
		
		
		public function getText(){
			return trim($this->text);
		}

	} //class
