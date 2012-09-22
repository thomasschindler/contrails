<?php

//	require_once(__DIR__ . '/class_opc.lint.inc.php');
//	require_once(__DIR__ . '/class_util.lint.inc.php');

/**
 *	MC - Master of Ceremony - Main Controller
 *
 *	[en] Main controller for actions
 *
 *	[de] hauptkontroller fuer aktions, ... (z.zt. fuer alles, was nicht mit der generierung von  
 *	[de] views zu tun hat)
 *
 *	@version	0.1.0
 *	@author		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@see		OPC
 *	@package	system
 */

/*.
	require_module 'standard';
	require_module 'hash';
	require_module 'regex';
	require_module 'pcre';
.*/

class MC {

	/**
	*	caches the calls to call action
	*	@var array
	*/

	private $call_action_cache = array();

	/**
	 *	[en] array of loaded config files
	 *	[de] array von schon geladenen config-dateien
	 *	@var	array
	 */

	private $configs = array();
		
	/**
	 *	[en] array of SQL events
	 *	[de] array von sql-events
	 *	@var array
	 */

	private $sql_events = array();
		
	/**
	 *	name of database table for listeners
	 *	@var string
	 */

	private $tbl_listeners = "sys_mc_listeners";

	/**
	 *	array that holds the listeners
	 *	@var array
	 */

	private $listeners = array();

	/**
	 *	set to true when listeners collection has been built
	 *	@var bool
	 */

	private $listeners_collected = false;

	/**
	 *	[en] name of database table which records existing / installed modules
	 * 	[de] db-tabelle, in der vorhandenen/installierte module stehen
	 *	@var string
	 */

	private $db_tbl_mod = 'sys_module';
		
	/**
	 *	[en] name of database table with a user module access rights (per page)
	 *	[de] db-tabelle mit benutzer-modul-zugriffsrechten je seite
	 *	@var string
	 */
	
	private $db_tbl_usr_ar = 'mod_page_mod_usr_ar';

	/**
	 *	[en] Name of database table storing group access rights to pages? (CHECKME)
	 *	[de] db-tabelle mit gruppen-modul-zugriffsrechten je seite
	 *	@var string
	 */

	private $db_tbl_grp_ar = 'mod_page_mod_grp_ar';
		
	/**
	 *	[en] array of static module actions
	 *	[de] array von statischen action-modulen
	 *	@var	array
	 */

	private $statics;

	/**
	 *	[en] referred to by methods of this class, added by strix 2012-02-17
	 *	@var	array
	 */

	private $access_rights = array();

	/**
	 *	[en] referred to by methods of this class, added by strix 2012-02-17
	 *	@var	bool
	 */

	private $no_access = false;

	/**
	 *	[en] referred to by methods of this class, added by strix 2012-02-17
	 *	@var	string
	 */

	private $mod = '';

	/**
	 *	(DOCUMENTME)
	 *	@var	object
	 */

	private $remote_connection;

	/**
	 *	Constructor
	 *	@return void
	 */

	function __construct() {			
		//TODO: tidy or load from an external file.		
		$this->sql_events = array(
			'mod_usradmin_usr'	=> array('delete' => array(array('trashcan', 'callback_delete', 'post'),),),
			'mod_usradmin_grp'	=> array('delete' => array(array('trashcan', 'callback_delete', 'post'),),),
			'mod_news' 			=> array('delete' => array(array('trashcan', 'callback_delete', 'post'),),),
			'mod_article' 		=> array('delete' => array(array('trashcan', 'callback_delete', 'post'),),),
			'mod_fbb'			=> array('delete' => array(array('trashcan', 'callback_delete', 'post'),),),
			'mod_level_page'	=> array('delete' => array(array('trashcan', 'callback_delete', 'post'),),),
			'mod_forum'			=> array('delete' => array(array('trashcan', 'callback_delete', 'post'),),),
		);
	}

	/*.	forward public void function debug(string $content, string $title=); .*/
	/*.	forward public array function collect_listeners(); .*/

	/*.	forward public bool function call_listeners(
			string $mod, 
			string $event,
			int $position=,
			array $addparams=
	); .*/

	/*.	forward public int function get_modul_id(string $modul); .*/

	/*.	forward public array function get_access_rights(int $pid=, int $uid=); .*/

	/*.	forward public mixed function get_mod_instance(
			string $modul,
			bool $is_static=,
			bool $constructor=
	); .*/

	/**
	 *	Call a module action (CHECKME)
	 *	
	 *	A very important method, TODO: investigate what types a module event might return
	 *
	 *	@param	array	$action		array holding module and event to call
	 *	@param	mixed	$params		arguments to the event (CHECKME)
	 *	@param	bool	$is_static	unused? (CHECKME)
	 *	@return	mixed
	 */

