<?php
/**
 *	This is the base class for module view classes
 *
 *	All view classes inherit from this, using these pointers to OPC, DB, etc
 *
 *	[de]das ist die grundklasse fuer view-klassen der module
 *	[de] alle view klassen erben von ihr. z.b. zeiger auf globale OPC, DB etc. klassen
 *
 *	@version	0.1.0	13.01.04
 *	@author		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@package	system
 */

class modView {
		
	/**
	 *	[en] pointer to global output controller
	 *	[de] zeiger auf globalen OutputController
	 *	@var	object OPC	$OPC
	 */
		
	public /*. OPC .*/ $OPC;

	/**
	 *	[en] pointer to global MC instance
	 *	[de] zeiger auf globale MC instanz
	 *	@var	object	MC	$MC
	 */
	
	public /*. MC .*/ $MC;
		
	/**
	 *	[en] pointer to global database wrapper
	 *	[de] zeiger auf globalen Datenbank-Layer
	 *	@var	object DB	$DB
	 */

	public /*. db_mysql .*/ $DB;

	/**
	 *	[en] pointer to global database wrapper
	 *	@var	object db_crud	$CRUD
	 */

	public $CRUD;

	/**
	*	[en] pointer to global session object
	*	[de] zeiger auf globale Session
	*	@var	object SESS	$SESS
	*/

	public $SESS;
		
	/**
	 *	[en] pointer to global client instance
	 *	[de] zeiger auf globale client instanz
	 *	@var	object	CLIENT	$CLIENT
	 */

	public $CLIENT;
		
	/**
	 *	[en] current page id
	 *	[de] aktuelle page-id
	 *	@var	int
	 */
	
	public $pid = 0;

	/**
	 *	Current view id - md5 hash (CHECKME)
	 *	@var	string
	 */

	public $vid = '';

	/**
	 *	Name of current module
	 *	@var	string
	 */
	
	public $mod_name = '';

	/**
	 *	Template directory (CHECKME)
	 *	@var	string
	 */
	
	public $tpl_dir = '';

	/**
	 *	Resource directory (CHECKME)
	 *	@var	string
	 */
	
	public $rsc_dir = '';
		
	/**
	 *	constructor
	 *	@return void
	 */

	function __construct()
	{
		// reserved
	}

	/**
	 *	variables set in the OPC under the module names
	 *
	 *	[de] variablen im OPC setzen, unter dem modul-namen
	 *
	 *	@param	string	$key
	 *	@param	mixed	$val
	 *	@return	void
	 */

	function set_var($key, $val)
	{
		$this->OPC->set_var($this->mod_name, $key, $val);
	}
		
	/**
	 *	Variables read from the OPC, udner the module names
	 *
	 *	[de] variablen aus OPC lesen, unter dem modul-namen
	 *
	 *	@param	string	$key
	 *	@return	mixed
	 */

	function get_var($key)
	{
		return $this->OPC->get_var($this->mod_name, $key);
	}

	/**
	*	alias for generate_view
	*	$param string $tpl template to use (without extension)
	*/
	function show($tpl)
	{
		return $this->OPC->generate_view($this->tpl_dir . $tpl.'.php');
	}

	/**
	 *	generate view shortcut
	 *
	 *	@param	string	$tpl		Location of template file? (CHECKME)
	 *	@param	bool	$add_path	Prepend template dir? (CHECKME)
	 *	@return	void
	 */

	function generate_view($tpl, $add_path = false)
	{
		if ($add_path) { $tpl = $this->tpl_dir . $tpl; }
		// all modules should modify their tpl calls accordingliy!!!
		if('tpl' === substr($tpl,-3)) { $tpl = substr($tpl,0,-3) . 'php'; }
		$this->OPC->generate_view($tpl);		// returns void
	}

	/**
	 *	get my access rights
	 *
	 *	@param	string	$access_right	Name of a permission? (CHECKME)	
	 *	@param	string	$mod			Name of a module
	 *	@return	bool
	 */

