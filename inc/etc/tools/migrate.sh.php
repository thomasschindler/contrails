#!/usr/bin/php -q
<?
chdir(substr(dirname(__FILE__),0,-13));
/**
	!! IMPORTANT !!
	-- all moving has to be done using SVN --

	the goal:
	move everything that belongs to a module to the module dir 
	
	- web/tpl/{LAYOUT}/{MODULE}/{NAME}.tpl						=> inc/mods/{MODULE}/template/{LAYOUT}/{NAME}.php
	- inc/etc/lang/{LANG}/{MODULE}.lang								=> inc/mods/{MODULE}/language/{LANG}.php
	- inc/etc/access/{MODULE}.access										=> inc/mods/{MODULE}/etc/access.php
	- inc/etc/form/{NAME}.form												=> inc/mods/{MODULE}/form/{NAME}.php
	- inc/etc/instance/{MODULE}.instance								=> inc/mods/{MODULE}/etc/instance.php	
	- inc/rsc/def/{MODULE}.def												=> inc/mods/{MODULE}/etc/version.php
	- inc/src/sql/tbl/{NAME}.sql												=> inc/mods/{MODULE}/version/{NAME}.sql
	
	we will need a better update mechanism...
	version.php - for now the old {MODULE}.def file - will hold information on versions
	if a version changes, a set of instructions can be performed (sql/create folders/etc)
	
	- we try and do the obvious first: - everything that goes by the name of the mod
		- tpl 
		- access
		- def
		- instance
		- lang
		
	- then the weak stuff
		- then we have a look at the def file for sql
		- we check the forms for {MODULE}."_" OR "mod_"{MODULE}
	
*/
// get the templates
$td = dir("web/tpl/");
while(false !== ($t = $td->read()))
{
	if($t == 'shared')
	{
		continue;
	}
	if(substr($t,0,1)!='.')
	{
		$layout[$t] = $t;
	}
}
// get the languages
$tl = dir("inc/etc/lang/");
while(false !== ($t = $tl->read()))
{
	if(substr($t,0,1)!='.')
	{
		$lang[$t] = $t;
	}
}
// get the forms
$tl = dir("inc/etc/form/");
while(false !== ($t = $tl->read()))
{
	if(substr($t,0,1)!='.')
	{
		if(substr($t,0,4) == 'mod_')
		{
			$k = substr($t,4);
		}
		else
		{
			$k = $t;
		}
		$k = preg_replace("/\./","_",$k);
		$form[trim($k)] = trim($t);
	}
}

$dir = 'inc/mods/';
$d = dir($dir);
// collect the modules
while(false !== ($e = $d->read()))
{
	if(substr($e,0,1)!='.')
	{
		$m[$e] = $e;
		// create the folders
		if(!is_dir($dir.$e.'/template'))
		{
			svn_mkdir($dir.$e.'/template');
			svn_mkdir($dir.$e.'/language');
			svn_mkdir($dir.$e.'/etc');
			svn_mkdir($dir.$e.'/form');
			svn_mkdir($dir.$e.'/version');
			svn_mkdir($dir.$e.'/assets');
		}
		// move the obvious

		svn_move('inc/etc/access/'.$e.'.access',$dir.$e.'/etc/access.php');
		svn_move('inc/etc/instance/'.$e.'.instance',$dir.$e.'/etc/instance.php');		
		svn_move('inc/rsc/def/'.$e.'.def',$dir.$e.'/etc/version.php');

		// the templates

		foreach($layout as $l)
		{
			svn_mkdir($dir.$e.'/template/'.$l.'/');
			// read through the tpl dir and move the files from .tpl to .php
			if(is_dir('web/tpl/'.$l.'/'.$e))
			{
				$tmp = dir('web/tpl/'.$l.'/'.$e);
				while(false !== ($te = $tmp->read()))
				{
					if(substr($te,0,1)==".")
					{
						continue;
					}
					$name = explode(".",$te);
					$name[sizeof($name)-1] = 'php';
					$name = implode(".",$name);
					svn_move('web/tpl/'.$l.'/'.$e.'/'.$te,$dir.$e.'/template/'.$l.'/'.$name);
				}
			}
		}

		// the languages
		foreach($lang as $l)
		{
			svn_move('inc/etc/lang/'.$l.'/'.$e.'.lang',$dir.$e.'/language/'.$l.'.php');
		}
		// the forms

		foreach($form as $k => $f)
		{
			if(substr($k,0,strlen($e))==$e)
			{
				$name = explode(".",$f);
				$name[sizeof($name)-1] = 'php';
				$name = implode(".",$name);	
				svn_move('inc/etc/form/'.$f,$dir.$e.'/form/'.$name);	
			}
		}

		// the sqls ... skip for now
	}
}

function svn_mkdir($d)
{
	if(!is_dir($d))
	{
		svn_exec("svn mkdir ".$d);
	}
}

function svn_move($f,$t)
{
	if(is_dir($f))
	{
		svn_exec("svn move ".$f." ".$t);		
	}
	if(is_file($f))
	{
		svn_exec("svn move ".$f." ".$t);
	}
}

function svn_exec($m)
{
	echo $m."\n";
	exec($m);
}

?>