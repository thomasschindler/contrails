<?
/**
 *	helper class for creation of regex
 *	all functions return a regular expression
 *	created: 12 05 2004
 *
 *	except for validate which can be used for validation
 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@version		1.0
 *	@since			1.0
 *	@package		util
 */

/*.
	require_module 'standard';
.*/

class regex{

	/**
	*	constructor
	*	@return void
	*/

	public function __construct()
	{
		// reserved
	}

	/**
	 *	returns a pointer to the instance
	 *	@return	regex
	 */

	function singleton() {
		static /*. regex .*/ $instance;
		if (!is_object($instance)) {
			$instance = new regex();
		}
		return $instance;
	}

	/**
	 *	returns regex for ascii
	 *	@param	bool	$empty	(DOCUMENTME)
	 *	@return	string
	 */

	function ascii_strict($empty = false)
	{
		$str_empty = $empty ? "^$|" : "";
		return $str_empty . '^[\.a-zA-Z0-9_-]+$';
	}

	/**
	 *	validates a string against a pattern and returns true or false
	 *	@param	string	$input	string to validate
	 *	@param	string	$regex	pattern
	 *	@return	bool
	 */

	function validate($input, $regex)
	{
		if(!preg_match("/".$regex."/",$input)) { return false; }
		return true;
	}

	/**
	 *	returns regex for int
	 *	@param	bool	$empty	(DOCUMENTME)
	 *	@return	string
	 */

	function rx_int($empty = false)
	{
		$str_empty = $empty ? "^$|" : "";
		return $str_empty.'^[0-9]+$';
	}

	/**
	 *	returns regex for float
	 *	@param	bool	$empty	(DOCUMENTME)
	 *	@return	string
	 */

	function rx_float($empty = false){
		$str_empty = $empty ? "^$|" : "";
		return $str_empty . '^[0-9]*[\.|,]{0,1}[0-9]*$';
	}

	/**
	 *	returns regex for email
	 *	@param	bool	$empty	(DOCUMENTME)
	 *	@return	string
	 */

	function email($empty = false){
		$str_empty = $empty ? "^$|" : "";
		return $str_empty . '^[0-9a-z]([-_.]?[0-9a-z_])*@[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}$';
	}

	/**
	 *	returns regex for http url
	 *	TODO: find out why commented out (CHECKME), strix 2012-02-23
	 *	TODO: remove this if unused, strix, 2012-02-23
	 *	
	 *	@param	bool	$empty	(DOCUMENTME)
	 *	@return	string
	 */

	function http($empty = false){
		// catch static links as well
		$str_empty = $empty ? "^$|" : "";
		#$reg =  $str_empty . '^http://([0-9a-zA-Z_-]+\.)+[0-9a-zA-Z_-]+(\.[a-z]{2,4}){0,1}(/['.chr(46).'+0-9a-zA-Z_-]+)*([/]){0,1}([,:0-9a-zA-Z_-]+\.[a-z]{1,5}){0,1}([?]){0,1}([_a-zA-Z0-9]+=[_a-zA-Z0-9]+[&]{0,1})*$';
		$reg = $str_empty . '[.]*';
		return $reg;
	}

	/**
	 *	returns regex for date
	 *	@param	bool	$empty	(DOCUMENTME)
	 */

	function date($empty = false){
		$datetime = &datetime::singleton();
		return $datetime->get_regex_date($empty);
	}

	/**
	 *	returns regex for time
	 *	@param	bool	$empty	(DOCUMENTME)
	 *	TODO: document return	(CHECKME)
	 */

	function time($empty = false){
		$datetime = &datetime::singleton();
		return $datetime->get_regex_time($empty);
	}

	/**
	 *	returns rexex for text with multiple lines
	 *	@param	bool	$empty	string
	 *	@return	string
	 */

	function text($empty = false){
		$empty = $empty == true ? "^$|" : "";
		return $empty . '^.*$';
	}

	/**
	 *	returns rexex for text with multiple lines
	 *	@param	bool	$empty	(DOCUMENTME)
	 *	@return	string
	 */

	function text_strict($empty = false){
		$empty = $empty == true ? "^$|" : "";
		return $empty . '^[^' . $this->forbidden_chars() . ']*$';
	}

}
?>
