<?php

/**
 *	MySQL database wrapper
 *
 *	CONTENTS:
 *
 *	For managing database connections:
 *		
 *		link(...)				- get a link resource?
 *		connect(...)			- connect to (master?) database server
 *		connect_slave(..)		- connect to a slave database server
 *
 *	To execute queries:
 *
 *		query(...)				- for queries which return results (eg, SELECT, SHOW TABLES, etc)
 *		query_bool(...)			- for bool queries (eg, INSERT, UPDATE, DELETE, CREATE TABLE, etc)
 *		insert_id(...) 			- returns ID of last inserted record
 *
 *	Sanitization, escaping and markup:
 *
 *		escape(...)				- escape values to be used in queries
 *		unescape(...)			- clean up values on their way back out
 *		unescape(...)			- clean up values on their way back out
 *		query_update_clean(...)	- (DOCUMENTME)
 *
 *	ROADMAP:
 *
 *	Core and module code should generally use the db_crud object to access the database.  Where
 *	appropopriate to call this object directly, please convert code to use query_bool unless
 *	a db_result object is needed / expected.  If more complicated operations must be performed, 
 *	please use templated queries to reduce the chance of SQL injection.
 *
 *	SQL EVENTS:
 *
 *	(DOCUMENTME) Perhaps more generic events raised by crud object would be simpler?
 *
 *	CACHING OF SQL QUERIES
 *
 *	(DOCUMENTME) Mechanism and rationale of this caching system
 *	(TODO) Benchmark / test cache performance, compare to caching of single objects via db_crud,
 *	and with respect to optimum balance of memory use and database use.
 *
 *	[de] db-klasse fuer mysql
 *	
 *	Created: 12 05 2004
 *
 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@version		1.0
 *	@since			1.0
 *	@package		util
 */

class db_mysql {
	
	/*
	 *	DBMS hostname / db-host-adresse
	 *	@var string
	 */

	private $db_host = '';
	
	/*
	 *	MySQL username / db-benutzername
	 *	@var string
	 */

	private $db_user = '';
	
	/*
	 *	Password for MySQL user / db-passwort
	 *	@var string
	 */

	private $db_pass = '';
	
	/*
	*	Database name / db-name
	*	@var string
	*/

	private $db_name = '';
	
	/*
	 *	[en] handle to database connection
	 *	[de] verbindungs-kennung von mysql_connect()
	 *	@var int
	 */

	private $db_link = /*. (resource) .*/ null;
	
	/**
	 *	[en] Set to true when a connection is established with the database server
	 * 	[de] variable die angibt, ob wir mit db-server verbunden sind
	 *	@var bool
	 */

	private $connected = false;
	
	/**
	 *	[en] controls whether sql-events are executed (CHECKME)
	 *	[de] werden sql-events ausgefuehrt
	 */

	private $do_sql_events = true;

	/**
	 *	Debug mode flag
	 *	@var bool
	 */
	
	public $debug = false;

	/**
	 *	(DOCUMENTME)
	 *	@var resource
	 */

	private $db_link_slave;

	/**
	 *	(DOCUMENTME)
	 *	@var bool
	 */

	private $connected_slave = false;

	/**
	 *	Pointer to MC object
	 *	@var object
	 */

	private /*. MC .*/ $MC = null;

	/**
	 *	A flat list of all tables in the database
	 *	@var string[int]
	 */

	private $tables = /*. (string[int]) .*/ array();

	/**
	 *	Set of schema loaded from the database, by name
	 *	@var db_table[string]
	 */

	private $schema = /*. db_table[string] .*/ array();

	/*. forward string function mysql_real_unescape_string(string $input, int $checkbr); .*/

	/**
	 *	constructor / konstruktor
	 *
	 *	@param	array	$options	db-options (db_type, db_host, db_user, db_pass, db_name)
	 *	@return void
	 */

	function __construct($options) 
	{
		if (array_key_exists('master', $options)) { $options = $options['master']; }

		$this->db_host = $options['db_host'];
		$this->db_user = $options['db_user'];
		$this->db_pass = $options['db_pass'];
		$this->db_name = $options['db_name'];
		$this->MC = &MC::singleton();
		//$this->CRUD = new db_crud();
	}

