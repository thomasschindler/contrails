<?php

/**
 *	Object to construct and run sanitized shell / OS commands, to help prevent shellcode injection
 *
 *	CURRENT STATUS:
 *
 *	Development / aplha
 *
 *	OVERVIEW:
 *
 *	This object takes a $template for a shell command into which $values are substituted
 *	if they meet $constraints placed upon them.  If the constraints are not met, the command will
 *	not run.  A warning may be sent to administrators/developers to alert them of either an 
 *	attempt to compromise production machines, or of unsafe values being passed which indicate
 *	possible code errors.
 *
 *	This attempts to defend against the following:
 *
 *	Directory traversal:	http://en.wikipedia.org/wiki/Directory_traversal_attack
 *	Shellcode injection:	http://www.theregister.co.uk/2012/03/01/electronic_voting_hacked_bender/
 *	Command modification:	Adding/changing switches and arguments of a command
 *	DoS by overwrite:		Overwriting system files to take down the site
 *	PWN by rename:			Eg: placing PHP in meta tags of image, renaming .php after upload
 *	URL/API redirection:	http://nrupentheking.blogspot.com/2011/04/url-obfuscation-hide-url.html
 *	... and many more ...
 *
 *	This will not protect against logical errors, such as users deleting files belonging to another
 *	user, only that the values match the constraints.
 *
 *	Where many similar commands are to be run in sequence / batched this may be instantiated 
 *	outside a loop, and loop variants set with $x->set_value('file_name', $file_name); and run()
 *	repeadedly.
 *
 *	DETAIL:
 *
 *	$template is simply a string containing %%placeholders%% for values.
 *
 *	$values is a simple dict of keys matching %%placeholders%%, all must be present.
 *
 *	$constraints is also a simple dict of keys matching placeholders, listing the tests which values
 *	must pass in order to assemble the command, with commas separating multiple tests.
 *
 *	CONSTRAINTS:  (Values must be...)
 *
 *		int				A string representation of an integer
 *		float			A string representation of a floating point number
 *		dir				Absolute location of a directory, no /../, &&, environment variables, etc
 *		file			Absolute location of a file, no /../, &&, environment variables, etc
 *		extant_file		Absolute location of a file which already exists
 *		extant_dir		Absolute location of a directory which already exists
 *		oneof:OPT1|OPT2	One of 'OPT1' or 'OPT2', eg oneof:portrait|landscape|center
 *		ext:x|y|..|z	Pipe separated list of allowed file extensions, case insensitive
 *		string			String which does not break out of quotes
 *		in:/some/path/	Checks that a file is in some directory, anti-traversal measure.
 *		url				Must be a non-local URL, not file:///etc/passwd
 *		query:a|b|c		Only allow a, b and c fields in URL query string
 *		begin:some		eg: begin:http://www.foo.com/ 
 *
 *	More constraints to be added as needed.
 *
 *	EXAMPLE:
 *
 *	The following is found in class_magick.inc.php, when generating thumbnails:
 *
 *		$command = ''
 *		 . $this->path . 'convert ' . $from
 *		 . ' -thumbnail ' . $width . 'x' . $height
 *		 . ' -gravity center'
 *		 . ' -extent ' . $width . 'x' . $height 
 *		 . ' -auto-orient'
 *		 . ' ' . $to;
 *		$i = shell_exec($command);
 *
 *	If any of these were not properly sanitized by calling code then injection and directory 
 *	traversal attacks may be possible.  To reduce this risk we might rewrite it to use this object:
 *
 *		$template = ''
 * 		 . '%%magick_path%%convert %%from_file%%'
 *		 . ' -thumbnail %%width%%x%%height%%'
 *		 . ' -gravity center'
 *		 . ' -extent %%width%%x%%height%%' 
 *		 . ' -auto-orient'
 *		 . ' %%to_file%%';
 *
 *		$values = array(
 *			'magick_path' => $this->path,
 *			'from_file' => $from,
 *			'width' => $width,
 *			'height' => $height,
 *			'to_file' => $to
 *		);
 *
 *		$constraints = array(
 *			'magick_path' => 'extant_directory',			//	must be an existing directory
 *			'from_file' => 'extant_file, ext:jpg|png',		//	must be an existing .jpg or .png
 *			'width' => 'int',								//	an integer and nothing else
 *			'height' => 'int',								//	an integer and nothing else
 *			'to_file' => 'file, ext:jpg|png|gif',			//	abs path with no /../, etc
 *		);
 *
 *		$cmd = new template_exec($template, $values, $constraints);
 *		if (!$cmd->passes()) { ... something did not pass constraint checks, handle here ... }
 *
 *		$result = $cmd->run();								//	get exit code of called program
 *		$lines = $cmd->get_output();						//	text from standard output
 *		if (0 == $result) { ... maybe parse output ... }
 *
 */

