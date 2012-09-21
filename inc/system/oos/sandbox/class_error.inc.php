<?php
/**
*	
*	basic error output handling
*	
*	
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		system
*/
	class error {
		
		var $txt = '';
/**
*	
*	set error text
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			txt - error text
*	@return			void
*	@access			public
*/
		function error($txt = '') {
			$this->txt = $txt;
		}
/**
*	
*	add text to the error message
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			txt - error text to add to the current error object
*	@return			void
*	@access			public
*/
		function add($txt) {
			$this->txt .= '-'.$txt;
		}

/**
*	set our private error handling
*/
		function e_start(){
			$this->err = array();
			$error_handler = array(&$this, 'catch_error');
			set_error_handler($error_handler);
		}
/**
*	flush our collected error
*	0 -> do nothing
*	1 -> alert 
*	2 -> alert and notify via email
*	3 -> alert, log and notify via email
*	4 -> show
*	5 -> show and notify via email
*	6 -> show, log and notify via email
*	7 -> log
*	8 -> log and notify via email
*/
		function e_flush($type = 0){
			if(!$this->err){
				return;
			}
			$type = (int)$type;
			switch($type){
				case 1:
					// alert
					$this->alert();
				break;
				case 2:
					// alert and notify via email
					$this->alert();
					$this->notify();
				break;
				case 3:
					// alert, log and notify via email
					$this->alert();
					$this->log();
					$this->notify();
				break;
				case 4:
					// show
					$this->show();
				break;
				case 5:
					// show and notify via email
					$this->show();
					$this->notify();
				break;
				case 6:
					// show, log and notify via email
					$this->show();
					$this->log();
					$this->notify();
				break;
				case 7:
					// log
					$this->log();
				break;
				case 8:
					// log and notify via email
					$this->log();
					$this->notify();
				break;
				case 0:
				default:
					return;
			}
			return;
		}
		/**
		*	alert
		*/
		function alert(){
			$msg = "ERROR";
			foreach($this->err as $err){
				$msg .= ' \n'.$err['errno'];
				$msg .= ' '.$err['errtype'];
				$msg .= ' \n';
			}			
			echo UTIL::get_js('alert("'.$msg.'");');
		}
		/**
		*	show the error in a separate window
		*/
		function show(){
			foreach($this->err as $err){
				$msg .= $err['errtype'];
				$msg .= ' \n'.$err['errmsg'];
				$msg .= ' \n'.$err['filename'].' @ '.$err['linenum'];
				$msg .= ' \n\n';
			}			
			echo UTIL::get_js('alert("'.$msg.'");');
		}


		/**
		*	log the error
		*/
		function log(){
			$CLIENT = CLIENT::singleton();	
				$DB = DB::singleton();
				$e_id = array();
				foreach($this->err as $err){
					$insert = "INSERT INTO sys_err_log (
						time,
						uid,
						client,
						errno,
						vars,
						post_vars,
						get_vars,
						errtype,
						errmsg,
						filename,
						linenum
					) VALUES (
						'".$err['time']."'	,
						'".$CLIENT->usr['id']."',
						'".serialize($CLIENT)."'	,
						'".$err['errno']."'	,
						'".serialize($err['vars'])."',
						'".serialize($_POST)."',
						'".serialize($_GET)."',
						'".$err['errtype']."'	,
						'".$err['errmsg']."'	,
						'".$err['filename']."'	,
						'".$err['linenum']."'	
					)";
					$DB->query($insert);
					$e_id[] = $DB->insert_id();
				}
		}
		/**
		*	notify me by email
		*/
		function notify(){
			// send an email
		}
		/**
		*	get the error as formatted text
		*/
		function get_err(){
			return $this->err;
		}
/**
*	error handler
*/
		function catch_error($errno, $errmsg, $filename, $linenum, $vars) {
			error_reporting(E_PARSE | E_ERROR | E_WARNING | E_USER_ERROR | E_USER_WARNING);
			#error_reporting(0);

			$level = error_reporting();
			if ( ($errno & $level) != $errno) return;

			$err = array();
			// timestamp for the error entry
			$err['time'] = time();
			// define an assoc array of error string
			// in reality the only entries we should
			// consider are E_WARNING, E_NOTICE, E_USER_ERROR,
			// E_USER_WARNING and E_USER_NOTICE
			$errortype = array (
				E_ERROR      => "Error",
				E_WARNING     => "Warning",
				E_PARSE      => "Parsing Error",
				E_NOTICE     => "Notice",
				E_CORE_ERROR   => "Core Error",
				E_CORE_WARNING  => "Core Warning",
				E_COMPILE_ERROR  => "Compile Error",
				E_COMPILE_WARNING => "Compile Warning",
				E_USER_ERROR   => "User Error",
				E_USER_WARNING  => "User Warning",
				E_USER_NOTICE   => "User Notice",
				E_STRICT     => "Runtime Notice"
			);

			$err['errno'] = $errno;
			$err['errtype'] = $errortype[$errno];
			$err['errmsg'] = $errmsg;
			$err['filename'] = $filename;
			$err['linenum'] = $linenum;
			
			$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
			if (in_array($errno, $user_errors)) {
				$err['vars'] = $vars;
			}
			
			$this->err[] = $err;
			return;
		}
	}

?>
