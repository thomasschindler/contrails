<?php

/**
 *	This is the base class for module action classes, which inherit OPC, DB, etc from here
 *
 *	[de] das ist die grundklasse fuer action-klassen der module
 *	[de] alle action klassen erben von ihr. z.b. zeiger auf globale OPC, DB etc. klassen
 *
 *	@version	0.1.0	29.01.04
 *	@author	hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@package	system
 */

/*.
	require_module 'standard';
.*/

class modAction {
		
	/**
	 *	[en] pointer to global output controller
	 *	[de] zeiger auf globalen OutputController
	 *	@var	object OPC	$OPC
	 */

	public /*. OPC .*/ $OPC;

	/**
	 *	[en] pointer to global database wrapper
	 *	[de] zeiger auf globalen Datenbank-Layer
	 *	@var	object DB	$DB
	 */

	public $DB;

	/**
	 *	[en] pointer to global database wrapper
	 *	@var	object db_crud	$CRUD
	 */

	public $CRUD;

	/**
	 *	[en] pointer to global session object
	 *	[de] zeiger auf globale Session
	 *	@var	object SESS $SESS
	 */

	public $SESS;
		
	/**
	 *	[en] pointer to client object
	 *	@var	object 
	 */
		
	public $CLIENT;

	/**
	 *	[en] pointer to global MC object
	 *	@var	object 
	 */
		
	public $MC;

	/**
	 *	[en] current page-id
	 *	@var	int
	 */

	public $pid = 0;


	/**
	 *	name of current module
	 *	@var	string
	 */

	public $mod_name = '';


	/**
	 *	current view id (an md5 hash) (CHECKME)
	 *	@var	string
	 */

	public $vid = '';

	/**
	 *	(DOCUMENTME) this is used a lot
	 *	@var	mixed
	 */

	public $data;

	/**
	 *	(DOCUMENTME)
	 *	Note that __ prefix is reserved in PHP (TODO) name this something else
	 *	@var string[int]
	 */

	private $__msg = /*. (string[int]) .*/ array();

	/*.	forward void function set_view(string $view); .*/

	/**
	 *	constructor / konstruktor
	 *	@return	void
	 */

	function __construct() { }

	/**
	 *	Return a JSON document / object to the client
	 *
	 *	@param	array	$data		(DOCUMENTME)
	 *	@param	array	$urlencode	(DOCUMENTME)
	 *	@return	void
	 */
		
	function json_send($data, $urlencode = null)
	{
		if(is_array($urlencode))
		{
			$lst = array();
			foreach($urlencode as $l) { $lst[$l] = true; }
			$data = $this->json_pre_encode($data, $lst);
		}

		$json = fJSON::encode($data);
		if (!UTIL::is_utf8($json)) { $json = utf8_encode($json); }
			
		// logging
		/*
		$f = fopen(CONF::web_dir().'/json.log','a+');
		fwrite($f,$json."\n");
		fclose($f);

		$f = fopen(CONF::web_dir().'/json.log','a+');
		fwrite($f,var_export(headers_list(),true)."\n");
		fclose($f);
		*/

		ob_clean();
		header("X-JSON: " . $json);
		die;
	}
	
	/**
	 *	generic method for ordering lists
	 *
	 *	(TODO) consider adding / moving this to db_crud object
	 *	(DOCUMENTME) assumptions about state of this object: $this->data[o], $this->data[i], etc
	 *
	 *	@return	bool
	 */

	function order()
	{
		$update = ''
		 . "UPDATE " . $this->data['table']
		 . " SET " . $this->data['table'] . "." . $this->data['o'] . " = " . $this->data['you_o']
		 . " WHERE " . $this->data['table'] . "." . $this->data['i'] . " = '" . $this->data['me_id'] . "'";
		$this->DB->query($update);

		$update = "UPDATE " . $this->data['table']
		 . " SET " . $this->data['table'] . "." . $this->data['o'] . " = " . $this->data['me_o']
		 . " WHERE " . $this->data['table'] . "." . $this->data['i'] . " = '" . $this->data['you_id'] . "'";
		$this->DB->query($update);

		return true;
	}
		
	/**
	 *	Recursively urlencode an array? (CHECKME)
	 *
	 *	@param	array	$data		(DOCUMENTME)
	 *	@param	array	$urlencode	(DOCUMENTME)
	 *	@return	array
	 */

