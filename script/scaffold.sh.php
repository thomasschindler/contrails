#!/usr/bin/php -q
<?

/*
* scaffold a module or a migration
*/

switch($argv[1])
{
	case 'mod':
		scaffold_mod($argv);
	break;
}

function scaffold_mod($a)
{
	// if the module already exists, we don't create the dir
	if(is_dir("inc/mods/".$a[2]))
	{
		die("this module already exists\n");
	}
	// initialize the folder structure
	$dirs = array
	(
		$a[2] => array
		(
			'language' => array(),
			'form' => array(),
			'etc' => array(),
			'assets' => array(),
			'template' => array
			(
				'contrails' => array()
			),
		)
	);
	scaffold_module_dirs("inc/mods/",$dirs);
	// fill them with the initial classes and some other useful files
	$files = array
	(
		'class_view.php' => 'class_'.$a[2].'View.inc.php',
		'class_action.php' => 'class_'.$a[2].'Action.inc.php',
		'form.php' => 'form/'.$a[2].'.php',
		'template.php' => 'template/contrails/'.$a[2].'.php',
		'en.php' => 'language/en.php',
		'access.php' => 'etc/access.php',
	);
	foreach($files as $src => $tgt)
	{
		scaffold_module_file($a[2],$src,$tgt);
	}
	
}

function scaffold_module_file($name,$src,$tgt)
{
	$d = file_get_contents('script/resources/scaffold/'.$src);
	$d = preg_replace('/begin/',$name,$d);
	file_put_contents('inc/mods/'.$name.'/'.$tgt, $d);
}

function scaffold_module_dirs($b,$a)
{
	foreach($a as $k => $list)
	{
		mkdir($b."/".$k);
		if(count($list) != 0)
		{
			scaffold_module_dirs($b."/".$k,$list);
		}
	}
}


?>