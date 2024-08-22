<?php


	namespace MHS\TxtProcessor;
	
	
	
	
	class Helper {
	

		

		static function regularizedLineEndings($text){
			
			return str_replace("\r\n", "\n", $text);
			
		}



		static function cleanUp($text, $keepLineBreaks = true, $preserveWhitespace = true){
			
			if(!$preserveWhitespace){
				//cleanup whitespace
				if($keepLineBreaks) $pattern  = '/[\t ]+/';
				else $pattern = '/\s\s+/';
				
				$text = preg_replace($pattern, ' ', $text);
			}
			
			//fix stray &
			$text = str_replace("&", "&amp;", $text);
			
			return $text;
		}


	

	
	}