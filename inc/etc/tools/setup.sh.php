#!/usr/bin/php -q
<?
/**

OOS INSTALLATION SCRIPT
VERY BASIC!!!!


**/

// move to the root folder
chdir(substr(dirname(__FILE__),0,-13));


if(sizeof($argv) == 2)
{
	if($argv[1] == "project")
	{
		$__project = true;
	}
}

echo "
###############################################################################
#
#	oOs - Community Management - © 2004-2006 hundertelf GmbH
#	oOS is available under the CC-GPL 
#	Further information at: www.organismos.cc or www.hundertelf.com
#
###############################################################################
#	
#	This Script will do the following things for you:
#		1. Adapt the main Configuration-File
#		2. Create a database and intialise it
#
#	This Script will NOT do the following things:
#		1. Check the availability of the following:
#			imagemagick - the convert command
#			apache 
# 			mysql
#		2. Check the validity of the information passed - you are fully responible!
#		3. Your apache needs mod_rewrite acitvated!
#		4. Check the permissions - Make sure the user of your apache is allowed to write in all folders!
#
###############################################################################

Do you want to continue? (yes/no) ";
$answer = oos_stdin();
if($answer != "yes")
{
	echo "Goodbye\n";
	die;
}

$vars = recover_data();

echo "
You will be asked for some information.
Before we apply this information in the system you will be asked to check it.

Suggestions in square brackets will be used if you don't specifiy your information.

";

echo "Name of the project ( no blanks - only ASCII ) [".$vars['project']."] ";
$vars['project'] = ($i = oos_stdin()) ? $i : $vars['project'];

echo "Database-Server [".$vars['db_server']."] ";
$vars['db_server'] = ($i = oos_stdin()) ? $i : $vars['db_server'];

echo "Database-User [".$vars['db_user']."] ";
$vars['db_user'] = ($i = oos_stdin()) ? $i :  $vars['db_user'];

echo "Database-Pass [****] ";
$vars['db_pass'] = ($i = oos_stdin()) ? $i :  $vars['db_pass'];

echo "Database-Name [".$vars['db_name']."] ";
$vars['db_name'] = ($i = oos_stdin()) ? $i : $vars['db_name'];

echo "URL [".$vars['url']."] http:// is important! ";
$vars['url'] = ($i = oos_stdin()) ? $i :  $vars['url'];

if(!$__project)
{

	echo "Path to inc [".$vars['inc']."] no trailing slash!";
	$vars['inc'] = ($i = oos_stdin()) ? $i :  $vars['inc'];

	echo "Path to web (Webroot) [".$vars['web']."] no trailing slash!";
	$vars['web'] = ($i = oos_stdin()) ? $i :  $vars['web'];

}
echo "Password for root [****] ";
$vars['pass'] = ($i = oos_stdin()) ? $i : $vars['pass'];

echo "
Thank you! - Please check the information:

###############################################################################
";

echo "Name of the project	".$vars['project']."\n";
echo "Database-Server		".$vars['db_server']."\n";
echo "Database-User		".$vars['db_user']."\n";
echo "Database-Pass		".$vars['db_pass']."\n";
echo "Database-Name		".$vars['db_name']."\n";
echo "URL			".$vars['url']."\n";
if(!$__project)
{
	echo "Path to inc		".$vars['inc']."\n";
	echo "Path to web		".$vars['web']."\n";
}
echo "Password for root	".$vars['pass']."\n";

echo "###############################################################################

Do you want to continue? (yes/no) ";
$answer = oos_stdin();
if($answer != "yes")
{
	echo "Goodbye\n";
	save_data();
	die;
}
/**
1. create the database
2. fill the database
3. set the root password
4. cp inc/etc/system/oos.cnf.default and modify
5. cp inc/etc/config/default.cnf and modify
*/
$link = @mysql_connect($vars['db_server'],$vars['db_user'],$vars['db_pass']);
if (!$link) 
{
	save_data();
	die('Could not connect: ' . mysql_error()."\n");
}
// ok we are connected
if(mysql_select_db($vars['db_name'],$link))
{
	echo "A database named ".$vars['db_name']." exists already. Do you want to use it? - This will delete all information in that database! (yes/no)";
	$answer = oos_stdin();
	if($answer == "yes")
	{
		// delete the database
		echo ".";
		$sql = 'DROP DATABASE '.$vars['db_name'].'';
		if(!mysql_query($sql, $link))
		{
			echo 'Error' . mysql_error() . "\n";
			echo "Please restart and specify a different database name.\n";
			$vars['db_name'] = "";
			save_data();
			die;
		}
	}
	else
	{
		echo "Please restart and specify a different database name.\n";
		$vars['db_name'] = "";
		save_data();
		die;
	}
}
echo ".";
$sql = 'CREATE DATABASE '.$vars['db_name'];
if (@mysql_query($sql, $link)) 
{
	save_data();
} 
else 
{
	save_data();
	echo 'Error creating database: ' . mysql_error() . "\n";
	die;
}
// ok we got the db
if(!is_file($vars['inc']."/etc/system/init.sql"))
{
print_r($vars);
	save_data();
	echo "Couldn't find database init file - Please check your oOS distribution.\n";
	die;
}
echo ".";

