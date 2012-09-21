#!/usr/bin/php
<?
/**
	merge from a project down to the trunk
	only merge files that exist in the trunk
	need to set a from-to policy
*/
$base = substr(dirname(__FILE__),0,-13);
chdir($base);

$trunk = $argv[1];
//$branch = 'svn://xsoo.org/oos/development/yoose/';

if(substr($trunk,0,6)!='svn://')
{
	die('please provide a valid trunk url'."\n");
}
// get the branch (myself)
$out =null;
myexec('svn info',&$out);
$tmp = explode(" ",$out[1]);
$branch = trim($tmp[1]);
foreach($out as $l)
{
	if(preg_match('/Last Changed Rev/',$l))
	{
		$tmp = explode(" ",$l);
		$root = trim($tmp[sizeof($tmp)-1]);
		break;
	}
}


// get the trunks head
$out = null;
myexec('svn info '.$trunk,&$out);
foreach($out as $l)
{
	if(preg_match('/Revision/',$l))
	{
		$head = explode(" ",$l);
		$head = trim($head[1]);
		break;
	}
}


if($root >= $head)
{
	die('nothing to do'."\n");
}
// get the files 
$out = null;
myexec('svn merge -r '.$root.':'.$head.'  '.$trunk,&$out);

// commit to branch
if($argv[2] == 'commit')
{
	myexec('svn commit -m "merged from trunk '.$trunk.'"');
}

function myexec($m,$out=null)
{
	echo $m."\n";
	exec($m,$out);
}

?>