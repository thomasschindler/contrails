<?php

/**
 *	this class does the logging and everything concerning the logging
 *	created for netzcheckers.de and their special needs
 */

/*.
	require_module 'standard';
	require_module 'mysql';
.*/

class log
{

	/**
	 *	Time when logging started
	 *	@var	int
	 */

	private $start = 0;

	/**
	 *	Time when logging ended
	 *	@var	int
	 */

	private $end = 0;

	/**
	 *	constructor / updates the table if necessary
	 *	@return	void
	 */
	
	public function __construct()
	{
		/*
		this breaks some configurations
		is this really needed?
		if(!CONF::get("sys_log_table_3"))
		{
			//	Added conditional to make this work on test instance. Table alteration was 
			//	triggering error and infinite redirects to error page.
			if ('http://fotofeliz.org.za' !== CONF::baseurl()) {
				//	TODO: establish whether this is still necessary
				$DB = &DB::singleton();
				$DB->query("ALTER TABLE sys_log ADD duration FLOAT  NULL  DEFAULT NULL");
				$DB->query("ALTER TABLE sys_log ADD mod_view VARCHAR(255)  NULL  DEFAULT NULL");
				$DB->query("ALTER TABLE sys_log ADD view VARCHAR(255)  NULL  DEFAULT NULL");
				CONF::set("sys_log_table_3","true");
			}
		}
		*/
		return;
	}

	public static function err($msg){
		return self::msg($msg, error_message_level::error);
	}	

	public static function warn($msg){
		return self::msg($msg, error_message_level::warning);
	}

	public static function dbg($msg){
		return self::msg($msg, error_message_level::debug);	
	}

	public static function msg($text, $level)
	{
		$logpath = CONF::dir('log') . CONF::project_name() . "/" . date("Ymd") . "/" . session_id();
		if(!is_dir($logpath))
		{
			mkdir($logpath, "0777", true);
		}
		switch ($level) {
			case error_message_level::error:
				//@todo: Decide on a message body!
				//mail(CONF::notification(), CONF::project_name() . ' - Error (Session: ' . session_id(), $text);
			case error_message_level::warning:
			case error_message_level::debug:
				//MC::log("Message >> Level " . error_message_level::s($level) . " >> $msg", "messages.log" , $logpath);
				MC::log("Message >> Level " . error_message_level::s($level) . " >> $text");
				break;
		}
	}
	
	/**
	 *	start - notes the moment logging has started
	 *	@return	void
	 */
	
	public function start()
	{
		$this->start = microtime(true);
	}
	
	/**
	 *	stop - stores the logging information in the sys_log datbase table
	 *	@return	void
	 */
	