$cmd = 'mysql --user='.$vars['db_user'].' --password='.$vars['db_pass'].' --execute="USE '.$vars['db_name'].'; SOURCE '.$vars['inc'].'/etc/system/init.sql;"';
exec($cmd);

#sleep(5);
$db_ready = false;
mysql_select_db($vars['db_name'],$link);
do
{
	$select = "SELECT id FROM mod_usradmin_usr WHERE id = 122";
	echo ".";
	$result = mysql_query($select,$link);
	if($result)
	{
		$row = mysql_fetch_row($result);
		if($row[0] == 122)
		{
			$db_ready = true;
		}
	}
} 
while(!$db_ready);

// db is filled
echo ".";
$update = "UPDATE mod_usradmin_usr SET pwd = '".md5($vars['pass'])."' WHERE id = 122";
mysql_query($update,$link);
// add the module administration to the first project
if(!$__project)
{
	$insert = "INSERT INTO `sys_module` VALUES (3, 'multiproject_mod_admin', 'Module Administration', 0, 1)";
	mysql_query($insert,$link);
}
// root password is set
if(!$__project)
{
	echo ".";
	$cnf = file_get_contents($vars['inc']."/etc/system/oos.cnf.tpl");
	$cnf = ereg_replace("__WEB_DIR__",$vars['web'],$cnf);
	$cnf = ereg_replace("__INC_DIR__",$vars['inc']."/",$cnf);
	$f = fopen($vars['inc']."/etc/system/oos.cnf","w+");
	fwrite($f,$cnf);
	fclose($f);
}
// main conf is done
echo ".";
$cnf = file_get_contents($vars['inc']."/etc/config/_default.cnf");
$cnf = ereg_replace("__PROJECT_NAME__",$vars['project'],$cnf);
$cnf = ereg_replace("__URL__",$vars['url'],$cnf);
$cnf = ereg_replace("__DB_HOST__",$vars['db_server'],$cnf);
$cnf = ereg_replace("__DB_USER__",$vars['db_user'],$cnf);
$cnf = ereg_replace("__DB_PASS__",$vars['db_pass'],$cnf);
$cnf = ereg_replace("__DB_NAME__",$vars['db_name'],$cnf);
$f = fopen($vars['inc']."/etc/config/".substr($vars['url'],7).".cnf","w+");
fwrite($f,$cnf);
fclose($f);
// project conf is done
echo ".";
if($vars['project'] != 'default')
{
	if(is_dir($vars['web'].'/assets/'.$vars['project']))
	{
		$cmd = "rm -R ".$vars['web'].'/assets/'.$vars['project'];
		exec($cmd);
	}
	$cmd = 'cp -R '.$vars['web'].'/assets/default '.$vars['web'].'/assets/'.$vars['project'];
	exec($cmd);
	$cmd = 'chmod -R  775 '.$vars['web'].'/assets/'.$vars['project'];
	exec($cmd);
}
// project assets folder copied
echo ".";
#exec("rm .vars");
// i think we're finished
// save some data before we go
if(!is_file($vars['inc']."/etc/system/.oos.data"))
{
	$data = array
	(
		'DB' => md5(file_get_contents($vars['inc']."/etc/system/init.sql")),
		'DATE' => date("r")
	);
	$f = fopen(".oos.data","w+");
	fwrite($f,base64_encode(serialize($data)));
	fclose($f);
}

echo "
###############################################################################
#
#	Congratulations! 
#	If your apache configuration is correct and the apache is up and running, 
#	pointing your browser to ".$vars['url']." should show your an oOS.
#
#	Have fun with it!
#
###############################################################################
";
//////////////////////////functions////////////////////////////////////////////
function save_data()
{
	global $vars;
	$f = fopen(getcwd()."/.".get_current_user().".vars","w+");
	fwrite($f,base64_encode(serialize($vars)));
	fclose($f);
}

function recover_data()
{
	if(is_file(getcwd()."/.".get_current_user().".vars"))
	{
		$l = file(getcwd()."/.".get_current_user().".vars");
		return unserialize(base64_decode($l[0]));
	}
	return array
	(
		'project' => get_current_user()."_oos",
		'db_server' => 'localhost',
		'db_user' => 'root',
		'db_pass' => '',
		'db_name' => get_current_user()."_oos",
		'url' => 'http://www.oos.local',
		'inc' => getcwd()."/inc",
		'web' => getcwd()."/web",
		'pass' => 'oOS_ROCKS'
 	);
}

function oos_stdin($length = 255)
{
	return trim(fgets(STDIN));
}

function my_system($command) {
  if (!($p=popen("($command)2>&1","r"))) {
   return 126;
  }

  while (!feof($p)) {
   $line=fgets($p,1000);
   $out .= $line;
  }
  pclose($p);
  return $out;
}

?>
