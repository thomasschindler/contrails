<?php

/**
 *	OPC - OutPut Controller
 *	
 *	responsible for generating the output of the modules
 *	Created: 12 05 2004
 *	
 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@version		1.0
 *	@since			1.0
 *	@package		system
 */

/*.
	require_module 'standard';
	require_module 'hash';
	require_module 'regex';
	require_module 'pcre';
	require_module 'gd';
.*/

class OPC 
{


	public $persistent_start_view = false;

	/**
	 *	for setting the current scope of call_view
	 *	@var	array
	 */

	private $current_scope = array('mod'=>'','vid'=>'','view'=>'');

	/**
	 *	for collecting flash errors? (CHECKME)
	 *	@var	array
	 */

	private $_flash_error = array();

	/**
	 *	for collecting flash success (CHECKME)
	 *	@var	array
	 */

	private $_flash_success = array();

	/**
	 *	for collecting flash warnings (CHECKME)
	 *	@var	array
	 */

	private $_flash_warning = array();

	/**
	 *	(DOCUMENTME)
	 *	@var	array
	 */

	private $_flash_information = array();

	/**
	 *	map for cleaning the url from unallowed characters
	 *	@var array
	 */

	private $clean_map;

	/**
	 *	array - representation of the page structure
	 *	is set by page or other modules-
	 *	is saved as serialized array with the page
	 *	@var array
	 */

	private $__struct;

	/**
	 *	stack is an array
	 *	used by call_view to adress the current value in struct
	 *	@var array
	 */

	private $__stack =  array(0);

	/**
	 *	array for handling changes in struct
	 *	@var array
	 */

	private $__keys;

	/**
	 *	array that holds the vids of all actual calls on the current page.
	 *	needed for cleaning up struct
	 *	is updated in all positionhandling functions
	 *	@var array
	 */

	private $__vid_coll = array();

	/**
	 *	internes array, in dem module werte/variablen speichern koennen
	 *	um sie im template wiederzuverwenden
	 *	see:	set_var() get_var()
	 *	@var	array	$_vars
	 */

	private $_vars = array();
		
	/**
	 *	[en] pointer to global session instance
	 * 	[de] zeiger auf globale session-instanz
	 *	@var	object	SESS
	 *	@see	SESS
	 */

	private $SESS;

	/**
	 * 	(DOCUMENTME)
	 *	@var array
	 */

	private $lnk_add = array();

	/**
	 *	[en] Array in which class to set_view are recorded? (CHECKME)
	 *	[de] array in dem wir uns die ueber set_view()
	 *	[de] gesetzten views merken
	 *	@var array
	 */

	private $set_view = array();

	/**
	 *	[en] current pid/PageID
	 *	[de] aktuelle pid (PageID)
	 *	@var int
	 */

	private $pid = 0;
		
	/**
	 *	(DOCUMENTME)
	 *	@var string
	 */

	private $start_view = '';

	/**
	 *	(DOCUMENTME)
	 *	@var string
	 */

	private $default_layout = 'oos';

	/**
	 *	(DOCUMENTME)
	 *	@var array
	 */

	private $lang_page = array();

	/**
	 *	Pointer to current location at top of $this->lang_page (CHECKME)
	 *	@var int
	 */

	private $lang_page_stack = 0;

	/**
	 *	(DOCUMENTME)
	 *	@var object
	 */
	
	private	$BURC;

	/**
	 *	(DOCUMENTME)
	 *	@var object
	 */
	
	private	$DBURC;

	/**
	 *	(DOCUMENTME)
	 *	@var object
	 */

	private	$SCS;

	/**
	 *	Array for holding additional icons registered alongside the default set
	 *	@var array
	 */

	private $register_icon = array();

	/**
	 *	Name of current module (CHECKME)
	 *	@var string
	 */

	private $__current_mod = '';

	/**
	 *	ID of current view - md5 hash (CHECKME)
	 *	@var string
	 */

	private $__current_vid = '';

	/*. forward public array function clean_map(); .*/
	/*. forward public int function set_pid(int $pid); .*/
	/*. forward public void function handle_link_type(string $type=); .*/
	/*.	forward public string function show_icon(string $name, bool $raw=); .*/

	/**
	 * 	Constructor, PHPLint does not like this one bit TODO: make PHPLint happy, strix 2012-02-17
	 *	@return void
	 */

	
	function __construct() 
	{
		$this->clean_map = $this->clean_map();
		$this->SESS = &SESS::singleton();
		$this->DB = &DB::singleton();
		$this->MC = &MC::singleton();

		$this->set_pid(UTIL::get_post('pid'));
		$this->handle_link_type(); 
		$this->start_view = CONF::start_view();
		$this->default_layout = CONF::default_layout();
	}

	

	/**
	*	return the form_error
	*	@return array
	*/	

	function form_error()
	{
		return $this->_form_error;
	}
	
	/**
	*	set the form_error
	*	@param $form_error array
	*	@return bool
	*/	

	function form_error_set($form_error)
	{
		$this->_form_error = $form_error;
		return true;
	}

	/**
	*	return the vars
	*	@return array
	*/	
	
	function vars()
	{
		return $this->_vars;
	}

	/**
	*	set the vars
	*	@param string key1
	*	@param string key2
	*	@param mixed value
	*	@return bool
	*/	
	
	function vars_set($key1,$key2=null,$value)
	{
		if($key2)
		{
			$this->_vars[$key1][$key2] = $value;
		
		}
		else
		{
			$this->_vars[$key1] = $value;
		}
		return true;
	}

	/**
	*	return the set_view
	*	@return array
	*/	
	
	function set_view_get()
	{
		return $this->set_view;
	}	
	
	/**
	*	return the struct
	*	@return array
	*/	
	
	function struct()
	{
		return $this->__struct;
	}

	/**
	*	return the current module
	*	@return string
	*/	
	
	function current_mod()
	{
		return $this->__current_mod;
	}
	
	/**
	*	return the pid
	*	@return int
	*/
	
	function pid()
	{
		return $this->pid;
	}
	
	/**
	*	set the pid
	*	@param int $pid
	*	@return bool
	*/
	
	function pid_set($pid)
	{
		$this->pid = $pid;
		return true;
	}
	
	/**
	 *	use the famfamfam icon set
	 *	@param	string	$which		Name of an icon in the set
	 *	@param	string	$lnk		A url or other valid HREF
	 *	@param	string	$onclick	Javascript to be on when link is clicked
	 *	@param	string	$css		A style attrib, probably (CHECKME)
	 *	@return	string
	 */                          

	function famfamfam($which, $lnk = '', $onclick = '', $css = '')
	{                    
		$wrap = array('', '');
		$onclicklink = '';
		if ('' != $onclick) { $onclicklink = 'onclick="' . $onclick . ';"';}
		if ('' != $lnk)
		{
			$wrap[0] = '<a href="'.$lnk.'" border="0" '.$onclicklink.'>';
			$wrap[1] = '</a>';
			$onclicklink = '';
		}

		$img = ''
		 . '<img ' . $css . ' src="/system/img/famfamfam/' . $which . '.png"'
		 . ' width="16" height="16" ' . $onclicklink . '>';

		return implode($img, $wrap);
	}

	/**
	*	log feedback (error/warning/success/information) to the db
	*	@param 	string 	$message 	the message
	*	@param 	string 	$type 		the type of feedback
	*	@return bool
	*/
	
	function feedback_log($message,$type)
	{
		$backtrace = debug_backtrace(false);
		//	we don't need to log this twice
		$i = 2;
		if ($backtrace[$i]['function'] == 'redirected_view_perform') { return false; }
		$c = &CLIENT::singleton();

		//	previous unsanitized version, remove once CRUD is tested
		#$this->DB->query("INSERT INTO sys_feedback_log (
		#	sys_feedback_log.time,
		#	sys_feedback_log.uid,
		#	sys_feedback_log.session,
		#	sys_feedback_log.type,
		#	sys_feedback_log.message,
		#	sys_feedback_log.class,
		#	sys_feedback_log.function,	
		#	sys_feedback_log.line,	
		#	sys_feedback_log.args
		#	) VALUES (
		#	".time().",
		#	".$c->usr['id'].",
		#	'".mysql_real_escape_string($this->SESS->id)."',
		#	'".mysql_real_escape_string($type)."',
		#	'".mysql_real_escape_string($message)."',
		#	'".$backtrace[$i]['class']."',
		#	'".$backtrace[$i]['function']."',
		#	'".$backtrace[$i]['line']."',
		#	'".base64_encode(serialize($backtrace[2]['args']))."'
		#)");

		$crud = new db_crud();

		$values = array(
			'time' => time(),
			'uid' => $c->usr['id'],
			'session' => $this->SESS->id,
			'type' => $type,
			'message' => $message,
			'class' => $backtrace[$i]['class'],
			'function' => $backtrace[$i]['function'],
			'line' => $backtrace[$i]['line'],
			'args' => base64_encode(serialize($backtrace[2]['args']))
		);

		$check = $crud->create('sys_feedback_log', $values, array());
		return $check;
	}
	
