<?php

	require_once(dirname(__FILE__) . '/db_crud.inc.php');
	require_once(dirname(__FILE__) . '/db_table.inc.php');

/**
 *	Object to implement templated database queries
 *
 *	The purpose of this object is to prevent SQL injection and other invalid, erroreous or malformed
 *	data in SQL queries by validating literal arguments against table definitions and user 
 *	specified constraints.
 *
 *	This is intended for constructing complex queries such a joins or deletes with complex WHERE
 *	clauses.  Simpler database operations are more easily executed via the db_crud object.
 *
 *	STATUS:
 *
 *		Development / Alpha
 *
 *	GENERAL USE:
 *
 *		$query = new db_query($sql, $values, $constraints);
 *		if (true == $query->valid) { $db->query($query->sql); }
 *
 *	Where:
 *
 *		$sql is a string containing the templated query, with %%placeholders%% representing
 *		untrusted variables, matching values to be inserted after checks.  A trivial example:
 *
 *			$sql = "SELECT * FROM `my_table` WHERE `number` > %%count%% AND title='%%title%%'";
 *
 *		$values are the set of values to be substituted, a dict of the form 'label' => 'literal':
 *
 *			$values = array(
 *				'count' => '100',
 *				'title' => 'an example'
 *			);
 *
 *		$constraints at this point are a dict of:
 *
 *			 'label' => 'table_name.field_name[, another_table_name.field_name]'
 *
 *		That is, labels may have as many constraints as necessary, in a comma separated list, and
 *		usually take the form of a field name.  The size and type of this field name will limit
 *		the possible values assigned to the label.  Other valid constraints are:
 *
 *			table 	- value must be the name of an existing database table
 *			int	- value must be an int
 *
 *	NOTES:
 *
 *		When constructing the values array, all variables should be cast as strings, since these
 *		are what the table scheme will use for comparisons, and what the SQL statement is ultimately
 *		created from.
 *
 *	ROADMAP:
 *
 *		Depending on user needs, additional constraints may be implemented - similar to the
 *		template_exec object - to impose limits not implied by table schemas.  An example might be
 *		a varchar field which MUST contain an email.
 *
 *		At present there is no check on whether all placeholders in the template are filled - this
 *		may be added in future.
 */

class db_query
{

	/**
	 *	Pointer to database wrapper singleton
	 *	@var db_mysql
	 */

	private $DB;

	/**
	 *	Set to true if values meet constraints and fill table
	 *	@var string
	 */

	public $valid = false;

	/**
	 *	Constructed SQL, if valid
	 *	@var string
	 */

	public $sql = '';

	/**
	 *	Last error message, if any
	 *	@var string
	 */

	public $err_msg = '';

	/**
	 *	Constructor
	 *
	 *	This is most of the public interface of this object so far.  Clients check validity of
	 *	the query immediately after construction and run it if good.
	 *
	 *	@param	string			$template		SQL statement with %%placeholders%% 
	 *	@param	string[string]	$values			Dict of 'label' => 'constraint, constraint, ...'
	 *	@param	string[string]	$constraints
	 *	@return	void
	 */

	public function __construct($template, $values, $constraints)
	{
		$this->DB = @DB::singleton();				//	pointer to global database wrapper object

		/*	check basic types of arguments ------------------------------------------------------ */		
		if (!is_string($template)) { $this->err_msg = "Template not a string."; return; }
		if (!is_array($values)) { $this->err_msg = "Values not an array."; return; }
		if (!is_array($constraints)) { $this->err_msg = "Constraints not an array."; return; }

		/*	check constraints ------------------------------------------------------------------- */		
		$check = $this->check_constraints($values, $constraints);
		if (false == $check) { return; }

		/*	substitute into template ------------------------------------------------------------ */		
		$sql = $template;

		foreach ($values as $label => $value)
		{
			$sql = str_replace('%%' . $label . '%%', $this->DB->escape($value), $sql);
		}

		$this->sql = $sql;
		$this->valid = true;
	}

	/**
	 *	Check that values fit constraints on them / tables
	 *
	 *	@param	string[string]	$values			Explained above
	 *	@param	string[string]	$constraints	Explained above
	 *	@return	bool			Returns true if all constraints are met, false if not.
	 */

	private function check_constraints($values, $constraints)
	{
		$tables = /*. (db_table[int]) .*/ array();			//	set of table schemata
		
		foreach ($values as $label => $value)
		{
			$count = 0;

			if (false == array_key_exists($label, $constraints))
			{
				print_r($constraints);
				$this->err_msg = "Label '$label' is unconstrained.";
				return false;
			}

			$constraint_set = explode(",", $constraints[$label]);

			foreach ($constraint_set as $constraint)
			{
				$constraint = trim($constraint);
				if ('' !== $constraint)
				{
					$check = $this->check_constraint($label, $value, $constraint);
					if (false == $check) { return false; }
					$count++;
				}
			}

			if (0 == $count) {
				$this->err_msg = "Label '$label' has no constraints.";
				return false;
			}
		}

		return true;
	}

	/**
	 *	Check a value against a single constraint
	 *
	 *	Constraints are usually of the form:
	 *
	 *		table_name.field_name
	 *
	 *	Where the value will be compared to the field definition.  Other constraint types are:
	 *
	 *		table	-	value must be the name of an existing table
	 *		int	-	value must be an integer
	 *
	 *	@param	string	$label		Label of value to be tested
	 *	@param	string	$value		Value to be tested
	 *	@param	string	$constrain	Field to test against
	 *	@return	bool
	 */

	private function check_constraint($label, $value, $constraint)
	{

		switch (trim(strtolower($constraint)))
		{
			case 'table':
				if ($this->DB->table_exists($value)) { return true; }
				$this->err_msg = "Table not found: `$value` for `$label`\n";
				return false;
				#break;	//...............................................................

			case 'int':
				if ($value == (string)((int)$value)) { return true; }
				$this->err_msg = "Value of `$label` must be an integer and nothing else.";
				return false;
				#break;	//...............................................................


			//	^^ add further constraint types here
		}

		//	Default is to assume the constraint is the name of a table field

		$parts = explode('.', $constraint);
		if (2 !== count($parts)) { $this->err_msg .= "Constraint is malformed.\n"; return false; }

		$table_name = $parts[0];		
		$field_name = $parts[1];

		if (false == $this->DB->table_exists($table_name))
		{
			$this->err_msg .= "Table '$table_name' not found while constraining '$label'.";
			return false;
		}

		$schema = $this->DB->get_schema($table_name);		/*	db_table object */

		if (is_null($schema))
		{
			$this->err_msg .= "Could not load schema for table `$table_name`.";
			return false;			
		}

		if (false == $schema->has_field($field_name))
		{
			$this->err_msg .= "Table '$table_name' does not contain field '$field_name'.";
			return false;			
		}

		if (false == $schema->accept_value($field_name, $value))
		{
			$this->err_msg .= "Constraint not met on '$label':\n" . $schema->err_msg;
			return false;
		}

		return true;
	}

}

?>
