<?php

	require_once(__DIR__ . '/db_mysql.inc.php');

/**
 *	The base class of database wrapper object
 *
 *	[en] This class is used only in the abstract, the only public method Is & DB :: singleton ()
 *	[en] [note the '&' character!], it returns a pointer to an instance of the appropriate wrapper
 *	[en] object, already connected to the database.
 *
 *	[de] basis-klasse zur erzeugung eines db-objektes
 *
 *	[de] diese klasse wird nur abstrakt verwendet, die einzige oeffentliche methode ist
 *	[de] &DB::singleton() [wichtig ! das '&' zeichen], die einen zeiger auf die db-instanz liefert
 *	[de] (im richtigen typ und schon connected)
 *
 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@date			12 05 2004
 *	@version		1.0
 *	@since			1.0
 *	@package		util
 */

class DB {

		/* no longer used
		var $db_user;
		var $db_pass;
		var $database;
		var $db_host;
		var $db;
		*/		

		/**
		 *	Instantiate a new database wrapper if one does not already exist and return as pointer
		 *	@param	array	$db_options		DBMS configuration set
		 *	@return	object
		 */

		function get_interface($db_options){
			$index = crc32(serialize($db_options));
			static $objects;

			if ($objects[$index]){ return $objects[$index]; }


			$objects[$index] = new db_mysql($db_options);
			$objects[$index]->connect();
			if (is_object($objects[$index])) { return $objects[$index]; }
			return null;
		}

		/**
		 *	Get a pointer to the singleton DB wrapper object
		 *
		 *	[en] All instantiations of the database rapper should use this method.
		 *
		 *	[de] liefert zeiger auf db-objekt
		 *	[de] wenn noch keines vorhanden, wird eines erzeugt. alle instanzierungen 
		 *	[de] einer db-klasse laufen ueber diese funktion. vorteil: nur eine instanz 
		 *	[de] fuer die laufzeit eines scriptes 
		 *
		 *	@return	object	instance of db_mysql, or object of same interface
		 */

		function &singleton() {
			static $instance;
			
			if (is_object($instance)) { return $instance; }
			
			//	[en] The object has not yet been instantiated - load DBMS config and do so
			//	[de] noch keine db-instanz da, config auslesen und entsprechende db-klasse
			//	[de] instanzieren

			$db_options = /*. (mixed[string]) .*/ CONF::db_options();
			
			$db_options_slave = -1;			

			// round robin load balancing 
			if ((array_key_exists('master', $db_options)) && (is_array($db_options['master'])))
			{
				if ((array_key_exists('slaves', $db_options)) && (is_array($db_options['slaves'])))
				{
					$slave_idx = rand(0, count($db_options['slaves']) - 1);
					$db_options_slave = $db_options['slaves'][$slave_idx];
				}
				$db_options = $db_options['master'];
			}
			
			$class_name = '';
			switch($db_options['db_type']) 
			{
				case 'mysql':
					$class_name = 'db_mysql';
				break;
				default:
					echo 'sorry, no db support for ['.$db_options['type'].'] :(';
				exit;
			}
			
			include_once(CONF::inc_dir() . 'system/'.$class_name.'.inc.php');
			
			$instance = new db_mysql($db_options);                        
			$instance->connect();
			if (-1 !== $db_options_slave) { $instance->connect_slave($db_options_slave); }

			return $instance;
		}

   }

?>
