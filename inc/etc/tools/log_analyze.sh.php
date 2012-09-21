#!/usr/bin/php -q
<?

ini_set("memory_limit","60M");
/*

<LOG time="1147336203">
	<PROJECT>austernbar</PROJECT>
	<URL>austernbar2.hundertelf.com</URL>
	<PID>348</PID>
	<MOD></MOD>
	<EVENT></EVENT>
	<FILES>YTowOnt9</FILES>
	<POST>YTowOnt9</POST>
	<GET>YToyOntzOjQ6ImZpbGUiO3M6MDoiIjtzOjM6InBpZCI7aTozNDg7fQ==</GET>
	<IP>195.14.204.188</IP>
	<SESSION>6b8ae48309fd1424068fbf355c18621a</SESSION>
	<BROWSER></BROWSER>
	<UID>200</UID>
	<NAME>Gast</NAME>
</LOG>

*/

$file = $argv[1];
if(!is_file($file))
{
	die("please specify a logfile to look at\n");
}

$stack = array("root");
$log = array();
$tmp = array();
$xpl = array();
$users = array();


function _start($parser, $name, $attrs) 
{

	if($name == "LOG" AND $attrs['TIME'])
	{
		global $tmp;
		$tmp["LOGTIME"] = $attrs['TIME'];
	}
	global $stack;
	$stack[sizeof($stack)] = $name;
	return;
}

function _end($parser, $name) 
{
	global $stack;
	if($stack[sizeof($stack)-1] != $name)
	{
		die("An error occured\n");
	}
	unset($stack[sizeof($stack)-1]);
	if($name == "LOG")
	{
		global $tmp;
		if(sizeof($tmp)!=0)
		{
			global $log;
			$log[$tmp['IP']][$tmp['SESSION']][] = $tmp;
			global $xpl;
			if(sizeof($xpl) == 0)
			{
				$xpl = $tmp;
			}
		}
		$tmp = array();
	}
	return;
}

function tag()
{
	global $stack;
	return $stack[sizeof($stack)-1];
}

function _data($parser,$data)
{
	global $tmp;
	global $users;
	switch(tag())
	{
		case 'NAME':
			$users[$data] = $data;
		break;
		case 'LOG':
		case 'XML':
			// do nothing
		break;
		default:
			$tmp[tag()] = $data;
	}
	return;
}

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "_start", "_end");
xml_set_character_data_handler ( $xml_parser, "_data" );
xml_parse($xml_parser,'<xml>'.file_get_contents($file).'</xml>');
xml_parser_free($xml_parser);
/**
now output something meaningful
*/
echo "##################################################################################################\n\n";
echo $xpl['PROJECT']."\n\n";


foreach($log as $ip => $data)
{
	$count['ip']++;

	echo $ip."\n";
	foreach($data as $session => $d)
	{
		$count['session']++;

		echo "\t".$session." (".sizeof($d).") ".$d[0]['URL']."\n";
		foreach($d as $e)
		{
			$count['click']++;
			echo "\t\t".date("r",$e['LOGTIME'])."\t".$e['NAME']."\t".$e['PID']."\t\t".$e['MOD']." ( ".$e['EVENT']." ) \n";
			// collect the referer
			if(strlen($e['REFERER']) > 0 )
			{
				$referer[$e['REFERER']]++;
			}
		}
	}
	echo "\n";
}


print_r($count);
print_r($users);
print_r($referer);
?>
