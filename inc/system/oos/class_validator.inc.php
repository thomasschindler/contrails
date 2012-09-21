<?php
define('VALIDATOR_ERROR_NONE', 1);
define('VALIDATOR_ERROR_EMPTY', 2);
define('VALIDATOR_ERROR_FORMAT', 3);
define('VALIDATOR_ERROR_UNIQUE', 4);
define('VALIDATOR_ERROR_COMPARE', 5);
define('VALIDATOR_ERROR_UPLOAD', 6);
define('VALIDATOR_ERROR_FLDCOMPARE', 7);

/**
* klasse zum validieren von werten an hand von tabellen-config-dateien
*
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		system
*/

	class validator {
	
		
		/**
		* zeiger auf globalen MC
		*@var	object
		*@access	private
		*/
		var $MC = null;
		
		/**
		* config-array der zu checkenden tabelle
		*@var	array
		*@access	private
		*/
		var $conf = array();
		
		
		/**
		* array von fehlerhaften feldern und deren fehler (lerr|format)
		*@var	array
		*@access	private
		*/
		var $error_fields = array();
		
		var $validate_mode = '';
		
		/**
		* konstruktor
		*
		*laed tabellen-konfiguration
		*
		*@param	string	modul-name
		*@param	string	tabellen-name
		*@return	bool	true, wenn tabellen-conf geladen werden konnte; false sonst
		*@access	public
		*/
		function validator($table_name,$mod=null) 
		{
			if(is_array($table_name))
			{
				$this->conf = $table_name;
			}
			else
			{
				$MC = &MC::singleton();
				$conf = $MC->table_config($table_name,$mod);
				$this->conf = $conf;
			}
		}
		
		function get_fields(){
			return $this->conf;
		}
		
		/**
		* prueft die gueltigkeit der uebergebenen daten, anhand der config
		*
		*@param	array	$data	ass. daten arary
		*@param	string	$mode	'enter' oder 'edit', unterschied: bei edit wird 'unique' nicht geprueft
		*@return	bool	true, wenn daten gueltig; false sonst
		*/
		function is_valid($data, $mode = 'enter') 
		{
			$this->data = &$data;
			#MC::log($data,"test.log",CONF::web_dir()."/");
			
			$valid = true;
			$this->err_fields    = array();
			$this->validate_mode = $mode;
			foreach($this->conf['fields'] as $field => $info) 
			{

				$check = null;

				if (isset($info['cnf']['form']) && $info['cnf']['form'] === false) continue;
				
				if($info['multiple'])
				{
					foreach($info['multiple'] as $k => $i)
					{
						switch($i['type']) 
						{
							case 'input':
							case 'password':
								$check[] = $this->valid_input($data[$field][$k], $i, $field);
							break;
							case 'textarea':
								$check[] = $this->valid_textarea($data[$field][$k], $i, $field);
							break;
							case 'select':
								$check[] = $this->valid_select($data[$field][$k], $i, $field);
							break;
							case 'radio':
								$check[] = $this->valid_radio($data[$field][$k], $i, $field);
							break;
							case 'checkbox':
								$check[] = $this->valid_checkbox($data[$field][$k], $i, $field);
							break;
							case 'date':
								$check[] = $this->valid_date($data[$field][$k], $i, $field);
							break;
							case 'file':
								$check[] = $this->valid_file($data[$field][$k], $i, $field);
							break;
						}						
					}
				}
				else
				{
					switch($info['cnf']['type']) 
					{
						case 'input':
						case 'password':
							$check = $this->valid_input($data[$field], $info['cnf'], $field);
						break;
						case 'textarea':
							$check = $this->valid_textarea($data[$field], $info['cnf'], $field);
						break;
						case 'select':
							$check = $this->valid_select($data[$field], $info['cnf'], $field);
						break;
						case 'radio':
							$check = $this->valid_radio($data[$field], $info['cnf'], $field);
						break;
						case 'checkbox':
							$check = $this->valid_checkbox($data[$field], $info['cnf'], $field);
						break;
						case 'date':
							$check = $this->valid_date($data[$field], $info['cnf'], $field);
						break;
						case 'file':
							$check = $this->valid_file($data[$field], $info['cnf'], $field);
						break;
						case 'captcha':
							$check[] = $this->valid_captcha($data[$field], $i, $field);
						break;
					}
				}
				
				if(is_array($check))
				{
					$tmp = $check;
					$check = VALIDATOR_ERROR_NONE;
					foreach($tmp as $e)
					{
						if($e == VALIDATOR_ERROR_NONE)
						{
							continue;
						}
						$check = $e;
					}
				}
				
				if ($check === VALIDATOR_ERROR_EMPTY) 
				{
					/*
					$txt = (isset($info['cnf']['err_empty'])) 
								? $info['cnf']['err_empty'] 
								: sprintf($this->def_err_empty, $info['label']);
					*/
					$txt = (isset($info['cnf']['err_empty'])) 
								? $info['cnf']['err_empty'] 
								: e::o('err_empty',array('%field%'=>$info['label']),null,'validator');
								
					$this->error_fields[$field] = array('type' => VALIDATOR_ERROR_EMPTY, 'text' => $txt);
					$valid = false;
				}
				elseif ($check === VALIDATOR_ERROR_FORMAT) 
				{
					$txt = (isset($info['cnf']['err_format'])) 
								? $info['cnf']['err_format'] 
								: e::o('err_format',array('%field%'=>$info['label']),null,'validator');
					$this->error_fields[$field] = array('type' => VALIDATOR_ERROR_FORMAT, 'text' => $txt);
					$valid = false;
				}
				elseif ($check === VALIDATOR_ERROR_UNIQUE) 
				{
					$txt = (isset($info['cnf']['err_unique'])) 
								? $info['cnf']['err_unique'] 
								: e::o('err_unique',array('%field%'=>$info['label']),null,'validator');
					$this->error_fields[$field] = array('type' => VALIDATOR_ERROR_UNIQUE, 'text' => $txt);
					$valid = false;
				}
				elseif ($check === VALIDATOR_ERROR_COMPARE) 
				{
//					$txt = e::o('err_compare',array('%field%'=>$info['label']),null,'validator');
					
					$txt = (isset($info['cnf']['err_compare'])) 
								? $info['cnf']['err_compare'] 
								: e::o('err_compare',array('%field%'=>$info['label']),null,'validator');
					
					$this->error_fields[$field] = array('type' => VALIDATOR_ERROR_COMPARE, 'text' => $txt);
					$valid = false;
				}
				elseif ($check === VALIDATOR_ERROR_UPLOAD) 
				{
					$txt = e::o('err_upload',array('%field%'=>$info['label']),null,'validator');
					$this->error_fields[$field] = array('type' => VALIDATOR_ERROR_UPLOAD, 'text' => $txt);
					$valid = false;
				}
				elseif ($check === VALIDATOR_ERROR_FLDCOMPARE) 
				{
#					$txt = e::o('err_fldcompare',array('%field%'=>$info['label']),null,'validator');
					
					$txt = (isset($info['cnf']['err_fldcompare'])) 
								? $info['cnf']['err_fldcompare'] 
								: e::o('err_fldcompare',array('%field%'=>$info['label']),null,'validator');
								
					$this->error_fields[$field] = array('type' => VALIDATOR_ERROR_FLDCOMPARE, 'text' => $txt);
					$valid = false;
				}
			}

			return $valid;
		}
		/**
			compares the captcha info from the session against the one passed via POST
		*/
		function valid_captcha($data,$cnf,$field)
		{
			//VALIDATOR_ERROR_COMPARE
			$OPC = &OPC::singleton();
			$c = $OPC->SESS->get('form','captcha');
			if(strtoupper($data) !== strtoupper($c))
			{
				return VALIDATOR_ERROR_COMPARE;
			}
			return VALIDATOR_ERROR_NONE;
		}
		/**
		* liefert ass. array der fehlerhaften felder
		*
		* feldnamen sind keys, mit values 'type' und 'text'
		*/
		function get_error_fields() {
			return $this->error_fields;
		}
		
		function valid_input($data, $cnf, $field) {
			// evals
			if (isset($cnf['eval'])) {
				foreach(explode(',', $cnf['eval']) as $t) {
					eval('$data = '.$t.'($data);');
				}
			}
			
			// empty=true ist default
			if (!isset($cnf['empty'])) $cnf['empty'] = true;
			
			// leer und darf es nicht sein ?
			if ($cnf['empty'] == false && $data == '') return VALIDATOR_ERROR_EMPTY;

			// compare
			if (isset($cnf['compare']) && $this->valid_compare($data, $cnf['compare'],$cnf['type']) == false) return VALIDATOR_ERROR_COMPARE;
			// unique (kann nur in verbindung mit tabellen-config gecheckt werden, nicht bei einzelaufruf)
			// sind wir im edit-mode, pruefen wir auch nicht
			if ($this->validate_mode != 'edit' && isset($cnf['unique']) && $cnf['unique'] == true) {
				$db = &DB::singleton();
				$tbl = $this->conf['table']['name'];
				if ($tbl != '') {
					$sql = 'SELECT count(*) AS cnt FROM '. $tbl . ' WHERE ' . $field .'=\''.addslashes($data).'\'';
					$res = $db->query($sql);
					$cnt = (int)$res->f('cnt');
					if ($cnt > 0) return VALIDATOR_ERROR_UNIQUE;
				}
			}
			// compare with other field
			if (isset($cnf['fldcompare']) && $this->valid_fldcompare($data, $cnf['fldcompare'],$cnf['type']) == false) return VALIDATOR_ERROR_FLDCOMPARE;
			
			// wenn es leer ist, und darf es sein, ist hier feierabend

			if ($cnf['empty'] == true && $data == '') return VALIDATOR_ERROR_NONE;
			
			// mindestlaenge ?
			if (isset($cnf['min']) && strlen($data) < $cnf['min']) return VALIDATOR_ERROR_FORMAT;
			
			// maximallaenge (default: 255)
			if (!isset($cnf['max'])) $cnf['max'] = 255;

			if (strlen($data) > $cnf['max']) return VALIDATOR_ERROR_FORMAT;
			
			// regex      
			if (isset($cnf['regex']) && !preg_match($cnf['regex'], $data)) return VALIDATOR_ERROR_FORMAT;
				
			// bestimmtes format (type)
			if (isset($cnf['format']) && $this->valid_format($data, $cnf['format']) == false) return VALIDATOR_ERROR_FORMAT;
			

			
			return VALIDATOR_ERROR_NONE;
		}
				
		function valid_textarea($data, $cnf, $field) {
			
			// evals
			if (isset($cnf['eval'])) {
				foreach(explode(',', $cnf['eval']) as $t) {
					eval('$data = '.$t.'($data);');
				}
			}
			
			// empty=true ist default
			if (!isset($cnf['empty'])) $cnf['empty'] = true;
			

			// leer und darf es nicht sein ?
			if ($cnf['empty'] == false && $data == '') return VALIDATOR_ERROR_EMPTY;
			
			// wenn es leer ist, und darf es sein, ist hier feierabend
			if ($cnf['empty'] == true && $data == '') return VALIDATOR_ERROR_NONE;
			
			// mindestlaenge ?
			//if (isset($cnf['min']) && strlen($data) < $cnf['min']) return VALIDATOR_ERROR_FORMAT;
			
			// maximallaenge (default: 255)
			//if (!isset($cnf['max'])) $cnf['max'] = 255;
			//if (strlen($data) > $cnf['max']) return VALIDATOR_ERROR_FORMAT;
			
			// regex
			//if (isset($cnf['regex']) && !preg_match($cnf['regex'], $data)) return VALIDATOR_ERROR_FORMAT;
				
			// bestimmtes format (type)
			if (isset($cnf['format']) && $this->valid_format($data, $cnf['format']) == false) return VALIDATOR_ERROR_FORMAT;
			
			// unique (kann nur in verbindung mit tabellen-konfig gecheckt werden, nicht bei einzelaufruf)
			// sind wir im edit-mode, pruefen wir auch nicht
			if ($this->validate_mode != 'edit' && isset($cnf['unique']) && $cnf['unique'] == true) {
				$db = &DB::singleton();
				$tbl = $this->conf['table']['name'];
				if ($tbl != '') {
					$sql = 'SELECT count(*) AS cnt FROM '. $tbl . ' WHERE ' . $field .'=\''.addslashes($data).'\'';
					$res = $db->query($sql);
					$cnt = (int)$res->f('cnt');
					if ($cnt > 0) return VALIDATOR_ERROR_UNIQUE;
				}
			}
			
			if (isset($cnf['compare']) && $this->valid_compare($data, $cnf['compare'],$cnf['type']) == false) return VALIDATOR_ERROR_COMPARE;
			
			return VALIDATOR_ERROR_NONE;
		}
		
		function valid_select($data, $cnf, $field) {
			
			// empty=true ist default
			if (!isset($cnf['empty'])) $cnf['empty'] = true;
			
			
			// bei multi=true kriegen wir array, sonst nur einen wert
			if ($cnf['multi'] == true) {
				if (!is_array($data)) $data = array();
				
				// leer und darf es nicht sein ?
				if ($cnf['empty'] == false && count($data) == 0) return VALIDATOR_ERROR_EMPTY;
				
				// wenn es leer ist, und darf es sein, ist hier feierabend
				if ($cnf['empty'] == true && count($data) == 0) return VALIDATOR_ERROR_NONE;
				
				// mindestlaenge ?
				if (isset($cnf['min']) && count($data) < $cnf['min']) return VALIDATOR_ERROR_FORMAT;
				
				// maximallaenge
				if (isset($cnf['max']) && count($data) > $cnf['max']) return VALIDATOR_ERROR_FORMAT;
			}
			else {
				// leer und darf es nicht sein ?
				if ($cnf['empty'] == false && $data == '') return VALIDATOR_ERROR_EMPTY;
				
				// wenn es leer ist, und darf es sein, ist hier feierabend
				if ($cnf['empty'] == true && $data == '') return VALIDATOR_ERROR_NONE;
			}
			if (isset($cnf['compare']) && $this->valid_compare($data, $cnf['compare'],$cnf['type']) == false) return VALIDATOR_ERROR_COMPARE;
			return VALIDATOR_ERROR_NONE;
		}
		
		function valid_radio($data, $cnf, $field) {
			// empty=true ist default
			if (!isset($cnf['empty'])) $cnf['empty'] = true;

			// leer und darf es nicht sein ?
			if ($cnf['empty'] == false && $data == '') return VALIDATOR_ERROR_EMPTY;
			
			// wenn es leer ist, und darf es sein, ist hier feierabend
			if ($cnf['empty'] == true && $data == '') return VALIDATOR_ERROR_NONE;
			
			if (isset($cnf['compare']) && $this->valid_compare($data, $cnf['compare'],$cnf['type']) == false) return VALIDATOR_ERROR_COMPARE;
			
		}
		
		function valid_checkbox($data, $cnf, $field) {
			// werte sind in array !
			
			// empty=true ist default
			if (!isset($cnf['empty'])) $cnf['empty'] = true;

			// leer und darf es nicht sein ?
			if ($cnf['empty'] == false && (!is_array($data) || count($data) == 0)) {
				 return VALIDATOR_ERROR_EMPTY;
			}
			
			if (isset($cnf['compare']) && $this->valid_compare($data, $cnf['compare'],$cnf['type']) == false) return VALIDATOR_ERROR_COMPARE;
			
			else {
				return VALIDATOR_ERROR_NONE;

			}
		}
		
		function valid_date($data, $cnf, $field) {
			// werte sind in array (tag, monat, jahr)!

			// empty=true ist default
			if (!isset($cnf['empty'])) $cnf['empty'] = true;

			$day   = $data['day'];
			$month = $data['month'];
			$year  = $data['year'];
			
			// leer und darf es nicht sein ?
			if ($cnf['empty'] == false && (!$day || !$month || !$year)) 
			{
				 return VALIDATOR_ERROR_EMPTY;
			}
			elseif ($cnf['empty'] == true && (!$day && !$month && !$year))
			{
				return VALIDATOR_ERROR_NONE;
			}
			elseif(strlen($day) != 2 || strlen($month) != 2 || strlen($year) != 4 ) 
			{
				return VALIDATOR_ERROR_FORMAT;
			}
			elseif(!checkdate($month, $day, $year)) 
			{
				return VALIDATOR_ERROR_FORMAT;
			}
			
			if (isset($cnf['compare']) && $this->valid_compare($data, $cnf['compare'],$cnf['type']) == false)
			{
				return VALIDATOR_ERROR_COMPARE;
			}
			else 
			{
				return VALIDATOR_ERROR_NONE;
			}
			
		}
		/**
		*	validate year
		*/
		function valid_year($data, $cnf, $field) {
			// werte sind in array (tag, monat, jahr)!

			// empty=true ist default
			if (!isset($cnf['empty'])) $cnf['empty'] = true;

			$year  = (int)$data['year'];
			
			// leer und darf es nicht sein ?
			if ($cnf['empty'] == false && (!$year)) {
				 return VALIDATOR_ERROR_EMPTY;
			}
			elseif (!checkdate(1, 1, $year)) {
				return VALIDATOR_ERROR_FORMAT;
			}
			else {
				return VALIDATOR_ERROR_NONE;
			}
		}
		
		function valid_file($data, $cnf, $field) 
		{
			// empty=true ist default
			if (!isset($cnf['empty'])) $cnf['empty'] = true;
						
			$filename = $_FILES[$field]['name'];
			
			if($_FILES[$field]['error'] AND $_FILES[$field]['name'] != '')
			{
				return VALIDATOR_ERROR_UPLOAD;
			}
			
			if ($cnf['empty'] == false && trim($filename) == '') 
			{
				 return VALIDATOR_ERROR_EMPTY;
			}
			
			
			// gueltige dateiendung
			if ($filename && isset($cnf['allow_ext']) && is_array($cnf['allow_ext'])) 
			{
				$parts = pathinfo($filename);
				$ext   = strtolower($parts['extension']);

				if (!in_array($ext, $cnf['allow_ext'])) 
				{
					return VALIDATOR_ERROR_FORMAT;
				}
			}

			// gueltiger mimetype
			if ($filename && isset($cnf['allow_mime']) && is_array($cnf['allow_mime'])) 
			{
				$mime = trim(UTIL::mime_content_type($_FILES[$field]['tmp_name']));
				if (!in_array($mime, $cnf['allow_mime'])) 
				{
					$p = explode("/",$mime);
					$mime  = $p[0]."/*";
					if (!in_array($mime, $cnf['allow_mime'])) 
					{					
						return VALIDATOR_ERROR_FORMAT;
					}
				}
			}
					
			
			if (isset($cnf['compare']) && $this->valid_compare($data, $cnf['compare'],$cnf['type']) == false) return VALIDATOR_ERROR_COMPARE;
			
			return VALIDATOR_ERROR_NONE;
		}
		
		/**
		* validiert nach einem bestimmten typ/format (email, zahl etc.)
		*
		*@param	mixed	$data
		*@param	string	$format
		*@return	bool	true, wenn gueltig; false sonst
		*/
		function valid_format($data, $format) {
			$regex = &regex::singleton();			
			switch($format) {
				case 'email':
					return filter_var($data,FILTER_VALIDATE_EMAIL) ? TRUE : FALSE;
					//return (preg_match('/^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*(\.[a-z]{2,4})*/i', $data)) ? TRUE : FALSE;
				break;
				case  'http':
#					$reg = '^((ht|f)tp(s?))\://([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(/\S*)?$';
					$reg = '^((ht|f)tp(s?))\://([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}[/a-zA-Z0-9\.\_\-]*$';

					return  (preg_match("/".$reg."/", $data)) ? TRUE : FALSE;
				break;
				default:
					return $regex->validate($data,$regex->$format());
			}
		}
		/**
		*	compare input against a comparation string
		*/
		function valid_compare($data,$compare,$type){
			switch($type){
				case 'checkbox':
					foreach($data as $key => $val){
						$data[$key]++;
					}
					$data = implode(',',$data);
				break;
				case 'select':
					if(is_array($data)){
						foreach($data as $key => $val){
							$data[$key]++;
						}
						$data = implode(',',$data);
					}
					else{
						$data++;
					}
				break;
				case 'radio':
					$data++;
				break;
				case 'date':
					$data = implode('.',$data);
				break;
				default:
					if(is_array($data)){
						$data = implode(',',$data);
					}
				break;
			}
			return trim($data) == trim($compare);
		}
		
		function valid_fldcompare($data,$fld,$type)
		{
			return (bool)($data == $this->data[$fld]);
		}		
		
	}

?>
