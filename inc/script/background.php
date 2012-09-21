#!/usr/bin/php -q
<?
if(!$argv[1])
{
	die("1");
}
$data = unserialize(base64_decode($argv[1]));
$_SERVER['HTTP_HOST'] = $data['server'];
$_POST['SESSION'] = $data['session'];
$_GET['SESSION'] = $data['session'];
include_once($data['oos']);
$OPC = new OPC();          
$MC  = &MC::singleton();
$SESS = &SESS::singleton();
$SESS->start();
$CLIENT = &CLIENT::singleton(true);
$DB = &DB::singleton();		
$CLIENT->su($data['uid']);       

$MC->call_action(array('mod'=>$data['mod'],'event'=>$data['event']),$data['params']);
die;
?>