	function call_action($action, $params = null, $is_static = false, $minimal=false) 
	{
		// call the listeners
		$this->call_listeners($action['mod'],$action['event'],1);
		// call the action
		$hash = md5(serialize($action).serialize($params).$is_static);
		if(@$this->call_action_cache[$hash])
		{
			$this->call_listeners($action['mod'],$action['event']);
			return $this->call_action_cache[$hash];
		}		
		$ret = array();
		$modul = (string)$action['mod'];						
		if(empty($action['mod'])) { $modul = 'login'; }		// missing mod intercept?
		
		$mod = MC::get_mod_instance($modul,false,true,$minimal);
		if (method_exists($mod, '_constructor')) { $mod->_constructor(); }
		if (is_object($mod)) { $ret = $mod->main($action, $params); }
		$this->call_action_cache[$hash] = $ret;
		$this->call_listeners($action['mod'],$action['event']);
		return $ret;
	}

	/**
	 *	[en] Execute a module action
	 *	[de] ausfuehren der action eines modules
	 *
	 *	@param	string	$mod			name of a module
	 *	@param	string	$event			name of an event on the module (CHECKME)
	 *	@param	mixed	$params			arguments to the action (CHECKME)
	 *	@param	int		$background		execute in background if 1, spool if 2
	 *	@param	string	$spoolpath		location to spool to
	 *	@param	bool 	$static 		return a cached version of that call
	 *	@param	bool 	$minimal 		initialize a minimal version of the module (eg: without client)
	 *	@return	mixed
	 */

	function call($mod, $event = null, $params = null, $background = 0, $spoolpath = null, $static=false, $minimal=false)
	{
		if ($background === 1)
		{           
			$OPC = &OPC::singleton();
			$lnk = $OPC->lnk_background($mod, $event, $params);
			exec($lnk." >/dev/null 2>&1 &");
			return true;
		}

		if($background === 2)
		{
			$OPC = &OPC::singleton();
			$lnk = $OPC->lnk_background($mod, $event, $params, null, true);
			$spoolpath = isset($spoolpath) ? $spoolpath : CONF::inc_dir() . "/tmp/spool/";
			$spoolpath = substr($spoolpath,-1) != "/" ? $spoolpath."/" : $spoolpath;
			UTIL::mkdir_recursive($spoolpath);

			// removed time to make sure, we don't create any of them twice
			// UTIL::file_put_contents($spoolpath.md5(time().serialize($params)),$lnk);

			UTIL::file_put_contents($spoolpath . md5(serialize($params) . $mod . $event), $lnk);
			//TODO: check for and handle error conditions, strix 2012-02-17
			return true;
		}	

		return $this->call_action(array('mod' => $mod, 'event' => $event), $params, $static, $minimal);
	}

	/**
	 *	Instantiate a module's action class and call an event directly (ie, not through main())
	 *	@param	array	$action		Array of 'mod' => some_module_name, 'directevent' => method_name
	 *	@return	mixed
	 */

	function call_direct_action($action)
	{
		$mod = MC::get_mod_instance((string)$action['mod']);
		$e = $action['directevent'];
		return $mod->$e();
		//return "UNTESTABLE";
	}

	/**
	 *	Instantiate a module's action class (CHECKME)
	 *	
	 *	Every module has a class named class_[modulename]Action.inc.php, this method creates and 
	 *	returns one of those.
	 *
	 *	date:			12 05 2004
	 *	
	 *	@author 	hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
	 *	@version	1.0
	 *	@since		1.0
	 *	@param		string	$modul 			name of a module
	 *	@param		bool	$is_static		is a static action, appears to be unused (CHECKME)
	 *	@param		bool	$constructor	set to true if we expect this to have a constructor
 	 *	@return		mixed
 	 */

	public function get_mod_instance($modul, $is_static = false, $constructor = true, $minimal = false)
	{
		$inc_dir = CONF::inc_dir()."/";

		// check for existence of the mod_dir.
		if(!is_dir($inc_dir . 'mods/' . $modul)) { return false; }
			
		$class_dir  = $inc_dir . 'mods/' . $modul . '/class_' . $modul . 'Action.inc.php';
		$class_name = $modul . '_action';

		require_once($inc_dir.'system/oos/class_modAction.inc.php');

		require_once($class_dir);

		if (true === $is_static) 
		{
			if (!is_object($this->statics[$mod])) { $this->statics[$mod] = new $class_name(); }
			$mod = $this->statics[$mod];
		}
		else 
		{
			$mod = new $class_name();
		}

		// call the constructor before we return
		if (true === $constructor)
		{
			$mod->_constructor($minimal);
		}

		return $mod;
	}

	/**
	 *	Calls a cleanup method on a named module (CHECKME)
	 *	creation date: 25 05 2004
	 *	
	 *	@author 	Thomas Schindler <development@hundertelf.com>
	 *	@version	1.0
	 *	@since		1.0
	 *	@param	string	$what	Name of a module, perhaps (CHECKME)
	 *	@param	array	$where	Arguments to the cleanup method on a module (CHECKME)
	 *	@return	bool
	 */