/*.
	require_module 'standard';
.*/

class template_exec
{

	/**
	 *	Command template into which values will be checked and substituted for %%placeholders%%
	 *	@var string
	 */

	private $template = '';

	/**
	 *	Dict of 'placholder' => 'value'
	 *	@var string[string]
	 */

	private $values = /*. (string[string]) .*/ array();

	/**
	 *	Dict of 'placholder' => 'constraints on this value'
	 *	@var string[string]
	 */

	private $constraints = /*. (string[string]) .*/ array();

	/**
	 *	When checking constraints any errors are noted here.
	 *	@var string
	 */

	public $err_msg = '';

	/**
	 *	Holds last command which was run
	 *	@var string
	 */

	public $shell_cmd = '';

	/**
	 *	Output of last executed command, array of lines printed
	 *	@var string[int]
	 */

	public	$output = /*. (string[int]) .*/ array();

	/**
	 *	Excit code of last executed command
	 *	@var int
	 */

	public	$return_var = -1;

	/**
	 *	Constructor
	 *	@param	string			$template		Command template
	 *	@param	string[string]	$values			Placeholder names and values
	 *	@param	string[string]	$constraints	Placeholder names and constraints on them
	 *	@return	void
	 */

	public function __construct($template, $values, $constraints)
	{
		$this->template = $template;
		if (is_array($values)) { $this->values = $values; }
		if (is_array($constraints)) { $this->constraints = $constraints; }
	}

	/**
	 *	Check a value against constraints on it
	 *
	 *	@param	string	$key	Name of a placeholder in the template
	 *	@return	bool			True if passed, false if not found or not passed
	 */