	public function end()
	{
		$end = time();
		$duration = microtime(true) - $this->start;
		
		$CLIENT = &CLIENT::singleton();
		$SESS = &SESS::singleton();
		$DB = &DB::singleton();
		$OPC = &OPC::singleton();
		
 		$startview = $OPC->get_start_view();
		
		// if we're behind a load balancer, we need to fetch the original ip
		$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	
		//	previous, unsanitized verson, remove once CRUD is tested
		#$insert = ''
		# . 'INSERT INTO sys_log ('
		#	 . ' sys_log.time, '
		#	 . ' sys_log.project, '
		#	 . ' sys_log.url, '
		#	 . ' sys_log.pid, '
		#	 . ' sys_log.mod, '
		#	 . ' sys_log.event, '
		#	 . ' sys_log.mod_view, '
		#	 . ' sys_log.view, '
		#	 . ' sys_log.files, '
		#	 . ' sys_log.post, '
		#	 . ' sys_log.get, '
		#	 . ' sys_log.ip, '
		#	 . ' sys_log.session, '
		#	 . ' sys_log.browser, '
		#	 . ' sys_log.uid, '
		#	 . ' sys_log.name, '
		#	 . ' sys_log.referer, '
		#	 . ' sys_log.duration'
		# . ') VALUES ('
		#	 . (string)$end . ', '
		#	 . "'" . CONF::project_name() . "', "
		#	 . "'" . $_SERVER['HTTP_HOST'] . "', "
		#	 . "'" . (string)(is_numeric($OPC->pid()) ? $OPC->pid() : 0) . "', "
		#	 . "'" . $DB->escape(UTIL::get_post("mod")) . "', "
		#	 . "'" . $DB->escape(UTIL::get_post("event")) . "', "
		#	 . "'" . $startview[1] . "', "
		#	 . "'" . $startview[2] . "', "
		#	 . "'" . base64_encode(serialize($_FILES)) . "', "
		#	 . "'" . base64_encode(serialize($_POST)) . "', "
		#	 . "'" . base64_encode(serialize($_GET)) . "', "
		#	 . "'" . $ip . "', "
		#	 . "'" . $SESS->id . "', "
		#	 . "'" . mysql_real_escape_string((string)get_browser()) . "', "
		#	 . $CLIENT->usr['id'] . ", "
		#	 . "'" . $CLIENT->usr['usr'] . "', "
		#	 . "'" . mysql_real_escape_string((string)$_SERVER['HTTP_REFERER']) . "', "
		#	 . "'" . $duration . "'"
		#. ')';
		#$DB->query($insert);

		$values = array(
			'time' => (string)$end,
			'project' => CONF::project_name(),
			'url' => $_SERVER['HTTP_HOST'],
			'pid' => (string)(is_numeric($OPC->pid()) ? $OPC->pid() : 0),
			'mod' => UTIL::get_post("mod"),
			'event' => UTIL::get_post("event"),
			'mod_view' => $startview[1],
			'view' => $startview[2],
			'files' => base64_encode(serialize($_FILES)),
			'post' => base64_encode(serialize($_POST)),
			'get' => base64_encode(serialize($_GET)),
			'ip' => $ip,
			'session' => $SESS->id,
			'browser' => (string)$this->get_browser(),
			'uid' => $CLIENT->usr['id'],
			'name' => $CLIENT->usr['usr'],
			'referer' => (string)@$_SERVER['HTTP_REFERER'],
			'duration' => $duration
		);
		
		//Fix to ensure that even if a NULL value is coming through, this gets inserted
		foreach($values as $key => $val)
			if($values[$key] === NULL)
				$values[$key] = '';
		
		$crud = new db_crud();
				
		$new_id = $crud->create('sys_log', $values,array());
		
		if($new_id < 0)
		{
			//throw new Exception("log: entry could not be written");
			mail('t@hotoshi.com','logging failed',$crud->err_msg);
		}
		
		// return $new_id;
	}


	private function get_browser()
	{
	    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	    // you can add different browsers with the same way ..
	    if(preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
	    {
			$browser = 'chromium';
	    }
	    elseif(preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
	    {
	    	$browser = 'chrome';
	    }
	    elseif(preg_match('/(safari)[ \/]([\w.]+)/', $ua))
	    {
	    	$browser = 'safari';
	    }
	    elseif(preg_match('/(opera)[ \/]([\w.]+)/', $ua))
	    {
	    	$browser = 'opera';
	    }
	    elseif(preg_match('/(msie)[ \/]([\w.]+)/', $ua))
	    {
			$browser = 'msie';
	    }
	    elseif(preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
	    {
	    	$browser = 'mozilla';
	    }
	    preg_match('/('.$browser.')[ \/]([\w]+)/', $ua, $version);
	    return array($browser,$version[2], 'name'=>$browser,'version'=>$version[2]);
	}

	/**
	 *	Returns the single instance of this object, instantiating on first call to this
	 *	@return	log
	 */

	public static function singleton() 
	{
		static /*. log .*/ $instance;	
		if (!is_object($instance)) 
		{
			$instance = new log();
		}
		return $instance;
	}
	
}

?>
