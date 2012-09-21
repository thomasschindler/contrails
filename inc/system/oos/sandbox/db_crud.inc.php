<?php

	require_once(dirname(__FILE__) . '/db_table.inc.php');

/**
 *	CRUD methods using the existing db object, tested against table schema
 *
 *	STATUS:
 *
 *	Development / Beta
 *
 *	BASIC OPERATIONS:
 *
 *		create(...)			-	Insert a new record into a database table
 *		load(...)			-	Load a single, complete row from a table
 *		update(...)			-	Update a single existing record
 *		delete(...)			-	Delete a singe row from a table
 *
 *	SET OPERATIONS:
 *
 *		count_range(...)	-	Count the number of items in a set
 *		load_range(...)		-	Load a small set of rows from a table
 *
 *	CONSTRAINTS:
 *
 *	The constraint set on load_range and count range are intended as filters on the result set.
 *	They are not for complex boolean operations, only for simple reduction by value, for example:
 *	orders placed by a user, items in a category, etc. For joins or complex conditions please use
 *	a templated query - this is a simple AND reduction by value as most typically needed by views.
 *
 *	Format of constraints array is a dict of 'field_name' => 'value', with the restriction that 
 *	value must pass the constraints on this field, and rows must meet all conditions.
 *
 *	NOTES / ROADMAP:
 *
 *	This current version of the object still uses mysql-specific functions.  To be replaced by 
 *	calls to same on underlying DB obejct when additional wrappers are available.
 *
 *	Table schema to be cached on db_mysql object, and load_schema(...) method moved there
 */

class db_crud
{

	/**
	 *	List of table names from database (assume not loaded when 0 == count($this->tables))
	 *	Lazily initialized by $this->table_exists(...)
	 *	@var string[int]
	 */

	private $tables = /*. (string[int]) .*/ array();

	/**
	 *	Pointer to the single db_mysql instance
	 *	@var db_mysql
	 */

	private $DB;

	/**
	 *	Error message of last operation
	 *	@var string
	 */

	public $err_msg = '';

	/**
	 *	Last SQL statement executed, made available for debugging / logging
	 *	@var string
	 */

	public $sql = '';

	/**
	 *	Constructor
	 *	@return	void
	 */

	public function __construct()
	{
		$this->DB = &DB::singleton();
	}

	//----------------------------------------------------------------------------------------------
	//	SINGLE RECORD METHODS - create(...), update(...), delete(...) and load(...)
	//----------------------------------------------------------------------------------------------

	/**
	 *	Insert a new record into a database table
	 *
	 *	@param	string			$table_name		Name of a database table
	 *	@param	string[string]	$values			Dict of fields and values
	 *	@param	string[string]	$defaults		Optional dict of default values
	 *	@return	int				Id of new record, -1 on failure
	 */

	public function create($table_name, $values, $defaults)
	{
		$schema = $this->DB->get_schema($table_name);		//	db_table object
		if (is_null($schema)) { return -1; }

		$field_names = $schema->get_field_names(true);		//	flat array [int] => [string]
		$checked_values = array();
		$ok = true;

		//	Check that all fields are filled and that all values meet table constraints
		foreach ($field_names as $field_name) {
			$primitive = $schema->primitive_type($field_name);
			$value = '';

			//	All fields must exist in at least one of $values or $defaults
			if (array_key_exists($field_name, $values)) { $value = $values[$field_name]; }
			else
			{
				if (array_key_exists($field_name, $defaults))
				{
					$value = $defaults[$field_name];
				}
				else
				{
					$this->err_msg .= "Missing value for $field_name (no default)\n";
					$ok = false;
				}
			}

			//	All values must fit the constraints on their field
			if (false == $schema->accept_value($field_name, $value)) {
				$this->err_msg .= "Cannot set value of $field_name: " . $schema->err_msg . "\n";
				$ok = false;
			}

			//	Everything except numeric values are quoted
			if (('int' !== $primitive) && ('float' !== $primitive))
			{
				$value = "'" . $this->DB->escape($value) . "'";
			}

			$checked_values[] = $value;
		}


		//	Do not try the call if anything is wrong with the new row
		if (false == $ok) { return -1; }	//	....................................................

		$this->sql = ''
			 . "INSERT INTO " . $table_name . "\n"
			 . "  (\n    " . implode(",\n    ", $field_names) . ")\n"
			 . "VALUES\n"
			 . "  (\n    " . implode(",\n    ", $checked_values) . "\n)\n";

		$check = $this->DB->query_bool($this->sql);
		if (true == $check) {
			$new_id = (string)mysql_insert_id();
			return $new_id;
		}
		return -1;
	}


