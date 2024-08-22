<?php

	namespace Solr;
	

	/*
		this class concerts the Search UI's GET parameters, not the parameters that SOLR it self understands
		this class understands about the URI string, and breaking that into name value pairs
		it only concerns itself with the format within the values to deal with encoding issues (e.g. retaining the quotes)
	*/

	class SearchParameters {

		public $params = [];
		protected $paramsAreTerms = [];
		protected $lowercase = [];

		/*
			@param $name string name of the parameter in both GET url string and as key to array
			@param $default string|array the default value of the param; should be same as expected values

		*/
		function add($name, $default = ""){
			$this->params[$name] = $default;
			$this->lastAdded = $name;
			return $this;
		}

		/* treat the last added parameter as one that has general search terms,
			i.e. keywords and phrases, to properly handle quotes and other issues
			MUST BE USED IN CHAIN, or RIGHT AFTER adding the params
		*/
		function isPhraselike(){
			$count = count($this->params); //all this finds
			if($count == 0) return;	// ... the most recent param ...
			$keys = array_keys($this->params); //... and uses it's key
			$this->paramsAreTerms[] = $keys[$count - 1];
		}


		/* 
			by "Terms" we mean search keywords or phrases, as opposed to fields with controlled vocabulary
			deal with quotes in phrases
		*/
		function encodeTerms($name){
			//prep quotes
			$temp = str_replace(["“","”"], ["\"", "\""], $this->terms); //in case someone pastes in a phrase
			$temp = str_replace(['"', "'"], ['\"', '\\\''], $temp);
			return rawurlencode($temp);
		}



		function decodeTerms($name){
			$this->decodeGET($name);
			$this->params[$name] = str_replace(['\"', '"'] , ['"', '\"'] , $this->params[$name]);
		}



		function fromURLString(){
			foreach($this->params as $name => $value){
				if(in_array($name, $this->paramsAreTerms)) $this->decodeTerms($name);
				else $this->decodeGET($name);
			}
		}


		function get($name){
			return $this->params[$name];
		}


		/* returns a full URL string suitable for adding to a URL in a link
		*/
		function toURLString($noLeadingAmp = true){
			$url = "";
			foreach($this->params as $name => $value){
				if($noLeadingAmp == false || !empty($url)) $url .= "&";
				$url .= $name . "=";
				if(in_array($name, $this->paramsAreTerms)) $url .= $this->encodeTerms($name);
				else $url .= $this->encodeGET($name);
			}
			
			return $url;
		}



		function decodeGET($name){
			if(!isset($_GET[$name])) return;
			else {
				//handle multiple terms separated by semi-colons
				$temp = rawurldecode($_GET[$name]);
				$temp = trim($temp);

				//fix amp
				$temp = str_replace("&", "&amp;", $temp);
				// not replacing any characters, because of foreign diacritics
				//			$baddins = preg_replace("/[^a-zA-Z0-9\-;]/", "", $temp);
			
				if(is_array($this->params[$name])){
					if(strpos($temp, ";") !== false){
						$val = explode(";", $temp);
						//trimming here because decode is after user, but before sent to SOLR
						foreach($val as $key => $value) $val[$key] = trim($value);
					} else {
						$val = [$temp];
					}
				} else $val = $temp;
			}

			$this->params[$name] = $val;
		}



		/*
			@param string get name of the GET string parameter

		*/
		function encodeGET($name){
			if(is_array($this->params[$name])){
				$val = implode(";", $this->params[$name]);
			} else $val = $this->params[$name];

			return rawurlencode($val);
		}

	}