	function cleanup($what, $where = array()) {

		if (!$mod = $this->get_mod_instance($what))
		{
			$select = "SELECT modul_name FROM " . $this->db_tbl_mod;
			$db = &DB::singleton();
			$res = $db->query($select);

			// run through the modules and try to find the one
			$mods = array();
			while ($res->next()){
				if (preg_match("/" . $res->f('modul_name') . "/", $what)) {
					$mods[] = $res->f('modul_name');
				}
			}

			if (sizeof($mods) == 0) { return false; }
			if(sizeof($mods) > 1){
				// try and get the module name from the string
				$what_pieces = explode("_",$what);
				$what = $what_pieces[1]; // used in alert!!
				$mods[0] = $what;
			}

			$mod = $this->get_mod_instance($mods[0]);
			if(!$mod) { return false; }
		}

		if (method_exists($mod, 'cleanup')) { return $mod->cleanup($where); }
		return false;
	}

	/**
	 *	call an sql - event
	 *	creation date: 12 05 2004
	 *
	 *	@author 	hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
	 *	@version	1.0
	 *	@since		1.0
	 *	@param		string	$table		name of a database table
	 *	@param		string	$mode		(DOCUMENTME)
	 *	@param		string	$when		(DOCUMENTME)
	 *	@param		array	$params		arguments for the module action? (CHECKME)
 	 *	@return		void
	 */

	public function call_event($table, $mode, $when, $params = null) {
		if (!is_array($this->sql_events[$table][$mode])) { return; }
		$params['when'] = $when;
		foreach($this->sql_events[$table][$mode] as $call) {
			$cmodul  = $call[0];
			$cmethod = $call[1];
			$cwhen   = $call[2];
			if ($cwhen == $when) {
				$this->call_action(array('mod' => $cmodul, 'event' => $cmethod), $params);
			}
		}
	}
			
	/**
	 *	provides object-pointer to MC
	 *
	 *	[en] each instantiation of this object has to run over this function to ensure that only
	 *	[en] one instance of this object exists
	 *
	 *	[de] liefert zeiger auf MC objekt
	 *	[de] jede instanzierung muss ueber diese funktion laufe, um sicherzustellen, dass immer nur
	 *	[de] eine instanz dieses objektes existiert
	 *
	 *	(return type removed for PHPLint)
	 */

	public /*. MC .*/ function singleton() {
		static /*. MC .*/ $instance;
			
		if (!is_object($instance)) { $instance = new MC(); }
			
		return $instance;
	}

	/**
	 *	Load table schema/configuration as an array (CHECKME)
	 *	[de] config-datei/array einer tabelle liefern
	 *
	 *	@param	string	$table	table name
	 *	@param	string	$mod	module name
	 *	@return	array
	 */

	public function table_config($table = '', $mod = null) 
	{
		if('' === $table) { return; }

		// some sort of precache? (CHECKME)(DOCUMENTME)
		if (isset($this->configs['table'][$table])) { return $this->configs['table'][$table]; }
			
		$mod_dir = CONF::inc_dir() . '/mods/';		// not testable, work around
		$form_file = '/form/' . $table . '.php';
		
		
		$file = $mod_dir . $mod . $form_file;
		$test = '';
		
		if($mod === null)
		{
			if(isset($this->__current_mod))
			{
				$mod = $this->__current_mod;
			}
			else
			{
				$OPC = &OPC::singleton();
				$mod = $OPC->current_mod();
			}
		}
		
		if(is_file($mod_dir.$mod.'/form/'.CONF::project_name().'/'.$table.'.php'))
		{
			$file = $mod_dir.$mod.'/form/'.CONF::project_name().'/'.$table.'.php';
		}
		else
		{
			if ('' === $mod) { $file = $mod_dir . $this->__current_mod . $form_file; }
			if (!is_file($file)) { $file = $mod_dir . $this->__current_mod . $form_file; }
			if (!is_file($file)) { $OPC = &OPC::singleton(); $file = $mod_dir . $OPC->current_mod() . $form_file; }
			if (!is_file($file)) { $file = CONF::inc_dir() . 'etc' . $form_file; }			
		}
		

		if (!is_file($file)) {
			if ('mod_' === substr($table, 0 , 4)) { $mod = preg_replace("/mod_/","",$table); }
			$parts = explode("_", $mod);
			foreach ($parts as $m)
			{
				$test .= $m;
				$file = $mod_dir . $test . $form_file;
				if (is_file($file)) { break; }					
			}
		}

		if (!is_file($file)) { MC::debug($file, 'MISSING: ' . $this->__current_mod); }

		include($file);								// probably not testable
		$this->configs['table'][$table] = $conf;
		return $conf;
	}
		
	/**
	 *	[en] config-data array for the access rights of a module
	 *	[de] config-datei/array fuer die zugriffsrechte eines moduls
	 *
	 *	@param	string	$modul	modul-name
	 *	@return	array
	 */

	public function access_config($modul)
	{
		if (isset($this->configs['access'][$modul])) return $this->configs['access'][$modul];
		// $file = CONF::inc_dir() . 'etc/access/'.$m.'.access';
		$file = CONF::inc_dir() . '/mods/' . $modul . '/etc/access.php';
		if(is_file($file))
		{
			include($file);
			$this->configs['access'][$modul] = $conf;
			return $conf;
		}
		return new Error("No access file found for " . $modul);
	}

