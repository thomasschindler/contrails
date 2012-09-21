<?php
/**
 *	
 *	CLIENT - knows everything we need to know about the client
 *	
 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
 *	@date			12 05 2004
 *	@version		1.0
 *	@since			1.0
 *	package		system
 *
 *	CHANGE in timborowski: 12.2.7
 *	usradmin gets called directly and not via mc call to inhibit a client class recursion
 */

/*.
	require_module 'standard';
	require_module 'pcre';
.*/

class CLIENT {
	
	/**
	 *	Pointer to user session object
	 *	@var SESS
	 */

	public $SESS;
	
	/**
	 *	(DOCUMENTME)
	 *	@var array
	 */

	public $usr = array();

	/**
	 *	(DOCUMENTME)
	 *	@var string
	 */
		
	public $auth_sess_key = 'tcia';

	/**
	 *	(DOCUMENTME)
	 *	@var mixed
	 */
		
	public $pid;

	/**
	 *	(DOCUMENTME)
	 *	The __ prefix is reserved in PHP (TODO) name this something else
	 *	@var array
	 */
		
	private $__get_allowed_pages;

	/**
	 *	Pointer to MC (Master of Ceremonies) singleton
	 *	@var MC
	 */

	public $MC;

	/**
	 *	Pointer to OPC (Putput Controller) singleton
	 *	@var OPC
	 */

	public $OPC;

	/**
	 *	Pointer to database wrapper singleton
	 *	@var db_mysql
	 */

	public $DB;

	/*. forward public void function set_auth($info); .*/
	/*. forward public bool function is_auth(); .*/

	/**
	 *	constructor
	 *
	 *	(DOCUMENTME) this is fairly involved, could use a summary
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	 *	@date			12 05 2004
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@param	bool	$auth 			(DOCUMENTME)
	 *	@param	bool	$ignore_cookie 	(DOCUMENTME)
	 *	@param	bool	$quickreturn 	(DOCUMENTME)
	 *	@return	void
	 */

	public function __construct($auth = false, $ignore_cookie = false, $quickreturn = false) 
	{
		$this->SESS = &SESS::singleton();
		$this->DB = &DB::singleton();
		$this->MC = &MC::singleton();
		$this->OPC = &OPC::singleton();
			
		if ($quickreturn) { return; }
			
		if ($auth) {
			if (
				(isset($_COOKIE[CONF::project_name()])) &&
				(true !== $this->is_auth()) &&
				(false == $ignore_cookie)
			) {
				//	(TODO) potential SQL injection point vie cookie, sanitize this
				$select = ''
				 . "SELECT usr, pwd"
				 . " FROM mod_usradmin_usr"
				 . " WHERE MD5(usr) = '" . $_COOKIE[CONF::project_name()] . "'";
				$r = $this->DB->query($select);
					
				if ($r->nr() == 1)
				{
					$data =  array
					(
						'__encoded' => true,
						'pwd' => $r->f('pwd'),
						'usr' => $r->f('usr')
					);
					$usradmin = $this->MC->get_mod_instance("usradmin",false,false);
					$usradmin->DB = &DB::singleton();
					$check = $usradmin->action_usr_validate($data);
					if (!is_error($check)) 
					{
						$this->set_auth($check);
						$p = CONF::default_pages();
						// this page is not allowed to registered users usually - so we redirect them
							
						// this is tough stuff and needs some watching:
						$url = CONF::baseurl() . "/" . $_SERVER['REQUEST_URI'];
						////
							
						if($this->pid == $p['usr_register'])
						{
							if($p['after_login'])
							{
								$location = ''
								 . CONF::baseurl() . "/page_" . $p['after_login'] . ".html?"
								 . $this->SESS->name . "=" . $this->SESS->id;
								header('Location: ' . $location);
								die;
							}
							else
							{
								$location = ''
								 . CONF::baseurl() . "/"
								 . $this->OPC->lnk(array('pid' => CONF::pid()));
								header('Location: ' . $location);
								die;
							}
						}
						if($url)
						{
							if(preg_match("/" . $this->SESS->name . "/",$url))				
							{
								$url = preg_replace(
									"/" . $this->SESS->name . "=[0-9a-z]{" . strlen($this->SESS->id) . "}/i",
									$this->SESS->name . "=" . $this->SESS->id,
									$url
								);
							}
							elseif(preg_match("/\?/",$url))
							{
								$url .= "&".$this->SESS->name.'='.$this->SESS->id;
							}
							else
							{
								$url .= '?'.$this->SESS->name.'='.$this->SESS->id;
							}
							header('Location:'.$url);
							die;
						}
						elseif($p['after_login'])
						{
							$location = ''
							 . CONF::baseurl() . "/page_" . $p['after_login'] . ".html?"
							 . $this->SESS->name . "=" . $this->SESS->id;
							header('Location: ' . $location);
							die;
						}
						else
						{
							$location = ''
							 . CONF::baseurl() . "/"
							 . $this->OPC->lnk(array('pid' => $this->OPC->pid()));
							header('Location: ' . $location);
							die;
						}
						ob_end_flush();
					}
					return;
				}
			}
			elseif (true === $this->is_auth())
			{
				$this->usr = $this->SESS->get('client', 'usr');
			}
			else
			{
				$usradmin = $this->MC->get_mod_instance("usradmin", false, false);
				$usradmin->DB = &DB::singleton();
				$default_usr = $usradmin->action_get_default_usr();	
			    $this->set_auth($default_usr);
			}
			$lang = e::lang();
			define("USR_LANG", $lang[$this->usr['lang']]);
		}
		else
		{
			$usradmin = $this->MC->get_mod_instance("usradmin");
			$usradmin->DB = &DB::singleton();
			$default_usr = $usradmin->action_get_default_usr();
			$this->set_auth($default_usr);
		}
		if(strlen(trim($this->usr['usr'])) == 0)
		{
			die("whooa");		/* (TODO) better handling of this case */
		}
			
	}

