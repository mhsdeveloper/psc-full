<?php

	namespace Wetvac\Models;


	class MarkerTool {

		private $text; //references to $text

		public $lineMatches = [
			"fullline" => "",
			"marker" => "",
			"openingP" => "",
			"content" => "",
			"closingP" => "",
		];

		private $activeMatches = [];

		/**
		 * returns an array with these parts:
		 * 
		 * 
		 */

		function parseFullLineMarker($markerName, &$text){
			$this->text = &$text;
			preg_match_all("/\s*(<p>)\s*(\{\{\s*" . $markerName . "\s*\}\})(.+)(<\/p>)\s*\n/U", $this->text, $matches);

			$parts = $this->lineMatches;
			if(isset($matches[0])) $parts['fullline'] = $matches[0];
			if(isset($matches[1])) $parts['openingP'] = $matches[1];
			if(isset($matches[2])) $parts['marker'] = $matches[2];
			if(isset($matches[1])) $parts['content'] = $matches[3];
			if(isset($matches[4])) $parts['closingP'] = $matches[4];

			$this->activeMatches = $parts;

			return $this;
		}

		function processContent($callback){
			if(is_array($this->activeMatches['content'])) foreach($this->activeMatches['content'] as $key => $value) $this->activeMatches['content'][$key] = call_user_func($callback, $value);
			else $this->activeMatches['content'] = call_user_func($callback, $this->activeMatches["content"]);
			return $this;
		}

		function paragraphsToElements($elementName){
			if(is_array($this->activeMatches['openingP'])) foreach($this->activeMatches['openingP'] as $key => $value) $this->activeMatches['openingP'][$key] = "<" . $elementName . ">";
			else $this->activeMatches['openingP'] = "<" . $elementName . ">";
			if(is_array($this->activeMatches['closingP'])) foreach($this->activeMatches['closingP'] as $key => $value) $this->activeMatches['closingP'][$key] = "</" . $elementName . ">";
			else $this->activeMatches['closingP'] = "</" . $elementName . ">";
			return $this;
		}

		function writeLine(){
			//trim all
			foreach($this->activeMatches as $key => $array){
				foreach($array as $key2 => $value) $this->activeMatches[$key][$key2] = trim($value);
			}

			//replace all
			$sets = count($this->activeMatches["fullline"]);

			for($i=0;$i<$sets;$i++){
				$replaceDISMOFO = str_replace(["\n"], "", $this->activeMatches['fullline'][$i]);
				$newline = $this->activeMatches['openingP'][$i] . $this->activeMatches['content'][$i] . $this->activeMatches['closingP'][$i];
				$this->text = str_replace($replaceDISMOFO, $newline, $this->text);
			}
		}
	}