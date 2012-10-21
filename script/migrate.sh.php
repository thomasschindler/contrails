#!/usr/bin/php -q
<?
/**
* 
* 	handles database migrations 
*	knows the following commands:
*		deduct 	> generates migrations for all tables in the database
*		sync 	> modifies the database according to the current migrations 
*		models 	> generates models based on the current migration info (will)
*	
*	notes:
*		expects a default.cnf.php to use for the database settings
*		all migrations will be stored in an archive for later reference
*	
*	@TODO:
*		update KEYS
*		relations (configuration and models)
*/



// include some necesary files
$start = microtime();
$tmp = pathinfo(__FILE__);
$base = substr($tmp['dirname'],0,-6);
$files = array
(
	'inc/etc/config/default.cnf.php',
	'inc/system/oos/class_mc.inc.php',
	'inc/system/oos/db.inc.php',
	'inc/system/oos/db_mysql.inc.php',
	'inc/system/oos/db_result.inc.php',
);
foreach($files as $file)
{
	if(!is_file($base.$file))
	{
		die("file missing: ".$base.$file."\n");
	}
	include($base.$file);
}


switch(trim(@$argv[1]))
{
	case 'deduct':
		deduct();
	break;
	case 'sync':
		sync();
	break;
	case 'models':
		models();
	break;
	default:
		help();
}

verbose("DONE [".($start-microtime())."]");

/**
*	show some help
*/
function help()
{
	echo "\nthe following commands are implemented\n\tdeduct 	> generates migrations for all tables in the database\n\tsync 	> modifies the database according to the current migrations \n\n";
	die;
}

/**
*	create a new set of models based on the current configuration
*/
function models()
{
	global $base;
	$d = scandir($base."script/resources/migrate/");
	foreach($d as $file)
	{
		if(preg_match("/php/",$file) && is_file($base."script/resources/migrate/".$file))
		{
			$table = substr($file,0,-4);
			include($base."script/resources/migrate/".$file);
			$s = "<?\nclass generated_".$table." extends model\n{\n";
			// add some vars
			$s .= "\tvar \$_fields = array();\n";
			$s .= "\tvar \$_table = '".$table."';\n";
			// add relationship description
			if(isset($config['relations']))
			{
				$s .= "\tvar \$_relations = array\n\t(\n".array2string($config['relations'],2)."\t);\n";
			}
			// 
	$s .= "
	protected function _keys()
	{
		return array(".array2string($config['keys'],2,true).");
	}

	protected function _fields()
	{
		return array(".array2string($config['fields'],2,true).");
	}

";
			// create the getter/setter method(s) (include some validation)
			foreach($config['fields'] as $field)
			{
				$tmp = explode("(",$field['Type']);
				$type = trim($tmp[0]);
				$length = NULL;
				if(isset($tmp[1]))
				{
					$tmp = explode(")",$tmp[1]);
					$length = (int)$tmp[0];
				}
				$s .= "
	function ".$field['Field']."(\$d=null)
	{
		if(\$d !== null)
		{
			if(!\$this->_valid(\$d,'".$type."'".(($length!==null)?",".$length:"")."))
			{
				return false;
			}
			\$this->_fields['".$field['Field']."'] = \$d;
			return true;
		}
		return \$this->_fields['".$field['Field']."'];
	}
";
			}
			$s .= "}\n?>";
			if(!is_dir($base."inc/models/".$table))
			{
				mkdir($base."inc/models/".$table);
			}
			file_put_contents($base."inc/models/".$table."/generated.php", $s);
			file_put_contents($base."inc/models/".$table."/model.php", "<?class ".$table." extends generated_".$table." {} ?>");
		}
	}
}

/**
*	compare the current structure with the migrations and update the structure
*/
function sync()
{
	global $base;
	$db = db_get();
	$structure = structure_get();
	verbose("SYNCING");
	// get non-existant tables first
	$d = scandir($base."script/resources/migrate/");
	foreach($d as $file)
	{
		if(preg_match("/php/",$file) && is_file($base."script/resources/migrate/".$file))
		{
			$table = substr($file,0,-4);
			if(!isset($structure[$table]))
			{
				verbose($table,1);
				include($base."script/resources/migrate/".$file);
				$fields = array();
				foreach($config['fields'] as $field)
				{
					$fields[] = field_get($field,$table);
				}
				if(isset($config['keys']))
				{
					foreach($config['keys'] as $key)
					{
						$fields[] = $key['type']." (".implode(",",$key['fields']).")";
					}
				}
				$q = "CREATE TABLE ".$table." (".implode(", ",$fields).") DEFAULT CHARSET=utf8";
				$db->query($q);
				// fill with data
				if(isset($config['data']))
				{
					foreach($config['data'] as $data)
					{
						$db->query("INSERT INTO ".$table." (".$table.".".implode(", ".$table.".",array_keys($data)).") VALUES ('".implode("','",$data)."')");
					}
				}
			}
			else
			{
				include($base."script/resources/migrate/".$file);
				if(serialize($structure[$table]['fields']) != serialize($config['fields']))
				{
					verbose("UPDATING ".$table,1);
					$alter = array();
					// FIELDS FIRST
					// check what to add
					foreach($config['fields'] as $k => $v)
					{
						if(!isset($structure[$table]['fields'][$k]))
						{
							/*  ALTER TABLE `sys_vid` ADD `test` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `mod_name`;*/
							$alter[$k] = 'ALTER TABLE '.$table.' ADD '.field_get($v,$table);
						}
					}
					// check what to remove
					foreach($structure[$table]['fields'] as $k => $v)
					{
						if(!isset($config['fields'][$k]))
						{
							/* ALTER TABLE `sys_vid` DROP `test`;*/
							$alter[$k] = 'ALTER TABLE '.$table.' DROP '.$table.'.'.$k;
						}
					}
					// check what to change
					foreach($config['fields'] as $k => $v)
					{
						if(!isset($alter[$k]))
						{
							foreach($v as $vk => $vv)
							{
								if($structure[$table]['fields'][$k][$vk] != $vv)
								{
									/* ALTER TABLE `sys_vid` CHANGE `mod_name` `mod_name` VARCHAR(244)  NOT NULL  DEFAULT '';*/
									$alter[$k] = 'ALTER TABLE '.$table.' CHANGE '.$table.'.'.$k.' '.field_get($v,$table);
								}
							}
						}
					}
					foreach($alter as $q)
					{
						$db->query($q);
					}
				}
				// NOW KEYS
				// @TODO
				if(serialize($structure[$table]['keys']) != serialize($config['keys']))
				{
					/* ALTER TABLE `sys_vid` DROP PRIMARY KEY; */
				}
			}

		}
	}
	if(count($structure)>0)
	{
		foreach($structure as $table => $config)
		{
			if(is_file($base."script/resources/migrate/".$table.".php"))
			{
				unset($structure[$table]);
			}
		}
	}
}