	/*	Database connection / init methods ------------------------------------------------------ */	

	/**
	*	[en] get the link 
	*	@return mysql_link
	*/
	
	function link()
	{
		return $this->db_link;
	}
	
	/**
	*	[de] connect to the database server and select our database
	*	[de] connect zu db-server und select der db
	*	@return	void
	*/

	public function connect() 
	{		
		$this->db_link = @mysql_connect($this->db_host, $this->db_user, $this->db_pass,true);

		if (!is_resource($this->db_link)) {
			trigger_error(mysql_error(), E_USER_ERROR);
		}
		
		if (!mysql_select_db($this->db_name, $this->db_link)) {
			trigger_error(mysql_error(), E_USER_ERROR);
		}

		$this->connected = true;
	}

	/**
	 *	[en] connect to slave database server and select our database
	 *	[de] connect zu db-server und select der db
	 *	@param	array	$options	DB configuration set
	 *	@return	void
	 */
	
	function connect_slave($options)
	{
		$this->db_link_slave = @mysql_connect($options['db_host'], $options['db_user'], $options['db_pass'],true);
		if (!is_resource($this->db_link_slave)) 
		{
			trigger_error(mysql_error(), E_USER_ERROR);
		}
		
		if (!mysql_select_db((string)$options['db_name'], $this->db_link_slave)) 
		{
			trigger_error(mysql_error(), E_USER_ERROR);
		}
		$this->connected_slave = true;
	}
	
	/**
	 *	Returns connection state of this object, used by unit tests
	 *	@return	bool
	 */

	function is_connected() {
		if (true == $this->connected) { return true; }
		return false;
	}

	/**
	 *	Run a database query and return a db_result object
	 *
	 *	This method should be used for SELECT and other queries which return a recordset.  For
	 *	INSERT, UPDATE, DELETE, CREATE TABLE, etc, please use related $this->query_bool(...)
	 *
	 *	(DOCUMENTME) Description of how slaves are arranged / called, and how db cache works
	 *	(TODO) Test efficiency and tradeoffs of caching this way, suspect a more efficient
	 *	cache is possible, particularly when db_crud is widely implemented.
	 *
	 *	[de] fuehrt uebergebenes sql aus
	 *	[de] liefert ein result-objekt zurueck
	 *
	 *	@param	string	$sql	SQL query to run
	 *	@param	bool	$cache	Cache results?
 	 *	@return	db_result
	 */

	public function query($sql, $cache = true) {

		// cache sqls
		if (true == $cache) {
			static /*. array .*/ $__sql_cache;
			if (false == is_array($__sql_cache)) { $__sql_cache = array(); }
			if (true == array_key_exists($sql, $__sql_cache))
			{
				$__sql_cache[$sql]->reset();
				return $__sql_cache[$sql];
			}
		}
		
		if (!$this->connected) 
		{
			trigger_error('no connect - no select', E_USER_ERROR);
		}
	
		$lnk = -1;
		if ($this->connected_slave)	{ $lnk = &$this->db_link_slave;	 }
		else { $lnk = &$this->db_link; }

		$query_id = mysql_query($sql, $lnk);
		if ($this->debug) { MC::debug($sql, 'db-debug'); }
		
		if (false === $query_id) {
			trigger_error('SQL-Syntax-Error:' . $sql . ' (' . mysql_error() . ')', E_USER_WARNING);
		}

		if(!isset($query_id)) {
			trigger_error('SQL ERROR! no connection', E_USER_WARNING);
		}

		//	TODO: better debugging / logging of database queries, especially for integration
		//	with functional testing, benchmarking and load testing tools.
		#MC::debug($sql);

		// create a new db_result object / neues result-objekt anlegen
		$result = new db_result();

		//	[en] Check if we have results, or a simple boolean return
		//	[de] wir versuchen an hand von is_resource() zu entscheiden, ob wir
		//	[de] ergebnisse haben (sprich obs ein SELECT o.ae. war)

		$result_data = array();
		if (is_resource($query_id)) {
			while($res = mysql_fetch_assoc($query_id)) {
				$result_data[] = $res;
			}
		}
		
		//	DEPRECATED: please use db_crud object or query_bool for inserts
		//	TODO: remove line below when all inserts have been moved over to more appropriate method

		if ('insert' === strtolower(substr(trim($sql), 0, 6))) { return @mysql_insert_id($lnk);	}
		
		$result->set($result_data);

		if ((isset($query_id)) && ($query_id != 1))	{ @mysql_free_result($query_id); }
		
		unset($result_data);

		// cache the result
		if ($cache) { $__sql_cache[$sql] = $result; }

		return $result;
	}
	