	/**
	 *	[en] Config data array for access rights of data elements
	 *	[de] config-datei/array fuer zugriffsrechte von datensaetzen
	 *
	 *	@param	string	$modul	tabellen-name
	 *	@return	array
	 *	@access	public
	 */
	
	function acl_config($table)
	{

		if (isset($this->configs['acl'][$table])) { return $this->configs['acl'][$table]; }
			
		$file = CONF::inc_dir() . '/etc/acl/'.$table.'.acl';
			
		if (!file_exists($file)) 
		{
			return new Error('No acl config-file found for table: '.$acl);
		}
			
		// for i18n !!!!
		$p_1 = explode('.',$table);
		$p_2 = explode('_',$p_1[sizeof($p_1)-1]);
		$this->mod = $p_2[sizeof($p_2)-1];
			
		include($file);
			
		$this->configs['acl'][$table] = $conf;

	

		return $conf;
	}

	/**
	 *	[en] Config data array listing the module events
	 *	[de] config-datei/array der module und ihrer 'ansprechbaren' events
	 *
	 *	@param	string	$modul	tabellen-name
	 *	@return	array
	 *	@access	public
	 */

	function event_config() {
		if (isset($this->configs['event'])) { return $this->configs['event']; }
			
		$file = CONF::inc_dir().'etc/event/event_config.inc.php';
			
		if (!file_exists($file)) { return new Error('No event config-file found'); }

		// PHPLint is unhappy with this, TODO: figure out how to make it happy, strix 2012-02-17
		include($file);
		$this->configs['event'] = $conf;

		return $conf;
	}
		
	/**
	 *	method for including the current instance-config file
	 *	@param	string	$modul	name of a module
	 *	@return	mixed
	 */

	function instance_config($modul) {
		if (isset($this->configs['instance'][$modul])) { return $this->configs['instance'][$modul]; }
			
		// $file = CONF::inc_dir() . 'etc/instance/' . $modul . '.instance';
		$file = CONF::inc_dir() . 'mods/' . $modul . '/etc/instance.php';
			
		if (!file_exists($file)) {
			return new Error('No instance config-file found for modul: '.$modul);
		}
			
		// for i18n !!!!
		$this->mod = $modul;
			
		// PHPLint is unhappy with this, TODO: figure out how to make it happy, strix 2012-02-17
		include($file);	
		$this->configs['instance'][$modul] = $conf;

		return $conf;
	}
		
	/**
	 *	switch access on and off.
	 *	if state is set to true.
	 *	no access will be granted to anything
	 *
	 *	@param	bool	$state	(DOCUMENTME)
	 *	@return	void
	 */

	function switch_access($state = false) { $this->no_access = $state; }
		
	/**
	 *	[en] check if current user has the right for the function of a module
	 *	[de] hat aktueller user das recht fuer die funktion eines moduls
	 *
	 *	@param	string	$modul		module name
	 *	@param	string	$func		function name (as in the access-config)
	 *	@param	int		$pid		page id
	 *	@param	int		$uid		user id
	 *	@return	bool	true if the user has the right, false otherwise
	 */

	function access($modul, $func, $pid = -1, $uid = -1) 
	{
		if ($this->no_access == true) { return false; }
		$conf = $this->access_config($modul);		
		if (is_error($conf)) { return true; }

		$mid = $this->get_modul_id($modul);

		$ar_nr = (int)$conf['rights'][$func];
			
		if (-1 == $uid) {
			$client = &CLIENT::singleton();
				
			if($client->usr['du']) { return false; }		
			if($client->usr['id'] == CLIENT::__root()) { return true; }
		}

		// for i18n !!!!
		$this->mod = $modul;	
		$ar = $this->get_access_rights($pid, $uid);		// assigned but not used? strix 2012-02-17

		return (($this->access_rights[$mid] & $ar_nr) == $ar_nr);
	}

	/**
	 *	Get arguments to a SQL statement for looking up permissions? (CHECKME)
	 *	This calls acladmin_action::action_acl_sql which returns a dict of arguments to a SQL
	 *	statement {'fields', 'join', 'group_by'}
	 *
	 *	TODO: check structure of returned array, poke this for SQL vulnerabilities.
	 *
	 *	@param	string	$table			Name of a database table (CHECKME)
	 *	@param	mixed	$add_params		Params for acladmin event (CHECKME)
	 *	@return mixed
	 */

	function acl_sql($table, $add_params = null) {
		$params = array('table' => $table);
		return $this->call_action(array('mod' => 'acladmin', 'event' => 'acl_sql'), $params, true);
	}

	/**
	 *	Check if an action is allowed by [access control list]? (CHECKME)
	 *
	 *	acladmin_action::action_acl_check determines if a policy is set and returns true if it is.
	 *
	 *	@param	string	$table			Name of a database table
	 *	@param  string	$right			Permission or policy to be tested
	 *	@param	int		$ar				(DOCUMENTME) An argument to acl_check event
	 *	@param	bool	$allow_empty	(DOCUMENTME) An argument to acl_check event
	 *	@return	mixed
	 */