	/**
	 *	add errors info
	 *	@param	string	 $s		An error message? (CHECKME)
	 *	@return	array
	 */

	function error($s = '',$mod=null,$view='next',$vid='all')
	{                     
		// if we pass a string:
		if('' != $s) 
		{ 
			if($mod !== null)
			{
				// get the scope
				$tmp = $this->SESS->get('_flash_error',$mod);
				if(!is_array($tmp))
				{
					$tmp = array();
				}
				$tmp[$view][$vid] = $s;
				$this->SESS->set('_flash_error',$mod,$tmp);
			}
			else
			{
				$this->feedback_log($s,'error');
				return $this->_flash_error[] = $s;	
			}
		}
		return $this->_flash_error;
	}

	/**
	 *	add warning info
	 *	@param	string	 $s		A message? (CHECKME)
	 *	@return	array
	 */

	function warning($s = '',$mod=null,$view='next',$vid='all')
	{                     
		// if we pass a string:
		if('' != $s) 
		{ 
			if($mod !== null)
			{
				// get the scope
				$tmp = $this->SESS->get('_flash_warning',$mod);
				if(!is_array($tmp))
				{
					$tmp = array();
				}
				$tmp[$view][$vid] = $s;
				$this->SESS->set('_flash_warning',$mod,$tmp);
			}
			else
			{
				$this->feedback_log($s,'warning');
				return $this->_flash_warning[] = $s;	
			}
		}
		return $this->_flash_warning;
	}

	/**
	 *	add success info
	 *	@param	string	 $s		A message? (CHECKME)
	 *	@return	array
	 */

	function success($s = '',$mod=null,$view='next',$vid='all')
	{
		// if we pass a string:
		if('' != $s) 
		{ 
			if($mod !== null)
			{
				// get the scope
				$tmp = $this->SESS->get('_flash_success',$mod);
				if(!is_array($tmp))
				{
					$tmp = array();
				}
				$tmp[$view][$vid] = $s;
				$this->SESS->set('_flash_success',$mod,$tmp);
			}
			else
			{
				$this->feedback_log($s,'success');
				return $this->_flash_success[] = $s;	
			}
		}
		return $this->_flash_success;
	}

	/**
	 *	record some informational message? (CHECKME)
	 *	@param	string	 $s		A message? (CHECKME)
	 *	@return	array
	 */

	function information($s = '',$mod=null,$view='next',$vid='all')
	{
		// if we pass a string:
		if('' != $s) 
		{ 
			if($mod !== null)
			{
				// get the scope
				$tmp = $this->SESS->get('_flash_information',$mod);
				if(!is_array($tmp))
				{
					$tmp = array();
				}
				$tmp[$view][$vid] = $s;
				$this->SESS->set('_flash_information',$mod,$tmp);
			}
			else
			{
				$this->feedback_log($s,'information');
				return $this->_flash_information[] = $s;	
			}
		}
		return $this->_flash_information;  
	}

	/**
	*	put all alerts for the current view/mod/vid in their respective scopes
	*/