	function json_pre_encode($data, $urlencode = null)
	{
		if (!is_array($urlencode)) { return $data; }

		foreach($data as $k => $v)
		{
				
			if (is_array($v))
			{
				$data[$k] = $this->json_pre_encode($v, $urlencode);
				continue;
			}

			if ($urlencode[$k]) { $data[$k] = rawurlencode($v); }
		}
		return $data;
	}
		
	/**
	 *	[de] Set OPC variables for current module? (CHECKME)
	 *	[de] variablen im OPC setzen, unter dem modul-namen
	 *
	 *	@param	string	$key	Name of an OPC variable on current module (checkme)
	 *	@param	mixed	$val	New value
	 *	@return void
	 */

	function set_var($key, $val) { $this->OPC->set_var($this->mod_name, $key, $val); }
		
	/**
	*	[en] Get the value of an OPC variable on the current module? (CHECKME)
	*	[de] variablen aus OPC lesen, unter dem modul-namen
	*
	*	@param	string	$key	Name of an OPC variable on current module (checkme)
	*	@return	mixed
	*/

	function get_var($key) { return $this->OPC->get_var($this->mod_name, $key); }

	/**
	 *	collects all data set as var 
	 *	and redirects the user to the view specified
	 *	used for preventing double entries in forms via refreshing
	 *	TODO: rewrite this comment
	 *	TODO: check this for SQL injection and other malice
	 *
	 *	@param	string	$v	Name of a view? (CHECKME)
	 *	@return void
	 */

	function redirected_view($v)
	{
		$att = array
		(
			'data[view]' => $v,
			'originating_event' => UTIL::get_post('event'),
			'directevent' => 'redirected_view_perform',
			'event' => 'nil',
			'mod' => $this->mod_name,
			'pid' => UTIL::get_post('pid'),
			'vid' => $this->vid
		);
		
		// add the form variables
		$att['form_error'] = $this->OPC->form_error();
		// get the errors
		$att['error'] = $this->OPC->error();
		$att['warning'] = $this->OPC->warning();
		$att['success'] = $this->OPC->success();
		$att['information'] = $this->OPC->information();
						
		// add all other vars
		$vars = $this->OPC->vars();
		
		$vars = base64_encode(serialize($vars));
		
		//foreach($vars[$this->mod_name] as $k => $v) { $temp[] = $this->build_post_array($v, 'vars[' . $k . ']'); }
		
		$att['vars'] = $vars;
		
		foreach($this->data as $k => $v) { $temp[] = $this->build_post_array($v, 'data[' . $k . ']'); }
		foreach($_GET['data'] as $k => $v)  { $temp[] = $this->build_post_array($v, 'data[' . $k . ']'); }
		foreach($_POST['data'] as $k => $v)  { $temp[] = $this->build_post_array($v, 'data[' . $k . ']'); }
		
		array_walk_recursive($temp,array($this, 'build_post'),&$att);
		
		$lnk = $this->OPC->lnk($att);

		header("Location: ".CONF::baseurl()."/".$lnk);
		die;
	}

	/**
	 * fills the array that holds vars and data post variables
	 */
	function build_post($item,$key,&$ret)
	{
		$ret[$key] = $item;
	}

	/**
	 * Setup the key structure for the variables passed through the link
	 */
	function build_post_array($input,$key)
	{
		$ret = array();
		if(is_array($input))
		{
			foreach($input as $k => $val)
			{
				$ret[] = $this->build_post_array($val,$key.'['.$k.']');
			}
		}
		else 
		{
			if($input != null)
			{
				$ret[$key] = $input;
			}
		}
		
		return $ret;
	}
	
	/**
	 *	this is where the redirected view is carried out
	 *
	 *	@return	bool
	 */

	function redirected_view_perform()
	{
		$vars = unserialize(base64_decode(UTIL::get_post('vars')));
		if(is_array($vars))
		{
			foreach($vars as $mod => $val)
			{
				foreach($val as $k => $v)
				{
					$this->OPC->vars_set($mod,$k,$v);
				}
			}
		}
		$form_error = UTIL::get_post('form_error');
		if($form_error)
		{
			$this->OPC->form_error_set($form_error);
		}
		$error = UTIL::get_post('error');
		if($error)
		{
			foreach($error as $e)
			{
				$this->OPC->error($e);				
			}
		}
		$warning = UTIL::get_post('warning');
		if($warning)
		{
			foreach($warning as $e)
			{
				$this->OPC->warning($e);				
			}
		}
		$success = UTIL::get_post('success');
		if($success)
		{
			foreach($success as $e)
			{
				$this->OPC->success($e);				
			}
		}
		$information = UTIL::get_post('information');
		if($information)
		{
			foreach($information as $e)
			{
				$this->OPC->information($e);				
			}
		}
		$this->set_view($this->data['view']);
		return false;
	}