	/**
	 *	become the superuser
	 *
	 *	@param	int		$uid	User ID
	 *	@return	void
	 */

	function su($uid = null)
	{
		if(!$uid)		/* (TODO) type-safe check */
		{
			/*
			$usr = $this->SESS->get('client', 'usr');
			$usr['id'] = $this->__root();
			$this->SESS->set('client', 'usr', $usr);
			$this->usr = $usr;
			*/
			$uid = CONF::su();
		}

		//	(TODO) use db_crud to sanitize this
		$select = "SELECT * FROM mod_usradmin_usr WHERE id = " .$uid;
		$usr = $this->DB->query($select);
		$params = $usr->r();
		$params['__encoded'] = true;

		//	found commented out at 2012-05-03
		#$usr = $this->action_usr_validate($params);

		$action = array('mod' => 'usradmin', 'event' => 'usr_validate');
		$this->usr = $this->MC->call_action($action, $params);
		return;
	}

	/**
	 *	authenticate the client
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	 *	@date			12 05 2004
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@param	array	$info	(DOCUMENTME) array of clientinfo
	 *	@return	void
	 */

	public function set_auth($info) 
	{
		$this->usr = $info;
		$this->SESS->set('client', $this->auth_sess_key, true);
		$this->SESS->set('client', 'usr', $info);
		$lang = e::lang();			
		define("USR_LANG_LOG",$lang[$this->usr['lang']]);		
	}

	/**
	 *	returns the root id
	 *	(DOCUMENTME) what is the root id?
	 *	@return	int
	 */

	function __root(){
		static /*. int .*/ $__root;
		if(!$__root){
			$__root = CONF::su();
		}
		return $__root;
	}

	/**
	 *	destroys the session for the current client
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	 *	@date			12 05 2004
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@return	void
	 */

	public function unset_auth(){
		$this->SESS->set('client', $this->auth_sess_key, false);
		session_destroy();
	}

