<?php

/**
 *	Object to represent / work with database table schema
 *
 *	This is  used by db_crud to compare passed arrays with table schema.
 *
 *	(TODO) consider adding a create() method to create this table
 *	(TODO) consider adding an update() method to change schema on module/frameowrk updates
 *
 *	(NOTE) On development oOS suppoerted a single database wrapper, MySQL - if wrappers are 
 *	developed for other DBMS then this may need some adjustment.
 *
 *	Author:		Richard Strickland, 2012
 */

class db_table {

	/* Properties ------------------------------------------------------------------------------- */

	/**
	 *	Pointer to single global DB object
	 *	@var db_mysql
	 */

	private /*. db_mysql .*/ $DB;

	/**
	 *	Name of database table represented in this schema
	 *	@var string
	 */

	private $name = '';

	/**
	 *	Field types (dict of "field_name" => string[string])
	 *
	 *	The members of the dict describe the field's properties.  Values for MySQL are:
	 *
	 * 		'Field' => Name of field, eg 'surname'
	 *		'Type' => DBMS field type, "int(11)", "mediumtext", "blob", etc
	 *		'Null' => 'YES'|'NO'
	 *		'Key' => 'PRI' or empty string
	 *		'Default' => default value of this field
	 *		'Extra' => 'auto_increment' on primary key, usually empty string
	 *		'Base' => base DBMS field type, eg: 'int', 'varchar', etc
	 *		'Size' => size of field reported by database, say '200' for a varchar(200)
	 *		'Weight' => order of field
	 *
	 *	Note that this is untested with other DBMS.
	 *
	 *	@var string[string][string]
	 */

	private	$fields = array();

	/**
	 *	Indexes (dict of "field_name" => "index size")
	 *	@var string[string]
	 */

	private	$indexes = array();

	/**
	 *	Constraints (dict of "field_name" => "constraint, constraint")
	 *	(TODO) Figure out how to make this from 'table configurations' as used by oOS
	 *	@var string[string]
	 */

	private	$constraints = array();

	/**
	 *	Character set used when creating tables
	 *	@var string
	 */

	private $charset = 'utf8';

	/**
	 *	Storage engine used when creating tables
	 *	@var string
	 */

	private $engine = 'MyISAM';

	/**
	 *	Message describing last error encountered by this object
	 *	@var string
	 */

	public $err_msg = '';

	/**
	 *	Is set to true when a table schema is loaded
	 *	@var bool
	 */

	public $loaded = false;

	/*	Initialization -------------------------------------------------------------------------- */

	/**
	 *	Constructor
	 *	@param	string	$table_name		Name of database table
	 *	@return	void
	 */

	public function __construct($table_name = '')
	{
		$this->DB = DB::singleton();
		$this->name = $table_name;
		$this->load();
	}

	/*	Copy schema from current database ------------------------------------------------------- */

	/**
	 *	Load schema from extant database table
	 *
	 *	@return	bool
	 */

	public function load()
	{
		$db = &DB::singleton();
		if ('' == $this->name) { return false; }
		
		$this->fields = array();
		$this->indexes = array();

		$sizes = array(
			'tinytext' => 256,
			'text' => 65535,
			'mediumtext' => 16777215,
			'longtext' => 4294967295,		/* TODO: more as necessary */
		);

		//	Load set of database field names and types
		$r = $db->query("DESCRIBE `" . $this->name . "`");
		$fields = $r->get();

		foreach($fields as $key => $field)
		{
			$typ = str_replace(array('(', ')'), array('|', '|'), $field['Type']);
			$parts = explode('|', $typ);
			$field['Base'] = strtolower($parts[0]);
			$field['Size'] = array_key_exists(1, $parts) ? $parts[1] : '';
			$field['Weight'] = $key;

			if (in_array($field['Base'], $sizes)) { $field['Size'] = $sizes[$field['Base']]; }

			$this->fields[$field['Field']] = $field;
		}

		//	Load set of indexes, assumes a maximum of 1 index per field
		$r = $db->query("SHOW INDEXES FROM `" . $this->name . "`");
		$indexes = $r->get();
		foreach($indexes as $key => $index) { $this->indexes[$index['Column_name']] = $index; }

		//	Load set of constraints on field values
		//	(TODO) figure out how to load extra constraints from modules

		$this->loaded = true;
		return $this->loaded;
	}	

