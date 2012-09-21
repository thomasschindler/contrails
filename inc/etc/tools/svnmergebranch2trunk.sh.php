#!/usr/bin/php
<?
/**
	merge from a project down to the trunk
	only merge files that exist in the trunk
	need to set a from-to policy
*/
$base = substr(dirname(__FILE__),0,-13);
chdir($base);

$branch = $argv[1];
//$branch = 'svn://xsoo.org/oos/development/yoose/';

if(substr($branch,0,6)!='svn://')
{
	die('please provide a valid branch url'."\n");
}
$rootfile = '.svnmerge.root.'.md5($branch);
// get my head
$out = null;
myexec('svn update',&$out);
$out = explode(" ",$out[0]);
$out = trim($out[sizeof($out)-1]);
$head = trim(preg_replace("/\./",'',$out));
// get the root
if(!is_file($rootfile))
{
	$out = null;
	myexec('svn log -v --stop-on-copy '.$branch,&$out);
	krsort($out);
	foreach($out as $k => $l)
	{
		if(preg_match("/-----/",$l))
		{
			$count++;
		}
		if($count==2)
		{
			$tmp = explode(" ",$out[$k+1]);
			$r = trim($tmp[0]);
			if(substr($r,0,1)!='r')
			{
				die('please run me again. if you see this message again something is wrong.'."\n");				
			}
			$root = substr($r,1); 
			$f = fopen($rootfile,'w+');
			fwrite($f,$root);
			fclose($f);
		}
	}
}
else
{
	$tmp = file($rootfile);
	$root = trim($tmp[0]);
}
$root = 438;
if($root > $head)
{
//	die('nothing to do ['.$root.':'.$head.']'."\n");
}
// get the files 
$out = null;
myexec('svn merge -r '.$root.':'.$head.' --dry-run '.$branch,&$out);

//print_r($out);

foreach($out as $l)
{
	if(substr(trim($l),0,1)=="A")
	{
		$l = trim(substr($l,1));
		echo "SKIPPING: ".'svn merge -r '.$root.':'.$head.' '.$branch.'/'.$l.' '.$l."\n";
		continue;
	}

	$l = trim(substr($l,1));
	myexec('svn merge -r '.$root.':'.$head.' '.$branch.'/'.$l.' '.$l);
	
}
// write the new root
$f = fopen($rootfile,'w+');
fwrite($f,($head+1));
fclose($f);
// commit to trunk
//myexec('svn commit -m "merged from branch '.$branch.'"');
if($argv[2] == 'commit')
{
	myexec('svn commit -m "merged from branch '.$branch.'"');
}

function myexec($m,$out=null)
{
	echo $m."\n";
	exec($m,$out);
}

?>