	/**
	 *	Discovers if a user exists given a user id or 'usr' (email address)
	 *
	 *	(TODO) return INT -1, rather than BOOL false to indicate failure.
	 *
	 *	@param	int		$ID		registered email address of a user (checkme)
	 *	@return	int
	 */

	function is_usr($ID = null){
		if (!$ID) { return false; }		/*	(TODO) type-safe check */

		//	(TODO) template this query
		$select = ''
		 . "SELECT id FROM mod_usradmin_usr"
		 . " WHERE usr = '" . $this->DB->escape($ID) . "'"
		 . " OR id = '" . $this->DB->escape($ID) . "'";

		$res = $this->DB->query($select);

		if ($res->nr() != 1) { return false; }
		return $res->f('id');
	}

	/**
	 *	is the client authenticated?
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	 *	@date			12 05 2004
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@return	bool	the auth_sess_key
	 */

	public function is_auth() 
	{
		return (bool)$this->SESS->get('client', $this->auth_sess_key);
	}

	/**
	 *	
	 *	adds info to the current session
	 *
	 * 	(DOCUMENTME) structure if $info array
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	 *	@date			12 05 2004
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@param	array	$info	array of clientinfo
	 *	@return	void
	 */

	public function set_info($info)
	{
		foreach($info as $key => $val) { $this->usr[$key] = $val; }
		$this->SESS->set('client', 'usr', $this->usr);
	}

	/**
	 *	set the clients capabilities to the session
	 *	
	 *	Client capabilities are:
	 *
	 *		'flash'			- (DOCUMENTME) discovery process, possible values
	 *		'screen'		- (DOCUMENTME) discovery process, value format
	 *		'javascript		- (DOCUMENTME) discovery process, possible values
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	 * 	@date			12 05 2004
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@param	array	$param	set of browser capabilites
	 *	@return	void
	 */
	
	public function set_umgebung($param){
		// store in session / in die session legen
		SESS::set('login','flash', $param['flash']);
		SESS::set('login','screen', $param['screen']);
		SESS::set('login','javascript', $param['javascript']);		
	}

	/**
	 *	the client knows all the pages he is allowed to see:
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	 *	@date			12 05 2004
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@param	array	$distinct_gids	(DOCUMENTME) flat array of integers?
	 *	@return	array	set of the pids the client is allowed to use
	 */

	public function get_allowed_pages($distinct_gids = null)
	{

		if(!$this->__get_allowed_pages || $distinct_gids)
		{
			$allowed_ids = array();
			$this->__get_allowed_pages = array();
			// do something for root:
				
			if (($this->usr['id'] == $this->__root()) && (!$distinct_gids))
			{
				$select = "SELECT id FROM mod_page";
				$res = $this->DB->query($select);
				while($res->next()) { $allowed_ids[] = $res->f('id'); }
				$allowed_ids = array_unique($allowed_ids);
				
				if ($distinct_gids) 	/* (TODO) type-safe check */
				{
					return $allowed_ids;		//..................................................
				}
					
				foreach ($allowed_ids as $id) { $this->__get_allowed_pages[$id] = $id; }

				return $this->__get_allowed_pages;		//..........................................
			}
				
			// do something for the rest
			if(is_array($distinct_gids))
			{
				//	(TODO) template this query
				$sql = ''
				 . 'SELECT id FROM mod_page_acl'
				 . ' WHERE'
				 . ' ('
					 . ' aid IN (' . implode(',', $distinct_gids) . ')'
					 . ' AND type=' . JIN_ACL_TYPE_GROUP
				 . ') '
				 . ' AND (ar & 1) = 1';
			}
			else
			{						
				if (!is_array($this->usr['groups'])) { return array(); }	//......................
				
				$gids = array_keys($this->usr['groups']);

				//	(TODO) template this query
				$sql = ''
				 . 'SELECT id FROM mod_page_acl'
				 . ' WHERE'
				 . ' ('
					 . '(aid=' . $this->usr['id'] . ' AND type='.JIN_ACL_TYPE_USER.') '
					 . 'OR (aid IN ('.implode(',', $gids).')'
					 . ' AND type='.JIN_ACL_TYPE_GROUP.')'
				 . ' )'
				 . ' AND (ar & 1) = 1';
			}

			$res = $this->DB->query($sql);		
				
			while($res->next())
			{
				array_push($allowed_ids,$res->f('id'));
			}

			$allowed_ids = array_unique($allowed_ids);
			if($distinct_gids)
			{
				return $allowed_ids;		//......................................................
			}

			foreach ($allowed_ids as $id) { $this->__get_allowed_pages[$id] = $id; }
		}
		return $this->__get_allowed_pages;
	}
		
