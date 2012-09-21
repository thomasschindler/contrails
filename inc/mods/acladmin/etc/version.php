<?
$def = array(
	/*
	*	the name of the module
	*/
	'name' => 'acladmin',
	/*
	*	type of the module
	*	can be system / virtual / standard
	*/
	'type' => 'standard',
	/*
	*	short description
	*/
	'description' => 'acladmin',
	/*
	*	all files of the implementation
	*	important for language handling
	*/
	'files' => array(
		'inc/etc/access/acladmin.access',
		'inc/mods/acladmin/class_acladminAction.inc.php',
		'inc/mods/acladmin/class_acladminView.inc.php',
		'inc/rsc/def/acladmin.def',
		'web/tpl/hundertelf/acladmin/acl_list.tpl'
	),
	/*
	*	all folders of the implementation
	*	this is used for installation and debugging
	*/
	'folders' => array(
		
	),
	/*
	*	all public events
	*/
	'events' => array(
		
	),
	/*
	*	all public views
	*/
	'views' => array(
		
	),
	/*
	*	author name
	*/
	'author' => 'hundertelf,Thomas Schindler,Joachim Klinkhammer,Oli Blum',
	/*
	*	author email
	*/
	'email' => 'development@hundertelf.com',
	/*
	*	creation date
	*/
	'date' => '2004',
	/*
	*	version number
	*/
	'version' => '1',
);
?>