	/**
	 *	Run a database query expected to return boolean
	 *
	 *	This is for operations such as INSERT, UPDATE and DELETE which do not return a resource.
	 *	Cache updates should be performed by calling code (eg: when a record is deleted or updated)
	 *
	 *	On returning false, calling code should check $this->err_msg
	 *
	 *	@param	string	$sql	Query to be run
	 *	@return	bool
	 */

	public function query_bool($sql)
	{
		if (!$this->connected) { trigger_error('no connect - no select', E_USER_ERROR); }
	
		//	(DOCUMENTME) link to documentation on how slaves are arranged
		$lnk = -1;
		if ($this->connected_slave)	{ $lnk = &$this->db_link_slave;	 }
		else { $lnk = &$this->db_link; }

		if ($this->debug) { MC::debug($sql, 'db-debug'); }
		$result = mysql_query($sql, $lnk);

		if (!isset($result)) { trigger_error('SQL ERROR! no connection', E_USER_WARNING); }
		
		if (false == $result) { return false; }

		if (is_resource($result)) {
			$msg = 'Resource returned for boolean query, please use query().';
			trigger_error($msg, E_USER_WARNING);
		}
		
		return true;
	}

	/**
	 *	[en] returns ID of previous insert operation
	 *	[de] liefert ID einer vorherigen insert-operation
	 *
	 *	@return	int	ID
	 */

	public function insert_id() 
	{
		if ($this->connected_slave) { $lnk = &$this->db_link_slave;	}
		else { $lnk = &$this->db_link; }
		$id = @(int)mysql_insert_id($lnk);
		return $id;
	}

	/* String sanitization and escaping --------------------------------------------------------- */

	/**
	 *	(DOCUMENTME)
	 *	@param	string	$i	INSERT statement? (CHECKME)
	 *	@return	string
	 */

	function query_update_clean($i)
	{
		$i = trim($i); 
		$i = $this->mysql_real_unescape_string($i,1);
		$i = stripslashes($i);
		$i = preg_replace("/''/","'",$i);
		$i = preg_replace('/""/','"',$i);
		$i = (
		 ((substr($i,0,1) === "'") || (substr($i,0,1) === '"'))
		 ? '"' . mysql_real_escape_string(substr($i,1,-1)).'"'
		 : $i
		 ); 
		return $i;
	}

	/**
	 *	Alias of mysql_real_escape_string
	 *	@param	string	$s		String to be escaped
	 *	@return	string
	 */

	function escape($s) { return mysql_real_escape_string($s); }

	/**
	 *	Alias of stripslashes
	 *	@param	string	$s		String to be unescaped
	 *	@return	string
	 */

	function unescape($s) { return stripslashes($s); }

	/**
	 *	Unescape a string (CHECKME)
	 *	@param	string	$input		Strign to unescape
	 *	@param	int		$checkbr	If 1 removes \r, if 2 also converts \n ro <br>
	 *	@return	string
	 */

	function mysql_real_unescape_string($input, $checkbr) 
	{ 
		$output = $input; 
		$output = str_replace("\\\\", "\\", $output); 
		$output = str_replace("\'", "'", $output); 
		$output = str_replace('\"', '"', $output); 

		if (1 == $checkbr) 
		{ 
			$output = str_replace('\n\r', '\n', $output); 
			$output = str_replace('\r\n', '\n', $output); 
			$output = str_replace('\r', '\n', $output); 
			$output = str_replace('\n', ' ', $output); 
		} 
		else if (2 == $checkbr) 
		{ 
			$output = str_replace('\n\r', '\n', $output); 
			$output = str_replace('\r\n', '\n', $output); 
			$output = str_replace('\r', '\n', $output); 
			$output = str_replace("\n", "<br>", $output); 
		}

		return $output;
	}
	
