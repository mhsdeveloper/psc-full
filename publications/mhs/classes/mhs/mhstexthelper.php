<?php
	/*
	 *	Class with static functions for processing text, such as html,
	 *	chunks from dbs
	 *
	 */
	
	namespace MHS;
	
	Class MHSTextHelper {
	
	
	
		/* Trims a string to the nearest whole word less than the $trim number of characters
		 */
		static function word_trim($stuff, $trim = 0, $ellipse_str = " ..."){
		
			$stuff = trim($stuff);
		
			if($trim && ($trim < strlen($stuff))){
				//shorten to trim length
				$x = $trim;
				for(; $x>0; $x--){
					//keep shortening the string until reach anything like " ,!?. \t\n\r\"\':;?()[]-|";
					// maybe use strpos?
					if(strpos(" ,!?. \t\n\r\"\':;?()[]-|", $stuff[$x]) !== false) break;
				}
				if(strlen($stuff) > ($x + 1))  $suffix = $ellipse_str;
				else $suffix = '';
				
				$stuff = substr($stuff,0,$x);
				$stuff = MHSTextHelper::fix_formatting_tags($stuff);
				$stuff .= $suffix;
			} else $stuff = MHSTextHelper::fix_formatting_tags($stuff);
	
			return $stuff;
		}



		static function end_trim($stuff, $trim = 0, $ellipse_str = " ..."){
		
			$len = strlen($stuff);
		
			if($len < $trim) {
				$stuff = MHSTextHelper::fix_formatting_tags($stuff);
				return $stuff;
			}
			
			$start = $len - $trim;
			$stuff = substr($stuff, $start);
		
			//shorten to trim length
			for($x=0; $x<$trim; $x++){
				//keep shortening the string until reach anything like " ,!?. \t\n\r\"\':;?()[]-|";
				// maybe use strpos?
				if(strpos(" ,!?. \t\n\r\"\':;?()[]-|", $stuff[$x]) !== false) break;
			}
			if(strlen($stuff) > ($x + 1))  $prefix = $ellipse_str;
			else $prefix = '';
			
			$stuff = substr($stuff, $x);

			$stuff = MHSTextHelper::fix_formatting_tags($stuff);

			$stuff = $prefix . $stuff;

	
			return $stuff;
		}
	
	


		static function word_count($string){
			//turn tabs newlines into spaces
			$string = str_replace(array("\t", "\r", "\n"), "", $string );
			
			//regularize spaces
			$string = preg_replace("/\s\s+/", " ", $string);
			
			//count
			$parts = explode(" ", $string);
			
			return count($parts);
		}


	
	
		/* this function completes html formating tags that might be unclosed due to truncated fragment for a post content.
		 * Assumes only <br> <br/> <span ..> <i> <b> <strong> <u> <em> allowed
		 * 
		 */
		static function fix_formatting_tags($text){

			$text = strip_tags($text, '<br><br/><span><i><b><u><strong><em>');
		
			$tempset = explode('<' , $text);
	
			$closetags = array();
			$closetags[0] = '';
			$taglvl = 0;
			$out = $tempset[0];
	
			for($x=1; $x < count($tempset); $x++){
				
				$out .= "<";
				
				$spl = $tempset[$x];
				
				//split html from text node
				$tagVtext = explode(">", $spl);
				$tag = $tagVtext[0];
				//trim out attributes
				$tags = explode(" ", $tag);
				$tag = $tags[0];
				
				//ignore empty tags
				if($tag == "br/" || $tag == "br" || $tag == "hr") $out .= $spl;
				else {
					//dis close  tag?
					if($tag[0] == "/") {
						//matches current open tag
						if($tag == ("/" . $closetags[$taglvl])){
							$out .= $spl;
						} else {
							//ERROR: close tag is not this tag, first close current tag
							$out .= "/" . $closetags[$taglvl] . ">";
							//discarded  closer
							
							$out .= $tagVtext[1];
						}
						//back out one level
						array_pop($closetags);
						$taglvl--;
					} else {
					// this is nested tag: record	
						$taglvl++;
						$closetags[$taglvl] = $tag;
						$out .= $spl;
					}
				}
			}
	
			//close rest of tags
			if($taglvl > 0){
				for(; $taglvl > 0; $taglvl--){
					$out .= "</" . $closetags[$taglvl] . ">";
				}
			}
	
			return $out;
	
		} //fix_formatting_tags()
		
		


		/* look for matches of swear words
		 * First looks for a match in the broad list: googles-bad-words.txt
		 * second, looks at a much shorter, more certainly offensive list: googles-bad-short.txt
		 *
		 * returns array:
		 *
		 *		"flagged" => true|false,   when the broad search found a match
		 *		"serious" => true|false,   when a match found in more definitive short list
		 *		
		 * returns false if can't load the list
		 * 	
		 */
		function find_swears($text){
			
			if(!isset($this->swear_words)) {
				
				if(!$temp = file_get_contents(SERVER_WWW_ROOT . "incl/googles-bad-words.txt")) return false;
				$this->swear_words = explode("\n",$temp);
				
				//load the short list
				if(!$temp = file_get_contents(SERVER_WWW_ROOT . "incl/googles-bad-short.txt")) return false;
				$this->swear_short = explode("\n", $temp);
			} 
			
			//Initial pass: the broad search
			$result = str_ireplace($this->swear_words, "",  $text, $count);
			
			
			//anything suspicious?
			if($count){
				
				//look for definite swears in our short list
				$result = str_ireplace($this->swear_short, "", $text, $count2);
				
			} else $count2 = 0;
			
			$flagged = $serious = false;
			if($count) $flagged = true;
			if($count2) $serious = true;
			
			return array(
				"flagged" => $flagged,
				"serious" => $serious,
			);
		}

	} // class


	
	
