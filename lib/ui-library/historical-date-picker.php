<?php


/*
		This handle all serverside logic for the historical date picker, including:

		1-prepopulating the form if GET params suggest so, as when returning to search form

				To do this, the php will generate dynamic JS that add the values


		2-on receiving the GET to pass to search, will concatenate parts to build search date.
				sanitizes (accepts only integers) and adds leading zero to day. Month is all set (pre-valued in select options)

				Empty months and years will get 00 for start dates and 99 for end dates to facilitate range searching,
				as specified in function arguments (see below)



		USAGE:

				include("historical-date-picker.php");

				//pass array of intended prefixes to use
				$myhd = new HistoricalDatePicker(array('startDate', 'endDate'));

				//in form html, do this:

				<fieldset>
						<? myHDP->build_inputs('startDate');?>
				</fieldset>

				Is this example, the inputs create are:
						<select name="startDate_month">
						<input name="startDate_day">
						<input name="startDate_year">

				//on receiving end, do this to concatenate:
				include("historical-date-picker.php";
				$myhd = new HistoricalDatePicker(array('startDate', 'endDate'));
				$startDate = $myhd->gather('startDate'); // yields string in format "YYYY-MM-DD"
					or
				$startDate = $myhd->gather('endDate',	'99', 'integer' ); // yields string in format "YYYYMMDD", with empty fields set to '99'


				TO PASS VALUE BACK TO FORM:
				either set [prefix]_y etc for each part in GET url,
				or pass [prefix] with the whole ISO or integer version, and the __constructor will figure it out.



*/


		class HistoricalDatePicker {


			function __construct($prefixArray) {

				//track incoming vars, from GET or POST.
				//first level keys are the prefixes for the sets of date inputs, such as 'start'..
				// second level is "_m" or "_d" or "_y"
				$this->incoming = array();

				$vars = array('_m', '_d', '_y');

				//sanitize the GET or POSTS expected from the provided prefixes
				foreach($prefixArray as $prefix){

					//check if just the prefix was passed as GET/POST, and split and parse
					if(isset($_GET[$prefix])) $this->parseDate($_GET[$prefix], $prefix);
					else if(isset($_POST[$prefix]))  $this->parseDate($_POST[$prefix], $prefix);

					//try to find individual parts
					else foreach($vars as $v){
						$temp = $prefix . $v;
						if(isset($_GET[$temp])) $_GET[$temp] = $this->sanitize($_GET[$temp], $prefix, $v);
						else if(isset($_POST[$temp])) $_POST[$temp] = $this->sanitize($_POST[$temp], $prefix, $v);
					}
				}
			}

			private function parseDate($date, $prefix){
				//drop the iso separater
				$date = str_replace('-', '', $date);

				if(strlen($date) == 8){
					$this->sanitize(substr($date, 0, 4), $prefix, '_y');
					$this->sanitize(substr($date, 4, 2), $prefix, '_m');
					$this->sanitize(substr($date, 6, 2), $prefix, '_d');
				} else return false;
			}


			private function sanitize($value, $prefix, $v){
				$value = preg_replace('/[^0-9]/', '', $value);

				//fix up the day, to be two digits. We don't bother to fix years, leaving that to ajax conveniences
				if($v == "_d" && strlen($value) == 1) $value = '0' . $value;

				$this->incoming[$prefix][$v] = $value;

				return $value;
			}






			// simple outputs the html for the datepicker with custom prefixed input names
			function build_inputs($prefix){


				//autogenerate value vars to set the "selected" attribute
				$checked01 = $checked02 = $checked03 = $checked04 = $checked05 = $checked06 = $checked07 = $checked08 = $checked09 = $checked10 = $checked11 = $checked12 = '';
				if(isset($this->incoming[$prefix]['_m'])) {
					$temp = 'checked' . $this->incoming[$prefix]['_m'];
					$$temp = 'selected="selected"';
				}


				//populate day
				if(isset($this->incoming[$prefix]['_d'])) {
					$temp_d = $this->incoming[$prefix]['_d'];

					//fix display of ends of range
					if($temp_d == '00' || $temp_d == '99') $temp_d = '';

					//fix leading zero
					if(strlen($temp_d) > 0 && $temp_d[0] == '0') $temp_d = substr($temp_d, 1);

				} else $temp_d = '';

				//populate year
				if(isset($this->incoming[$prefix]['_y'])) $temp_y = $this->incoming[$prefix]['_y'];
				else $temp_y = '';



$html =<<<EOT
						<fieldset class="label_below">
							<select name="{$prefix}_m">
											<option value="">---</option>
											<option value="01" {$checked01}>Jan</option>
											<option value="02" {$checked02}>Feb</option>
											<option value="03" {$checked03}>Mar</option>
											<option value="04" {$checked04}>Apr</option>
											<option value="05" {$checked05}>May</option>
											<option value="06" {$checked06}>June</option>
											<option value="07" {$checked07}>July</option>
											<option value="08" {$checked08}>Aug</option>
											<option value="09" {$checked09}>Sep</option>
											<option value="10" {$checked10}>Oct</option>
											<option value="11" {$checked11}>Nov</option>
											<option value="12" {$checked12}>Dec</option>
							</select>
							<label>month</label>
						</fieldset>

						<fieldset class="label_below">
							<input name="{$prefix}_d" value="{$temp_d}" size="1" maxlength="2" />
							<label>day</label>
						</fieldset>

						<fieldset class="label_below">
							<input name="{$prefix}_y" value="{$temp_y}" size="3" maxlength="4" />
							<label>year</label>
						</fieldset>
EOT;



			print $html;

		}//build inputs






		/*
			function to concatenate form elements into single string representing date

			arguments:
					$prefix		: input/select prefix from form page
					$empty		: '00' (default) or '99'
					$format		: 'iso' (default) or  'integer'
					$on_false	: what to return on false, defaults to ''

			returns an array, which each input prefix as a key to the output string
				or false on unformable date (no year, missing inputs).

		*/

		function gather($prefix, $empty = '00', $format = 'iso', $on_false = ''){

			//fix args
			$format = strtolower($format);

			if($empty != '00' && $empty != '99') $empty = '00';


			if(isset($this->incoming[$prefix])) {

				if($format == 'iso') $sep = '-';
				else $sep = '';

				//check for empties
				if(empty($this->incoming[$prefix]['_y'])) return $on_false;
				if(empty($this->incoming[$prefix]['_m'])) $this->incoming[$prefix]['_m'] = $empty;
				if(empty($this->incoming[$prefix]['_d'])) $this->incoming[$prefix]['_d'] = $empty;

				//check that year is four digits
				if(strlen($this->incoming[$prefix]['_y']) < 4) return $on_false;

				$out = $this->incoming[$prefix]['_y'] . $sep . $this->incoming[$prefix]['_m'] . $sep . $this->incoming[$prefix]['_d'];
			} else $out = $on_false;

			return $out;
		}



		/*
			Format the date for human display

			$format			: 'mhs' (default) is like '1 February 1780'; 'us' is 'February 1, 1780'
		*/

		function display($prefix, $format = 'mhs') {


			if(isset($this->incoming[$prefix])) {

				$months = array('01' => 'January',
								'02' => 'February',
								'03' => 'March',
								'04' => 'April',
								'05' => 'May',
								'06' => 'June',
								'07' => 'July',
								'08' => 'August',
								'09' => 'September',
								'10' => 'October',
								'11' => 'November',
								'12' => 'December',
								'00' => '',
								'99' => ''
								);


				//MONTH
				if(isset($this->incoming[$prefix]['_m']) && !empty($this->incoming[$prefix]['_m'])) {
					$month = $months[$this->incoming[$prefix]['_m']];
				} else $month = '';

				//DAY
				if(isset($this->incoming[$prefix]['_d'])) {
					$day = (strlen($this->incoming[$prefix]['_d']) > 1 && $this->incoming[$prefix]['_d'][0] == '0') ? substr($this->incoming[$prefix]['_d'], 1) : $this->incoming[$prefix]['_d'];
					if($day == '00' || $day == '99') $day = '';
				} else $day = '';

				if($format == 'mhs') $out = $day . ' '. $month . ' ' . $this->incoming[$prefix]['_y'];
				else $out = $month . ' '. $day . ', ' . $this->incoming[$prefix]['_y'];

				return $out;
			}

			else return '';
		} //display


	} //end class