	/**
	 *	[en] set the view on the output controller for current vid and module
	 *	[de] view fuer einen bestimmten bereich eines modules setzen
	 *
	 *	@param	string	$view	Name of a view? (CHECKME)
	 *	@return	void
	 */

	function set_view($view) {
		$this->OPC->set_view($this->vid, $this->mod_name, $view);
	}
		
	/**
	*	Set the start view / start-view setzen
	*
	*	@param	string	$view			Name of a view? (CHECKME)
	*	@param	bool	$persistent		(DOCUMENTME)
	*	@return void
	*/

	function set_start_view($view, $persistent = false) {
		$this->OPC->set_start_view($this->vid, $this->mod_name, $view, $persistent);
	}

	/**
	 *	set start view or view depending on the var in get_post : __b
	 *	TODO: examine this for security issues
	 *
	 *	@param	string	$view			Name of a view
	 *	@param	bool	$persistent		(DOCUMENTME)
	 *	@return void
	 */

	function set_auto_view($view = 'online', $persistent = true)
	{
		$boxed = !UTIL::get_post('__b') ? $boxed = UTIL::get_post('boxed') : UTIL::get_post('__b');

		$sv = $this->OPC->get_start_view();

		if ($boxed) {
			$this->set_start_view($view, $persistent);
			return;
		}
		
		if (((string)$sv[0] === $this->vid) && ((string)$sv[1] === $this->mod_name))
		{
			$this->set_start_view($view, $persistent);
			return;
		}

		$this->set_view($view);
	}

	/**
	 *	set a module,event and params to return when a certain event is called in me
	 *
	 *	@param	string	$event		name of an event? (CHECKME)
	 *	@param	array	$call		(DOCUMENTME)
	 *	@param	array	$params		Arguments for making a link? (CHECKME)
	 *	@param	string	$mod		Name of a module
	 *	@return	void
	 */

	function set_return_value($event, $call, $params = null, $mod = null)
	{
		$mod = $mod ? $mod : $this->mod_name;
		$this->OPC->lnk_add('__r[' . $mod . '][' . $event . '][c][event]', $call['event']);
		$this->OPC->lnk_add('__r[' . $mod . '][' . $event . '][c][mod]', $call['mod']);

		if (is_array($params)) {
			foreach ($params as $key => $val) {
				$this->OPC->lnk_add('__r['.$mod.']['.$event.'][p]['.$key.']',$val);
			}
		}
	}

	/**
	 *	check an access right / permission
	 *	@param	string	$access_right	Function name, as in access-config
	 *	@param	string	$mod			Name of a module
	 *	@return	bool
	 */

	function access($access_right, $mod = '')
	{
		$mod = (('' !== $mod) ? $mod : $this->mod_name);
		return $this->MC->access($mod, $access_right);
	}

	/**
	 *	add a message to the messages array (CHECKME)
	 *	@param	string	$msg	Message to add? (CHECKME)
	 *	@return int
	 */

	function add_msg($msg = '')
	{
		if ('' === $msg) { return sizeof($this->__msg); }
		$this->__msg[] = $msg;
		$this->set_var('__msg',$this->__msg);
		return sizeof($this->__msg);
	}
	
	/**
	 *	Allow client to download a file
	 *
	 *	@param	string	$data	Contents of file
	 *	@param	string	$name	Name to given to file download
	 *	@param	string	$mime	Mime type of file
	 *	@return	void
	 */

