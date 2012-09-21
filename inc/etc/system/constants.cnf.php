<?
	/**
	* this file holds all the constants ( or should do so )
*	@package	divconf
		force view name is an optional fourth parameter 
		for opc->call_view
		it forces call view to use the view_name instead of the vid
	*/
	define("FORCE_VIEW_NAME",true);

	define('JIN_CLASS_VALIDATOR', 'system/class_validator.inc.php');
	define('JIN_CLASS_FORM', 'system/class_form.inc.php');
	define('JIN_CLASS_EXIF_READER', 'system/exif_reader.inc.php');
	define('JIN_CLASS_EXIF_WRITER', 'system/exif_writer.inc.php');
	define('JIN_CLASS_CONNECTOR', 'system/remote_connection.class');
	define('JIN_CLASS_BURC', 'system/class_burc.inc.php');
	define('JIN_CLASS_CYPHER', 'system/class_cypher.inc.php');
	define('JIN_CLASS_EXIF_REGEX', 'system/exif_regex.inc.php');
	
	
	define('JIN_ACL_TYPE_GROUP', 1);
	define('JIN_ACL_TYPE_USER',  2);
	/**
		switch between different types of link creation
		for now: 
			static / dynamic / burc
	*/
	define('LINK_TYPE',CONF::linktype());
	define("RESERVED","_06dk_");

?>