	function access($access_right, $mod = '')
	{
		$mod = ('' !== $mod) ? $mod : $this->mod_name;
		return $this->MC->access($mod,$access_right);
	}

	/**
	 *	(DOCUMENTME)
	 *	@return	array
	 */
		
	function get_msg()
	{
		$msg = $this->get_var('__msg');
		if (!is_array($msg)) { $msg = array(); }
		return $msg;
	}

	/**
	 *	alert the messages that have been set in action
	 *
	 *	@param	string	$type	Alert box type
	 *	@return	string	A series of Javascript alert statements? (CHECKME)
	 */

	function alert_msg($type = 'alert')
	{
		$msg = $this->get_var('__msg');
		if (!is_array($msg)) { return false; }

		$alert = '';
		foreach($msg as $line) { $alert .= (string)$line . '\\' . 'n'; }

		return UTIL::get_js($type . '("' . $alert . '")');
	}

	/**
	 *	mdb handling (DOCUMENTME)
	 *	@param	array	$params		Additional arguments to MC->call_action
	 *	@return	mixed				Return value of module action
	 */

	function mdb_get($params = array())
	{
		$action = array('mod' => 'wysiwyg', 'event' => 'mdb_get');
		return $this->MC->call_action($action, $params);
	}

	/**
	 *	(DOCUMENTME)
	 *	@param	int		$id			(DOCUMENTME)
	 *	@param	array	$params		additional arguments to MC->call_action()
	 *	@return	mixed				Return value of module action
	 */

	function mdb_show($id, $params = array())
	{
		$params['mdb_id'] = $id;
		$action = array('mod' => 'wysiwyg', 'event' => 'mdb_show');
		return $this->MC->call_action($action, $params);
	}

	/**
	 *	(DOCUMENTME)
	 *	@param	int		$id			(DOCUMENTME)
	 *	@param	array	$params		additional arguments to MC->call_action()
	 *	@return	mixed				Return value of module action
	 */

	function mdb_download($id, $params = array())
	{
		$params['id'] = $id;
		$action = array('mod' => 'wysiwyg', 'event' => 'mdb_download');
		return $this->MC->call_action($action, $params);
	}
		
	/**
	 *	Sort a database result set? (CHECKME)
	 *	TODO: tody and refactor, strix 2012-02-21
	 *
	 *	@param	object	$r	db result set to order
	 *	@param	array	$d	keys to display? (CHECKME)
	 *	@param	string	$o	order by field? (CHECKME)
	 *	@param	string	$i	ID for the table? Pri key? (CHECKME)
	 *	@param	string	$t	Name of table? (CHECKME)
	 *	@param	string	$e	Event to send? (CHECKME)
	 *	@return	array	
	 */

	function order($r, $d, $o, $i, $t , $e = null)
	{
		$ret = array();
		while($r->next())
		{
			$cnt++;
			$row = $r->r();
			$tmp = array();

			foreach ($row as $key => $val)
			{
				if (in_array($key, $d)) { $tmp[] = $val; }
				if ($key == $o) { $ord = $val; }		// assigned but not used?
			}
			// create the links

			// down
			if($down = $r->nf($o))
			{
				$ret[$cnt]['down'] = $this->OPC->lnk(
					array(
						'event' => $e,
						'directevent' => 'order',
						'mod' => $this->mod_name,
						'data[table]' => $t,
						'data[me_o]' => $row[$o],
						'data[me_id]' => $row[$i],
						'data[you_o]' => $down,
						'data[you_id]' => $r->nf($i),
						'data[i]' => $i,
						'data[o]' => $o
					)
				);					
			}

			// up
			if($up = $r->lf($o))
			{
				$ret[$cnt]['up'] = $this->OPC->lnk(
					array(
						'event' => $e,
						'directevent' => 'order',
						'mod' => $this->mod_name,
						'data[table]' => $t,
						'data[me_o]' => $row[$o],
						'data[me_id]' => $row[$i],
						'data[you_o]' => $up,
						'data[you_id]' => $r->lf($i),
						'data[i]' => $i,
						'data[o]' => $o
					)
				);
			}

			$ret[$cnt]['label'] = implode(" ", $tmp);
			$ret[$cnt][$i] = $row[$i];
		}

		return $ret;
	}
	