	/**
	 *	Limit a SQL query to $num results, return db_result object
	 *
	 *	(DOCUMENTME) what uses this?
	 *	(TODO) can something more elegent replace this?s
	 *
	 *	@param	string	$sql		SQL SELECT statement
	 *	@param	int		$num		Max number of results, default is 10
	 *	@param	array	$params		(DOCUMENTME)
	 *	@param	bool	$cache		Cache DB results
	 *	@return	object	
	 */

	function paging_query($sql, $num = 10, $params = array(), $cache = true)
	{
		$r = $this->query($sql,$cache);
		$resultnum = $r->nr();
		
		if (preg_match("/LIMIT/",$sql))
		{
			$p = explode("LIMIT",$sql);
			$sql = $p[0];
		}
		
		elseif(preg_match("/limit/",$sql))
		{
			$p = explode("limit",$sql);
			$sql = $p[0];
		}

		$token = md5($sql)."_offset";
		
		$offset = (bool)UTIL::get_post($token) ? (int)UTIL::get_post($token) : 0;

		$sql .= " LIMIT " . ($offset * $num) . ", " . ($num+1);
		$r = $this->query($sql, $cache);

		$OPC = &OPC::singleton();

		if (!$params['pid']) { $params['pid'] = $OPC->pid(); }

		if ($r->nr() > $num)
		{
			$r->rm($num);
			$params[$token] = ($offset+1);
			$r->nav['next'] = $OPC->lnk($params);
		}

		if (0 != $offset)
		{
			$params[$token] = ($offset-1);
			$r->nav['last'] = $OPC->lnk($params);
		}

		$r->nav['num'] = $resultnum;
		$r->nav['current'] = $offset+1;

		if ($resultnum > $num)
		{
			$pages = $resultnum / $num;
			for ($i = 0; $i < $pages; $i++)
			{
				$params[$token] = $i;
				$r->nav['pages'][($i+1)] = $OPC->lnk($params);
			}
		}

		return $r;
	}
	
	/*	templated queries ----------------------------------------------------------------------- */

	/**
	 *	Construct / run a templated query and return a recordset
	 *
	 *	This should be used for queries which return a result set, eg SELECT or DESCRIBE
	 *	See notes in db_query.inc.php for information on templating and constraint types.
	 *
	 *	@param	string		$template	SQL template with %%placeholders%%
	 *	$param	string[string]	$values		dict of placeholder names and values
	 *	$param	string[string]	$constraints	doct of placeholder names and constraints
	 *	@return	db_result			Will be empty on failure
	 */

	public function templated_query($template, $values, $constraints)
	{
		$empty = new db_result();
		$query = new db_query($template, $values, $constraints);

		if ('' !== $query->err_msg)
		{
			$this->err_msg = "Could not construct templated query:\n" . $query->err_msg;
			return $empty;
		}

		$result = $this->query($query->sql);
		return $result;
	}

	/**
	 *	Construct / run a templated query and return a bool
	 *
	 *	This should be used for operations which return a boolean status, eg DELETE, UPDATE and INSERT
	 *	See notes in db_query.inc.php for information on templating and constraint types.
	 *
	 *	@param	string		$template	SQL template with %%placeholders%%
	 *	$param	string[string]	$values		dict of placeholder names and values
	 *	$param	string[string]	$constraints	doct of placeholder names and constraints
	 *	@return	bool
	 */

	public function templated_query_bool($template, $values, $constraints)
	{
		$query = new db_query($template, $values, $constraints);

		if ('' !== $query->err_msg) 
		{
			$this->err_msg = "Could not constrct templated bool query:\n" . $query->err_msg;
			return false;
		}

		$result = $this->query_bool($query->sql);
		return $result;
	}