	/*	Field names, types and indexes ---------------------------------------------------------- */

	/**
	 *	Discover if a named field exists in this table
	 *	@return	bool
	 */

	public function has_field($field_name)
	{
		if (false == $this->loaded) { return false; }
		if (array_key_exists($field_name, $this->fields)) { return true; }
		return false;
	}

	/**
	 *	Get the basic type of a field
	 *	@param	string	$field_name		Name of a field in this table
	 *	@return	string					string|int|float|date|bool|etc... or empty string on error
	 */

	public function primitive_type($field_name) {
		if (false == $this->has_field($field_name)) { return ''; }
		$base_type = $this->fields[$field_name]['Base'];
		$primitive = 'string';

		$float_types = array('float', 'double', 'real');

		$int_types = array(
			'tinyint',			'smallint',			'mediumint',
			'int',				'integer',			'bigint',
			'year',				'timestamp'
		);

		if ('enum' == $base_type) { $primitive = 'enum'; }

		if (in_array($base_type, $int_types)) { $primitive = 'int'; }
		if (in_array($base_type, $float_types)) { $primitive = 'float'; }
		return $primitive;
	}

	/**
	 *	Make simple array of field names
	 *
	 *	The option to omit auto_increment fields is for the sake of INSERT operations
	 *
	 *	@param	bool	$omit_auto_increment	Do not include auto_increment fields
	 *	@return	string[int]
	 */

	public function get_field_names($omit_auto_increment = false)
	{
		if (false == $this->loaded) { $this->load(); }
		$field_names = /*. (string[int]) .*/ array();
		foreach ($this->fields as $field_name => $field_type)
		{
			if ((false == $omit_auto_increment) || ('auto_increment' !== $field_type['Extra']))
			{
				$field_names[] = $field_name;
			}
		}
		return $field_names;
	}

	/*	Constraints on field values ------------------------------------------------------------- */

	/**
	 *	Set additional constraints on the value of a field (eg, must be an email address)
	 *	See list of valid constraints in header of this file.
	 *
	 *	@param	string[string]	$constraints	Array of 'field_name' => 'constraint, constraint'
	 *	@return	bool
	 */

	public function set_constraints($constraints)
	{
		$this->constraints = $constraints;
		//	(TODO) check constraints, issue parser warnings and return false if there's a problem
		return true;
	}

	/**
	 *	Check if a value matches what a field may contain
	 *
	 *	@param	string	$field_name		Name of a field
	 *	@param	string	$field_value	Candidate value
	 *	@return	bool
	 */	

	public function accept_value($field_name, $field_value)
	{
		if (false == array_key_exists($field_name, $this->fields)) { return false; }
		if (false == is_string($field_value)) { $field_value = (string)$field_value; }
	
		$field_def = $this->fields[$field_name];			//	Dict of properties
		$size = (int)('0' . $field_def['Size']);			//	Bytes? (CHECKME)


		$primitive_type = $this->primitive_type($field_name);
		#echo "primitive_type: $primitive_type\n";

		switch($primitive_type) {
			case 'string':
				if (0 == $size) { return true; }		// unknown size
				if (strlen($field_value) <= $size) { return true; }
				$this->err_msg = "Field ($size) to small to contain this string.";
				break;		//......................................................................

			case 'int':
				if ($field_value === (string)((int)$field_value)) { return true; }
				//	(TODO) check bounds
				$this->err_msg = "Value must be an int and nothing else.\n";
				break;		//......................................................................

			case 'float':
				if ($field_value === (string)((float)$field_value)) { return true; }
				//	(TODO) check bounds
				$this->err_msg = "Value must be a float and nothing else.\n";
				break;		//......................................................................

			case 'enum':
				$allowed = explode(",", $field_def['Size']);
				#foreach($allowed as $item) { echo "allow:" . $item . "\n"; }
				if (true == in_array("'" . $field_value . "'", $allowed)) { return true; }
				$this->err_msg = "Value '$field_value' is not a member of enum.";
				break;		//......................................................................

			default:
				$err_msg = 'Unrecognized primitive/base type: ' . $primitive_type;
				break;
		}

		return false;
	}

