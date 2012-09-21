<?php

	require_once(__DIR__ . '/db.inc.php');

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
		return;
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
			'sys_log.time' => (string)$end,
			'sys_log.project' => CONF::project_name(),
			'sys_log.url' => $_SERVER['HTTP_HOST'],
			'sys_log.pid' => (string)(is_numeric($OPC->pid()) ? $OPC->pid() : 0),
			'sys_log.mod' => UTIL::get_post("mod"),
			'sys_log.event' => UTIL::get_post("event"),
			'sys_log.mod_view' => $startview[1],
			'sys_log.view' => $startview[2],
			'sys_log.files' => base64_encode(serialize($_FILES)),
			'sys_log.post' => base64_encode(serialize($_POST)),
			'sys_log.get' => base64_encode(serialize($_GET)),
			'sys_log.ip' => $ip,
			'sys_log.session' => $SESS->id,
			'sys_log.browser' => (string)get_browser(),
			'sys_log.uid' => $CLIENT->usr['id'],
			'sys_log.name' => $CLIENT->usr['usr'],
			'sys_log.referer' => (string)$_SERVER['HTTP_REFERER'],
			'sys_log.duration' => $duration
		);

		$crud = new db_crud();
		$new_id = $crud->create('sys_log', $values, array());
		// return $new_id;
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