	/**
	 *	Construct and run a templated query and return a result set resource 
	 *
	 *	DEVELOPMENT PROPOSAL - unimplemented pending feedback
	 *
	 *	Since db_result objects load all records into an array they can only hold small result sets.
	 *	This proposed method would execute a templated query and return a handle to the recordset
	 *	(DBMS resource) which could then be used to retrieve rows from arbitrarily large result sets
	 *	using $this->fetch_assoc($resource) (wrap mysql_fetch_assoc($resource)).
	 *
	 *	The alternative to implementing this method is to fix the db_result object so that it only
	 *	loads records from the result set as needed, again via $this->fetch_assoc($resource)
	 *
	 *	@param	string		$template	SQL template with %%laceholders%%
	 *	@param	string[string]	$values		dict of placeholder names and values
	 *	@@param	string[string]	$constraints	dict of placeholder names and constraints
	 */

	public function templated_query_res($template, $values, $constraints)
	{
		//	(TODO)
	}

	/*	table schema cache ---------------------------------------------------------------------- */

	/**
	 *	Load a table schema from the database into $this->schema array/cache
	 *
	 *	Note that this does not return the schema object, just adds it to the cache.
	 *
	 *	@param	string	$table_name		Name of an extant database table
	 *	@param	bool	$force_reload	Set true to reload cached schema
	 *	@return	bool					True if schema cached, false if not.
	 */

	public function load_schema($table_name, $force_reload = false)
	{
		//	Check that table exists
		if (false == $this->table_exists($table_name)) {
			$this->err_msg = "Table $table_name not found, or database error.";
			return false;
		}

		//	Check if already cached
		if ((in_array($table_name, $this->schema)) && (!$force_reload))
		{
			$this->err_msg = '';
			return true;
		}

		//	Load and cache the schema
		$schema = new db_table($table_name);
		if (false == $schema->loaded) {
			$this->err_msg = "Could not load schema for $table_name.";
			return false;
		}

		$this->schema[$table_name] = $schema;
		$this->err_msg = '';
		return true;
	}

	/**
	 *	Load / return a table schema object from cache
	 *
	 *	@param	string	$table_name	Name of a database table
	 *	@return	db_table
	 */

	public function get_schema($table_name, $force_reload = false)
	{
		if ((false == array_key_exists($table_name, $this->schema)) || (true == $force_reload))
		{
			$check = $this->load_schema($table_name, $force_reload);
			if (false == $check) { return null; }
		}

		return $this->schema[$table_name];
	}

	/**
	 *	Set constraints on field values
	 *	@param	string			$table_name		Name of a database table
	 *	@param	string[string]	$constraints	Dict of 'field_names' => 'constraint, constraint'
	 *	@return	bool			True if set, false if error or table not found
	 */

	public function set_constraints($table_name, $constraints) {
		if (false == $this->load_schema($table_name)) { return false; }
		$schema = $this->schema[$table_name];

		$check = $schema->set_constraints($constraints);
		$this->schema[$table_name] = $schema;
		return $check;
	}

	/*	deprecated CRUD operations -------------------------------------------------------------- */

	/**
	 *	Insert using table config as template (only fields in $data are used)
	 *
	 *	(DEPRECATED) please use db_crud->create() instead
	 *	(TODO) remove this once db_crud is fully implemented and all calls to this have been
	 *	removed from the codebase, or rewrite to call CRUD and issues dev / debug notice.
	 *
	 *	[de] insert mit table-config
	 *	[de] es werden nur spalten berucksichtigt, die in $data gesetzt sind
	 *
	 *	@param	string	$table	table name / tabellenname
	 *	@param	array	$data	data / daten
	 *	@return	int		$id		new record id / des neuen datensatzes
	 */