	function acl_check($table, $right, $ar, $allow_empty = false) {
		$action = array('mod' => 'acladmin', 'event' => 'acl_check');
		$params = array(
			'table'  => $table, 
			'right' => $right,
			'ar' => $ar,
			'allow_empty' => $allow_empty
		);
		return $this->call_action($action, $params, true);	
	}

	/**
	 *	Check acceess control by looking up in database (CHECKME)
	 *
	 *	@param	string	$table	name of a database table
	 *	@param	string	$right	permission to check for (CHECKME)
	 *	@param	int		$pid	page id (CHECKME)
	 *	@return	array
	 */

	function acl_access($table, $right, $pid = -1) {
		$client = &CLIENT::singleton();
		if ($client->usr['id'] == CLIENT::__root()) { return 1; }
			
		$pid = $pid === -1 ? UTIL::get_post('pid') : $pid;
		if (-1 == $pid) { return array(); }
			
		if(!$this->CLIENT->usr['acl'][$table][$pid]){
			$db = &DB::singleton();
			$usr = &CLIENT::singleton();
			$uid = (int)$usr->usr['id'];
			$gid = array_keys($usr->usr['groups']);
			
			// this looks like it could be improved upon

			$sql = ''
			 . 'SELECT BIT_OR(' . $table . '_acl.ar) AS ar'
			 . ' FROM ' . $table . '_acl'
			 . ' WHERE'
			 . ' ('
			 . '  (' . $table.'_acl.type=' . JIN_ACL_TYPE_USER . ' AND aid=' . $uid .') '
			 . ' OR '
			 . '  (' . $table . '_acl.type=' . JIN_ACL_TYPE_GROUP . ' AND aid IN (' . implode(',', $gid) . ') )'
			 . ' )'
			 . ' AND id=' . $pid;

			$res = $db->query($sql);
				
			$row = $res->r();

			$this->CLIENT->usr['acl'][$table][$pid] = $row['ar'];
					
		}

		// check dis!!!
		#MC::debug($this->CLIENT->usr['acl']);
		return $this->acl_check($table, $right, $this->CLIENT->usr['acl'][$table][$pid]);
	}

	/**
	 *	Get array of modules and their access rights for the current user
	 *
	 *	Optional argument $uid returns the rights for this specific user
	 *
	 *	[de] liefert array von modulen und deren zugriffsrechten fuer den aktuellen user optional
	 *	[de] parameter uid returns the rights for this specific user
	 *
	 *	@param		int		$pid		Page ID? (CHECKME)
	 *	@param		int		$uid		User ID? (CHECKME)
	 *	@return	array
	 */

	function get_access_rights($pid = -1, $uid = -1) 
	{
		// if user is root user....
		if ($uid !== -1) { $client = &CLIENT::client_with_id($uid);}
		else { $client = &CLIENT::singleton();}
		
		$db = &DB::singleton();
			
		if($client->usr['id'] == CLIENT::__root())
		{
			// get all mods
			$select = "SELECT id FROM sys_module";
			$res = $db->query($select);
			while ($res->next()) { $this->access_rights[$res->f('id')] = 9999999999; }
			$ar_read[$pid] = true;
			return $this->access_rights;
		}
			
		// else...

		//	[en] Access rights for modules, for current user to read from page.
		//	[en] User rights are inherited/copied from groups (CHECK TRANSLATION)
		//	[de] zugriffsrechte fuer module, fuer aktuellen user aus seite lesen user-rechte werden
		//	[de] zu gruppen-rechten addiert

		$opc = &OPC::singleton();
		$ar = array();		// mid => ar

		if ($pid  === -1) { $pid = $opc->get_pid(); }		// default to current page ID (CHECKME)

		$sql = ''
		 . "SELECT mid, ar FROM " . $this->db_tbl_grp_ar
		 . " WHERE pid=" . (string)$pid
		 . " AND gid IN (" . implode(',', array_keys($client->usr['groups'])) . ")";
				
		$res = $db->query($sql);
		while ($res->next()) { $ar[$res->f('mid')] = (int)$ar[$res->f('mid')] | $res->f('ar'); }

		$sql = ''
		 . "SELECT mid, ar FROM " . $this->db_tbl_usr_ar
		 . " WHERE pid=" . (string)$pid . " AND uid=" . (string)$client->usr['id'];

		$res = $db->query($sql);
		while ($res->next()) { $ar[$res->f('mid')] = (int)$ar[$res->f('mid')] | $res->f('ar'); }

		$this->access_rights = $ar;
		#$ar_read[$pid] = true;
			
		return $this->access_rights;
	}

	/**
	 *	Create or grant an access right? (CHECKME)
	 *	@param	string	$func		index in access file
	 *	@param	string	$mod		module name
	 *	@param	int		$pid		page id to grant access right to
	 *	@param	int		$id			user or group id 
	 *	@param	string	$type		default: usr other: grp
	 *	@return void
	 */

	function set_access_rights($func, $mod, $pid, $id = -1, $type = 'usr')
	{
		// unimplmented on review at 2012-02-16, strix
	}
		