	public function check_constraints($key)
	{
		if (false == array_key_exists($key, $this->values)) { return false; }
		if (false == array_key_exists($key, $this->constraints)) { return false; }

		$ok = true;
		$constraints = explode(',', $this->constraints[$key]);
		$value = $this->values[$key];

		foreach ($constraints as $constraint)
		{
			$constraint = trim($constraint);			

			//	Simple constraints
			switch (strtolower($constraint))
			{
				case 'int':
					if ((string)((int)$value) != $value) {
//					if (((int)$value) != $value) {
//					if ((string)((int)$value) != (string)$value) {
						$ok = false;
						$this->err_msg .= "'$key' must be an integer, and nothing else";
					}
					break;		//..................................................................

				case 'float':
					if (!is_numeric($value)) {
						$this->err_msg .= "'$key' must be a number, and nothing else";
						$ok = false;
					}
					break;		//..................................................................

				case 'string':
					//TODO improve this
					$quoteme = array('|', '>', '<', '"', "'", '#', '[', ']', '(', ')', '\\');
					$value = str_replace("\\n", '', $value);
					$value = str_replace("\\r", '', $value);
					$value = str_replace("\\t", '', $value);

					//	ignore if safely quoted
					foreach($quoteme as $item) { $value = str_replace('\\' . $item, '', $value); }

					//	check for unquoted
					foreach($quoteme as $item) {
						if (false !== strpos($value, $item)) {
							$ok = false;
							$this->err_msg .= ''
							 . "String contains unescaped, unsafe character '$item', pass as:"
							 . "position: " . strpos($value, $item) . "<br/>\n"
							 . "<small><pre>" . escapeshellcmd($value) . "</pre></small><br/>\n";
						}
					}

					#if (escapeshellcmd($value) !== $value)
					#{
					#	$ok = false;
					#	$this->err_msg .= ''
					#	 . "String contains unsafe characters, pass as:"
					#	 . escapeshellcmd($value) . "<br/>\n";
					#}
					break;		//..................................................................

				case 'dir':
					$value = str_replace('//', '/', $value);
					if ($this->clean_path($value) !== $value)
					{
						$ok = false;
						$this->err_msg .= "'$key' must be an absolute directory location.\n";
					}
					break;		//..................................................................

				case 'file':
					$value = str_replace('//', '/', $value);
					if ($this->clean_path($value) !== $value)
					{
						$ok = false;
						$this->err_msg .= ''
						 . "'$key' must be an absolute file location.\n"
						 . "<small><pre>$value</pre></small>\n";
					}
					break;		//..................................................................

				case 'extant_dir':
					if ((false == file_exists($value)) || (false == is_dir($value)))
					{
						$ok = false;
						$this->err_msg .= "'$key' must be an existing directory ($value).\n";
					}
					break;		//..................................................................

				case 'extant_file':
					// it is possible for imagmagick to accept an index to a page in a pdf file:
					// filename[index]
					// for checking, we remove this
					$value = preg_replace('/\[[0-9]*\]/','',$value);
					if ((false == file_exists($value)) || (false == is_file($value)))
					{
						$ok = false;
						$this->err_msg .= "'$key' must be an existing file ($value).\n";
					}
					break;		//..................................................................

				case 'url':
					//TODO: implement
					break;		//..................................................................

				default:	/* handle inrecognized constraints here, debug warn for developers */
			}

			//	Value must begin...
			if ('begin:' === substr($constraint, 0, 6))
			{
				$match = substr($constraint, 6);
				if (substr($value, 0, strlen($match)) !== $match)
				{
					$this->err_msg .= "'$key' must begin '$match'\n";
					$ok = false;
				}
			}

			//	Enum, value must be one of...
			if ('oneof:' === substr($constraint, 0, 6))
			{
				$found = false;			//	set true if match found
				$match_set = explode('|', substr($constraint, 6));

				foreach ($match_set as $match)
				{
					if (trim($match) == trim($value)) { $found = true; }
				}

				if (false == $found)
				{
					$ok = false;
					$this->err_msg .= "'$key' Must be one of " . implode(', ', $match_set) . "\n";
				}
			}

			//	Extension must be one of...
			if ('ext:' === substr($constraint, 0, 4))
			{
				$found = false;			//	set true if one of the extensions match value
				$match_set = explode('|', substr($constraint, 4));

				foreach ($match_set as $ext)
				{
					$ext = strrev("." . trim($ext));
					$match = substr(strrev($value), 0, strlen($ext));
					if (strtolower($match) == strtolower($ext)) { $found = true; }
				}

				if (false == $found)
				{
					$this->err_msg .= "'$key' must end with one of " . implode(', ', $match_set) . "\n";
					$ok = false;
				}
			}

			//	Querystring can contain only...
			if ('query:' === substr($constraint, 0, 6))
			{
				//	(TODO) implement
			}

			//	Check for placeholder injection
			//	(TODO) implement
		}

		//	(TODO) use framework debug mode to control these messages
		//echo "template_exec::check_constraints($key) - returns $ok <br/>\n";
		//if (false == $ok) { echo "template_exec->err_msg - " . $this->err_msg . "<br/>\n"; }
		return $ok;
	}

