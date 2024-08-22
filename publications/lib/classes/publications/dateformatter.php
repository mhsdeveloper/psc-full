<?php

	/*
	 *	Class for helper functions that format dates
	 *
	 */
	
	
	namespace Publications;
	
	
	
	class DateFormatter {
		
		
		
		/* Takes a format like 2000-11-26, or 20001126
		 * returns format like 12 January 2000
		 * pass true to 2nd arg to get 12 Jan. 2000
		 */
		
		static function ISOtoMHS($isodate, $abbrMonth = false){
			
			//remove dashes
			$isodate = str_replace("-", "", $isodate);
			
			$fullMonths = array(
				1 => "January",
				2 => "February",
				3 => "March",
				4 => "April",
				5 => "May",
				6 => "June",
				7 => "July",
				8 => "August",
				9 => "September",
				10 => "October",
				11 => "November",
				12 => "December",
			);
			
			$shortMonths = array(
				1 => "Jan.",
				2 => "Feb.",
				3 => "Mar.",
				4 => "Apr.",
				5 => "May",
				6 => "June",
				7 => "July",
				8 => "Aug.",
				9 => "Sep.",
				10 => "Oct.",
				11 => "Nov.",
				12 => "Dec.",
			);
			
			//grab year
			$year = substr($isodate, 0, 4);
			
			//look for month
			if(strlen($isodate) > 5) {
				
				//remember, we don't have dashes anymore
				$month = substr($isodate, 4, 2);
				//remove leading zero
				if($month[0] == "0") $month = substr($month, 1);
				//get Month equivalent
				if($abbrMonth) $monthName = $shortMonths[$month];
				else $monthName = $fullMonths[$month];
			} else $monthName = "";
			
			//look for day
			if(strlen($isodate) > 7){
				$day = substr($isodate, 6, 2);
				if($day[0] == "0") $day = substr($day, 1);
			} else $day = "";
			
			if($day == "0" or $day == "99") $day = "";
			
			//add our spacing
			if(!empty($day)) $day .= " ";
			if(!empty($monthName)) $monthName .= " ";			
			
			return $day . $monthName . $year;
		}
		
		
		
	}
	