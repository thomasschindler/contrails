<?
/**
*	
*class for timehandling
*
*used for persistent date - and time display
*
*	following formatting can be used:
*	year:
*	Y YYYY
*	y YY
*	
*	month:
*	n 1-12
*	m 01-12
*	M Jan - Dec
*	F January - December
*	
*	day:
*	l Monday - Sunday
*	j 1 -31
*	d 01 - 31
*	D Mon - Sun
*	
*	hour:
*	g 1 - 12
*	G 0 - 23
*	h 01 - 12
*	H 00 - 23
*	
*	minute:
*	i 00-59
*	
*	second:
*	s 00- 59
*	
*	not yet implemented: !!!!
*	a am - pm
*	A AM - PM
*	S st, nd, rd or th	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		util
*/
class oos_datetime {
		/**
		*	constructor
		*/
		function oos_datetime(){
			/**
			* what a date needs dont change !!
			*/
			$this->date_full = array('_d', '_m', '_y');
			/**
			* what time needs dont change !!
			*/
			$this->time_full = array('_h', '_i');
			/**
			*	date scheme
			*	translates parts of the time computation into the output modes
			*/
			$this->date_map = array(
				'_d' => 'd',
				'_m' => 'm',
				'_y' => 'Y'
			);
			/**
			*	order of date
			*/
			$this->date_order = array(
				'_d','_m','_y'
			);
			/**
			*	seperator for date
			*/
			$this->date_sep = ".";
			/**
			*	date scheme
			*	translates parts of the time computation into the output modes
			*/
			$this->time_map = array(
				'_h' => 'H',
				'_i' => 'i',
				);
			/**
			*	order of date
			*/
			$this->time_order = array(
				'_h', '_i'
			);
			/**
			*	seperator for time
			*/
			$this->time_sep = ":";
			/**
			*	separator between date and time
			*	has to be different to all other separators
			*/
			$this->time_date_sep = " ";
		}
		/**
		*	singleton
		*/
		function &singleton() {
			static $instance;
			
			if (is_object($instance)) {
				return $instance;
			}
			
			return  new oos_datetime();
		}
		/**
		*	transform a given date into a timestamp
		*	date has to conform to the date definition ( see constructor )
		*/
		function date2stamp($date,$time = false){
			/*if($time){
				$parts = explode($this->time_date_sep,$date);
				if(sizeof($parts) != 2){
					return -1;
				}
				$date = $parts[0];
				$time = $parts[1];
			}*/
			$date = trim($date);
			$time = trim($time);
			if(!isset($date)){
				return -1;
			}
			$date_parts = explode($this->date_sep,$date);
			if(sizeof($date_parts) != sizeof($this->date_order)){
				return -1;
			}
			for($i=0;$i<sizeof($this->date_order);$i++){
				${$this->date_order[$i]} = $date_parts[$i];
			}
			foreach($this->date_full as $item){
				if(!isset(${$item}) OR ${$item} == 00 OR (strlen(${$item} == 2 AND substr(${$item},0,1) == 0))){
					${$item} = 0;
				}
			}
			if($time != false){
				$time_parts = explode($this->time_sep,$time);
				if(sizeof($time_parts) != sizeof($this->time_order)){
					return -1;
				}
				for($i=0;$i<sizeof($this->time_order);$i++){
					${$this->time_order[$i]} = $time_parts[$i];
				}
			}
			foreach($this->time_full as $item){
				if(!isset(${$item}) OR ${$item} == 00 OR (strlen(${$item} == 2 AND substr(${$item},0,1) == 0))){
					${$item} = 0;
				}
			}

			return mktime($_h,$_i,$_s,$_m,$_d,$_y);
		}
		/**
		*	transform a given timestamp into the date definition
		*/
		function stamp2date($stamp){
			foreach($this->date_order as $key => $item){
				$format .= $this->date_map[$item];
				if($key < sizeof($this->date_map) - 1){
					$format .= $this->date_sep;
				}
			}
			return date($format,$stamp);
		}
		/**
		*	transform a given timestamp into the time definition
		*/
		function stamp2time($stamp){
			$format .= $this->time_date_sep;
			foreach($this->time_order as $key => $item){
				$format .= $this->time_map[$item];
				if($key < sizeof($this->time_map) - 1){
					$format .= $this->time_sep;
				}
			}
			return trim(date($format,$stamp));
		}
		/**
		*	return a string for a timestamp
		*	will be hooked up to i18n
		*	switch for different types
		*/
		function stamp2string($type,$stamp,$short = false){
			$which = getdate($stamp);
			switch($type){
				case 'month':
					return $this->month2string($which['mon'],$short);
				break;
				case 'day':
					return $this->day2string($which['wday'],$short);
				break;
			}
		}
		/**
		*	get a full german string of a month
		*	TODO: i18n !!!!!!!!!!!!
		*/
		function month2string($month,$short = false) {
			static $str_m_l = array('', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli',
								'August', 'September', 'Oktober', 'November', 'Dezember');
								
			static $str_m_s = array('', 'Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul',
								'Aug', 'Sep', 'Okt', 'Nov', 'Dez');

			if($month == 'list'){
				if($short){
					return $str_m_s;
				}
				return $str_m_l;				
			}

								
			if($short){
				return $str_m_s[(int)$month];
			}
			return $str_m_l[(int)$month];
		}
		/**
		*	get a full german string of a day
		*/
		function day2string($day,$short = false) {
			static $str_d_l = array(
				'Sonntag',
				'Montag',
				'Dienstag',
				'Mittwoch',
				'Donnerstag',
				'Freitag',
				'Samstag'
			);
			static $str_d_s = array(
				'So',
				'Mo',
				'Di',
				'Mi',
				'Do',
				'Fr',
				'Sa'
			);
			
			if($day == 'list'){
				if($short){
					return $str_d_s;
				}
				return $str_d_l;				
			}
			
			if($short){
				return $str_m_s[(int)$day];
			}
			return $str_m_l[(int)$day];
		}
		/**
		*	returns the stamp for the beginning of the day specified by tday
		*	if no stamp is given the current day is used
		*/
		function stamp2base($type = 'day',$tday = null){
			$now = empty($tday) ? getdate(time()) : getdate($tday);
			switch($type){
				case 'day':
					return mktime(0,0,0,$now['mon'],$now['mday'],$now['year']);
				break;
				case 'month':
					return mktime(0,0,0,$now['mon'],1,$now['year']);
				break;
				case 'year':
					return mktime(0,0,0,1,1,$now['year']);
				break;
			}
		}
		/**
		*	get an array with stamps of the beginning of the month and the end
		*/
		function month2rangeStamp($month,$year)
		{
			$date1 = mktime(0,0,0,$month,1,$year);
			$days = date("t",$date1);
			$date2 = mktime(23,59,59,$month,$days,$year);
			$output = array($date1,$date2);
			return $output;
		}
		
