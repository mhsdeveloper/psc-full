<?php

	namespace MHS\Helpers;


	/* a class for conversions between roman numerals etc
	*/


	class RomanNumerals {


		/* convert a number under 3000
		*/
		static public function fromDecimal($dec){

			$dec = (string)$dec;
			$count = strlen($dec);
			$roman = "";

			//any thousands?
			if($count == 4){
				$t = (int)$dec[0];
				for($x=0;$x<$t;$x++) $roman .= "m";
			}

			//hundreds
			if($count > 2){
				if($count == 4) $t = $dec[1];
				else $t = $dec[0];

				switch($t){
					case "0": break;
					case "1": $roman .= "c"; break;
					case "2": $roman .= "cc"; break;
					case "3": $roman .= "ccc"; break;
					case "4": $roman .= "cd"; break;
					case "5": $roman .= "d"; break;
					case "6": $roman .= "dc"; break;
					case "7": $roman .= "dc"; break;
					case "8": $roman .= "dcc"; break;
					case "9": $roman .= "cm"; break;
				}
			}

			//tens
			if($count > 1){
				if($count == 4) $t = $dec[2];
				else if($count == 3) $t = $dec[1];
				else $t = $dec[0];

				switch($t){
					case "0": break;
					case "1": $roman .= "x"; break;
					case "2": $roman .= "xx"; break;
					case "3": $roman .= "xxx"; break;
					case "4": $roman .= "xl"; break;
					case "5": $roman .= "l"; break;
					case "6": $roman .= "lx"; break;
					case "7": $roman .= "lxx"; break;
					case "8": $roman .= "lxxx"; break;
					case "9": $roman .= "xc"; break;
				}
			}

			//tens
			if($count > 0){
				if($count == 4) $t = $dec[3];
				else if($count == 3) $t = $dec[2];
				else if($count == 2) $t = $dec[1];
				else $t = $dec[0];

				switch($t){
					case "0": break;
					case "1": $roman .= "i"; break;
					case "2": $roman .= "ii"; break;
					case "3": $roman .= "iii"; break;
					case "4": $roman .= "iv"; break;
					case "5": $roman .= "v"; break;
					case "6": $roman .= "vi"; break;
					case "7": $roman .= "vii"; break;
					case "8": $roman .= "viii"; break;
					case "9": $roman .= "ix"; break;
				}
			}

			return $roman;
		}


	} //class