/**
*	get fields string for alterations and creations
*/

function field_get($field,$table)
{
	$tmp = array();
	$tmp[] = $table.".".$field['Field'];
	$tmp[] = $field['Type'];
	$tmp[] = $field['Null'] == 'NO' ? "NOT NULL" : "NULL";
	$tmp[] = !empty($field['Default']) ? "DEFAULT '".$field['Default']."'" : "";
	return implode(" ",$tmp);
}

/**
*	connect to the db
*	load the structure 
*	write the structure into files
*/
function deduct()
{
	global $base;
	$migration = structure_get();
	verbose("DEDUCTING");
	// write the migrations
	foreach($migration as $table => $config)
	{
		if(!is_file($base."script/resources/migrate/".$table.".php"))
		{
			verbose($table,1);
			$data = data_get($table);
			if(count($data)>0)
			{
				$config['data'] = $data;
			}
			file_put_contents($base."script/resources/migrate/".$table.".php", array2string($config));
		}
	}
}

/**
* returns a db object we can use
*/

function db_get()
{
	return DB::get_interface(HOST_CONF::db_options());
}

/**
*	return a nice string represenation of an array
*/

function array2string($a,$l=1,$inline=false)
{
	$s = '';
	if($l == 1)
	{
		$s = "<?\n\$config = array\n(";
	}
	$p = str_repeat("\t", $l);
	foreach($a as $k => $v)
	{
		$s .= $p."'".$k."' => ";
		if(is_array($v))
		{
			$s .= "array\n".$p."(\n".array2string($v,$l+1).$p."),\n";
		}
		else
		{
			if(is_null($v))
			{
				$s .= "NULL,\n";
			}
			elseif(is_string($v))
			{
				$s .= "'".$v."',\n";	
			}
			elseif(is_numeric($v))
			{
				$s .= $v.",\n";
			}
			else
			{
				$s .= "'".$v."',\n";
			}
		}
	}
	if($l == 1)
	{
		$s .= "\n);\n?>";
	}
	if($inline === true)
	{
		$s = preg_replace("/\n/","",$s);
		$s = preg_replace("/\t/","",$s);
	}
	return $s;
}

/**
*	return the current live db structure
*/
function structure_get()
{
	verbose("GETTING THE STRUCTURE");
	// get the configuration
	$conf = HOST_CONF::db_options();
	$tables = array();
	$structure = array();
	// connect to the database
	$db = db_get();
	// read all tables
	$r = $db->query("show tables");
	// filter all tables that already have migrations
	while($r->next())
	{
		$table = $r->f('Tables_in_'.$conf['master']['db_name']);
		$tables[$table] = $table;
	}
	// get the fields
	// @TODO: deduct relations
	if(count($tables) > 0)
	{
		foreach($tables as $table)
		{
			verbose($table,1);
			// get the fields
			$fields = $db->query("show fields from ".$table);
			while($fields->next())
			{
				$structure[$table]['fields'][$fields->f('Field')] = $fields->r();
			}
			// get the keys
			$create = $db->query("SHOW CREATE TABLE ".$table);
			$lines = explode("\n",$create->f('Create Table'));
			foreach($lines as $line)
			{
				$line = trim($line);
				$tmp = explode(" ",$line);
				$type = null;
				switch(strtoupper($tmp[0]))
				{
					case 'PRIMARY':
						$type = 'PRIMARY KEY';
					break;
					case 'KEY':
						$type = 'KEY';
					break;
					case 'UNIQUE':
						$type = 'UNIQUE';
					break;
					case 'FULLTEXT':
						$type = 'FULLTEXT';
					break;
				}
				if(isset($type))
				{
					$line = preg_replace("/`/","",$line);
					$tmp = explode("(",$line);
					$tmp = explode(")",$tmp[1]);
					$tmp = explode(",",$tmp[0]);
					$structure[$table]['keys'][] = array
					(
						'type' => $type,
						'fields' => $tmp
					);
				}
			}
		}
	}
	return $structure;
}

/**
*	index the data on the required table and return it as an array
*/

function data_get($table)
{
	$db = db_get();
	$r = $db->query("SELECT * FROM ".$table);
	return $r->get();
}

/**
*	show some info
*/

function verbose($s,$t=0)
{
	echo str_repeat("\t",$t).$s."\n";
}

?>
