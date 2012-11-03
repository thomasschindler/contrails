<?
/**
*	CONFIGURATION-FILE
*	copy this file to your webroot and modify for your needs
*/
class HOST_CONF 
{
	function dir($s)
	{
		$dir = array
		(
			'log' => '/tmp/'
		);
		if(isset($dir[$s]))
		{
			return $dir[$s];
		}
		return false;
	}
	function cache()
	{
		return 0;
	}
	function live()
	{
		return true;
	}
	function project_name()
	{
		return 'tajapa';
	}
	function baseurl()
	{
		return 'http://tajapa.local';
	}
	function default_layout()
	{
		return 'contrails';
	}
	function default_pages()
	{
		
	}
	function default_lang()
	{
		return 'en';
	}
	function lang()
	{
		return 'de';
	}
	function guest()
	{
		return 200;
	}
	function su()
	{
		return 122;
	}
	function pid($lang = null)
	{
		return 348;
	}
	function db_options() 
	{
		return array
		(
			'master' => array
			(
				'db_type' => 'mysql',
				'db_host' => 'localhost',
				'db_user' => 'root',
				'db_pass' => 'root', 
				'db_name' => 'tajapa',
			)
		);
	}
}

?>
