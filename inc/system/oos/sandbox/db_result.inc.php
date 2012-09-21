<?php
/**
 *	Database result object
 *
 *	[en] Objects of this type are returned by the query() and templated_query() methods of database wrappers, as well as
 *	[en] load_range() CRUD method.
 *
 *	[de] db-result-objekt
 *	[de] wird von der query()-funktion der db-klasse zurueckgeliefert
 *
 *	Created: 12 05 2004
 *
 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@version		1.0
 *	@since			1.0
 *	@package		util
 */

class db_result {

	/**
	 *	Entire buffered result set as a 2d array
	 *	@var array
	 */

	private /* string[string[int]] */ $data = array();

	/**
	 *	[en] Index of current row in $data array
	 *	[de] array-zeiger auf aktuellen datensatz
	 */

	private $data_pnt = -1;

	/**
	 *	Last error message, if any
	 *	@var string
	 */

	public $err_msg = '';

	/**
	 *	constructor / konstruktor
	 *	@return void
	 */

	function __construct() {
		// nothing to do just yet
	}

	/**
	 *	Alias of stripslashes, presumably so that further unescaping could be added here
	 *	@param	string	$s	String to strip slashes from
	 *	@return	string
	 */

	function f_cleanup($s) { return stripslashes($s); }

	/**
	 *	[en] returns the contents of a named field of the current row
	 *	[en] TODO: lots of checking of pointer, etc here - this is an important method
	 *	[de] liefert den inhalt eines benannten feldes der aktuellen zeile
	 *
	 * 	@param	string	$name 	Name of a field in current or given row
	 *	@param	int		$id		Row number in result set
	 *	@return	string
	 */

	function f($name, $id = -1) 
	{
		if ($id >= 0)
		{
			$id--;
			return $this->f_cleanup((string)$this->data[$id][$name]);
		}

		if ($this->data_pnt == -1)
		{
			return $this->f_cleanup((string)$this->data[0][$name]);
		}
		else 
		{
			return $this->f_cleanup((string)$this->data[$this->data_pnt][$name]);
		}
	}

	/**
	 *	returns the value of the field in the next row, if any
	 * 	@param	string	$name 	Name of a field in current or given row
	 *	@return	string
	 */

	function nf($name) {
		$my_pnt = $this->data_pnt + 1;
		if ($my_pnt > count($this->data)) { return ''; }
		else { return (string)$this->data[$my_pnt][$name]; }
	}

	/**
	 *	returns the value of the field in the previous row, if any
	 * 	@param	string	$name 	Name of a field in current or given row
	 *	@return	string
	 */

	function lf($name) {
		$my_pnt = $this->data_pnt - 1;
		if ($my_pnt <= -1) { return ''; }
		else { return (string)$this->data[$my_pnt][$name]; }
	}

	/**
	 *	[en] returns the current row as an associative array
	 *	[de] liefert die aktuelle zeile als ass. array
	 *
	 *	@return	array
	 */

	function r()
	{
		if ($this->data_pnt == -1) { return $this->data[0]; }
		return $this->data[$this->data_pnt];
	}

	/**
	 *	[en] Returns the number of existing records / rows
	 *	[de] liefert die anzahl der vorhandenen saetze/zeilen
	 *
	 *	@return	int
	 */

	function nr()
	{
		return count($this->data);
	}

	/**
	*	[en] remove a row
	*	@return	bool
	*/

	function rm($fld)
	{
		unset($this->data[$fld]);
		return true;
	}

	/**
	*	[en] Increment the data pointer, return false at end of result set, true otherwise
	*	[de] erhoeht den datensatz-zeiger um 1
	*	[de] liefert FALSE, wenn kein weiterer wert da, TRUE sonst
	*	@return	bool
	*/

	function next()
	{
		$nr = count($this->data);
		if (0 == $nr) { return false; }

		if ($this->data_pnt == ($nr-1)) { return false; }
		$this->data_pnt++;
		return true;
	}

	/**
	 *	[en] set the data pointer back to the beginning of the set
	 *	[de] setzt den datenzeiger auf den anfang
	 *	@return void
	 */

	function reset()
	{
		$this->data_pnt = -1;
		reset($this->data);
	}

	/**
	 *	[en] Load the result set from the passed array and set the pointer to the start
	 * 	[de] daten-array setzen
	 *	[de] wird z.b. von db-objekt nach query aufgerufen
	 *
	 *	@param	array	$arr	2d associative array of strings (string[string[int]])
	 *	@return void
	 */

	function set($arr) {
		if (!is_array($arr)) { $arr = array(); }

		$this->data = $arr;
		reset($this->data);
		$this->data_pnt = -1;
	}

	/**
	 *	Sets the value of a named field in the given or current row
	 *	@param	string	$fld	Field name
	 *	@param	string	$val	Value to be assigned
	 *	@param	int		$idx	Index in data array
	 *	@return	void
	 */

	function f_set($fld, $val, $idx = -1)
	{
		if(0 > $idx) { $idx = $this->data_pnt; }
		$this->data[$idx][$fld] = $val;
	}

	/**
	 *	Returns the index of the current row in the result set
	 *	@return	int
	 */

	function id()
	{
		return $this->data_pnt;
	}

	/**
	 *	Shuffles the result set.
	 *	Note: this will prevent a more efficient object in the future, better to use ORDER BY RAND()
	 *	in the query this set is built from, suggest removing, strix 2012-02-21
	 *	@return void
	 */

	function shuffle()
	{
		shuffle($this->data);
	}

	/**
	 *	[en] return the complete result set as a 2d array
	 *	[de] komplettes daten-array liefern
	 *
	 *	@return	array
	 */

	function get()
	{
		if (!is_array($this->data)) { $this->data = array(); }
		return $this->data;
	}

}

?>