	public function insert($table, $data) 
	{

		$conf = array();
		$mm_fields = array();
		$sql = '';

		if(!is_array($table)) { $conf = $this->MC->table_config($table); }
		else { $conf = $table; }

		$db_table = $conf['table']['name'];
				
		$sql_fields = $sql_values = array();

		if (
			(is_array($conf['table']['exclude_sys_fields'])) &&
			(in_array('pid', $conf['table']['exclude_sys_fields']))
		) {
			// TODO: find out what should be here and do it, strix 2012-02-21
		}
		else 
		{
			$sql_fields[] = 'pid';
			$sql_values[] = $data['pid'];
		}
		
		foreach ($conf['fields'] as $field => $info) 
		{
			if ($info['cnf']['db_ignore']) { continue; }

			//	[en] default value from config, instead of data
			//	[de] default-wert aus konfig, statt data
			if ($info['cnf']['default'] && !isset($data[$field])) 
			{
				$data[$field] = $info['cnf']['default'];
			}
			
			//	[en] Skip fields not present in $data array
			//	[de] keine daten, kein insert

			if ((!array_key_exists($field, $data)) && (!isset($data[$field]))) { continue; }
			
			$sql_data = $data[$field];
			
			//	[en] format fields according to type set in the table config
			//	[de] je nach typ, passiert noch was

			switch((string)$info['cnf']['type']) {

				case 'select':
					// realtions-felder kommen nicht in dieses sql mit rein
					if (isset($info['cnf']['relation'])) {
						$mm_fields[]  = $field;
						continue 2;
					}
					break;		//..................................................................

				case 'checkbox':
					// realtions-felder kommen nicht in dieses sql mit rein
					if (isset($info['cnf']['relation'])) {
						$mm_fields[]  = $field;
						continue 2;
					}
					break;		//..................................................................

				case 'date':
					// datums-felder werden in timestamp gewandelt
					$sql_data = mktime(
						(int)$sql_data['hour'],
						(int)$sql_data['minute'],
						(int)$sql_data['second'], 
						(int)$sql_data['month'],
						(int)$sql_data['day'],
						(int)$sql_data['year']
					);
					break;		//..................................................................
			}
			
			$sql_fields[] = $field;
			$sql_values[] = "'" . addslashes((string)$sql_data) . "'";
		}
		
		//-- auto fields / auto-felder
		if (is_array($conf['table']['sys_fields'])) {
			foreach($conf['table']['sys_fields'] as $name => $field) {
				switch($name) {
					case 'created':
						$sql_fields[] = $field;
						$sql_values[] = time();
						break;

					case 'changed':
						$sql_fields[] = $field;
						$sql_values[] = time();
						break;
				}
			}
		}

		if (isset($data['vid'])) {
			$sql_fields[] = 'vid';
			$sql_values[] = "'" . (string)$data['vid'] . "'";
		}
		
		$sql = ''
		 . 'INSERT INTO ' . (string)$db_table . ' (' . implode(', ', $sql_fields).')'
		 . ' VALUES (' . implode(', ', $sql_values).')';

		@$this->query($sql);   
		$last_id = $this->insert_id();
		#echo $last_id;

		// We have mm relationships / haben wir mm beziehungen
		foreach($mm_fields as $field) {
			$sql_values = array();
			if (!is_array($data[$field])) { $data[$field] = explode(',', $data[$field]); }
			
			foreach($data[$field] as $fid) {
				$sql_values[] = '(' . $last_id . ', \'' . addslashes((string)$fid) . '\')';
			}
			
			$sql = ''
			 . 'INSERT INTO ' . (string)$conf['fields'][$field]['cnf']['relation']['mm']
			 . ' (local_id, foreign_id) '
			 . ' VALUES ' . implode(', ', $sql_values);

			@$this->query($sql);
			
		}
		
		return $last_id;
	}
	
	
	/**
	 *	Delete one or more records taking table config into account
	 *
	 *	(DEPRECATED) please use db_crud->delete(...) instead.
	 *	(TODO) remove when safe to do so
	 *
	 *	[de] delete mit table-konfig
	 *	[de] wenn trashcan-flag vorhanden, wird das gesetzt, sonst ganz geloscht
	 *
	 *	@param	string	$table	tabellen-name
	 *	@param	array	$ids	'PKEY-NAME' => 'ID' oder 'PKEY-NAME' => array('IDS')
	 *	@param	bool	$no_trash	wenn true, wird datensatz nicht in trashcan gelegt, selbst wenn so konfiguriert
	 *	@return void
	 */