	/**
	 *	(DOCUMENTME)
	 *
	 *	@return	array	(DOCUMENTME) structure of this array
	 */

	function get_my_pages()
	{
		static $get_my_pages;
		if(!$get_my_pages)
		{
			$pids = $this->get_allowed_pages();
			$set = new NestedSet();
			$set->set_table_name("mod_page");
			$pages = $set->getNodes(1,"*"," AND mod_page.id IN (".implode(", ",$pids).") ");
			foreach($pages as $p)
			{
				$get_my_pages[$p['id']] = $p;
			}
		}
		return $get_my_pages;
	}

	/**
	*	Remotely read values (uses browsecab.ini, extension must be registered in php.ini)
	*
	*	[de] remote-werte werden ausgelesen [verwendet browscab.ini] muss am server liegen
	*	[de] und wird in der php.ini eingetragen
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*
	*	@param	string	$browser_var	desired attribute
	*	@return	string					value of desired attribute
	*/

	public function remote($browser_var){
			static $browser_ret;
			if(!$browser_ret){

			$browser_ret = array(
				'browser_komp' 		=> $browser[5],
				'plattform' 		=> $browser->platform,
				'browser_name' 		=> $browser->browser,
				'browser_version' 	=> $browser->majorver,
				'browser_js' 		=> $browser->javascript,
				'css'		 		=> 1, // keine ahnung, was olli meinte
				'ip'		 		=> $_SERVER['REMOTE_ADDR'],
				'aol'		 		=> 0,	// keine ahnung, was olli meinte
				'uri'		 		=> $_SERVER['REQUEST_URI'],
				'status'	 		=> $_SERVER['REDIRECT_STATUS'],
				'sprache'	 		=> $_SERVER['HTTP_ACCEPT_LANGUAGE'],
				'port'	 	 		=> $_SERVER['SERVER_PORT'],
				'flash' 			=> SESS::get('login', 'flash'),
				'screen' 			=> SESS::get('login', 'screen'),
				'javascript' 		=> SESS::get('login', 'javascript'),
			);

		}

		return $browser_ret[$browser_var];
		//SESS::get('login', 'javascript'),
	}

	/**
	 *	returns the client object for the given uid
	 *
	 *	@param	int		$uid	ID of user record
	 *	@return	CLIENT
	 */

	function &client_with_id($uid)
	{
		static $instance_list;
		if(!is_object($instance_list[$uid]))
		{
			$instance_list[$uid] = new CLIENT(false, false, true);
			$mc = &MC::singleton();
			$instance_list[$uid]->usr = $mc->call('usradmin', 'usr_validate', array('uid' => $uid));
		}
		return $instance_list[$uid];
	}

	/**
	 *	used for perfomance optimization
	 *
	 *	every creation of client object has to use this in the following form:
	 *
	 *		new_object = &CLIENT::singleton()
	 *	
	 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	 *	@date			12 05 2004
	 *	@version		1.0
	 *	@since			1.0
	 *
	 *	@param	array	$params			Additional arguments for constructor (DOCUMENTME)
	 *	@param	bool	$ignore_cookie	(DOCUMENTME)
	 *	@return			an object of client
	 */

	public function &singleton($params = null, $ignore_cookie = false) 
	{
		static $instance;	
		if (!is_object($instance)) 
		{
			$instance = new CLIENT($params,$ignore_cookie);
		}
		return $instance;
	}

}
?>
