<?php

	namespace MHS\TxtProcessor;

	/* this is the base class for most basic text processing
	*/


	class Processor {

		protected $text = '';

		//plain string to find
		protected $string;

		//regex pattern to find
		protected $pattern;

		//modifiers for our regex use
		protected $modifiers;

		//output from pregs
		protected $matches;




		public function setText($text){
			$this->text = $text;
			$this->text = Helper::regularizedLineEndings($this->text);

		}




		public function hasString($string){
			if(strpos($this->text, $string) !== false) return true;
			else return false;			
		}
		
		

		/* sets the string to use for subsequent replace functions,
		 * using chained-style method calls
		 */
		public function findString($string){
			$this->string = $string;

			//clear pattern since we're using string
			$this->pattern = "";

			return $this;
		}
		
		


		/* Don't add delimiters: we'll add /, and we'll
		 * escape any / that you need in your pattern
		 */
		public function findRegex($pattern, $modifiers = ""){


			//unescape any escaped slashes
			$pattern = str_replace('\/', '/', $pattern);

			//escape any slashes
			$this->pattern = str_replace('/', '\/', $pattern);

			$this->modifiers = $modifiers;

			//clear string since we're using pattern
			$this->string = "";

			return $this;
		}




		public function replaceWith($string = ""){

			if(!empty($this->string)) {
				$this->text = str_replace($this->string, $string, $this->text);
			}

			else if(!empty($this->pattern)){
				$regex = "/" . $this->pattern . "/U" . $this->modifiers;

				$this->text = preg_replace($regex, $string, $this->text);
			}

			return $this;
		}
		
		
		
		
		
		/* callback function is passed the index (zero-based) of each
		 * occurence of $stringMatch in turn. Callback should return a string
		 * which will take the place of that instance of the $stringMatch.
		 * 
		 * Useful for finding all instances of a tag and assigning incremental id, for example.
		 */
		public function replaceEach($stringMatch, $callback){
			
			$fragments = explode($stringMatch, $this->text);
			
			$output = "";
			
			$count = count($fragments);
			
			for($i=0; $i < $count; $i++){
				$output .= $fragments[$i];
				
				//skip last fragment, because string was before it
				if($i == $count - 1) break;
				
				$output .= $callback($i);
			}
			
			$this->text = $output;
		}

		
		

		/* ___Paragraph() functions

		 * find TEI paragraphs with the pattern in them.
		 * These functions use the string or a regex from ->findString() and ->findRegex()
		 * For regex, it assume that parenthetical patterns are not used,
		 * that is, it keeps ( in $this->matches) just the first member of the match sub array
		 * from the preg_match_all
		 */

		public function inParagraphs() {

			$regex = $this->prepParaRegex();

			preg_match_all($regex, $this->text, $matches);

			//map to simplier flat array
			$this->matches = [];
			foreach($matches[0] as $match) $this->matches[] = $match;

			return $this;
		}


		/* remove the paragraph(s) that matched  from inPragraphs()
		 * 
		 */
		public function removeParagraphs(){
			foreach($this->matches as $string) {
				$this->text = str_replace($string, "", $this->text);
			}
			
			return $this;
		}
		
		
		/* remove the pattern or string from text 

		 * if removes surrounding whitespace from plain strings, also, unless false passed.
		 * 		 */
		public function remove($surroundingWhitespace = true){
			
			if(!empty($this->string)){
				if($surroundingWhitespace){
					$pattern = "/ *" . $this->string . " */";
					$this->text = preg_replace($pattern, "", $this->text);		
				} else {
					$this->text = str_replace($this->string, "", $this->text);
				}
			}
			
			else if(!empty($this->pattern)){
				$this->text = preg_replace($this->pattern, "", $this->text);
			}

			return $this;
		}

		
		
		public function unwrap(){
			
			foreach($this->matches as $string) {
				
				//modify string to remove <p>s
				$replacement = preg_replace("/<p.+>/U", "", $string);
				$replacement = str_replace("</p>", "", $replacement);
				
				$this->text = str_replace($string, $replacement, $this->text);
			}
			
			return $this;
		}



		//wraps, but keeps <p> tags
		public function wrapWith($tagname){
			
			foreach($this->matches as $string) {
				
				$tagStart = "<" . $tagname . '><p>';
				$tagEnd = "</p></" . $tagname . '>';
				
				//modify string to remove <p>s
				$replacement = preg_replace("/<p.*>/U", $tagStart, $string);
				$replacement = str_replace("</p>", $tagEnd, $replacement);

				$this->text = str_replace($string, $replacement, $this->text);
			}
			
			return $this;
		}
		
		
		//replaces <p> tags with tagname
		public function rewrapWith($tagname){
			
			foreach($this->matches as $string) {
				
				$tagStart = "<" . $tagname . ">";
				$tagEnd = "</" . $tagname . ">";
				
				//modify string to remove <p>s
				$replacement = preg_replace("/<p.*>/U", $tagStart, $string);
				$replacement = str_replace("</p>", $tagEnd, $replacement);

				$this->text = str_replace($string, $replacement, $this->text);
			}
			
			return $this;
		}
		



		public function getMatches(){
			return $this->matches;
		}

		
		
		
		public function getText(){
			return $this->text;
		}
		
		
		
		private function prepParaRegex(){
			//strings needs to be first made into regex
			if(!empty($this->string)) {
				//escape regex chars
				$pattern = preg_quote($this->string, '/');
			} else $pattern = $this->pattern;

			$regex = '/<p.*>.*' . $pattern . '.*<\/p>/U' . $this->modifiers;

			return $regex;
		}



	} //class