	/**
	 *	Load a single row from a database table into an associative array
	 *
	 *	Note that this will return an empty array on error (database error, invalid identifier,
	 *	row not found or more than one row shares this identifier).  This is for loading a single,
	 *	complete object with a minimum of complications.
	 *
	 *	Intended use is by actions and views with hard-coded table and field names, so only the
	 *	$identifier is checked and sanitized.  If in future this assumption does not hold then code
	 *	to sanitize $table_name and $field_name should be added.
	 *
	 *	@param	string	$table_name		Name of a database table
	 *	@param	string	$field_name		Field containing a unique identifier for this row
	 *	@param	string	$identifier		Unique ID of this row
	 *	@return	string[string]			Dict of 'fieldname' => 'value', empty array on failure
	 */

	public function load($table_name, $field_name, $identifier)
	{
		$this->sql = '';
		$this->err_msg = '';

		/* load table schema -------------------------------------------------------------------- */
		if (false == $this->DB->load_schema($table_name)) {
			$this->err_msg = "Table `$table_name` not found.";
			return array();
		}

		$schema = $this->DB->get_schema($table_name);			//	db_table object

		if (is_null($schema)) {
			$this->err_msg = "Could not load database table schema.";
			return array();
		}

		/* check identifier field --------------------------------------------------------------- */
		if (false == $schema->has_field($field_name)) {
			$this->err_msg = "Field `$field_name` not found in table `$table_name`.";
			return array();
		}

		/* check identifier value --------------------------------------------------------------- */
		if (false == $schema->accept_value($field_name, $identifier)) {
			$this->err_msg .= ''
			 . "Identifier `$identifier` does not fit in field `$field_name`.\n"
			 . "Primitve type: " . $schema->primitive_type($field_name) . "\n"
			 . $schema->err_msg;

			return array();
		}

		/* try load the record ------------------------------------------------------------------ */
		$quot = "\"";
		if ('int' == $schema->primitive_type($field_name)) { $quot = ''; }

		$this->sql = ''
		 . "SELECT * FROM `$table_name`"
		 . " WHERE `$field_name`=" . $quot . $this->DB->escape($identifier) . $quot;

		$result = $this->DB->query($this->sql);

		if (1 == $result->nr()) { return $result->r(); }

		if (0 == $result->nr()) { $this->err_msg = "Record not found."; }
		if (1 < $result->nr()) { $this->err_msg = "Record not unique."; }

		return array();
	}

	/**
	 *	Update a single row with the given values
	 *
	 *	This will return true on success, false on error.  Error conditions include database 
	 *	connection failure, record not found, record not unique (ie, identifier mathes more than one 
	 *	table row) or values not matching the constraints of the field they are to be updated into.
	 *
	 *	@param	string			$table_name		Name of a database table
	 *	@param	string			$field_name		A field containing unique identifier of this row
	 *	@param	string			$identifier		A value which uniquely identifies a single row
	 *	@param	string[string]	$values			New values of fields in this row
	 *	@return	bool							Check $this->err_msg if false
	 */