	/**
	 *	[en] (DOCUMENTME) makes a nested array of page and module ids, something to do with 
	 *	[en] permissions, appears to be unused except for some call in class_pageView, perhaps
	 *	[en] should be removed to there.
	 *
	 *	[de] liefert ein array aller f�r diese seiten mit rechten versehenen module	
	 *
	 *	@param	int		$pid	Optional page id, current page is used if unspecified
	 *	@return	array
	 */

	function get_page_mods($pid = -1) {
		$opc = &OPC::singleton();
			
		if (-1 == $pid) { $pid = (int)$opc->get_pid(); }

		static $known_mods = array();
		if (array_key_exists($pid, $known_mods)) { return $known_mods[$pid]; }
			
		$db = &DB::singleton();
		$known_mods[$pid] = array();

		$sql = ''
		 . 'SELECT DISTINCT mid FROM ' . $this->db_tbl_usr_ar
		 . ' WHERE pid=' . (string)$pid;

		$res = $db->query($sql);
		while($res->next()) { $known_mods[$pid][$res->f('mid')] = $res->f('mid'); }

		$sql = ''
		 . 'SELECT DISTINCT mid FROM ' . $this->db_tbl_grp_ar
		 . ' WHERE pid=' . (string)$pid;

		$res = $db->query($sql);
		while($res->next()) { $known_mods[$pid][$res->f('mid')] = $res->f('mid'); }
			
		return $known_mods[$pid];
	}
		
	/**
	 *	[en] Loads and returns table of installed modules
	 *	[en] Return value is array of {'id', 'modul_name', 'label', 'sys_trashcan', 'virtual'}
	 *	[de] liefert alle module, die 'installiert' sind, existieren
	 *
	 *	@return	array
	 */

	function get_mods() {
		$db = &DB::singleton();

		$sql = ''
		 . 'SELECT * FROM ' . $this->db_tbl_mod   /* table: sys_module */
		 . ' WHERE 1 ' . $db->table_restriction($this->db_tbl_mod)
		 . ' ORDER BY label ASC';

		$res = $db->query($sql);
		return $res->get();
	}

	/**
	 *	Get events supported by a module
	 *
	 *	This appears to read the PHP switch statement which is usually in the 'main' method of the 
	 *	modAction class.
	 *
	 *	@param	string	$mod	name of a module
	 *	@return	array
	 */

	function get_mod_events($mod)
	{
		$class_file = "/mods/" . $mod . "/class_" . $mod . "Action.inc.php";
		$f = UTIL::file_get_contents(CONF::inc_dir() . $class_file);	// TODO: handle error cases
		$event = /*. (string[string]) .*/ array();

		if(CONF::get("checksum_" . $mod) != md5($f))
		{
			CONF::set("checksum_" . $mod,md5($f));
			$functions = explode("function ", $f);
			$func_main = '';

			// isolate the first function whose name begins with 'main'
			foreach($functions as $function_body)
			{
				if('main' === substr($function_body, 0, 4))
				{
					$func_main = $function_body;
					break;
				}
			}
			if ('' === $func_main) { return $event; }

			// break up the switch statement
			$cases = explode("case ", $func_main);
			unset($cases[0]);
			foreach($cases as $e)
			{
				$e = explode(":",$e);
				$tmp = (string)preg_replace("/'/", "", $e[0]);
				$tmp = (string)preg_replace('/"/', '', $tmp);
				$event[trim($tmp)] = trim($tmp);
			}
			CONF::set("events_" . $mod, $event);
			return $event;
		}
		return CONF::get("events_" . $mod);
	}

	/**
	*	[en] returns the ID of a module given its name
	*	[de] liefert die id eines moduls
	*
	*	@param	string	$modul	modul-name
	*	@return	int
	*/

	public function get_modul_id($modul) {
		//TODO: handle error cases
		static $map_mod = array();
			
		if (0 == count($map_mod)) {
			$db = &DB::singleton();
			$res = $db->query('SELECT * FROM ' . $this->db_tbl_mod);
			while($res->next()) {
				$map_mod[$res->f('modul_name')] = $res->f('id');
			}
		}

		return @(int)$map_mod[$modul];
	}

	/**
	 *	[en] return the name of a module given its id, empty string on failure
	 *	[de] liefert den namen eines moduls
	 *
	 *	@param	int		$mid	module ID
	 *	@return	string
	 */

	function get_modul_name($mid) {
		static $map_mod = array();

		if (0 == count($map_mod)) {
			$db = &DB::singleton();
			$res = $db->query('SELECT * FROM '. $this->db_tbl_mod);
			while ($res->next()) { $map_mod[(string)$res->f('modul_name')] = (int)$res->f('id'); }
		}
			
		foreach ($map_mod as $name => $id)
		{
			if ((int)$id == $mid) { return (string)$name; }
		}
		
		return '';
	}

