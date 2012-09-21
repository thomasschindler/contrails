#!/usr/bin/php -q
<?
/*
m		module
e		event
a		arguments
s		session
c		config
u		user id
h		help
*/
// get the options
$a = getopt('m:e:a::s::c::u::h::t::');

if(isset($a['h']) OR !isset($a['m']) OR !isset($a['e']))
{
	echo 
"
call with the following arguments

m		module
e		event

optional:
a		arguments (name1:value1/name1:value2/name2:value3)
s		session
c		config
u		uid
t		parallel exectution (how many threads) default:1
h		help

";
	die;
}
// start in the right directory
chdir(substr(dirname(__FILE__),0,-7));
// get and init the system
$config = substr(dirname(__FILE__),0,-7).'/etc/config/'.(isset($a['c'])?$a['c']:'default').'.cnf';
if(!is_file($config))
{
	die("no config found\n");
}
// now check for threads
// only allow one if not explicitly increased
$threads = shell_exec('ps -ax');
$threads = explode("\n",$threads);
foreach($threads as $t)
{
	if(preg_match("/".$a['m']."/",$t) AND preg_match("/".$a['e']."/",$t))
	{
        if(preg_match("/\/usr\/bin\/php/",$t))
        {
			++$threadcnt;
		}
	}
}
$concurrent = 1;
if($a['t'])
{
 	$concurrent = (int)$a['t'];
}
if($threadcnt > $concurrent)
{
	die;
}

include_once($config);
$_SERVER['HTTP_HOST'] = stripslashes(substr(HOST_CONF::baseurl(),7));
$_POST['SESSION'] = isset($a['s'])?$a['s']:php_uname('n')."_".@date("Ymd");
$GET['SESSION'] = isset($a['s'])?$a['s']:php_uname('n')."_".@date("Ymd");
include_once(substr(dirname(__FILE__),0,-7).'/oos.sys');
$OPC = new OPC();          
$MC  = &MC::singleton();
$SESS = &SESS::singleton();
$SESS->start();
$CLIENT = &CLIENT::singleton(true);
$DB = &DB::singleton();	
if($a['u'])	
{
	$CLIENT->su($a['u']); 
}
// construct the arguments
$arguments = array();

if($a['a'])
{
    if(substr($a['a'],0,1)=="=")
    {
            $a['a'] = substr($a['a'],1);
    }
	$arg = explode("/",$a['a']);
	foreach($arg as $pair)
	{
		$tmp = explode(":",$pair);
		if($arguments[$tmp[0]])
		{
			if(!is_array($arguments[$tmp[0]]))
			{
				$arguments[$tmp[0]] = array($arguments[$tmp[0]]);
			}
			$arguments[$tmp[0]][] = $tmp[1];
		}
		else
		{
			$arguments[$tmp[0]] = $tmp[1];	
		}
	}
}
// call it
$MC->call($a['m'],$a['e'],$arguments);
// go home
die;
?>