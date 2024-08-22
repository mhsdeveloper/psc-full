<?php

	namespace Solr;
	


	/*
		this class concerns itself with:
		storing words, phrases, and ranges along with properties like are they required ( SOLR +)
		or to be reject (SOLR -)

		does not format for SOLR (see SOLR\Query)
	*/

	class Field {

		public $name = "";
		public $terms = [];
		public $wholeFieldRequired = true;
		public $wholeFieldNegative = false;
		public $weight = "";
		public $highlighted = false;
		public $isRange = false;
		public $rangeStart = "";
		public $rangeEnd = "";

		function __construct($name){
			$this->name = $name;
		}



		function setWeight($w){
			$this->weight = $w;
			return $this;
		}


		function highlight(){
			$this->highlighted = true;
			return $this;
		}


		function addRangeStart($value){
			$this->isRange = true;
			$this->rangeStart = $value;
		}


		function addRangeEnd($value){
			$this->isRange = true;
			$this->rangeEnd = $value;
		}

		


		function addRequiredTerms($value, $delim = '\"'){
			return $this->addTerms($value, $delim, true);
		}

		function addRejectTerms($value, $delim = '\"'){
			$this->wholeFieldRequired = false;
			$this->wholeFieldNegative = true;
			return $this->addTerms($value, $delim, false);
		}




		/* supported inputs:

			\"Phrase search\" expects escaped quote delimiters
			multiple key words space separated

		*/
		function addTerms($value, $delim = '\"', $required = false, $reject = false){
			$phrases = [];
			$words = [];

			//SOLR is case insensitive
			//not true!!
//			$value = strtolower($value);

			//regularize inner spaces
			$value = preg_replace("/\s\s+/", " ", $value);

			//filter out and quoted phrases
			if($delim == '\"') $regdelim = '\\' . $delim;
			else $regdelim = $delim;
			$regex = "/" . $regdelim .  "(.+)" . $regdelim . "/U";
			preg_match_all($regex, $value, $matches);

			//add phrases and remove from original string, so that rest can be tokenized for keywords
			if(isset($matches[1])){
				foreach($matches[1] as $phrase){
					$phrases[] = $phrase;
					$value = str_replace($delim . $phrase .$delim, "", $value);
				}
			}

			//bools not supported yet
			$value = str_replace([" and ", " or "], " ", $value);

			//regularize inner spaces
			$value = preg_replace("/\s\s+/", " ", $value);
			$value = trim($value);
			$words = explode(" ", $value);

			//add phrases
			foreach($phrases as $phrase){
				$this->addTerm($phrase, $required, true, $reject);
			}

			foreach($words as $word){
				if(strlen($word)< 2) continue;
				$this->addTerm($word, $required, false, $reject);
			}
			return $this;
		}


		function fieldIsOptional(){
			$this->wholeFieldRequired = false;
			return $this;
		}


		function addTerm($value, $required = false, $isPhrase = false, $reject = false){
			if($reject) $required = false;
			$this->terms[] = ['value' => $value, 'phrase' => $isPhrase, 'required' => $required, 'reject' => $reject];

			return $this;
		}

	}