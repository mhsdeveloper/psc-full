<?php

	namespace MHS\TxtProcessor;




	class LineByLine extends Processor {

		private $currentSectionCloser = "";

		private $paragraphReplacementTag = false; //set to "div" or some other tag name to replace wrapping <p>'s

		private $output = "";

		private $store = [];

		private $currentStoreKey = "";

		/* adds the opener string to the output, and tracks the closer,
			to be used just before next sections starts
		*/
		public function newSection($opener, $closer, $pReplacer = false){
			$this->closeSection();

			$this->paragraphReplacementTag = $pReplacer;

			$this->currentSectionCloser = $closer;

			$this->output .= $opener;

			return $this;
		}


		/* starts tracking output and storing at $key in the store, to
			be retrieved later by getStoredText
		*/
		public function newSectionStored($key, $opener, $closer, $pReplacer = false){
			$this->closeSection();

			$this->currentStoreKey = $key;

			$this->paragraphReplacementTag = $pReplacer;

			$this->currentSectionCloser = $closer;

			if(!isset($this->store[$this->currentStoreKey])) $this->store[$this->currentStoreKey] = "";

			$this->store[$this->currentStoreKey] .= $opener;

			return $this;
		}


		public function closeSection(){
			if(empty($this->currentSectionCloser)) return;

			if(!empty($this->currentStoreKey)){
				$this->store[$this->currentStoreKey] .= $this->currentSectionCloser . "\n";
				$this->currentStoreKey = "";
			} else {
				$this->output .= $this->currentSectionCloser . "\n";
			}
			$this->currentSectionCloser = "";
		}



		public function getStored($key){
			$txt = $this->store[$key];
			$this->store[$key] = "";
			return $txt;
		}



		public function forEachLine($processFunction){

			$lineHandler = new Line();

			$lines = explode("\n", $this->text);


			$this->break = false;

			foreach($lines as $line){

				$lineHandler->text = $line;

				$processFunction($lineHandler);

				if($this->break) break;
			}
		}



		public function append($line, $keepPattern = false){

			$lineText = $line->text;

			if(!empty($line->pattern) and $keepPattern === false) {
				$pattern = "/ *" . $line->pattern . " */U";
				$lineText = preg_replace($pattern, "", $lineText);
			}

			if($this->paragraphReplacementTag !== false){
				$lineText = str_replace('<p>', '<' . $this->paragraphReplacementTag . '>', $lineText);
				$lineText = str_replace('</p>', '</' . $this->paragraphReplacementTag . '>', $lineText);
			}

			if(!empty($this->currentStoreKey)){
				$this->store[$this->currentStoreKey] .= $lineText . "\n";
			} else {
				$this->output .= $lineText . "\n";
			}
		}



		public function appendOutput($text){
			$this->output .= $text;
		}




		/* map the output back to the text, so parent funcs operate on edited version */
		public function updateText(){
			$this->text = $this->output;
		}



		//useful for retrieving Text after find/replace calls, which affect whole text at once
		public function getOutput(){
			return $this->output;
		}

	} //class
