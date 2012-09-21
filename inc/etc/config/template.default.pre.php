<?
/**
*	CONFIGURATION-FILE
*	copy this file to your webroot and modify for your needs
*/
class HOST_CONF 
{
	function cache()
	{
		return 86400;
	}
	function live()
	{
		return true;
	}
	function project_name()
	{
		return '__PROJECT_NAME__';
	}
	function l10n()
	{
		return false;
	}
	function baseurl()
	{
		return '__URL__';
	}
	function default_layout()
	{
		return 'oos';
	}
	function default_pages()
	{
		return array(
			'usr_home' => 1, 
			'usr_online' => 328,
			'usr_register' => 334,
		);
	}
	function default_lang()
	{
		return 'en';
	}
	function lang()
	{
		return 'de';
	}
	function uid()
	{
		return 200;
	}
	function pid($lang = null)
	{
		return 348;
	}
	function __root()
	{
		return 122;
	}
	function db_options() 
	{
		return array
		(
			'master' => array
			(
				'db_type' => 'mysql',
				'db_host' => '__DB_HOST__',
				'db_user' => '__DB_USER__',
				'db_pass' => '__DB_PASS__', 
				'db_name' => '__DB_NAME__',
			)
		);
	}
}

?>