	/**
	 *	instantiate a class
	 *
	 *	[en] (very widely used, TODO: examine more closely, strix 2012-02-17)
	 *	[en] PHPLint does not like anything about this, TODO: figure out how to make it happy
	 *	[de] eine klasse instanzieren
	 *
	 *	@param	string	$class_name class name (kurform: eg, 'validator')
	 *	@param	mixed	$a	constructor argument, optional
	 *	@param	mixed	$b	constructor argument, optional
	 *	@return	object
	 */

	public function create($class_name, $a = null, $b = null) {
		$prefixed = constant('JIN_CLASS_' . strtoupper($class_name));
		list($package, $class_file) = explode('/', (string)$prefixed);
		require_once(CONF::inc_dir() . '/' . $package . '/oos/' . $class_file);

		if (is_null($a) && is_null($b)) { return (new $class_name()); }
		if (is_null($b)) { return (new $class_name($a)); }
		return (new $class_name($a, $b));
	}

	/**
	 *	Debug function can be invoked statically
	 *	Created: 12 05 2004
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
	 *	@version		1.0
	 *	@since			1.0
	 *	@param	string	$content	content of debug message
	 *	@param	string	$title		title of debug message
	 *	@return	void
	 */

	public function debug($content, $title = '') {
		ob_start();
		echo '<pre>DEBUG:' . $title . "----\n";
		print_r($content);
		echo "\n----</pre>";
		$ret = ob_get_contents();
		ob_end_clean();
			
		echo $ret;
	}

	/**
	 *	Add a log entry, creating the log file if it does not exist
	 *
	 *	@param	string	$log	Message or data to log
	 *	@param	string	$name	Name of log file, optional
	 *	@return	string	Location of the written log file
	 */
	function log($log, $file = null)
	{
		if($file === null)
		{
			$file = CONF::inc_dir() . "/tmp/default.log";
		}
		elseif(!is_file($file))
		{
			return;
		}
		//TODO: check for and handle failure, strix 2012-02-17
		//note: 'a+' will create file if it does not exist and set pointer to end of file
		$handle = fopen($file, "a+");
		fwrite($handle, date("r") . "\n" . var_export($log, true) . "\n");
		fclose($handle);
		
		return $file;
	}
	/**
	 *	send a log-message as mail. can be invoced statically
	 *
	 *	@param	string	$msg	Message to be emailed
	 *	@param	string	$from	Email address
	 *	@param	string	$type	Label used in subject, optional
	 *	@return void
	 */

	function send_log($msg, $from, $type = 'warning') 
	{
		$type    = (string)str_replace(array("\n", "\r"), array('-', '-'), $type);
		$from    = (string)str_replace(array("\n", "\r"), array('-', '-'), $from);
		$msg     = (string)str_replace(array("\n", "\r"), array('-', '-'), $msg);
		$subject = '[OOS] MC::send_log on ['.CONF::project_name().'] type ['.$type.']';
		$mailto  = 'thomas.schindler@hundertelf.com, joe@menschheit.org';
		$body    = "Hello!\nThis is MC::send_log with a message for you:\n\n".
					"Project: ".CONF::project_name()."\nDomain:  ".CONF::baseurl()."\n\n".
					"Sender:  " . $from . "\nType:    ".$type."\nMessage: ".$msg."\n\n".
					"End of message. Have a nice day";

		//TODO: check for and handle failure, strix 2012-02-17
		@mail($mailto, $subject, $body);
	}


	/***********************************************************************************************
	 *	start of listening functions
	 **********************************************************************************************/


	/**
	 *	register the listeners to the current instance
	 *	creates an object var -  array of all params needed
	 *	this array is called listeners
	 *	@return array
	 */

	function collect_listeners() {
		$db = &DB::singleton();
		$listeners = array();

		$sql = ''
		 . "SELECT mod_shout, event_shout, mod_listen, event_listen, att_listen, pre"
		 . " FROM " . $this->tbl_listeners
		 . " WHERE start <= '" . time() . "' AND stop >= '" . time() . "'";

		$res = $db->query($sql);


		while ($res->next()) {

			$att = @unserialize($res->f('att_listen'));
			if (!is_array($att)) { $att = array(); }
				
			$mod_shout = (string)$res->f('mod_shout');
			$event_shout = (string)$res->f('event_shout');
			$mod_listen = (string)$res->f('mod_listen');
			$event_listen = (string)$res->f('event_listen');
			$pre = (int)$res->f('pre');

			$params = array
			(
				'mod' => array('mod' => $mod_listen, 'event' => $event_listen),
				'att' => $att
			);

			$listeners[$mod_shout][$event_shout][$pre][] = $params;
		}
		$this->listeners_collected = true;
		$this->listeners = $listeners;
		return $this->listeners;
	}

	/**
	 *	call all mods with the appropriate values that are registered to listen for a certain
	 *	module and event
	 *
	 *	@param	string	$mod		name of a module
	 *	@param	string	$event		name of an event
	 *	@param	int		$position	(DOCUMENTME) (CHECKME)
	 *	@param	array	$add_params	extra parameters to pass to event? (CHECKME)
	 *	@return bool
	 */