	public function update($table_name, $field_name, $identifier, $values)
	{
		$this->sql = '';
		$this->err_msg = '';

		if (false == $this->DB->load_schema($table_name)) {
			$this->err_msg = "Table `$table_name` not found.";
			return false;
		}

		$schema = $this->DB->get_schema($table_name);			//	db_table object
		$changed = /*. (string[int]) .*/ array();				//	values to be updated

		//	Try to load the existing row
		$row = $this->load($table_name, $field_name, $identifier);
		if (0 == count($row))
		{
			$this->err_msg .= "Record not found, or not unique. $table_name - $field_name - $identifier\n";
			return false;
		}

		//	Compare current fields and values, and update
		foreach ($row as $c_field => $c_value)
		{
			$newval = $values[$c_field];

			//	Check that field exists in table and that value has changed
			if ((array_key_exists($c_field, $values)) && ($newval !== $c_value))
			{
				//	Check that new value matches field constraints
				if ($schema->accept_value($c_field, $newval))
				{
					if (true == $schema->quoted($c_field))
					{
						$newval = "\"" . $this->DB->escape($newval) . "\"";
					}
					$changed[] = "`$c_field`=$newval";
				}
				else
				{
					$this->err_msg = ''
					 . "Value of $c_field does not meet constraints:\n"
					 . $schema->err_msg;

					return false;
				}
			}
		}

		//	(TODO) consider noting fields given in $values which do not exist in table

		//	Check that a db query needs to be made
		if (0 == count($changed)) { return true; }				//..................................

		//	Finally, run the query
		$quot = '';
		if (true == $schema->quoted($field_name)) { $quot = "\""; }

		$this->sql = ''
		 . "UPDATE `$table_name`"
		 . " SET " . implode(", ", $changed)
		 . " WHERE `$field_name`=" . $quot . $this->DB->escape($identifier) . $quot . "\n";

		$check = $this->DB->query_bool($this->sql);
		if (false == $check) { $this->err_msg = $this->db->err_msg; }
		return $check;
	}

	/**
	 *	Delete a single row from a database table
	 *
	 *	This method will delete a single record from $table_name.  $key should be the name of the
	 *	primary key of this table, or a field in which a single record has a unique value.  It
	 *	returns true on success, false if row was not found, row was not unique (ie, more than one
	 *	row is matched by identifier), database connection error or invalid identifier.
	 *
	 *	Please check $this->err_msg on failure.
	 *
	 *	@param	string	$table_name		Name of an extant database table
	 *	@param	string	$field_name		Name of field containing unique identifier, eg, PriKey
	 *	@param	string	$identifier		Value uniqely identifying a record
	 *	@return	bool
	 */

