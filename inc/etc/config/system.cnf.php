<?
/**
*	do not modify this file!
*/
class CONF extends HOST_CONF
{
	function web_dir()
	{
		if(method_exists("HOST_CONF","web_dir"))
		{
			return HOST_CONF::web_dir();
		}
		return substr(__FILE__,0,-27)."web";
	}
	function inc_dir() 
	{
		if(method_exists("HOST_CONF","inc_dir"))
		{
			return HOST_CONF::inc_dir();
		}
		return substr(__FILE__,0,-26);
	}
	function tmp_dir() 
	{
		return CONF::inc_dir().'tmp';
	}
	function start_view() 
	{
		return array('start', 'page', 'view');
	}
	function session_options() 
	{
		return array
		(
			'name'          => 'SESSION',
			'data_name'     => '_MY_SESSION_DATA',
			'use_cookies'   => false,
			'cache_limiter' => 'private, must-revalidate',
		);
	}
	/**
	*	set the linktype
	*/
	function linktype()
	{
		return "scs";
	}
	/**
	*	get allows you to retrieve a custom configuration
	*	it will look in etc/config/{PROJECT_NAME}/{cnf}
	*	and return null if it doesn't find anything
	*/
	function get($cnf,$refresh=false)
	{
		static $__custom_cnf__;
		if(!$__custom_cnf__[$cnf] OR $refresh)
		{
			if(!is_file(CONF::cnf_dir()."/".$cnf))
			{
				return null;
			}
			$__custom_cnf__[$cnf] = unserialize(UTIL::file_get_contents(CONF::cnf_dir()."/".$cnf));
		}
		return $__custom_cnf__[$cnf];
	}
	/**
	*	creates a file for each custom configuration
	*	the file will be created in: etc/config/{PROJECT_NAME}/{cnf} 
	*	with cnf as filename
	*	and val as value
	*	this value will be returned by custom
	*	if it exists it will be overwritten
	*/
	function set($cnf,$val)
	{
		$cnf_dir = CONF::cnf_dir();
		// format val
		// open the file for writing no matter what it contains or whether it exists
		$cnf_file = fopen($cnf_dir."/".$cnf,"w+");
		fwrite($cnf_file,serialize($val));
		fclose($cnf_file);
		// flush the cache
		CONF::get($cnf,true);
		return true;
	}
	function rm($cnf)
	{
		$cnf_dir = CONF::cnf_dir();
		UTIL::delete_file($cnf_dir."/".$cnf);
		return true;
	}
	function cnf_dir()
	{
		static $cnf_dir;
		if(!$cnf_dir)
		{
			$cnf_dir = CONF::inc_dir()."/etc/config/".CONF::project_name();
			if(!is_dir($cnf_dir))
			{
				mkdir($cnf_dir,0700);
			}
		}
		return $cnf_dir;
	}
}	
?>