	public function delete($table, $ids, $no_trash = false) {

		$conf = $this->MC->table_config($table);
		
		if (!is_array($ids)) $ids = array($ids);
		
		$pkey = array_keys($ids);
		$pkey = $pkey[0];

		if (!is_array($ids[$pkey])) $ids[$pkey] = array($ids[$pkey]);
		
		if ((isset($conf['table']['sys_fields']['deleted'])) && (false == $no_trash)) {
			$field = $conf['table']['sys_fields']['deleted'];

			$sql = ''
			 . 'UPDATE ' . (string)$conf['table']['name']
			 . ' SET ' . (string)$field . '=1'
			 . ' WHERE id IN (' . implode(', ', $ids[$pkey]) . ')';

			@$this->query($sql);
			
		}
		else
		{
			$sql = ''
			 . 'DELETE FROM ' . (string)$conf['table']['name']
			 . ' WHERE ' . $pkey . ' IN (' . implode(', ', $ids[$pkey]) . ')';

			@$this->query($sql);
			
			// there exists an mm relationship / haben wir relationen ?
			foreach($conf['fields'] as $field => $info) {
				if (isset($info['cnf']['relation']) && $info['cnf']['relation']['mm']) {
					$sql = ''
					 . 'DELETE FROM ' . $info['cnf']['relation']['mm']
					 . ' WHERE local_id IN (' . implode(', ', $ids[$pkey]) . ')';
					@$this->query($sql);
				}
			}
			
		}
		
		$params = array();
		$params['id'] = $ids['id'];
		$params['table'] = $table;
		
		if ($this->do_sql_events) { $this->MC->call_event($table, 'delete', 'post', $params); }
	}
	
	/**
	 *	[en] Update one or more records, using table config as template
	 *	[de] update mit table-konfig
	 *
	 *	(DEPRECATED) please use db_crud->update(...) instead
	 *	(TODO) remove once db_crud fully implemented and tested, and any calls to this have been
	 *	removed.
	 *
	 *	@param	string	$table	table name / tabellenname
	 *	@param	array	$data	associative array of fields and value / ass. daten-array
	 *	@param	array	$ids	array of primary keys / ass. array von pkeys
	 *	@return	int				(DOCUMENTME) / anzahl ge-updateter saetze
	 */

	function update($table, $data, $ids) {
		$conf = array();
		if (!is_array($table)) { $conf = $this->MC->table_config($table); }
		else { $conf = $table; }

		if (!is_array($ids)) { $ids = array('id' => $ids); }
		
		$table = $conf['table']['name'];
		
		$sql_fields = $mm_fields = array();

		foreach($conf['fields'] as $field => $info) {

			// no data, no insert / keine daten, kein insert			
			if ((!array_key_exists($field, $data)) && (!isset($data[$field]))) { continue; }
			
			$d = $data[$field];
			if (is_array($data[$field])) {
				foreach($data[$field] as $d)
				{
					//TODO: discover what should happen here, strix 2012-02-21
				}
			}
			
			if ('select' === (string)$info['cnf']['type']) {
				if (isset($info['cnf']['relation'])) {
					$mm_fields[]  = $field;
					continue;
				}	
			}

			// also for checkbox
			if ('checkbox' === (string)$info['cnf']['type']) {
				if (isset($info['cnf']['relation'])) {
					$mm_fields[]  = $field;
					continue;
				}
			}
			
			$sql_fields[] = (string)$field . '=\'' . addslashes($d) . '\'';
		}
		
		//-- auto fields / auto-felder
		if (is_array($conf['table']['sys_fields'])) {
			foreach($conf['table']['sys_fields'] as $name => $field) {
				switch((string)$name) {
					case 'changed': $sql_fields[] = (string)$field . '=' . time(); break;
				}
			}
		}

		//-- pkeys
		$pkeys = array();
		foreach($ids as $id_field => $id_val) {
			$pkeys[] = (string)$id_field . '=\'' . addslashes((string)$id_val) . '\'';
		}
		
		$sql = ''
		 . 'UPDATE ' . $table . ' SET ' . implode(',', $sql_fields)
		 . ' WHERE ' . implode(' AND ', $pkeys);

		@$this->query($sql);
	
		//	[en] We have mm relationships (since we have only one (the first) pkey?)
		//	[de] haben wir mm beziehungen (da haben wir nur einen (den ersten) pkey !?)

		reset($ids);
		list($a, $mm_pkey) = each($ids);
		foreach($mm_fields as $field) {
			
			$sql = ''
			 . 'DELETE FROM ' . (string)$conf['fields'][$field]['cnf']['relation']['mm']
			 . ' WHERE local_id=' . (int)$mm_pkey;

			@$this->query($sql);
			
			$sql_values = array();
			foreach($data[$field] as $fid) {	
				$sql_values[] = '(' . (int)$mm_pkey . ', \'' . addslashes($fid) . '\')';
			}
			
			$sql = ''
			 . 'INSERT INTO ' . (string)$conf['fields'][$field]['cnf']['relation']['mm']
			 . ' (local_id, foreign_id) '
			 . ' VALUES ' . implode(', ', $sql_values);

			@$this->query($sql);
		}

		$last_id = $this->insert_id();
		return $last_id;
	}
	