	/**
	 *	Clean a path to make injection and directory traversal harder
	 *
	 *	@param	string	$path	File or directory location
	 *	@return	string
	 */

	public function clean_path($path)
	{
		$path = str_replace('//', '/', $path);

		//	Unicode directory traversal, see: http://www.schneier.com/crypto-gram-0007.html
		$path = str_ireplace("%c0%af", '/', $path);
		$path = str_ireplace("%c0%9v", '/', $path);
		$path = str_ireplace("%c1%1c", '/', $path);

		//	Precent encoded
		$path = str_ireplace("%2f", '/', $path);
		$path = str_ireplace("%5c", '/', $path);
		$path = str_ireplace("%2e", '.', $path);

		//	Classic directory traversal
		$path = preg_replace("/\/\.+\//", "/", $path);

		//	Control characters and injection
		$path = escapeshellcmd($path);

		return $path;
	}

	/**
	 *	Clean file name of disallowed characters
	 *
	 *	There are doubtless more.  This should be expanded in time to deal with extended
	 *	character sets and any control characters I have missed.
	 *
	 *	@param	string	$file_name		Candidate file name
	 *	@param	bool	$allow_space	Set true to allow spaces in file name
	 *	@param	string
	 */

	public function clean_file_name($file_name, $allow_space = false)
	{
		$disallow = array(
			'/', '\\', '?', '#', '$', '%', '&', '*', '!', '|'. '"', '\'',
			';', ':', '>', '<', '`', "\t", "\n", "\r"
		);

		foreach($disallow as $item) { $file_name = str_replace($item, '', $file_name); }
		if (!$allow_sapce) { $file_name = str_replace(' ', '', $file_name); }
		return $file_name;
	}

	/**
	 *	Set / change one of the values
 	 *
	 *	@param	string	$key	Name of a placeholder in the template
	 *	@param	string	$value	New value
	 *	@return	bool			True if new value passes constraints on this placeholder
	 */

	public function set_value($key, $value) {
		$this->values[$key] = $value;
		return $this->check_constraints($key);
	}

	/**
	 *	Check all values, assemble command and run it
	 *
	 *	stdout from this is stored as lines in $this->output
	 *	TODO: figure out how to do backgrounds on MS Windows hosts
	 *
	 *	@param	bool	$background		Run in background
	 *	@return	int						Exit code of called command, -1 if not run
	 */

	public function run($background = false)
	{
		$shell_cmd = $this->template;
		$ok = true;

		//	Check and replace all values
		foreach($this->values as $key => $value)
		{			
			if (false == $this->check_constraints($key)) { $ok = false; }
			else { $shell_cmd = str_replace('%%' . $key . '%%', $value, $shell_cmd); }
		}
		
		//	Look for any unfilled placeholders
		if (false !== strpos($shell_cmd, '%%')) {
			$this->err_msg  .= "Unfilled placeholder in template.<br/><pre>$shell_cmd</pre><br/>";
			$ok = false;
		}
		
		//	Do not run this if any condition was not satisfied
		if (false == $ok) {
			//	(TODO) use framework debug mode to control these messages
			#echo "template_exec->run() - returns false<br/>\n";
			#echo "template_exec->err_msg: " . $this->err_msg . "<br/>\n";
			#echo "<small><pre>" . $shell_cmd . "</pre></small><br/>\n";
			return -1;
		}

		//	In background?
		if ($background) { $shell_cmd .= " &> /dev/null &"; }

		//	Everything checks out, run command
		$this->shell_cmd = $shell_cmd;
		exec($shell_cmd, $this->output, $this->return_var);
		//TODO: logging and debug calls here
		return $this->return_var;
	}

	/**
	 *	Get output of last command to be run
	 *	@return	string[int]		Array of output lines
	 */

	public function get_output() { return $this->output; }

}

?>