	function form($form,$id=null,$ignore=array())
	{
		if($id === null)
		{
			$t = $this->MOF->obtain($form);
		}
		else
		{
			$t = $this->MOF->obtain($form,$id);
		}
		$f = new FORMS($t->form($ignore));
		return $f;
	}


	/**
	* create and return a link
	*/
	function lnk($a)
	{
		if(!isset($a['mod']))
		{
			$a['mod'] = $this->mod_name;
		}
		return $this->OPC->lnk($a);
	}

	/**
	 *	Set default properties and link framework objects.
	 *
	 *	Must occur after?(CHECKME) view object is instantiated
	 *
	 *	[de] initialisierung, setzen von standard-werten
	 * 
	 *	[de] muss nach dem instanzieren eines view-objectes aufgerufen werden, das 
	 *	[de] geschieht automatisch schon in OPC::call_view. hier werden default-
	 *	[de] eigenschaften gesetzt, z.b. $OPC, $DB, [$LANG, $SESS]
	 *
	 *	@see	OPC::call_view()
	 *	@return	void
	 */

	public function _constructor() {
		$this->OPC    = &OPC::singleton();
		$this->DB     = &DB::singleton();
		$this->CRUD   = &db_crud::singleton();
		$this->CRUD->load_return_object(); // set to return an object 
		$this->MOF 	  = &MF::singleton();
		
		$this->SESS   = &SESS::singleton();
		$this->CLIENT = &CLIENT::singleton();
		$this->MC     = &MC::singleton();
			
		$this->pid     = (int)UTIL::get_post('pid');
		$this->vid     = (int)UTIL::get_post('vid');
			
		$this->OPC->lnk_add('vid',$this->vid);

		// check to see, if we can transport via vid and call via data.....
  		$this->data	   = UTIL::get_post('data');

  		//if there is a custom folder we use this for tpl
  		
		/*NOTE:
  			due to change: should be possible to decide on different layouts in custom
  			directory - must be handled by the mod though
  			-> maybe through instance config???
  			-> we have the vid here, so we could have a config file for each instance
  			-> spares us db lookups
		*/
			
		/*
		$this->tpl_dir = ''
		 . CONF::web_dir()
		 . '/tpl/' . $this->OPC->default_layout() . '/'
		 .  $this->mod_name .'/';

		if(!is_dir($this->tpl_dir)){
			$this->tpl_dir = CONF::web_dir() . '/tpl/oos/'. $this->mod_name .'/';
		}
		*/
			
		$this->tpl_dir = ''
		 . CONF::inc_dir()
		 . '/mods/' . $this->mod_name
		 . '/template/' . $this->OPC->default_layout() . '/';

		if (!is_dir($this->tpl_dir))
		{
			$this->tpl_dir = CONF::inc_dir() . '/mods/' . $this->mod_name . '/template/contrails/';
		}			
			
		$this->MC->__current_mod = $this->mod_name;
			
  		// is a start_view set?
		if ($this->OPC->persistent_start_view)
		{
			$start_view = $this->OPC->get_start_view();
			// take this and add it to the links
  			$this->OPC->lnk_add("__sv[vid]",$start_view[0]);
  			$this->OPC->lnk_add("__sv[mod]",$start_view[1]);
  			$this->OPC->lnk_add("__sv[method]",$start_view[2]);
  		}

		//
		$boxed = (bool)UTIL::get_post('__b');
		if (($boxed) && ($this->mod_name === (string)UTIL::get_post("mod")))
		{
			$this->OPC->lnk_add('__b',1);
			$this->set_var('boxed',1);
		}

		$vid = UTIL::get_post('vid');
		$this->OPC->lnk_add('vid',$vid);
	}		
	
}

?>