	/**
	 *	[en] render table restrictions as SQL? (CHECKME)
	 *	[de] liefert zu einer tabelle die einschraenkenden felder, und deren werte
	 *
	 *	@param	string 	$table	tabellen-name
	 *	@return	string
	 */

	public function table_restriction($table) {
		$conf = $this->MC->table_config($table);
		
		if (!is_array($conf['table']['sys_fields'])) { return ''; }
		
		$ret = array();
		
		if ($fld = $conf['table']['sys_fields']['deleted']) {
			$ret[] = '(NOT ' . (string)$conf['table']['name'] . '.' . (string)$fld . ')';
		}
		
		return (count($ret) > 0) ? ' AND (' . implode(' AND ', $ret) . ')' : '';
	}
	
	/**
	 *	[en] Returns all of the current database tables + additional information as db_result object
	 *	[de] liefert alle tabellen der aktuellen datenbank + zusatzinformationen als db-result
	 *
	 *	@return	object	db_result
	 */

	public function get_tables()
	{
		return @$this->query('SHOW TABLE STATUS FROM ' . $this->db_name);
	}
	
	/**
	 *	Load / return a flat list of all table names from the database
	 *
	 *	@param	bool	$force_reload	Set to true to force this list to be reloaded
	 *	@return	string[int]
	 */

	public function list_tables($force_reload = false) 
	{
		if ((true == $force_reload) || (0 == count($this->tables)))
		{
			$this->tables = array();

			$r = $this->query(" SHOW TABLES FROM ".$this->db_name);
			while($r->next())
			{
				$this->tables[] = $r->f('Tables_in_'.$this->db_name);
			}
		}
		return $this->tables;
	}


	/**
	 *	Discover if a table exists in the database
	 *	
	 *	This is used by sundry CRUD methods and sundry to check that a table exists before 
	 *	attempting to load its schema or write to it.
	 *
	 *	@param	string	$table_name		Name of a database table
	 *	@param	bool	$force_reload	Clear the cached list of tables before checking
	 *	@return	bool
	 */

	public function table_exists($table_name, $force_reload = false)
	{
		if ((true == $force_reload) || (0 == count($this->tables))) { $this->list_tables(true); }
		return in_array($table_name, $this->tables);
	}

	/**
	 *	[en] returns all fields of a table as a db_result object
	 *	[de] liefert alle felder einer tabelle als db-result
	 *
	 *	(DEPRECATED) This is superceded by db_table object
	 *	(TODO) discover if this is used anywhere, remove if not
	 *	
	 *	@param	string	$table	table name / tabellen-name
	 *	@return	object	db-result-set
	 */

	public function get_fields($table)
	{
		return @$this->query('SHOW COLUMNS FROM ' . addslashes($table) . '');
	}
	
	/**
	 *	[en] set SQL events flag (controls whether SQL events are raised)
	 *	[de] ausfuehren von sql-events an/abschalten
	 *
	 *	@param	bool	$val	New value of flag
	 *	@return	void
	 */

	public function sql_events($val) {
		$this->do_sql_events = $val;
	}
	
}

?>
