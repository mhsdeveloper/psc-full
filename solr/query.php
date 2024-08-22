<?php

	namespace Solr;
	

	/*
		format/build the whole URI including query string that SOLR understands;

		q= +author:(+Adams, +Louisa +Catherine) +recipient:(+Adams, +Abigail) +date_when:[16000202 TO 17000722] +index:apde^1000 +series:EJA

		q= +author:(+Adams, +Louisa +Catherine) +recipient:(+Adams, +Abigail) +person_keyword:Adams,_Abigail +index:apde^1000 +series:EJA

		+author:(+HUSC)
		+person_keyword:(+HUSC +HUSC +HUSC)

		+text:(+john +cotton +vocabulary)^1000

		PHRASE
		+text:\"john cotton vocabulary\"^1000


		&q= +author:(+Adams, +Louisa +Catherine) +index:apde^1000


		http://localhost:8983/solr/collection1/select/?wt=php

		&fl=id,text,author,recipient,person_keyword,date_when,date_to,teaser,resource_uri

		&hl=true
		&hl.maxAnalyzedChars=500000
		&hl.requireFieldMatch=true
		&hl.fl=text,notes
		&hl.fragsize=100

		&facet=true
		&&facet.field=authorStr
		&facet.field=recipientStr

		&sort=date_when asc,date_to asc
		&start=0
		&rows=10"



		$field = $Query->addField("text");
		$field->addTerm("george");
		$field->addRequiredTerms($terms array);


	*/

	class Query {
		
		protected $fields = [];

		protected $phraseDelim = '"';

		public $fieldList = [];

		protected $hlFields = [];
		protected $useHighlighting = true;
		protected $hlRequireFieldMatch = true;
		protected $hlFragSize = 100;
		protected $hlMaxAnalyzedChars = 50000;

		protected $start = 0;
		protected $rows = 25;
		protected $sortField = "";
		protected $sortDir = "asc";
		protected $groupingField = "";
		protected $groupingCount = "";

		protected $queryString = "";

		protected $facet = false;
		protected $facetFields = [];


		function addSearchField($name){
			$field = new Field($name);
			$this->fields[] = $field;
//			$this->fieldList[] = $name;
			return $field;
		}


		function addRangeField($name, $start, $end){
			$field = new Field($name);
			$field->addRangeStart($start);
			$field->addRangeEnd($end);
			$this->fields[] = $field;
//			$this->fieldList[] = $name;
			return $field;
		}

		function addIntegerDateRange($name, $start, $end){
			$start = preg_replace("/[^0-9]/", "", $start);
			while(strlen($start) < 8) $start .= "0";

			$end = preg_replace("/[^0-9]/", "", $end);
			while(strlen($end) < 8) $end .= "9";

			$this->addRangeField($name, $start, $end);
		}


		function addReturnField($name){
			if(!in_array($name, $this->fieldList)) $this->fieldList[] = $name;
		}


		function addFacetField($field){
			$this->facet = true;
			$this->facetFields[] = $field;
		}


		/* either the GET param or an integer to directly set the row count */
		function setRows($paramName){
			if(is_numeric($paramName)) {
				$this->rows= $paramName;
				return;
			}
			if(!isset($_GET[$paramName])) return;
			$num = $_GET[$paramName];
			$this->rows = preg_replace("/[^0-9]/", "", $num);	
		}

		function setStart($paramName){
			if(!isset($_GET[$paramName])) return;
			$start = $_GET[$paramName];
			$this->start = preg_replace("/[^0-9]/", "", $start);	
		}

		function build(){
			$str = "";
			
			foreach($this->fields as $field){
				if($field->isRange){
					$str .= " " . $this->buildRangeField($field);
				}

				else {
					$str .= " " . $this->buildTermsField($field);
				}
			}

			//fix empty queries
			$query = trim(preg_replace("/\s\s+/", " ", $str));
			if(empty($query)) $query = "id:*";
			$str = "q=" . rawurlencode($query);


			//rest of the setup
			if($this->useHighlighting){
				$str .= "&hl=true";
				$str .= "&hl.maxAnalyzedChars=" . $this->hlMaxAnalyzedChars;
				$str .= "&hl.fragsize=" . $this->hlFragSize;
				$str .= "&hl.requireFieldMatch=true";
				$str .= "&hl.fl=" . implode(",", $this->hlFields);
			}

			if(!empty($this->sortField)){
				$str .= "&sort=" . $this->sortField . "%20" . $this->sortDir;
			}

			//fields to show
			$str .= "&fl=" .implode(",", $this->fieldList);

			//facets?
			if($this->facet){
				//f.<fieldname>.facet.<parameter>
				$str.= "&facet=true&facet.mincount=1";

				foreach($this->facetFields as $field){
					$str .= "&facet.field=" . $field;
				}
			}

			//pagination
			$str .= "&start=" . $this->start;
			$str .= "&rows=" . $this->rows;

			if(!empty($this->groupingField)){
				$str .= "&group=true&group.field=" . $this->groupingField;
				if(!empty($this->groupingCount)) $str .= "&group.limit=" . $this->groupingCount;
			}

			$this->queryString = $str;
		}

		


		function buildTermsField($field){
			$fieldStr = "";
			if(empty($field->terms)) return $fieldStr;

			foreach($field->terms as $term){
				//if(empty($term['value'])) continue;
				$fieldStr .= " ";
				if($term['required']) $fieldStr .= "+";
				else if($term['reject']) $fieldStr .= "-";
				if($term['phrase']) $fieldStr .= $this->phraseDelim;
				$fieldStr .= $term['value'];
				if($term['phrase']) $fieldStr .= $this->phraseDelim;
			}
			$fieldStr = $field->name .":(" . $fieldStr . ")";
			if($field->wholeFieldNegative) $fieldStr = "-" . $fieldStr;
			else if($field->wholeFieldRequired) $fieldStr = "+" . $fieldStr;
			
			if(!empty($field->weight)) $fieldStr .= "^" . $field->weight;
			if($field->highlighted) $this->hlFields[] = $field->name;

			return $fieldStr;
		}


		function buildRangeField($field){
			$fieldStr = $field->name .":[" . $field->rangeStart  . " TO " . $field->rangeEnd . "]";
			if($field->wholeFieldNegative) $fieldStr = "-" . $fieldStr;
			else if($field->wholeFieldRequired) $fieldStr = "+" . $fieldStr;
			if(!empty($field->weight)) $fieldStr .= "^" . $field->weight;
			if($field->highlighted) $this->hlFields[] = $field->name;
			return $fieldStr;
		}


		function setSortField($name, $dir = "asc"){
			$this->sortField = $name;
			if($dir != "asc") $this->sortDir = "desc";
			else $this->sortDir = "asc";
		}


		function setGroupingField($field){
			$this->groupingField = $field;
		}


		function setGroupingCount($count){
			$this->groupingCount = $count;
		}


		function getQueryString(){
			return $this->queryString;
		}
	}