	public function delete($table_name, $field_name, $identifier)
	{
		$this->sql = '';
		$this->err_msg = '';

		if (false == $this->DB->load_schema($table_name)) {
			$this->err_msg = "Table `$table_name` not found.";
			return false;
		}

		$schema = $this->DB->get_schema($table_name);				//	db_table object

		//	Check that field exists
		if (false == $schema->has_field($field_name))
		{
			$this->err_msg = 'Field not found.';
			return false;
		}

		//	Load, to check that row exists and is unique
		$row = $this->load($table_name, $field_name, $identifier);
		if (0 == count($row))
		{
			$this->err_msg = 'Record not found or not unique.';
			return false;
		}

		//	Try to delete it
		$this->sql = ''
		 . "DELETE FROM `$table_name`"
		 . " WHERE `$field_name`= '" . $this->DB->escape($identifier) . "'";

		$check = $this->DB->query_bool($this->sql);
		if (false == $check) { $this->err_msg = "Database query failed: " . $db->err_msg; }
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//	RANGE METHODS - count or load a set of rows from the database
	//----------------------------------------------------------------------------------------------

	/**
	 *	Count the number of rows in a table matching constraints
	 *
	 *	Return value of -1 indicates error, check $this->err_msg to find out what went wrong.
	 *	Please note that the count() function in MySQL is O(n), so this should only be used where
	 *	the number of records satisfying constraints is small, and appropriate indexing is in place.
	 *
	 *	To reiterate: counting records is like counting beans in a bucket, it WILL NOT SCALE to
	 *	large sets, it will tie up the database, cause lots of disk seeks/reads and dramatically 
	 *	slow the site if misused.
	 *
	 *	@param	string	$table_name			Name of a database table
	 *	@param	string	$constraints		See explanation above.
	 *	@return	int							Number of records or -1 on failure
	 */

	public function count_range($table_name, $id_field, $constraints)
	{
		/* check arguments ---------------------------------------------------------------------- */
		if (false == $this->DB->table_exists($table_name))
		{
			$this->err_msg = "Unknown table: $table_name\n";
			return -1;
		}

		$schema = $this->DB->get_schema($table_name);

		if (is_null($schema))
		{
			$this->err_msg = "Could not load table schema '$table_name':\n" . $this->err_msg;
			return -1;
		}

		if (false == $schema->has_field($id_field))
		{
			$this->err_msg = "Unknown id field '$id_field'.";
			return -1;
		}

		/* make the where clause ---------------------------------------------------------------- */
		$where_clause = '';
		if (false == is_array($constraints)) { $constraints = array(); }
		if (count($constraints) > 0)
		{
			if (false == $this->validate_constraints($table_name, $constraints)) { return -1; }
			$where_clause = ' WHERE ' . $this->serialize_constraints($table_name, $constraints);
		}

		/* run the query ------------------------------------------------------------------------ */

		$this->sql = "SELECT count(`$id_field`) FROM `$table_name`" . $where_clause;
		$result = $this->DB->query($this->sql);
		$first_row = $result->r();

		if (is_array($first_row))
		{
			foreach ($first_row as $key => $value)
			{
				return (int)$value;
			}
		}
		return -1;
	}

	/**
	 *	Load a database result set given constraints on a table
	 *
	 *	Note that this should only be used when expected number of results is bounded or small,
	 *	they will all be read into memory and could significantly impact server if an entire table
	 *	were loaded this way.
	 *
	 *	This method is not sutiable for the execution of complex queries or for returning large
	 *	sets.  To do something like a join, use complex boolean conditions or use built in MySQL
	 *	functions, use a templated query instead.
	 *
	 *	@param	string	$table_name			Name of a database table
	 *	@param	array	$fields				Flat array for field names, or empty array for all
	 *	@param	array	$constraints		(TODO)
	 *	@param	string	$order_by			Name of field to order by
	 *	@param	string	$order				'ASC|'DESC' ascending, descending order
	 *	@param	int		$limit				Limit number of matches
	 *	@param	int		$offset				Begin from offset (eg, for pagination)
	 *	@return	db_result
	 */

	public function load_range(
		$table_name, $fields, $constraints, 
		$order_by = '', $order = 'ASC',
		$limit = -1, $offset = -1
	) {

		$empty = new db_result();
		$field_list = '';
		$where_clause = '';
		$order_clause = '';
		$limit_clause = '';

		/*	check table exists ------------------------------------------------------------------ */
		if (false == $this->DB->table_exists($table_name))
		{
			$this->err_msg = "Attempting to load from from non-existent table: $table_name\n";
			$empty->err_msg = $this->err_msg;
			return $empty;
		}

		/*	try to load the schema -------------------------------------------------------------- */
		$schema = $this->DB->get_schema($table_name);	/* db_table object */

		if (is_null($schema))
		{
			$this->err_msg = "Could not load schema for table: $table_name\n";
			$empty->err_msg = $this->err_msg;
			return $empty;
		}

		/*	check / construct list of fields ---------------------------------------------------- */
		if ((is_array($fields)) && (count($fields) > 0))
		{

			foreach ($fields as $field_idx => $field_name)
			{
				if (('*' !== $field_name) && (false == $schema->has_field($field_name)))
				{
					$this->err_msg = "Unknown: $table_name\n";
					$empty->err_msg = $this->err_msg;
					return $empty;
				}
				else { $fields[$field_idx] = "`" . $field_name . "`"; }
			}
			$field_list = implode(", ", $fields);

		}
		else { $field_list = '*'; }				// default is all fields

		/*	construct where clause -------------------------------------------------------------- */
		if (false == is_array($constraints)) { $constraints = array(); }
		if (count($constraints) > 0) {
			if (false == $this->validate_constraints($table_name, $constraints))
			{
				$empty->err_msg = $this->err_msg;
				return $empty;
			}
			$where_clause = 'WHERE ' . $this->serialize_constraints($table_name, $constraints);
		}

		/*	construct order by clause ----------------------------------------------------------- */
		if ('' !== $order_by)
		{
			$order = trim(strtoupper($order));				//	be a little flexible
			if ('DESC' !== $order) { $order = 'ASC'; }		//	prevents injection on this var

			$order_clause = " ORDER BY `$order_by` $order";
		}

		/*	construct limit clause -------------------------------------------------------------- */

		if (-1 != $limit)
		{
			$limit_clause = " LIMIT " . (string)((int)$limit);
			if (-1 != $offset) { $limit_clause .= " OFFSET " . (string)((int)$offset); }
		}

		/*	everything checks out, run the query ------------------------------------------------ */

		$this->sql = ''
		 . "SELECT $field_list\n"
		 . " FROM `$table_name`\n"
		 . (($where_clause == '') ? '' : " $where_clause\n")
		 . (($order_clause == '') ? '' : " $order_clause\n")
		 . (($limit_clause == '') ? '' : " $limit_clause\n")
		 . '';

		$result = $this->DB->query($this->sql);
		return $result;
	}

	/**
	 *	Validate a set of constraints against table schema
	 *
	 *	Since the constraint set will often include data from the browser (identifiers, pagination
	 *	numbers, etc), it is especially important that these are checked and sanitized.
	 *
	 *	For now constraints will only take the form 'field_name' => 'literal value'
	 *
	 *	@param	string	$table_name		Name of an extant datbase table
	 *	@param	array	$constraints	Constraint set as described in file info above.
	 *	@return	bool
	 */

	private function validate_constraints($table_name, $constraints)
	{
		$schema = $this->DB->get_schema($table_name);

		if (is_null($schema))
		{
			$this->err_msg = "Could not load schema for table: $table_name\n";
			return false;
		}


		if (false == $schema->loaded)
		{
			$this->err_msg = "Could not load schema for table: $table_name\n";
			return false;
		}

		foreach ($constraints as $field_name => $literal_value)
		{
			if (false == $schema->has_field($field_name))
			{
				$this->err_msg = "Cannot constrain, no such field: $field_name\n";
				return false;
			}

			if (false == $schema->accept_value($field_name, $literal_value))
			{
				$this->err_msg = "Value does not match field: $field_name\n";
				return false;
			}
		}

		return true;
	}

	/**
	 *	Convert a set of constraints to SQL WHERE clause
	 *
	 *	This assumes that the constraints are valid according to the function above.  If not then
	 *	this will not prevent SQL injection.
	 *
	 *	@param	array	$constraints	Constraint set as described in file info above.
	 *	@return	string
	 */

	private function serialize_constraints($table_name, $constraints)
	{

		$schema = $this->DB->get_schema($table_name);

		if (is_null($schema)) { return ''; }



		$terms = /*. (string[int]) .*/ array();
		$serialized = '';

		foreach ($constraints as $field_name => $literal_value)
		{
			if (true == $schema->quoted($field_name))
			{
				//	string types
				$terms[] = "`$field_name`=" . "\"" . $this->DB->escape($literal_value) . "\"";
			}
			else
			{
				//	numeric types
				$terms[] = "`$field_name`=" . $literal_value;
			}
		}

		$serialized = implode(' AND ', $terms);
		return $serialized;
	}

	/*
	* 	read from a table
	*	pass values or ranges of values for fields in as an array
	*	array
	*	(
			'combine' => 'AND'
			// if there are more than two 
			array
			(
				'field' => 'field'
				'operator' => '='
				'value' => 'value'
			),
			array
			(
				'field' => 'field'
				'operator' => '='
				'value' => 'value'
			),
			array
			(
				'combine' => 'OR'
				array
				(
					'field' => 'field'
					'operator' => '='
					'value' => 'value'
				),
				array
				(
					'field' => 'field'
					'operator' => '='
					'value' => 'value'
				)
			)
	*	)
	*/


	/* TODO: figure out what this might have been for, remove if possible
	function read_build_values($values)
	{
		if(!is_array($values))
		{
			return;
		}
		$w = "(";
		foreach($values as $k => $v)
		{
			if(isset($v['field']))
			{
				$w .= $v['combine']." ".$v['field']." ".$v['operator']." '".mysql_real_escape_string($v['value'])."' ";
			}
			else
			{
				$combine = $v['combine'];
				unset($v['combine']);
				$w .= $combine." ".$this->read_build_values($v);
			}
		}
		$w .= ")";
		return $w;
	}
	*/
	
	/**
	 *	Returns pointer to the single CRUD object
	 *
	 *	Each instantiation must be run over this function to ensure that only one instance of this
	 *	object exists.
	 *
	 *	[de] liefert zeiger auf CRUD objekt
	 *
	 *	@return	object	zeiger auf CRUD objekt
	 *	@access	public
	 */

	public static function &singleton() 
	{
		static $instance;
		if (!is_object($instance)) { $instance = new CRUD(); }
		return $instance;
	}
}

?>