	/**
	 *	Check if a field should be quoted
	 *
	 *	Will return TRUE for unrecognized fields - fail-safe behavior to reduce opportunities
	 *	for SQL injection.
	 *
	 *	@param	string	$field_name		Name of a field this table is expected to contain
	 *	@return	bool	
	 */

	function quoted($field_name)
	{
		if (false == $this->has_field($field_name)) { return true; }
		$primitive = $this->primitive_type($field_name);
		if (('int' === $primitive) || ('float' === $primitive)) { return false; }
		return true;
	}

	/**
	 *	Check if a value matches what field requires, and any additional constraints placed on it
	 *
	 *	(TODO) Implement, integrate this with the 'table configurations' hooha as loaded by
	 *	MC::table_config($table_name).  Unimplemented as at 2012-03-08 due to time pressures and 
	 *	decisions to be taken regarding simplifying structure of table configurations and converting
	 *	'eval' setup to a safer constraints system, such as that used by template_exec.
	 *
	 *	@param	string	$field_name		Name of a field in this table
	 *	@param	string	$field_value	Candidate value to be tested against constraints
	 *	@return	bool
	 */

	public function check_constraints($field_name, $field_value) {
		//	(TODO) implment
		return $this->accept_value($field_name, $field_value);
	}

	/*	Methods to modify tables to new schema -------------------------------------------------- */

	/**
	 *	(Re)Create this table in database, for use by installation and software update scripts
	 *	@return bool
	 */

	#public function create() {
		//(TODO) integrate into site software install mechanism, so modules can set them up
	#}

	#public function update() {
		//(TODO) integrate into site software update mechanism, so modules can update table schemas
	#}

	/*	Text output / export -------------------------------------------------------------------- */

	/**
	 *	Message describing last error, if one was found, empty string if none reported.
	 *	@return	string
	 */

	function last_error() { return $this->err_msg(); }

	/**
	 *	Render this schema into SQL, ie: CREATE TABLE
	 *
	 *	(TODO) consider how best to include indexes in output, or whether that should be separate.
	 *
	 *	@return	string
	 */

	function to_sql()
	{
		if (false == $this->loaded) { return ''; }

		$field_lines = array();

		foreach($this->fields as $dict)
		{
			$field_lines[] = "    `" . $dict['Field'] . '` ' . $dict['Type'];
		}

		$sql = ''
		 . "CREATE TABLE `" . $this->name . "` (\n"
		 . implode(",\n", $field_lines)
		 . "\n)"
		 . " ENGINE=" . $this->engine
		 . " DEFAULT CHARSET=" . $this->charset . ";\n";

		//TODO: figure out how best to include indexes in output
		return $sql;
	}

	/**
	 *	Debug / admin method - print this schema as HTML
	 *	@return	string
	 */

	function to_html()
	{

		$html = ''
		 . "<h2>" . $this->name . "</h2>\n"
		 . "<b>Fields:</b><br/>\n"
		 . $this->array_to_html_table($this->fields)
		 . "<br/>\n"
		 . "<b>Indexes:</b><br/>\n"
		 . $this->array_to_html_table($this->indexes)
		 . "<br/>\n";

		return $html;
	}
	
	/**
	 *	Utility method for rendering arrays as HTML tables
	 *	(TODO) replace this with framework's method for doing this
	 *
	 *	@param	string[string][int]	$ary_dicts	Array of dicts
	 *	@return	string
	 */

	function array_to_html_table($ary_dicts)
	{

		$th = '';
		$rows = '';

		foreach($ary_dicts as $idx => $dict)
		{
			$headrow = false;
			$row = '';
			if ('' == $th) { $headrow = true; }
			foreach($dict as $key => $value)
			{
				$row .= "\t\t<td>" . htmlentities($value) . "</td>\n";
				if (true == $headrow) { $th .= "\t\t<td>" . htmlentities($key) . "</td>\n"; }
			}
			$rows .= "\t<tr>\n$row\t</tr>\n";
		}

		$html = ''
		 . "<table>\n"
		 . "\t<tr>\n" . $th . "\t</tr>\n"
		 . $rows
		 . "</table>\n";
		
		return $html;
	}

}

?>
