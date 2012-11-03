#!/usr/bin/php -q
<?

/**
*	this script handles the setup of everything for a new installation of contrails
*	simply launch it, answer the questions, and you're good to go
*/
$start = time();
$data = array
(
	'project' => array
	(
		'q' => 'Does this project have a name?',
		'd' => 'tajapa',
	),
	'baseurl' => array
	(
		'q' => 'What is the url you want to be reachable under?',
		'd' => 'tajapa.local',
	),
	'db_host' => array
	(
		'q' => 'Please specifiy the db host',
		'd' => 'localhost',
	),
	'db_name' => array
	(
		'q' => 'Please specifiy the db name',
		'd' => 'tajapa',
	),
	'db_user' => array
	(
		'q' => 'Please specifiy the db user',
		'd' => 'root',
	),
	'db_pass' => array
	(
		'q' => 'Please specifiy the db password',
		'd' => 'root',
	),
);

echo "Welcome to the contrails setup\nI will now ask ".count($data)." questions and setup your system.\nIf you leave anything blank the default will be used\n\n";

// collect the data
foreach($data as $k => $v)
{
	$tmp = get_data($v['q']." [".$v['d']."]");
	if(strlen($tmp) > 0)
	{
		$data[$k]['d'] = $tmp;
	}
}
// set up the db

$db_options = array
(
	'master' => array
	(
		'db_type' => 'mysql',
		'db_host' => $data['db_host']['d'],
		'db_user' => $data['db_user']['d'],
		'db_pass' => $data['db_pass']['d'],
		'db_name' => $data['db_name']['d'],
	)
);

$tmp = pathinfo(__FILE__);
$base = substr($tmp['dirname'],0,-6);
$files = array
(
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
// does the db exist?
// if it does not exist, create it
if(!($link = @mysql_connect($data['db_host']['d'],$data['db_user']['d'],$data['db_pass']['d'])))
{
	die("I could not connect to the db.\n");
}
if(!@mysql_select_db($data['db_name']['d'],$link))
{
	// create the db
	if(!mysql_query("CREATE DATABASE ".$data['db_name']['d'],$link))
	{
		die("Unfortunately i could not create the db\n");
	}
}
$db = DB::get_interface($db_options);
// if so, is it empty?
$r = $db->query("SHOW TABLES FROM ".$data['db_name']['d']);
if($r->nr() !== 0)
{
	if(get_data("This db is not empty. Can we clean it? [no|yes]") === 'yes')
	{
		$db->query("DROP DATABASE ".$data['db_name']['d']);
		mysql_query("CREATE DATABASE ".$data['db_name']['d'],$link);
	}
	else
	{
		die("Aborting the installation\n");
	}
}
// DB DONE
// set up the configuration
$data['baseurl']['d'] = preg_replace("/http:/","",$data['baseurl']['d']);
$data['baseurl']['d'] = preg_replace("/\//","",$data['baseurl']['d']);

$d = file_get_contents($base."inc/etc/config/template.default.cnf.php");
$d = preg_replace("/__URL__/","http://".$data['baseurl']['d'],$d);
$d = preg_replace("/__PROJECT_NAME__/",$data['project']['d'],$d);
$d = preg_replace("/__DB_HOST__/",$data['db_host']['d'],$d);
$d = preg_replace("/__DB_USER__/",$data['db_user']['d'],$d);
$d = preg_replace("/__DB_PASS__/",$data['db_pass']['d'],$d);
$d = preg_replace("/__DB_NAME__/",$data['db_name']['d'],$d);
file_put_contents($base."inc/etc/config/".$data['baseurl']['d'].".cnf.php", $d);

// now call migrate sync
// migrate models
// done
echo "SETTING UP THE DB\n";
passthru("script/migrate.sh.php sync ".$data['baseurl']['d']);
echo "GENERATING THE MODELS\n";
passthru("script/migrate.sh.php models ".$data['baseurl']['d']);

echo "\nOK, now please set your webroot to:\n\n".$base."web\n\n";

echo "DONE [".(time()-$start)."]\n";

function get_data($q)
{
	return trim(shell_exec("read -p '".$q.": ' name\necho \$name"));
}




?>