<?php
	// get the pre config
	if(!@include_once("etc/config/".$_SERVER['HTTP_HOST'].".pre.php"))
	{
		@include_once("etc/config/default.pre");
	}
	// include config or send to installation
	if(!@include_once("etc/config/".$_SERVER['HTTP_HOST'].".cnf.php"))
	{
		if(!@include_once('etc/config/default.cnf.php'))
		{
			die('please install');
		}
	}
	// main oos configuration - independent of host configurations
	include_once('etc/config/system.cnf.php'); 
	// include all necessary basic stuff
	$inc_dir = CONF::inc_dir();
	include_once($inc_dir . '/etc/system/global_functions.inc.php');	// all global functions should be defined here
	include_once($inc_dir . '/etc/system/constants.cnf.php');			// all constants should be defined here
	include_once($inc_dir . '/system/oos/class_mc.inc.php');			// master-of-ceremony
	include_once($inc_dir . '/system/oos/class_opc.inc.php');			// output controller
	include_once($inc_dir . '/system/oos/class_e.inc.php');				// internationalization
	include_once($inc_dir . '/system/oos/class_client.inc.php');		// client-klasse
	include_once($inc_dir . '/system/oos/class_sess.inc.php');			// session-verwaltung
	include_once($inc_dir . '/system/oos/class_modView.inc.php');		// parent fuer alle View-klassen
	include_once($inc_dir . '/system/oos/class_modAction.inc.php');		// parent fuer alle Action-klassen
	include_once($inc_dir . '/system/oos/class_error.inc.php');			// fehler-klasse
	include_once($inc_dir . '/system/oos/class_burc.inc.php');			// best uniform resource connector
	include_once($inc_dir . '/system/oos/class_template_exec.inc.php');	// sanitze commands run at the shell		
	include_once($inc_dir . '/system/oos/class_util.inc.php');			// allg. hilfsklasse
	include_once($inc_dir . '/system/oos/db.inc.php');					// datenbankverbindung
	include_once($inc_dir . '/system/oos/db_result.inc.php');			// sql results
	include_once($inc_dir . '/system/oos/db_table.inc.php');			// database table schema
	include_once($inc_dir . '/system/oos/db_query.inc.php');			// templated sql queries
	include_once($inc_dir . '/system/oos/db_crud.inc.php');				// sql abstraction and sanitization
	include_once($inc_dir . '/system/oos/class_nested_sets.inc.php');	// tree
?>