	function alerts_update()
	{
		// check the session for the current scope
		// information
		$tmp = $this->SESS->get('_flash_information',$this->current_scope['mod']);
		if(is_array($tmp))
		{
			$view = $this->current_scope['view'];
			$vid = $this->current_scope['vid'];
			if($tmp[$view][$vid])
			{
				$this->_flash_information[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$view = $this->current_scope['view'];
			$vid = 'all';
			if($tmp[$view][$vid])
			{
				$this->_flash_information[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$view = 'next';
			$vid = $this->current_scope['vid'];
			if($tmp[$view][$vid])
			{
				$this->_flash_information[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);
			}
			$view = 'next';
			$vid = 'all';
			if($tmp[$view][$vid])
			{
				$this->_flash_information[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$this->SESS->set('_flash_information',$this->current_scope['mod'],$tmp);
		}
		// warning
		$tmp = $this->SESS->get('_flash_warning',$this->current_scope['mod']);
		if(is_array($tmp))
		{
			$view = $this->current_scope['view'];
			$vid = $this->current_scope['vid'];
			if($tmp[$view][$vid])
			{
				$this->_flash_warning[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$view = $this->current_scope['view'];
			$vid = 'all';
			if($tmp[$view][$vid])
			{
				$this->_flash_warning[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$view = 'next';
			$vid = $this->current_scope['vid'];
			if($tmp[$view][$vid])
			{
				$this->_flash_warning[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);
			}
			$view = 'next';
			$vid = 'all';
			if($tmp[$view][$vid])
			{
				$this->_flash_warning[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$this->SESS->set('_flash_warning',$this->current_scope['mod'],$tmp);
		}
		// error
		$tmp = $this->SESS->get('_flash_error',$this->current_scope['mod']);
		if(is_array($tmp))
		{
			$view = $this->current_scope['view'];
			$vid = $this->current_scope['vid'];
			if($tmp[$view][$vid])
			{
				$this->_flash_error[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$view = $this->current_scope['view'];
			$vid = 'all';
			if($tmp[$view][$vid])
			{
				$this->_flash_error[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$view = 'next';
			$vid = $this->current_scope['vid'];
			if($tmp[$view][$vid])
			{
				$this->_flash_error[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);
			}
			$view = 'next';
			$vid = 'all';
			if($tmp[$view][$vid])
			{
				$this->_flash_error[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$this->SESS->set('_flash_error',$this->current_scope['mod'],$tmp);
		}
		// success
		$tmp = $this->SESS->get('_flash_success',$this->current_scope['mod']);
		if(is_array($tmp))
		{
			$view = $this->current_scope['view'];
			$vid = $this->current_scope['vid'];
			if($tmp[$view][$vid])
			{
				$this->_flash_success[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$view = $this->current_scope['view'];
			$vid = 'all';
			if($tmp[$view][$vid])
			{
				$this->_flash_success[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$view = 'next';
			$vid = $this->current_scope['vid'];
			if($tmp[$view][$vid])
			{
				$this->_flash_success[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);
			}
			$view = 'next';
			$vid = 'all';
			if($tmp[$view][$vid])
			{
				$this->_flash_success[] = $tmp[$view][$vid];
				unset($tmp[$view][$vid]);	
			}
			$this->SESS->set('_flash_success',$this->current_scope['mod'],$tmp);
		}
	}



	/**
	 *	Accessor for the page layout
	 *	@return string
	 */

	function default_layout() { return $this->default_layout; }

	/**
	 *	Set the page layout
	 *	TODO: consider checking this value and returning a bool
	 *	@param	string	$layout		Default is 'oos'
	 *	@return void
	 */

	function set_layout($layout) { $this->default_layout = $layout; }

	/**
	 *	construct the cleaning map
	 *	@return	array
	 */

	function clean_map() {
		$ascii_map = array(
			chr(223) => 'ss',
			chr(246) => 'oe',
			chr(252) => 'ue',
			chr(228) => 'ae',
		);
		return $ascii_map;
	}

	/**
	 *	set and return the lang page
	 *	@param	string	$page	(DOCUMENTME)
	 *	@return void
	 */

	function lang_page_start($page)
	{
		//TODO: tidy
		$this->lang_page_stack = isset($this->lang_page_stack) ? ++$this->lang_page_stack : 0;
		#$this->lang_log[] = "starting: ".$page." ( ".$this->lang_page_stack." )";			
		$this->lang_page[$this->lang_page_stack] = $page;
	}

	/**
	 *	return current lang_page from the stack
	 *	@return string
	 */

	function lang_page() { return @$this->lang_page[$this->lang_page_stack]; }

	/**
	 *	(DOCUMENTME)
	 *	@return void
	 */

	function lang_page_end()
	{
		$page = $this->lang_page();						// assigned but not used, strix 2012-02-20
		unset($this->lang_page[$this->lang_page_stack]);
		#$this->lang_log[] = "ending: " . $page . "( ".$this->lang_page_stack." )";
		$this->lang_page_stack = $this->lang_page_stack <= 0 ? 0 : --$this->lang_page_stack;
	}

	/**
	 * 	Wrapper for the standard urlencode function
	 *	@param	string	$str	String to urlencode
	 *	@return	string
	 */

	function urlencode($str){
		$map = $this->clean_map();
		foreach ($map as $in => $out) { $str = preg_replace("/".(string)$in."/", (string)$out, $str); }
		return urlencode($str);
	}

	/**
	 *	is called in init of opc as first call
	 *   switches between the different link types and sets all necesarry vars
	 *	@param	string	$type	one of {'dynamic', 'burc', 'dburc', 'scs', 'path'}
	 *	@return void
	 */

	function handle_link_type($type = LINK_TYPE)
	{
		switch($type)
		{
			case "dynamic":
				$pid = UTIL::get_post('pid');
				if (isset($pid)) { $this->pid = $pid; }
				else { $this->pid = CONF::pid(); }
				break;		//......................................................................

			case "burc":
				/* 
					15.02.2006: 
					check if the user gave us a directory or a file
					if it is not a burc file it might be a directory
					if it is a directory we check to see wether we
					find a page with the same name and send the user 
					to that page
					-> see burc
				*/

				//TODO: check and sanitize $_GET['file'], investgate error cases as handled by BURC

				$vars = array();
				$this->BURC = new BURC();
				$burc = $this->BURC->parse(@$_GET['file']);

				if (!$burc)	{
					$this->pid = $this->BURC->pid(@$_GET['file']);
				}
				else
				{
					$vars = $this->BURC->get($burc);
					$this->pid = $this->BURC->pid($burc);
				}

				if (is_null($this->pid)) { $this->pid = CONF::pid(); }

				$_GET = UTIL::oos_array_merge(@$_GET, @$vars);
				$this->BURC->cleanup();
				break;		//......................................................................

			case "dburc":
				$this->DBURC = new DBURC();

				//TODO: check and sanitize $_GET['file'], investgate error cases as handled by BURC

				$burc = $this->DBURC->parse($_GET['file']);
				$vars = $this->DBURC->get($burc);
				if (!($burc)) { $this->pid = $this->DBURC->pid($_GET['file']); }
				else { $this->pid = $this->DBURC->pid($burc); }
				if (is_null($this->pid)) { $this->pid = CONF::pid(); }
				$_GET = UTIL::oos_array_merge($_GET, $vars);
				break;    	//......................................................................

			case 'scs':                    
				//TODO: sanity check $_GET arg 
				if (preg_match('/form_/', (string)$_GET['file']))
				{
					$p = explode("form_", (string)$_GET['file']);
					$p = explode("_",$p[1]);
					$p = explode(".",$p[0]);
					$this->pid = (int)$p[0];
					$_GET['pid'] = $p[0];
				}
				else
				{
					$this->SCS = new SCS();
					$burc = $this->SCS->parse($_GET['file']);
					$vars = $this->SCS->get($burc);

					if(!$burc)                                
					{       
						
						if ((array_key_exists('file', $_GET)) && ('' != (string)$_GET['file']))
						{

							$DB =& DB::singleton();

							//	previous unsanitized version, SQL injection point, 2012-05-03
							#$r = $DB->query("SELECT * FROM mod_page WHERE url = '" . (string)$_GET['file'] . "'");
							#$this->pid = (int)$r->f('id');

							$template = "SELECT * FROM mod_page WHERE url='%%file%%'";
							$values = array('file' => (string)$_GET['file']);
							$constraints = array('file' => 'mod_page.url');

							$r = $DB->templated_query($template, $values, $constraints);

							if($r->nr() != 1)
							{
								$this->pid = null;
							}
							else
							{
								$this->pid = (int)$r->f('id');

								if ((!is_null($this->pid)) && ($this->pid >= 0))
								{
									if(preg_match('/form_/', (string)$_GET['file']))
									{
										$p = explode("form_", (string)$_GET['file']);
										$p = explode("_", $p[1]);
										$p = explode(".", $p[0]);
										$this->pid = (int)$p[0];
										$_GET['pid'] = $p[0];
									}
								}
							}
						}   
						 
						else
						{
							$this->pid = CONF::pid();								
						}
						
					}
					else
					{
						$this->pid = $this->SCS->pid($burc);
					}       
				}

				if (is_null($this->pid))
				{               
					if (strlen((string)$_GET['file']) >0)
					{
						header("HTTP/1.0 404 Not Found");
						/*
						if(!preg_match('/\.html/',$_GET['file']))	
						{
							die;
						}
						*/
						$this->pid = CONF::default_pages('404');
						if (is_array($this->pid))  /* why might it be an array? strix 2012-02-20 */
						{
							throw new Exception("No 404 page found");
						}
					}
					else
					{
						$this->pid = CONF::pid();	
					}
				}
				
				$_GET = UTIL::oos_array_merge($_GET,$vars);
				break;		//......................................................................

			case 'path':			
				$this->DBURC = new DBURC();
				$burc = $this->DBURC->parse($_GET['file']);
				$vars = $this->DBURC->get($burc);
				if (0 == (int)$vars['burc']['error'])
				{
					if (!$burc) { $this->pid = $this->DBURC->pid($_GET['file']); }
					else { $this->pid = $this->DBURC->pid($burc); }

					if(is_null($this->pid)) { $this->pid = CONF::pid(); }						
				}
				$_GET = UTIL::oos_array_merge($_GET, $vars);
				break;		//......................................................................

		}	// end switch($type)

		// fallback: we assume that fallback linktype will always be dynamic
		if ((is_null($this->pid)) && ("dynamic" != LINK_TYPE))
		{
			$this->handle_link_type('dynamic');
		}

		$_GET['pid'] = $this->pid;
	}

	/**
	 *	set the current page id
	 *
	 *	[en] OPC is the init (GET / POST variable) of the page to display
	 *	[de] pid setzen
	 *	[de] erfolgt beim init des OPC (von GET/POST variable) und beim anzeign der seite
	 *
	 *	@param	int		$pid
	 *	@return	int		The new page ID that was set
	 */

	public function set_pid($pid)
	{
		if (0 == strlen((string)$pid)) { 
			$this->pid = CONF::pid();
		} else {
			$this->pid = $pid;
		}
		return $this->pid;
	}
		
	/**
	 * 	return the current page id
	 *	@return	int	pid
	 */

	function get_pid() { return $this->pid; }

	/**
	 * 	creates a button
	 *
	 * 	@param	string	$type 		Determines icon type, mady be 'edit' or 'online'
	 * 	@param	string 	$msg		Link text, default is 'edit'
	 * 	@param	string	$event		Argument to $this->lnk, default is 'edit'
	 * 	@param	array	$params 	Additional arguments to $this->lnk
	 *	@param	bool	$popup		Open in new window on button press, default is false
	 *	@param	bool	$block		Create as block element (div), default is true
	 *	@return	string				html fragment
	 *
	 */

	function create_button(
		$type = 'edit', $msg = 'edit', $event = 'edit', $params = array(),
		$popup = false, $block = true
	) {

		$img = "icon_edit_red.gif";		// defaults
		$img_type = 'arrow_red';
		
		switch(strtolower(trim($type))) {
			case "edit":
				$img = "icon_edit_red.gif";
				$img_type = 'arrow_red';
				break;		//......................................................................

			case "online":
				$img = "icon_edit_back_red.gif";
				$img_type = 'online_red';
				break;		//......................................................................
			
			default:
				//TODO: handle this case, strix 2012-02-20
		}
			
		$img = $this->show_icon($img_type, true);
			
		$url = $this->lnk(UTIL::oos_array_merge(array('event' => $event), $params));
			
		if ($popup) { $onclick = 'onClick="return popup(\''.$url.'\')"'; }			
		$tag = $block == true ? "div" : "span";
			
		return ''
		 . "<$tag class=\"create_" . $type. "_button\">"
		 . "<a href=\"$url\" $onclick>"
		 . "<img src=\"$img\" border=\"0\" style=\"width:20px;height:20px;\">"
		 . "</a>"
		 . "<span>"
		 . "<a href=\"$url\" $onclick>|$msg</a>"
		 . "</span>"
		 . "</$tag>";
	}

	/**
	 *	returns a linked icon
	 *	in the same way as create_button but without msg
	 *
	 *	@param	string	$type		Icon type passed to show_icon, default is 'edit'	
	 *	@param	string	$event		Used by $this->lnk, default is edit
	 *	@param	array	$params		Additional arguments to $this->lnk
	 *	@param	bool	$popup		Open in new window when icon is clicked?
	 *	@param	string	$onclick	Alternate onClick attribute for link, only used if !popup
	 *	@return	string	An html image link
	 */

	function create_icon(
		$type = 'edit', $event = 'edit', $params = array(), $popup = false, $onclick = ''
	) {

		$url = $this->lnk(UTIL::oos_array_merge(array('event' => $event), $params));
		$onclick = ($popup) ? ' onClick="return popup(\''.$url.'\')"' : $onclick;
		return '<a href="' . $url . '"' . $onclick . '>' . $this->show_icon($type) . '</a>';
	}

	/**
	 *	Register an icon with the OPC
	 *	@param	string	$name	Friendly name of the icon
	 *	@param	string	$path	Location of icon file (CHECKME: relative to what?)
	 *	@param	string	$alt	Alt text of icon images (CHECKME)
	 *	@return	bool
	 */

	function register_icon($name, $path, $alt)
	{
		//TODO: sanitize and check for errors here, strix 2012-02-20
		$this->register_icon[$name]['path'] = $path;
		$this->register_icon[$name]['alt'] = $alt;
		return true;
	}

	/**
	 *	Render an icon as HTML
	 *	@param	string	$name	Friendly name of an icon
	 *	@param	bool	$raw	(DOCUMENTME)
	 *	@return	string			An HTML img tag
	 */

	function show_icon($name, $raw = false)
	{
		$std_path =  '/system/img/icons/';
		$img = '';
		$alt = '';

		switch (strtolower(trim($name)))
		{
			case "delete":
				$img = "icon_delete.gif";
				$alt = e::o('alt_delete', null, null, 'opc');
				break;		//......................................................................

			case "save_blue":
				$img = "icon_save_blue.gif";
				$alt = e::o('alt_delete', null, null, 'opc');
				break;		//......................................................................
		
			case "emtpy":
				$img = "icon_empty.gif";
				$alt = e::o('alt_delete', null, null, 'opc');
				break;		//......................................................................

			case "open_red":
				$img = "icon_open_red.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "critical":
				$img = "icon_critical.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "page":
				$img = "icon_mod_page.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "edit_menu_red":
				$img = "icon_edit_menu_red.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "fill_red":
				$img = "icon_fill_red.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;

			case "eye_red":
				$img = "icon_eye_red.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "arrow_gray":
				$img = "icon_edit_gray.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "view_red":
				$img = "icon_view_red.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "mdb":
				$img = "icon_mdb.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "help_blue":
				$img = "icon_help_blue.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "link":
				$img = "icon_link.gif";
				$alt = e::o('alt_delete',null,null,'opc');
				break;		//......................................................................

			case "offline":
				$img = "icon_offline.gif";
				$alt = e::o('alt_offline',null,null,'opc');
				break;		//......................................................................

			case "online":
				$img = "icon_online.gif";
				$alt = e::o('alt_online',null,null,'opc');
				break;		//......................................................................

			case "moveup":
				$img = "icon_move_up.gif";
				$alt = e::o('alt_moveup',null,null,'opc');
				break;		//......................................................................

			case "movedown":
				$img = "icon_move_down.gif";
				$alt = e::o('alt_movedown',null,null,'opc');
				break;		//......................................................................

			case "recycle":
				$img = "icon_recycle.gif";
				$alt = e::o('alt_recycle',null,null,'opc');
				break;		//......................................................................

			case "open":
				$img = "icon_open.gif";
				$alt = e::o('alt_open',null,null,'opc');
				break;		//......................................................................

			case "close":
				$img = "icon_close.gif";
				$alt = e::o('alt_close',null,null,'opc');
				break;		//......................................................................

			case "view":
				$img = "icon_view.gif";
				$alt = e::o('alt_view',null,null,'opc');
				break;		//......................................................................

			case "add":
				$img = "icon_add.gif";
				$alt = e::o('alt_add',null,null,'opc');
				break;		//......................................................................

			case "arrow_red":
				$img = "icon_edit_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "arrow_blue":
				$img = "icon_edit_blue.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "delete_page":
				$img = "icon_delete_page.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;

			case "move_gray":
				$img = "icon_move_gray.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "rights_red":
				$img = "icon_rights_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "close_red":
				$img = "icon_close_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "info_red":
				$img = "icon_information.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "warning_red":
				$img = "icon_warning.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "warning_red":
				$img = "icon_warning_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

				case "save_red":
				$img = "icon_save_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "edit":	// fall through
			case "edit_page_red":
				$img = "icon_edit_page_red.gif";
				$alt = e::o('alt_edit',null,null,'opc');
				break;		//......................................................................

			case "page_red":
				$img = "icon_mod_page_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "new_page_red":
				$img = "icon_new_page_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "minus_red":
				$img = "icon_minus_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "plus_red":
				$img = "icon_plus_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "new_user_red":
				$img = "icon_new_user_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "logout":
				$img = "icon_logout_blue.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "login":
				$img = "icon_login_blue.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "move_red":
				$img = "icon_move_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "resize_red":
				$img = "icon_resize_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "close_red":
				$img = "icon_close_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "online_red":
				$img = "icon_edit_back_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "user_red":
				$img = "icon_mod_usradmin_usr.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "speaker_red":
				$img = "icon_speaker_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "group_red":
				$img = "icon_mod_usradmin_grp.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "user_blue":
				$img = "icon_mod_usradmin_usr_blue.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "news":
				$img = "icon_mod_news.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "edit_user_red":
				$img = "icon_edit_user_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "delete_user_red":
				$img = "icon_delete_user_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "edit_container_red":
				$img = "icon_edit_container_red.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "edit_article":
				$img = "icon_edit_article.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			case "add":						//DUPLICATE CASE, UNREACHABLE
				$img = "icon_add.gif";
				$alt = e::o('alt_standard',null,null,'opc');
				break;		//......................................................................

			default:
				if ($this->register_icon[$name])
				{
					$img = (string)$this->register_icon[$name]['path'];
					$alt = (string)$this->register_icon[$name]['alt'];
					$std_path =  '';
				}
		}
			
		if ($raw) { return $std_path.$img; }

		$inf = @getimagesize(CONF::web_dir() . $std_path . $img);
			
		return ''
		 . '<img'
		 . ' src="' . $std_path.$img . '"'
		 . ' border="0"'
		 . ' title="' . $alt . '"'
		 . ' alt="' . $alt . '"'
		 . ' style="width:' . (string)$inf[0] . 'px;height:' . (string)$inf[1] . 'px"'
		 . '>';
	}

	/**
	 *	generates a form action according on the link_type
	 *
	 *	@param	int		$pid	Page Id
	 *	@param	string	$hook	(DOCUMENTME)
	 *	@param	string	$anchor	Anchor to be added to URL
	 *	@return	string
	 */

	function action($pid = -1, $hook = '', $anchor = '')
	{                                                                   
		$pid = (-1 !== $pid) ? $pid : $this->pid;
		$pid = isset($pid) ? $pid : $this->pid;
		
		$ret = '';
		switch(LINK_TYPE)
		{
			case "dynamic":		$ret = "index.php";						break;
			case "burc":		$ret = "form_" . $pid . ".html";		break;

			case 'dburc':
				$this->lnk_add('pid',$pid);
				$ret = "index.php";
				break;		//......................................................................

			case 'scs':    
				$ret = ''
				 . "form_" . $pid
				 . (('' !== $hook) ? "_" . $hook : '') . ".html"
				 . (('' !== $anchor) ? "#" . $anchor : '');
				break;

			case 'path':
				$this->lnk_add('pid',$pid);
				$ret = "index.php";					
				//return $this->lnk_path($pid)."/form.html";	
				break;

			case 'ice':
				$ret = ice_generate_link(null, '', array('pid=' . $pid));
				break;		//......................................................................

			default:
				//TODO: add a default case, log error, etc
		}
		return $ret;
	}

	/**
	 *	Create a link to fire some event run by /system/background.php (CHECKME)
	 *
	 *	@param	string	$mod			Name of a module (CHECKME)
	 *	@param	string	$event			Name of an event to be linked to? (CHECKME)
	 *	@param	array	$params			Additional event arguments? (CHECKME)
	 *	@param	int		$uid			A user ID (CHECKME)
	 *	@param	bool	$withoutpath	(DOCUMENTME)
	 *	@return	string
	 */

	function lnk_background($mod, $event, $params = array(), $uid = -1, $withoutpath = false)
	{
		/*
			$data = unserialize(base64_decode($argv[1]));
			$_SERVER['HTTP_HOST'] = $data['server'];
			include_once($data['oos']);
			$OPC = new OPC();
			$MC  = &MC::singleton();
			$CLIENT = &CLIENT::singleton(true);
			$DB = &DB::singleton();		
			$CLIENT->su($data['uid']);
			$MC->call_action(array('mod'=>$data['mod'],'event'=>$data['event']),$data['params']);
		*/                                           
			             
		if(-1 == $uid)
		{
			$client = &CLIENT::singleton();				
			$uid = $client->usr['id'];
		}
			
		$d = array
		( 
			'uid' => $uid,
			'mod' => $mod,
			'event' => $event,
			'params' => $params,
			'server' => $_SERVER['HTTP_HOST'],
			'oos' => CONF::inc_dir()."/oos.sys",
			'session' => $this->SESS->id
		);

		if ($withoutpath) { return base64_encode(serialize($d)); }
		return CONF::inc_dir() . "system/background.php " . base64_encode(serialize($d));
	}
		
	/**
	 *	Generates a link
	 *
	 *	[en] Any link generation should happen through this function.  Session ID, etc are added
	 *	[en] automatically.
	 *
	 *	[de] generiert einen link
	 *	[de] jede link-generierung sollte ueber diese funktion laufen, 
	 *	[de] da hier session-id etc. automatisch angefuegt werden
	 *
	 *	@param	array	$params			Arguments passed in query string ($_GET) (CHECKME)
	 *	@param	string	$path			(DOCUMENTME)
	 *	@param	bool	$set_anchor		(DOCUMENTME)
	 *	@param	string	$filetype		Extension shown in link, default is 'html'
	 *	@param	int		$permanent		(DOCUMENTME) Argument to BURC (only) it seems
	 *	@return	string	url with session-params etc.
	 */

	public function lnk(
		$params = null, 
		$path = null,
		$without = null,
		$set_anchor = true,
		$filetype = "html",
		$permanent = 0
	) {

		$link = '';					//	return value
		$seen = array();		

		if (is_array($without)) {
			foreach($without as $name) $seen[$name] = true;
		}

		if ($without == "only"){
			foreach ($this->lnk_add as $key => $val) {
				$seen[$key] = true;
			}
		}

		if ($without == 'strict'){
			foreach ($this->lnk_add as $key => $val) {
				if(!$params[$key]){
					$seen[$key] = true;
				}
			}
		}

		// kick out the session here....
						
		switch(LINK_TYPE)
		{
			case "burc":
				if (!$this->BURC) { $this->BURC = new BURC(); }
				foreach ($this->lnk_add as $key => $val) 
				{
					if (!isset($seen[$key])) { $lnk_add[$key] = $val; }
					if ($key == 'vid') { $anchor = md5($val); }
				}

				// in case of a remote call we collect all links with the array making the link up
				// not implemented in dynamic and static yet
					
				#$lnk = $this->BURC->lnk(array_merge($lnk_add,$params));
				$lnk = $this->BURC->lnk(
					UTIL::oos_array_merge($lnk_add, $params),
					($set_anchor==true ? $anchor : null ),
					$filetype, $permanent
				);
					
				if ($this->__remote_call)
				{
					$this->__remote_links[$lnk] = UTIL::oos_array_merge($lnk_add, $params);
				}
					
				$link = $lnk;
				break;		//......................................................................

			case "dburc":
				if (!$this->DBURC){ $this->DBURC = new DBURC(); }

				foreach ($this->lnk_add as $key => $val) {
					if (!isset($seen[$key])) { $lnk_add[$key] = $val; }
				}
				// in case of a remote call we collect all links with the array making the link up
				// not implemented in dynamic and static yet
					
				$lnk = $this->DBURC->lnk(UTIL::oos_array_merge($lnk_add, $params));
					
				if($this->__remote_call)
				{
					$this->__remote_links[$lnk] = UTIL::oos_array_merge($lnk_add, $params);
				}
					
				$link = $lnk;
				break; 		//......................................................................

			case 'scs':             
				if (!$this->SCS) { $this->SCS = new SCS(); }

				foreach ($this->lnk_add as $key => $val) 
				{
					if (!isset($seen[$key])) { $lnk_add[$key] = $val; }
				}

				$burc = UTIL::oos_array_merge($lnk_add, $params);
				$CLIENT = &CLIENT::singleton(true);

				

				if($CLIENT->usr['is_default'])
				{
					if (@$burc['event']) { $lnk = "-" . $this->SCS->lnk($burc); }
					else { $lnk = ''; }                        
				}    
				else
				{
					if($_COOKIE['scs_mod'])
					{
						if($burc['mod'] == $_COOKIE['scs_mod'])
						{
	   						$lnk = "-" . $this->SCS->lnk($burc);						
						}
						else
						{
							$lnk = '';
						}
					}
					else
					{
	   					$lnk = "-" . $this->SCS->lnk($burc);
					}
				}
					
				// get the url for the pid 
				$DB = &DB::singleton();
				$crud = new db_crud();

				//	previous, unsanitized version, remove once CRUD is tested
				#$sql = ''
				# . "SELECT * FROM mod_page"
				# . " WHERE id=" . (isset($burc['pid']) ? (string)$burc['pid'] : '1');
				#$r = $DB->query($sql);

				$identifier = (isset($burc['pid']) ? (string)$burc['pid'] : '1');
				$page = $crud->load('mod_page', 'id', $identifier);		
				
				if (0 == count($page)) { /* (TODO) no such page, handle error */ }

				$name = $page['url'];

				if ((0 == strlen($page['url'])) && (strlen($page['name']) > 0))
				{
					$name = UTIL::norm($page['name']) . ".html";
					$values = array('url' => $name);
					$check = $crud->update('mod_page', 'id', $page['id'], $values);

					if (false == $check) { /* (TODO) update failed, handle error */ }
				}

				//	previous version using db_result object, remove once CRUD is tested
				#if (strlen($r->f('url')) > 0) { $name = (string)$r->f('url'); }
				#else
				#{                         
				#	if (strlen($r->f('name')) > 0)
				#	{
				#		$name = UTIL::norm($r->f('name')) . ".html";       
				#		$sql = '' 	/* TODO: sanitize, strix 2012-02-20 */
				#		 . "UPDATE mod_page SET url='" . $name . "'"
				#		 . " WHERE id = " . (string)$r->f('id');
				#		$DB->query($sql);
				#	}
				#}

				$url = "/" . preg_replace("/^\//", "", $name);
				$p = explode(".", $url);
				$p[0] .= $lnk;
				$url = implode(".", $p);

				$link = $url;
				break;		//......................................................................

			case 'path':
				if (!$this->DBURC) { $this->DBURC = new DBURC(); }

				foreach ($this->lnk_add as $key => $val) 
				{
					if (!isset($seen[$key])) { $lnk_add[$key] = $val; }
				}

				// in case of a remote call we collect all links with the array making the link up
				// not implemented in dynamic and static yet
					
				$lnk = $this->DBURC->lnk(UTIL::oos_array_merge($lnk_add,$params));
					
				if ($this->__remote_call)
				{
					$this->__remote_links[$lnk] = UTIL::oos_array_merge($lnk_add,$params);
				}

				// get the full path for the pid
				// add it with index.htm
					
				// 
				if ($lnk_add['pid']) { $pid = $lnk_add['pid']; }					
				if ($params['pid']) { $pid = $params['pid']; }
				if ($pid) {	return $this->lnk_path($pid) . '/index.html' . $lnk; }

				$link = $lnk;					
				break;		//......................................................................

			case 'ice':
				$url_params = array();
					
				if (is_array($params)) {
					foreach($params as $key => $val) {
						if (is_array($val)) { $val = $this->serialize($val); }							
						$url_params[] = urlencode($key).'='.urlencode($val);
						$seen[$key] = true;
					}
				}
					
				foreach ($this->lnk_add as $key => $val) {
					if (isset($seen[$key])) continue;
					$url_params[] = urlencode($key).'='.urlencode($val);
				}


				$link = ice_generate_link(null, '', $url_params);
				break;		//......................................................................

			case "dynamic":		// fall through
			default:
				$url = 'index.php';
					
				$url_params = array();
					
				if (is_array($params)) {
					foreach($params as $key => $val) {
						if(is_array($val)){
							$val = $this->serialize($val);
						}							
						$url_params[] = urlencode($key).'='.urlencode($val);
						$seen[$key] = true;
					}
				}
					
				foreach ($this->lnk_add as $key => $val) {
					if (isset($seen[$key])) continue;
					$url_params[] = urlencode($key).'='.urlencode($val);
				}
					
				if (count($url_params) > 0) { $url = $url . '?' . implode('&', $url_params); }

				if (!preg_match('/pid/i', $url)){
					$MC = &MC::singleton();
					$url .= "&pid="
					 . $MC->call_action(array('event'=>'get_start_page', 'mod' => 'page'));
				}
					
				$link = $url;
				break;		//......................................................................s
		}

		return $link;
	}

	/**
	 *	returns the path for pid or the whole array
	 *	if link type is set to path only creates paths above the start pid (CONF::pid())
	 *
	 *	@param	int		$pid	Page ID
	 *	@return array
	 */

	function lnk_path($pid = -1)
	{
		// get the whole tree and save one index with the path for each pid
		if(!$this->_lnk_path)
		{
			$set = new NestedSet();
			$set->set_table_name('mod_page');
			$tree = $set->getNodes(null,'id,name');
			$stack = array();

			foreach($tree as $k => $n)
			{
				if($n['id'] == CONF::pid())
				{
					$bottom = $n['level'];
					break;
				}
			}

			reset($tree);
			foreach($tree as $k => $n)
			{
				// bring the stack down to my level
				while(sizeof($stack) >= $n['level']) { array_pop($stack); }					
				array_push($stack,UTIL::norm($n['name']));
					
				// this can be more elegant. - some other time...
				$tmp = array();
				$cnt = 2;
				foreach($stack as $name)
				{
					if ($cnt++>$bottom) { $tmp[] = $name; }
				}
				$this->_lnk_path[$n['id']] = '/' . implode('/', $tmp);
			}
		}
		if (-1 != $pid) { return $this->_lnk_path[$pid]; }
		return $this->_lnk_path;
	}

	/**
	 *	return current lnk_add as hidden HTML Form fields
	 *	@param	array	$params		Form field names (array key) and values (array values)
	 *	@param	bool	$local		(DOCUMENTME)
	 *	@return	string
	 */
	
	function lnk_hidden($params = null, $local = false)
	{
			
		if (is_array($params))
		{
			foreach ($params as $name => $value)
			{
				$output[$name] = "<input type='hidden' name='$name' value='$value'>";
				$seen[$name] = true;
			}
		}

		foreach ($this->lnk_add as $name => $value)
		{
			if ($seen[$name]) { continue; }
			if ((true == $local) && ($output[$name])) { continue; }
			$output[$name] = "<input type='hidden' name='$name' value='$value'>";
		}

		return implode('',$output);
	}

	/**
	 *  Records/caches (prerendered) links in an array on this object
	 *
	 *	[de] key/values angeben, die automatisch bei jeder link-erzeugung 
	 *	[de] und als hidden-form-werte mitgenommen werden 
	 *	
	 *	@param:	string	key		(DOCUMENTME)
	 *	@param:	mixed	val		(DOCUMENTME)
	 *	@return void
	 */

	function lnk_add($key, $val) {
		//TODO: consider adding a return value to this, to indicate cache hits/misses/udpates?
		if (is_array($val)) { $val = $this->serialize($val); }
		$this->lnk_add[$key] = $val;
	}
	
	/**
	 *	Remove a link from the cache/array
	 *	@param	string	$key	(DOCUMENTME)
	 *	@return void
	 */

	function lnk_remove($key) {
		//TODO: consider adding a return vaue to this
		unset($this->lnk_add[$key]);
	}
		
	/**
	 *	Returns the cache of prerendered links? (CHECKME)
	 *
	 *	[de] liefert array der keys/values, die bei jedem link 
	 *	[de] und formular mitgeschleift werden sollen 
	 *
	 *	@return	array
	 */

	public function get_lnk_add() {
		return $this->lnk_add;
	}

	/**
	 *	serialize arrays:
	 *	@param	array	$arr	Array to be serialized
	 *	@return	string
	 */

	function serialize($arr){
		switch (LINK_TYPE) {
			case "static":		return preg_replace("/:/","|",serialize($arr));
			case "dynamic":		return serialize($arr);
			default:			//TODO: handle default case
		}
	}

	/**
	 *	unserialize arrays:
	 *	@param	string	$str	To be unserialized into a array? (CHECKME) check serializing objects
	 *	@return	array
	 */

	function unserialize($str)
	{
		switch (LINK_TYPE) {
			case "static":		return unserialize(preg_replace("/\|/", ":", $str));
			case "dynamic":		return unserialize($arr);
			default:			//TODO: handle default case
		}
	}

	/**
	 *	Returns pointer to the single OPC object
	 *
	 *	Each instantiation must be run over this function to ensure that only one instance of this
	 *	object exists.	
	 *
	 *	[de] liefert zeiger auf OPC objekt
	 *	[de] jede instanzierung muss ueber diese funktion laufe, um sicherzustellen,
	 *	[de] dass immer nur eine instanz dieses objektes existiert
	 *
	 *	@return	object	zeiger auf OPC objekt
	 *	@access	public
	 */

	function &singleton() {
		static $instance;			
		if (!is_object($instance)) { $instance = new OPC(); }
		return $instance;
	}

	/**
	 *	add some js for insertion into the header
	 *	@param	string	$js		(DOCUMENTME)
	 *	@return void
	 */

	function js_set($js) { $this->js_set[] = $js; }

	/**
	 *	(DOCUMENTME)
	 *	@param	string	$f		(DOCUMENTME)
	 *	@return	void
	 */

	function js_onload($f = '')
	{
		if('' !== $f)
		{
			//return 'onload="alert(\'test\');"';
			$this->js_onload[] = "self.name='mainframe'";
			return 'onload="' . implode("; ", $this->js_onload) . '"';
		}

		$this->js_onload[] = preg_replace('/"/', "'", $f);
	}

	/**
	 *	output the collected js
	 *	@return	string
	 */

	function js_get() {
		if ($this->js_set) { return implode("'\n",$this->js_set); }
		return '';
	}

	/**
	 *	class a module view
	 *
	 *	[en] instantiates appropriate view class of a module,
	 *	[en] calls the main method, and returns the result
	 *
	 *	[de] view eines moduls aufrufen
	 *	[de] instanziert entsprechende view-klasse eines moduls, 
	 *	[de] ruft dessen main()-methode auf, und liefert das zurueck
	 *
	 *	@param	array	$view
	 *	@return	string	ergebnis der main() methode der view-klasse des moduls
	 */

	// shortcut for call_view
		
	public function call($modul_name, $method_name = null, $vid = null)
	{
		return $this->call_view($vid,$modul_name,$method_name);
	}

	/**
	 *	Call a view on a module
	 *
	 *	@param	string	$view_name			Name of the view
	 *	@param	string	$modul_name			Name of a module
	 *	@param	string	$method_name		(DOCUMENTME)
	 *	@param	bool	$force_view_name	(DOCUMENTME)
	 *	@return	mixed
	 */

	function call_view($view_name=null, $modul_name, $method_name = '', $force_view_name = false)
	{		

		if($view_name === null)
		{
			$view_name = md5(UTIL::get_post('pid').$modul_name.@$this->call_view_counter[$modul_name]++);
		}

		// make sure all stored alerts are in their scope
		$this->alerts_update();
		// set the current module for translation
		$this->lang_page_start($modul_name);
		//
		$class_dir  = CONF::inc_dir().'/mods/' . $modul_name . '/class_' . $modul_name . 'View.inc.php';
		//
		$class_name = $modul_name . '_view';
						
		if (!file_exists($class_dir)) 
		{
			echo 'ERROR: new view found [' . $modul_name  . ' ('.$class_dir.')]';
			return;
		}
		include_once($class_dir);
			
		//_UEBERLEGEN: nur eine instanz je modul
		$mod = new $class_name();
			
		if (method_exists($mod, '_constructor')) 
		{
			$mod->_constructor();
		}

		if (@isset($this->set_view[$modul_name][$view_name])) 
		{
			$method_name = $this->set_view[$modul_name][$view_name];
		}
						
		$this->lang_page_end();


		$this->__current_mod = $modul_name;
		$this->__current_vid = $view_name;
		
		$this->current_scope['mod'] = array();
		$this->current_scope['mod'] = $modul_name;
		$this->current_scope['view'] = $method_name;
		$this->current_scope['vid'] = $view_name;

		// set the display row and column in the module
		return $mod->main($view_name, $method_name);
	}

	/**
	 *	
	 *
	 *	@param	string	$modul_name		Name of a module
	 *	@param	string	$method_name	A method of a module's view class
	 *	@param 	int		$vid			View ID
	 *	@return	mixed
	 */

	function get($modul_name, $method_name = null, $vid = null)
	{
		ob_start();
		ob_clean();
		$this->call_view($vid, $modul_name, $method_name, (!$vid ? 0 : 1));
		$data = ob_get_contents();
		ob_end_clean();
		return $data;
	}
		
	/**
	 *	[en] set the view for a specific module? (CHECKME)
	 *	[de] view fuer ein bestimmtes modul setzen
	 *
	 *	@param	string	$view_name		Name of a view of the given module
	 *	@param	string	$modul_name		Name of a module
	 *	@param	string	$method_name	Name of a moethod on the module's view class? (CHECKME)
	 *	@return void
	 */

	function set_view($view_name, $modul_name, $method_name) 
	{
		$this->set_view[$modul_name][$view_name] = $method_name;
	}
		
	/**
	 *	[en] Set the initial view for a module? (CHECKME)
	 *	[de] start-view setzen
	 *	
	 *	@param	string	$view_name		Name of a view on the given module
	 *	@param	string	$modul_name		Name of a module
	 *	@param	string	$method_name	Name of a method on the module's view class
	 *	@param	bool	$persistent		(DOCUMENTME)
	 *	@return void
	 */

	function set_start_view($view_name, $modul_name, $method_name, $persistent = false) {
		if ($persistent) { $this->persistent_start_view = true; }
		$this->start_view = array($view_name, $modul_name, $method_name);
	}
		
	/**
	 *	[en] provide / return the start view
	 *	[de] start-view liefern
	 *
	 *	@return	array	0=>view_name, 1=>modul_name, 2=>method_name
	 */

	function get_start_view() {
		return $this->start_view;
	}

	/**
	 *	generate a view
	 *
	 *	[en] Template code is read and eval()'d
	 *
	 *	[de] generiert einen view
	 *	[de] template-code wird eingelesen und ge'eval'd
	 *
	 *	@param	string	$template	Template file name
	 *	@param	bool	$up			(DOCUMENTME) Seems to increment a stack, TODO: investigate
	 *	@return void
	 */
		
	public function generate_view($template, $up = true) 
	{
		if(true == $up) { array_push($this->__stack, -1); }		/*	push the stack one higher */

		// OPC and SESS objects to make available to the template? (CHECKME)
		// OPC und SESS objekte fuer das template bekannt machen
		$OPC    =& $this;
		$SESS   =& $this->SESS;
		$DB     =& DB::singleton();
		$MC     =& MC::singleton();			
		$CLIENT =& CLIENT::singleton();
			
		// call this to make sure we have the remote vars in our vars array
		// this will only be called once
		// so be aware of problems with call_actions called in views!!!!! -> remote results 
		// will not show up here
		$this->merge_local_remote_vars();
			
		//$this->lang_page_start($this->__current_mod);
		//MC::debug($template);
			
		include($template);
			
		//$this->lang_page_end();
			
		if (true == $up) { array_pop($this->__stack); } 		/*	pop the end off the stack */
		return;
	}

	/**
	 *	parse a string for calls to views and or controllers and call those
	 *	{%[view|action]|module|event|[serialized array]%}
	 *
	 *	@param	string	$s	String which may contain serialized? calls to views
	 *	@return	string
	 */                                                      

	function parse($s)
	{
		$out = '';						// return value
		$s = explode("{%",$s);
		if (1 == count($s)) { return $s[0]; }
		foreach ($s as $k => $v)
		{               
			if (preg_match("/%}/",$v))
			{
				$tmp = explode("%}",$v);               
				// parse the execution  
				$exec = explode("|",$tmp[0]);

				switch($exec[0])
				{
					case 'view':
						$out .= $this->get($exec[1], $exec[2], (int)$exec[3]);
						break;		//..............................................................

					case 'action':     
						//TODO: stricter comparisons, and figure out exactly what this should do
						if ($exec[3] && unserialize($exec[3])) { $exec[3] = unserialize($exec[3]); }
						$out .= $this->MC->call($exec[1], $exec[2], $exec[3]);
						break;
	
					default:	//TODO: handle default case
				}

				// add the static content to the output
				$out .= $tmp[1];
			}                   
			else
			{                           
				$out .= $v;
			}
		}
		return $out;
	}
		
	/**
	 *	merges local and remote values
	 *	remote values override local values!!!
	 *	@return void
	 */

	function merge_local_remote_vars()
	{
		return;
	}

	/**
	 *	Store/save a variable
	 *
	 *	[de] variable merken/speichern
	 *
	 *	@param	string	$mod		Module name / namespace
	 *	@param	string	$varname	Variable name
	 *	@param	mixed	$val		Value to store
	 *	@return	void
	 */

	public function set_var($mod, $varname, $val) {
		$this->_vars[$mod][$varname] = $val;
	}

	/**
	 *	Return a preset variable of the current module
	 *
	 * 	[de] variable zurueckliefern
	 *
	 *	@param		string	$varname	Variable name
	 *	@return	mixed
	 */

	public function var_get($varname) { return $this->_vars[$this->__current_mod][$varname]; }

	/**
	 *	Return a preset variable of a named module
	 *
	 *	@param		string	$mod		Module name / namepsace
	 *	@param		string	$varname	variable name
	 *	@return		mixed
	 */

	public function get_var($mod, $varname) { return @$this->_vars[$mod][$varname]; }
		
	/**
	 *	retrieves the array with information for the current stack position in struct
	 *	@param	string	$view_name		Name of a view on the given module
	 *	@param	string	$module_name	Name of a module
	 *	@return	
	 */
		
	function get_position_info($view_name, $module_name)
	{
		foreach($this->__stack as $item)
		{
			if(!isset($value)) { $value = $this->__struct; }
			$value = $value[$item];
		}

MC::log(debug_backtrace());
die;

		// case 1 is called when a module wasnt in this position before
		if(!is_array($value))
		{
			return $this->set_position_info($view_name,$module_name);
		}

		// case 2 is called in normal operation
		if($this->validate_position_info($view_name,$module_name,$value) == true)
		{
			array_push($this->__vid_coll,$value["vid"]);
			return $value;

			// a case 2.1 should react when position info could not be validated
			// this case should try and find previous modules in the position and 
			// decide on what to do with them. if possible
			// WHAT WE DO: 
			// we remember the modules per page that are not in use anymore
			// these modules can be integrated via container as well 
			// ( a second list in containers via which we can insert lost modules ) 
	
			/*
			if (false == $this->validate_position_info($view_name, $module_name, $value))
			{			
				MC::debug($value,$view_name." - ".$module_name);
				MC::debug($this->__struct);	
			}
			*/
		}
		else 
		{
			$act_array = array('mod' => 'page', 'event' => 'callback_lost_mods_save');
			$this->MC->call_action($act_array, $this->remember_position_info($value));
		}

		// case 3 doesnt seem to appear
		if (isset($this->__keys[sizeof($this->__stack)][$view_name][$module_name]))
		{
			$top_of_stack = $this->__keys[sizeof($this->__stack)][$view_name][$module_name];
			return $this->reset_position_info($view_name, $module_name, $top_of_stack);
		}

		// case 4 is called when we change the module
		// final try to catch this shit
		return $this->set_position_info($view_name,$module_name);
	}

	/**
	 *	tries to loop through a strucure array
	 *	and remember everything except template and container ( or other future structuring modules )
	 * 	in an array which is returned by the function
	 *
	 *	@param	array	$struct		(DOCUMENTME)
	 *	@param	array	$rem_coll	(DOCUMENTME)
	 *	@param	int		$counter	(DOCUMENTME)
	 *	@return array
	 */

	function remember_position_info($struct, $rem_coll = array(), $counter = 0){
		$client = &CLIENT::singleton();
		foreach($struct as $key => $item){
			if(is_array($item)){
				$rem_coll = $this->remember_position_info($item, $rem_coll, $counter++);
				$counter = sizeof($rem_coll);
			}
			else
			{
				$rem_coll[$counter][$key] = $item;
				if(!@$rem_coll[$counter]['meta']){
					@$rem_coll[$counter]['meta'] = array(
						'time' => time(),
						'uname' => $client->usr['usr'],
						'uid' => $client->usr['id']
					);
				}
			}			
		}
		return $rem_coll;
	}

	/**
	 *	function for instantiation: returns the new vid
	 *
	 *	write mod_name and vid to sys_vid
	 *
	 *	Warning: though following the original function, original implementation could hit an
	 *	infinite loop on database failure.  Added a base case, giving up after 100 retries
	 *	(arbitrary) - this should be reviewed by an original developer who better understands the
	 *	intent of doing it this way. (TODO)
	 *
	 *	@param	string	$mod_name	Name of a module
	 *	@return string
	 */

	function set_vid($mod_name)
	{
		$crud = new db_crud();
		$stop = false;
		$retries = 100;

		//	previous, unsanitized version, remove once CRUD is tested, strix 2012-05-03
		#$vid = (string)md5(uniqid(rand(), true));
		#$insert = "INSERT INTO sys_vid (pid, vid, mod_name) VALUES ('".$this->pid."','$vid', '$mod_name')";
		#$res = $this->DB->query($insert);

		while (false == $stop)
		{

			$vid = (string)md5(uniqid(rand(), true));

			$values = array(
				'pid' => $this->pid,
				'vid' => $vid,
				'mod_name' => $mod_name
			);

			$stop = $crud->create('sys_vid', $values, array());
			$retries--;

			if (0 == $retries) { $stop = true; }
		}

		//	previous, unsanitized version, remove once CRUD is tested, strix 2012-05-03
		#while (is_error($res))
		#{
		#	$vid = (string)md5(uniqid(rand(), true));
		#	$insert = "INSERT INTO sys_vid (vid, mod_name) VALUES ('$vid', '$mod_name')";
		#	$res = $this->DB->query($insert);
		#}

		return $vid;
	}

	/**
	 *	sets position info in the struct (CHECKME)
	 *	@param	string	$view_name	name of a view
	 *	@param	string	$mod_name	name of a module
	 *	@return	array
	 */
		
	function set_position_info($view_name,$mod_name){
		$vid = $this->set_vid($mod_name);
		// IMPORTANT!: sequence has to be kept like this: view_name / mod_name / vid
		$token = array(
			"view_name" => $view_name,
			"mod_name" => $mod_name,
			"vid" => $vid
		);

		array_push($this->__vid_coll,$vid);
			
		$do = '$this->__struct["'. implode('"]["',$this->__stack).'"] = $token;';
		eval($do);			// there must be a better way
		return $token;		// why called token? strix 2012-02-20
	}
		
	/**
	 *	resets the position from __keys
	 *	@param	string	$view_name	name of a view on given module
	 *	@param	string	$mod_name	name of a module
	 *	@param	string	$vid		a view-id (an md5 hash)
	 *	@return array
	 */
		
	function reset_position_info($view_name, $mod_name, $vid){
		// IMPORTANT!: sequence has to be kept like this: view_name / mod_name / vid
		$token = array(
			"view_name" => $view_name,
			"mod_name" => $mod_name,
			"vid" => $vid
		);
			
		array_push($this->__vid_coll,$vid);
			
		$do = '$this->__struct["'. implode('"]["',$this->__stack).'"] = $token;';

		eval($do);			// TODO: get trid of this eval, strix 2012-02-20
		return $token;
	}
		
	/**
	 *	validates position info against module_name and view_name
	 *	@param	string	$view_name		Name of a view
	 *	@param	string	$module_name	Name of a module
	 *	@param	array	position_info	(DOCUMENTME)
	 *	@return	bool
	 */
		
	function validate_position_info($view_name, $module_name, $position_info){
		if (
			($position_info["mod_name"] != $module_name) ||
			($position_info["view_name"] != $view_name)
		) { return false; }
		return true;
	}

	/**
	 *	called by page before generate_view
	 *	sets the struct in opc and calls a function to remember the keys
	 *	@param	array	$struct		(DOCUMENTME)
	 *	@return void
	 */

	function set_struct($struct){
		#MC::debug($struct);
		$this->__struct = $struct;
		$this->set_keys($struct);
	}

	/**
	 *	function to clean the struct up after page is built
	 *	takes dead nodes out of the struct
	 *	is called after generate_view by page
	 *
	 *	@param	array	$struct		(DOCUMENTME)
	 *	@param	int		$level		(DOCUMENTME)
	 *	@param	array	$mystack	(DOCUMENTME)
	 */

	function clean_struct($struct, $level = 0, $mystack = array()) {

		foreach ($struct as $key => $value ){
			if (is_array($value)){
				array_push($mystack, $key);
				$this->clean_struct($value, ($level + 1), $mystack);
				array_pop($mystack);
			}
			else
			{
				if ($key == "vid") {
					//if this vid is not in $this->__vid_coll - get rid of it
					if (!in_array($value,$this->__vid_coll)) {
						$do = 'unset($this->__struct["'. implode('"]["',$mystack).'"]);';
						eval($do);		//TODO: get rid of this eval, strix 2012-02-20
					}
				}
			}
		}
	}

	/**
	 *	remember the keys in case struct changes
	 *
	 *	@param	array	$keys	(DOCUMENTME)
	 *	@param	int		$level	(DOCUMENTME)
	 *	@return	void	
	 */

	function set_keys($keys, $level = 0) {
		if (!is_array($keys)) { return; }
		foreach ($keys as $key => $value) {
			if (is_array($value))
			{
				$this->set_keys($value, ($level + 1));
			}
			else
			{
				if ($key == "view_name") { $view_name = $value; }
				if ($key == "mod_name") { $mod_name = $value; }
				// TODO: check $mod_name and $view_name have been set before use
				if ($key == "vid") { $this->__keys[$level][$view_name][$mod_name] = $value; }
			}
		}
	}

	/**
	 *	debug function 
	 *	@return void
	 */

	function debug() {
		// TODO: tidy, move to centralized debugging printout / report
		echo "<br>--------------DEBUGGING START------------------<br>";
		$this->$myName = get_class($this);

		echo "class: ".$this->$myName."<hr>methods:<br>";
		$this->$myMethods = get_class_methods($this);

		while (list($key, $myMethod) = each($this->$myMethods)) {
		    echo "&nbsp;&nbsp;&nbsp;".$myMethod."<br>";
		}

		echo "<hr>class vars:<br>";
		$classVars = get_class_vars(get_class($this));

		echo util::readArray($classVars,1);
		echo "<hr>";
		$objectVars = get_object_vars($this);
		echo util::readArray($objectVars,1);
		echo "--------------DEBUGGING END--------------------<br>";	
	}

}	// end of class OPC

?>
