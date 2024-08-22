<?php

	namespace MHS;

	class Needle {

		const DRIVER = "mysql";
		const CHARSET = "utf8";

		private $dbc;
		private $statement;
		private $isInsert = false;
		private $parameters = [];
		private $markers = [];
		private $errors = [];
		private $lastid = 0;



		function __construct($database, $user, $password, $host = "localhost"){
			$pdoStart = self::DRIVER . ":host=" . $host . ";dbname=" . $database . ";charset=" . self::CHARSET;
		    $this->dbc = new \PDO($pdoStart, $user, $password);
		}


		public function prepQuery($query){
			if(stripos($query, "insert ") !== false ){
				$this->isInsert = true;
			}
			$this->statement = $this->dbc->prepare($query);

			return $this->gleenMarkers($query);
		}


		public function gleenMarkers($query){
			preg_match_all("/(:[\d\w]*)/", $query, $matches);
			if(isset($matches[0]) and count($matches[0])){
				foreach($matches[0] as $marker){
					$this->markers[$marker] = "";
				}
			}
			return true;
		}

		/* Auto builds the insert statement assuming that the parameter names are just
		 the column names with leading :
		 */
		public function buildInsertQuery($table){
			$query = "INSERT INTO " . $table . " SET ";
			$params = [];
			foreach($this->parameters as $marker => $value){
				$params[] = substr($marker, 1) . " = " . $marker; 
			}
			$query .= implode(", ", $params);

			return $query;
		}


		public function setParam($sqlMarker, $param){
			if($sqlMarker[0] != ":") $sqlMarker = ":" . $sqlMarker;
			$this->parameters[$sqlMarker] = $param;
			return true;
		}


		/*
			queryParameterSets

			Run multiple queries for a single prepared statement;

			Takes an array of subarrays, running the prepared statement (prepQuery()) on each subarray.
			It assumes each member of the subarray to be the params for the query
			in the order used in the prepared statement, or in a consistent order matching
			the optional $markers array.

			@param rows 
					this is expected to be a uniform set of subarrays, with each
					member being the next column to be 

			@param markers
					a simple array of markers that match those in the prepared statement

		public function queryParameterSets($sets, $markers = false){
			foreach($sets as $set){
				

			}
		}
		*/


		public function getQueryString(){
			return $this->statement->queryString;
		}


		public function runQuery($parameters = false){
			if(!$parameters) $parameters = $this->parameters;

			if(!$this->statement->execute($parameters)) {
				throw new \Exception($this->statement->errorInfo()[2]); //sending just the message
				return false;
			} else {
				if($this->isInsert) $this->lastid = $this->dbc->lastInsertId();
				else $this->lastid = 0;
				return true;
			}
		}

		public function getRow(){
			return $this->statement->fetch(\PDO::FETCH_ASSOC);
		}

		public function getAllRows(){
			return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
		}

		public function getLastInsertId(){
			return $this->lastid;
		}


		public function getErrors(){
			return $this->errors;
		}
	}