	function download($data = '', $name = '', $mime = '')
	{
		if('' === $data)
		{
			$file = UTIL::get_post('file');
			if (!is_file($file)) { return; }
			$size = filesize($file);
			$data = (string)readfile($file);
		}
		else
		{
			if(is_file($data))
			{
				$file = $data; 				// to extract the name
				$size = @filesize($file);
				$data = @file_get_contents($file);
			}
			else
			{
				$size = mb_strlen($data);	
			}
		}
		
		if ('' === !$name)
		{
			$name = UTIL::get_post('name');
			if(!$name)
			{
				$p = explode("/", $file);
				$name = $p[sizeof($p)-1];
			}	
		}

		if('' === $mime) { $mime = UTIL::get_post('mime'); }
		if('' === $mime) { $mime = 'application/x-download'; }

		ob_clean();
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: public, must-revalidate");
		header("Pragma: hack");
		header("Content-type: " . $mime);
		header("Content-Length: " . (string)$size);
		header("Content-Disposition: attachment; filename=" . $name);
		header("Content-Transfer-Encoding: binary");
		echo $data;
		ob_flush();
		die;
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
	
	function form($form,$id=null,$ignore=array())
	{

		if($id==null)
		{
			$t = &$this->MOF->obtain($form);
		}
		else
		{
			$t = $this->MOF->obtain($form,$id);
		}

		$c = $t->form($ignore);
		
		$f = new FORMS($c);
		
		if(!$f->valid($this->data))
		{
			$this->OPC->error(e::o('generic_form_error',null,null,null,true));
			return false;
		}
		$this->OPC->success(e::o('generic_form_success',null,null,null,true));
		// make sure all integer values are at least set to 0
		foreach($c['fields'] as $fld => $dat)
		{
			if($dat['cnf']['format'] == 'int' && empty($this->data[$fld]))
			{
				$this->data[$fld] = 0;
			}
		}
		$t->set($this->data);

		if($id == null)
		{
			return $this->MOF->register($t);
		}
		return true;
	}

	/**
	 *	Initiialization, set standard values
	 *	(TRANSLATEME)
	 *	[de] initialisierung, setzen von standard-werten
	 *	[de] muss nach dem instanzieren eines action-objectes aufgerufen werden, das 
	 *	[de] geschieht automatisch schon in MC::call_action. hier werden default-
	 *	[de] eigenschaften gesetzt, z.b. $OPC, $DB, [$LANG, $SESS]
	 *
	 *	minimal should only be used under very special circumstances.
	 *  it will lead to you not knowing anything about the user!
	 *	
	 *	@see		OPC::call_view()
	 *	@return		void
	 */

	public function _constructor($minimal=false) 
	{

		$this->OPC	  = &OPC::singleton();
		$this->DB	  = &DB::singleton();
		$this->CRUD   = &db_crud::singleton();
		$this->CRUD->load_return_object(); // set to return an object 
		$this->MOF 	  = &MF::singleton();

		$this->SESS	  = &SESS::singleton();
		$this->MC	  = &MC::singleton();
		
		if($minimal === true)
		{
			$this->CLIENT = &CLIENT::singleton(null,false,true);	
		}
		else
		{
			$this->CLIENT = &CLIENT::singleton();
		}
		
		
		$this->pid	   = (int)UTIL::get_post('pid');
		$this->vid	   = UTIL::get_post('vid');
		$this->data	   = UTIL::get_post('data');
  			
		// is there a start view in get_post?
		/*
		if($this->OPC->persistent_start_view !== true && ($__sv = UTIL::get_post('__sv')))
		{
			$this->OPC->set_start_view($__sv['vid'], $__sv['mod'], $__sv['method'],true);
			$this->vid = $__sv['vid'];
		}
		*/

		if(@$this->OPC->persistent_start_view !== true && ($__sv = UTIL::get_post('__sv')))
		{
			// take this and add it to the links
			$sv = $this->OPC->set_start_view($__sv['vid'], $__sv['mod'], $__sv['method'],true);
			$this->vid = $__sv['vid'];
			$this->OPC->lnk_add("__sv[vid]",$sv[0]);
			$this->OPC->lnk_add("__sv[mod]",$sv[1]);
			$this->OPC->lnk_add("__sv[method]",$sv[2]);
  		}

		$this->MC->__current_mod = $this->mod_name;
  			
		// do we have to return to a other module?
  			
		$__r = UTIL::get_post('__r');
		$event = UTIL::get_post('event');
			
		if (is_array($__r[$this->mod_name][$event]))
		{
			$this->MC->call_action(
				$__r[$this->mod_name][$event]['c'],
				$__r[$this->mod_name][$event]['p']
			);
			unset($_GET['__r']);
			unset($_POST['__r']);
			unset($__r[$this->mod_name][$event]);
		}

		if(is_array($__r))
		{
			foreach($__r as $mod => $v1){
				foreach($v1 as $event => $v2){
					$this->set_return_value($event, $v2['c'], $v2['p'] ,$mod);
				}
			}
		}
	}

}
	

?>
