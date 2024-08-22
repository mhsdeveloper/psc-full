<?php

	namespace App\Http\Middleware;


	class QueryParams {

		/**
		 * Process REST QueryString params and values
		 * 
		 */




		/**
		 * looks for operators embeded in Query string parameter values, e.g.:
		 * 		?student_id=gte:3   returns ">="
		 * 
		 * returns default of "=" for use in  eloquent ->where($field, "=", $value) calls
		 * 
		 */
		 static function getOperator($value){
			if(strpos($value, ":") === false) return "=";

			$parts = explode(":", $value);

			switch($parts[0]){
				case "gte": return ">="; // param=gte:value
				case "gt" : return ">"; // param=gt:value 
				case "lte" : return "<="; //param=lte:value
				case "lt" : return "<";  // param=lt:value
				case "~" : return "like"; // param=~:value
				default: return "=";   //param=value
			}
		}


		/**
		 * remove any operator syntax from query string values
		 * 	e.g. lte:4  returns 4
		 * 
		 */
		static function cleanValue($value){
			if(strpos($value, ":") === false) return $value;
			return explode(":", $value)[1];
		}


	}