	function call_listeners($mod, $event, $position = 0, $add_params = null)
	{
		//TODO: tidy, make clear to PHPLint, strix 2012-02-17
		if (!$this->listeners_collected) 
		{ 
			$this->listeners = $this->collect_listeners();
		}
		if (!is_array(@$this->listeners[$mod])) { return true; }
		if (!is_array(@$this->listeners[$mod][$event])) { return true; }
		if (!is_array(@$this->listeners[$mod][$event][$position])) { return true; }

		$position_array = $this->listeners[$mod][$event][$position];

		foreach ($position_array as $item)
		{
			$_param = array
			(
				'mod_shout' => $mod, 
				'event_shout' => $event,
				'att' => $item['att'],
				'add_params' => $add_params
			);
			$ret = $this->call_action($item['mod'], $_param);
			if (false === $ret) { return false; }
		}
		return true;
	}

	/**
	 *	Register a listener to the listener table
	 *
	 *	@param	string	$mod_shout		Name of a module
	 *	@param	string	$event_shout		Name of an event
	 *	@param	string	$mod_listen		Name of a module
	 *	@param	string	$event_listen	Name of an event
	 *	@param	string	$att_listen		(DOCUMENTME), optional
	 *	@param	int		$start			(DOCUMENTME), optional
	 *	@param	int 	$stop			(DOCUMENTME), optional
	 *	@param	bool	$pre			(DOCUMENTME), optional
	 *	@return	int						ID of created listener in database
	 */

	function register_listener(
		$mod_shout, $event_shout,
		$mod_listen, $event_listen,
		$att_listen = null, $start = 0, $stop = 2147483647, $pre = false)
	{
		$db = &DB::singleton();
		//TODO: check and sanitize these values, strix 2012-02-17

		$insert = ''
		 . "INSERT INTO " . $this->tbl_listeners /* is 'sys_mc_listeners' */
		 . " (mod_shout, event_shout, mod_listen, event_listen, att_listen, start, stop, pre)"
		 . " VALUES"
		 . " ("
			 . "'" . $mod_shout . "', "
			 . "'" . $event_shout . "', "
			 . "'" . $mod_listen . "', "
			 . "'" . $event_listen . "', "
			 . "'" . (isset($att_listen) ? serialize($att_listen) : '' ) . "', "
			 . "'" . (string)$start . "', "
			 . "'" . (string)$stop . "', "
			 . "'" . ($pre ? '1' : '0') . "'"
		 . ")";

		$db->query($insert);		
		$id = (int)$db->insert_id();

		return $id;
	}

	/**
	 *	Update a listener
	 *	Created: 12 05 2004
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@param	int		$id
	 *	@param	string	$mod_shout		Name of a module
	 *	@param	string	$event_shout		Name of an event
	 *	@param	string	$mod_listen		Name of a module
	 *	@param	string	$event_listen	Name of an event
	 *	@param	string	$att_listen		(DOCUMENTME), optional
	 *	@param	int		$start			(DOCUMENTME), optional
	 *	@param	int 	$stop			(DOCUMENTME), optional
	 *	@param	bool	$pre			(DOCUMENTME), optional
	 *	@return	void
	 */

	public function update_listener(
		$id, $mod_shout, $event_shout, $mod_listen, $event_listen, 
		$att_listen = null, $start = 0, $stop = 2147483647, $pre = false)
	{
		$db = &DB::singleton();

		//TODO: check and sanitize these values before updating, strix 2012-02-17

		$sql = ''
		 . "UPDATE " . $this->tbl_listeners /* is 'sys_mc_listeners' */
		 . " SET"
			 . " mod_shout='$mod_shout',"
			 . " event_shout='$event_shout',"
			 . " mod_listen='$mod_listen',"
			 . " event_listen='$event_listen',"
			 . " att_listen='".serialize($att_listen)."',"
			 . " start='$start',"
			 . " stop='$stop',"
			 . " pre='$pre'"
		 . "WHERE id=" . $id;

		$db->query($sql);
		//return $db->insert_id();
	}
		
	/**
	*	returns all mods/events which the given module is listening to
	*	optionally, only a specific ID
	*	
	*	@param	string	$mod_listen		Name of a module
	*	@param	int		$id				Constrain to a single listener, optional
	*	@return	object	Returns a database result obejct
	*/

	function get_listen_targets($mod_listen, $id = -1) {
		$db = &DB::singleton();
		$sql = ''
		 . "SELECT * FROM " . $this->tbl_listeners
		 . " WHERE mod_listen='" . addslashes($mod_listen) . "'";

		if ($id !== -1) { $sql .= " AND id=" . $id; }
		//TODO: handle case of database error, strix 2012-02-17
		return $db->query($sql);
	}
			
	/**
	 *	unregisters a listener by id
	 *	@param	int		$id		The id of a listener in the table
	 *	@return	bool
	 */

	function unregister_listener($id)
	{
		$db = &DB::singleton();
		$delete = "DELETE FROM " . $this->tbl_listeners . " WHERE id = '" . $id . "'";
		//TODO: handle case of database error, strix 2012-02-17
		return $db->query($delete);
	}

}

?>