		/**
		* liefert 7x5 array eines 'kalenderblattes' eines monats
		*/
		function get_calendar($month, $year, $feature = true) {
			//$monthStamp  = $this->month2rangeStamp($month, $year);
			list($ts_start, $ts_end)  = $this->month2rangeStamp($month, $year);
			$daysInMonth = date("t", $ts_start);
			$firstDay    = date("w", $ts_start);

			$sheet = array();
			$cnt_day = $cnt = 0;
			for($week=0; $week<=5; $week++) {
				for($day=0; $day<=6; $day++) {
					$sheet[$week][$day] = ($cnt++ < $firstDay || $cnt_day > $daysInMonth - 1) ? '' : (++$cnt_day);
				}
			}

			$next_month_year = $last_month_year = '';
			
			$prev_month = ($month == 1) ? '12-'.($year-1) : ($month-1).'-'.$year;
			$next_month = ($month == 12) ? '1-'.($year+1) : ($month+1).'-'.$year;
			if (!$feature && $month == date('m') && $year == date('Y')) $next_month = '';
			
			return array(
				'sheet'      => $sheet,
				'prev_month' => $prev_month,
				'next_month' => $next_month,
				'month_ts_start' => $ts_start,
				'month_ts_end'   => $ts_end,
			);
						
			$lastMonth = $month != 1 ? $month - 1 : 12;
			$lastYear = $lastMonth == 12 ? $year - 1 : $year;
			$nextMonth = $month != 12 ? $month + 1 : 1;
			$nextYear = $nextMonth == 1 ? $year + 1 : $year;


			

		}
		/**
		*	returns regex for current configuration
		*	for date
		*
		*	year:
		*	Y YYYY
		*	y YY
		*	
		*	month:
		*	n 1-12
		*	m 01-12
		*	M Jan - Dec
		*	F January - December
		*	
		*	day:
		*	l Monday Tuesday Wednesday Thursday Friday Saturday Sunday
		*	j 1 -31
		*	d 01 - 31
		*	D Mon - Sun
		*	
		*/
		function get_regex_date(){
			$regex = "^";
			foreach($this->date_order as $item){
				$counter++;
				switch($this->date_map[$item]){
					case "d";
						$regex .= "[0-9]{2}";
					break;
					case "D":
						$regex .= "[A-Z]{1}[a-z]{2}";
					break;
					case "l":
						$regex .= "[A-Z]{1}[a-z]{2,5}day";
					break;
					case "j":
						$regex .= "[1-9]{1}|[1-3]{1}[0-9]{1}";
					break;
					case "n":
						$regex .= "[1-9]{1}|1[0-2]{1}";
					break;
					case "F":
						$regex .= "[A-Z]{1}[a-z]{2,8}";
					break;
					case "m";
						$regex .= "[0-9]{2}";
					break;
					case "M":
						$regex .= "[A-Z]{1}[a-z]{2}";
					break;
					case "y":
						$regex .= "[0-9]{2}";						
					break;
					case "Y";
						$regex .= "[0-9]{4}";
					break;
				}
				if($counter != sizeof($this->date_full)){
					switch($this->date_sep){
						case ".":
							$regex .= "\.";
						break;
						default:
							$regex .= $this->date_sep;
					}
				}
			}
			$regex .= "\$";
			return $regex;
		}
		/**
		*	returns regex for current configuration
		*	for time
		*
		*hour:
		*g 1 - 12
		*G 0 - 23
		*h 01 - 12
		*H 00 - 23
		*
		*minute:
		*i 00-59
		*
		*second:
		*s 00- 59
		*/
		function get_regex_time(){
			$regex = "^";
			foreach($this->time_order as $item){
				$counter++;
				switch($this->time_map[$item]){
					case "h":
						$regex .= "[0-1]{1}[0-9]{1}";
					break;
					case "g":
						$regex .= "[0-9]{1,2}";
					break;
					case "H";
						$regex .= "[0-9]{2}";
					break;
					case "G":
						$regex .= "[0-9]{1,2}";
					break;
					case "i":
					case "s":
						$regex .= "[0-9]{2}";
					break;
				}
				if($counter != sizeof($this->time_full)){
					switch($this->time_sep){
						case ".":
							$regex .= "\.";
						break;
						default:
							$regex .= $this->time_sep;
					}
				}
			}
			$regex .= "\$";
			return $regex;
		}
		/**
		*	returns the current format string for date
		*	year:
		*	Y YYYY

		*	y YY
		*	
		*	month:
		*	n 1-12
		*	m 01-12
		*	M Jan - Dec
		*	F January - December
		*	
		*	day:
		*	l Monday Tuesday Wednesday Thursday Friday Saturday Sunday
		*	j 1 -31
		*	d 01 - 31
		*	D Mon - Sun
		*/
		function get_format_date(){
			$format = "^";
			foreach($this->date_order as $item){
				$counter++;
				switch($this->date_map[$item]){
					case "d";
						$format .= "01 - 31";
					break;
					case "D":
						$format .= "Mon - Sun";
					break;
					case "l":
						$format .= "Monday - Sunday";
					break;
					case "j":
						$format .= "1 - 31";
					break;
					case "n":
						$format .= "1-12";
					break;
					case "F":
						$format .= "January - December";
					break;
					case "m";
						$format .= "01 - 12";
					break;
					case "M":
						$format .= "Jan - Dec";
					break;
					case "y":
						$format .= "YY";
					break;
					case "Y";
						$format .= "YYYY";
					break;
				}
				if($counter != sizeof($this->date_full)){
					switch($this->date_sep){
						case ".":
							$format .= "\.";
						break;
						default:
							$format .= $this->date_sep;
					}
				}
			}
			return $format;
		}
		/**
		*	returns format for current configuration
		*	for time
		*
		*hour:
		*g 1 - 12
		*G 0 - 23
		*h 01 - 12
		*H 00 - 23
		*
		*minute:
		*i 00-59
		*
		*second:
		*s 00- 59
		*/
		function get_format_time(){
			foreach($this->time_order as $item){
				$counter++;
				switch($this->time_map[$item]){
					case "h":
						$format .= "01 - 12";
					break;
					case "g":
						$format .= "1 -12";
					break;
					case "H";
						$format .= "00 -23";
					break;
					case "G":
						$format .= "0 -23";
					break;
					case "i":
					case "s":
						$format .= "00 - 59";
					break;
				}
				if($counter != sizeof($this->time_full)){
					switch($this->time_sep){
						case ".":
							$format .= "\.";
						break;
						default:
							$format .= $this->time_sep;
					}
				}
			}
			$format .= "\$";
			return $format;
		}
		/**
		*	returns the current input string for date
		*	year:
		*	Y YYYY
		*	y YY
		*	
		*	month:
		*	n 1-12
		*	m 01-12
		*	M Jan - Dec
		*	F January - December
		*	
		*	day:
		*	l Monday Tuesday Wednesday Thursday Friday Saturday Sunday
		*	j 1 -31
		*	d 01 - 31
		*	D Mon - Sun
		*/
		function get_input_date($value = array()){
			foreach($this->date_order as $item){
				switch($this->date_map[$item]){
					case "d";
						$input .= "<input type='text' name='day' value='".$value['day']."' size='2'>";
					break;
					case "D":
						$input .= "<input type='text' name='day' value='".$value['day']."' size='3'>";
					break;
					case "l":
						$input .= "<input type='text' name='day' value='".$value['day']."' size='8'>";
					break;
					case "j":
						$input .= "<input type='text' name='day' value='".$value['day']."' size='2'>";
					break;
					case "n":
						$input .= "<input type='text' name='month' value='".$value['month']."' size='2'>";
					break;
					case "F":
						$input .= "<input type='text' name='month' value='".$value['month']."' size='12'>";
					break;
					case "m";
						$input .= "<input type='text' name='month' value='".$value['month']."' size='2'>";
					break;
					case "M":
						$input .= "<input type='text' name='month' value='".$value['month']."' size='3'>";
					break;
					case "y":
						$input .= "<input type='text' name='year' value='".$value['year']."' size='2'>";
					break;
					case "Y";
						$input .= "<input type='text' name='year' value='".$value['year']."' size='4'>";
					break;
				}
			}
			return $input;
		}
		/**
		*	returns input for current configuration
		*	for time
		*
		*hour:
		*g 1 - 12
		*G 0 - 23
		*h 01 - 12
		*H 00 - 23
		*
		*minute:
		*i 00-59
		*
		*second:
		*s 00- 59
		*/
		function get_input_time($value = array()){
			foreach($this->time_order as $item){
				switch($this->time_map[$item]){
					case "h":
					case "g":
					case "G":
					case "H":
						$input .= "<input type='text' name='hour' value='".$value['hour']."' size='2'>";
					break;
					case "i":
						$input .= "<input type='text' name='minute' value='".$value['minute']."' size='2'>";
					break;
					case "s":
						$input .= "<input type='text' name='second' value='".$value['second']."' size='2'>";
					break;
				}
			}
			return $input;
		}
}
?>
