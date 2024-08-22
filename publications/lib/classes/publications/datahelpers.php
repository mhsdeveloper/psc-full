<?php 

	namespace Publications;

	class DataHelpers {

		/* convert dates to a sortable integer format that allows for CE/BCE
			assumes 4 digits or less to be just a year.
			also accepts iso dates: YYYY-MM or YYYY-MM-DD
		*/
		static function DateToSortInt($date, $bce = false){
			//convert any incomplete iso-style date to 8 digits
			if(strpos($date, "-") !== false){
				//missing day-of-month
				if(strlen($date) == 7) $date .= "-00";
				$date = str_replace("-", "", $date);
			}
		
			//fix pre year 1000 dates
			while(strlen($date) < 4) $date = "0" . $date;
		
			//fix year only dates
			while(strlen($date) < 8) $date .= "0";
		
			if(!$bce) {
				//add era
				return "CE" . $date;
			}
		
			//now invert the year for BCE dates
			$year = substr($date, 0, 4);
			$year = 10000 - intval($year);
			$date = $year . substr($date, 4, 4);
			return "BC" . $date;
		}
		


		
		/* create a sort string from all the name components:
			last-first-maiden-middle-variants-suffix
		*/
		static function SortStringFromName($name){
			if(is_array($name)){
				$out = $name;
			} else {
				$out["family_name"] = trim($name->family_name);
				$out["given_name"] = trim($name->given_name);
				$out["middle_name"] = trim($name->middle_name);
				$out["birth_name"] = trim($name->birth_name);
				$out["variants"] = trim($name->variants);
				$out["suffix"] = trim($name->suffix);
			}

			if(!isset($out['family_name'])) $out['family_name'] = "";
			if(!isset($out['given_name'])) $out['given_name'] = "";
			if(!isset($out['middle_name'])) $out['middle_name'] = "";
			if(!isset($out['birth_name'])) $out['birth_name'] = "";
			if(!isset($out['variants'])) $out['variants'] = "";
			if(!isset($out['suffix'])) $out['suffix'] = "";

			

			//force family name only names to sort at end
			if(empty($out['given_name']) && empty($out['variants']) && empty($out['middle_name']) && empty($out['birth_name'])){
				$out['family_name'] = "ZZ-" . $out['family_name'];
			}
			//empty family also sort at end
			if(empty($out['family_name'])) $out['family_name'] = "ZZZZ-";

			$sort = $out['family_name'] . $out['given_name'] . $out['birth_name'] . $out['middle_name'] . $out['variants'] . $out['suffix'];

			return $sort;
		}

	}
