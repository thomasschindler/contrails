<?
/**
*		
*		USAGE:
*		
*		1. Setting up the server
*			Set up a domain/subdomain with the webroot api instead of web (in all examples we will use api.oos.hundertelf.com )
*			Make sure the config file for this domain/subdomain uses the same parameters as the config file of the installation you want to interact with
*			
*		2. Setting up the client
*			The client can now call modules and events in this fashion:
*			
*			http://api.domain.com/{USERNAME}/{MD5(PASSWORD)}/{MODULE}/{EVENT}.{RETURNTYPE}[?{optional=parameters}]
*			OR:
*			http://api.domain.com/{USERNAME}/{MD5(PASSWORD)}/{MODULE}/{EVENT}/{ARGUMENT}/{ARGUMENT}/...
*
* 			- the default is returntype is xml
* 			- the additional parameters will be passed in an indexed array in the array arguments
* 
* 			- caching:
* 				caching can be switched on/off in the url:
* 				/cache:{seconds}/user/md5(pwd)/module/event/attribute1/attribute2
* 				this will cache the output for {seconds} as a file and only return the contents of the file
* 				it will also send the appropriate expiration header
*
*			The user USERNAME has to exist.
*			PASSWORD is always transferred md5 encoded.
*			The user has to have the access-right to the accesspoint EVENT on the default pid (CONF::pid() / Startpage) OR the optional pid (parameters)
*			RETURNTYPE may be
*				xml 					will return arrays into xml
*				json					will return arrays into json
*				php (serialized php)	
*				passthru 				will return whatever the module returns directly
*				plist 					will return a plist in the osx format
*			
*			a testcall can be made like this
*
*				$username = "root";
*				$password = "root";
*				$api_url = "http://api.oos.hundertelf.com/".$username."/".md5($password)."/api_test/echo.json?id=234";
*				$curl_handle = curl_init();
*				curl_setopt($curl_handle, CURLOPT_URL, $api_url);
*				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
*				$data['data'] = curl_exec($curl_handle);
*				$data['status'] = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
*				curl_close($curl_handle);
*				echo "<pre>";
*				print_r($data);
*				echo "</pre>";
*			
*			This will return your parameters as an echo
*			
*		
*		STATUS CODES:
*		
*         | "100"  ; Section 10.1.1: Continue
*        | "101"  ; Section 10.1.2: Switching Protocols
*       
*          | "200"  ; Section 10.2.1: OK
*          | "201"  ; Section 10.2.2: Created
*          | "202"  ; Section 10.2.3: Accepted
*          | "203"  ; Section 10.2.4: Non-Authoritative Information
*          | "204"  ; Section 10.2.5: No Content
*          | "205"  ; Section 10.2.6: Reset Content
*          | "206"  ; Section 10.2.7: Partial Content
*          
*          | "300"  ; Section 10.3.1: Multiple Choices
*          | "301"  ; Section 10.3.2: Moved Permanently
*          | "302"  ; Section 10.3.3: Found
*          | "303"  ; Section 10.3.4: See Other
*          | "304"  ; Section 10.3.5: Not Modified
*          | "305"  ; Section 10.3.6: Use Proxy
*          | "307"  ; Section 10.3.8: Temporary Redirect
*          
*          | "400"  ; Section 10.4.1: Bad Request
*          | "401"  ; Section 10.4.2: Unauthorized
*          | "402"  ; Section 10.4.3: Payment Required
*          | "403"  ; Section 10.4.4: Forbidden
*          | "404"  ; Section 10.4.5: Not Found
*          | "405"  ; Section 10.4.6: Method Not Allowed
*          | "406"  ; Section 10.4.7: Not Acceptable
*          | "407"  ; Section 10.4.8: Proxy Authentication Required
*          | "408"  ; Section 10.4.9: Request Time-out
*          | "409"  ; Section 10.4.10: Conflict
*          | "410"  ; Section 10.4.11: Gone
*          | "411"  ; Section 10.4.12: Length Required
*          | "412"  ; Section 10.4.13: Precondition Failed
*          | "413"  ; Section 10.4.14: Request Entity Too Large
*          | "414"  ; Section 10.4.15: Request-URI Too Large
*          | "415"  ; Section 10.4.16: Unsupported Media Type
*          | "416"  ; Section 10.4.17: Requested range not satisfiable
*          | "417"  ; Section 10.4.18: Expectation Failed
*          
*          | "500"  ; Section 10.5.1: Internal Server Error
*          | "501"  ; Section 10.5.2: Not Implemented
*          | "502"  ; Section 10.5.3: Bad Gateway
*          | "503"  ; Section 10.5.4: Service Unavailable
*          | "504"  ; Section 10.5.5: Gateway Time-out
*          | "505"  ; Section 10.5.6: HTTP Version not supported
*          
*
* 
*/
ob_start();
// parse the data
$tmp = explode("/",$_REQUEST['_rewrite_file']);
// add the arguments from the path:
$path = $tmp;
if(substr($path[0],0,5)=='cache')
{
	unset($path[0]);
	unset($path[1]);
	unset($path[2]);
	unset($path[3]);
	unset($path[4]);	
}
else
{
	unset($path[0]);
	unset($path[1]);
	unset($path[2]);
	unset($path[3]);	
}
foreach($path as $v)
{
	$_REQUEST['arguments'][] = $v;
}
// analyze the request
if(sizeof($tmp) < 4)
{
	response_send(400);
}
if(@substr($tmp[0],0,5)=='cache')
{
	$ctmp = explode(":",$tmp[0]);
	$cache['file'] = md5(serialize($_REQUEST['_rewrite_file']));
	$cache['max_age'] = $ctmp[1]+rand(0,60); // add a random number to the cache to distribute simultanious calls 
	$cache['expires'] = time()+$cache['max_age'];
	$cache['time'] = $ctmp[1];
	
	$data['usr'] = $tmp[1];
	$data['pwd'] = strtolower($tmp[2]);
	$data['mod'] = $tmp[3];
	$etmp = explode(".",$tmp[4]);
}
else
{
	$data['usr'] = $tmp[0];
	$data['pwd'] = strtolower($tmp[1]);
	$data['mod'] = $tmp[2];
	$etmp = explode(".",$tmp[3]);
}
$data['event'] = $etmp[0];
$data['type'] = isset($etmp[1])?$etmp[1]:'xml';

$tmp = null;
$ctmp = null;
$etmp = null;
// handle the cache
$cache_base = "/tmp/oosapicache_".md5($_SERVER['HOST'])."/";
if(@$cache)
{
	if((@filemtime($cache_base.$cache['file'])+$cache['time']) > time())
	{
		$data = file_get_contents($cache_base.$cache['file']);
		$etag = md5($data);
		$rqheaders = apache_request_headers();
		if($rqheaders['ETag'] == $etag)
		{	
       		 	header('HTTP/1.1 304');
        		die;
		}
		ob_clean();
		header('HTTP/1.1 200');
//		header('Content-type: text/html; charset=utf-8');
		header('Cache-Control: max-age='. $cache['max_age']);
		header('Expires: '.date("D, d M Y H:i:s", $cache['expires']));
		header('ETag: '.$etag);
		header('Pragma: cache');
		echo $data;
		die;
	}
	// if not, we continue and build the data anew
}
// validate the data
// from here on we need oos
// prepare for oos
// pid:
include_once('../inc/oos.sys');
if(!@$_REQUEST['pid'])
{
	$data['pid'] = CONF::pid();
}
else
{
	$data['pid'] = (int)$_REQUEST['pid'];
}
$OPC = &OPC::singleton();
$OPC->set_pid($data['pid']);
$MC  = &MC::singleton();
$MF  = &MF::singleton();
$SESS = &SESS::singleton();
$SESS->start();
$CLIENT = &CLIENT::singleton(true);
// validate the credentials
// authenticate the user
$auth = $MC->call('usradmin','usr_validate',array('usr' => $data['usr'],'pwd' => $data['pwd'],'__encoded' => true));
if(is_error($auth))
{
	response_send(401);
}
$CLIENT->set_auth($auth);
// does a module like this exist?
$mods = $MC->get_mods();
$data['error'] = true;
foreach($mods as $mod)
{
	if($mod['modul_name'] == $data['mod'])
	{
		$data['error'] = false;
		break;
	}
}
if($data['error'])
{
	response_send(400);	
}
// do we have the right to access this event?
if(!$MC->access($data['mod'],$data['event']))
{
	response_send(405);
}
// allright, then we pack the other request and hit the module with the request

//unset($_REQUEST['_rewrite_file']);
foreach($_REQUEST as $k => $v)
{
	$p[strtolower($k)] = $v;
}
// add the additional parameters as arguments

unset($_REQUEST);

$response = $MC->call($data['mod'],$data['event'],$p);

$type = isset($response['type'])?$response['type']:$data['type'];

$MF->flush();

response_send($response['status'],$response['data'],$type,$cache);
// functions
function response_pack($response,$type='xml')
{
	switch($type)
	{
		case 'passthru':
			return $response;
		break;
		case 'json':
			return Zend_Json_Encoder::encode($response);
		break;
		case 'php':
			return serialize($response);
		break;
		case 'xml':
			if(!is_array($response))
			{
				return;
			}
			return UTIL::array2xml($response);
		break;
		case 'plist':
			return UTIL::array2plist($response);
		break;
	}
}
function response_send($header=404,$response=null,$type=null,$cache=null)
{
	global $cache_base;
	if($response)
	{
		$etag = "E".md5($response);
		$rqheaders = apache_request_headers();
		if($rqheaders['ETag'] == $etag)
		{
			ob_clean();
			header('HTTP/1.1 304');
			die;
		}
	}
	ob_clean();
	header('HTTP/1.1 '.$header);
	if($header == 200 AND $cache)
	{
		$max_age = $cache[1]+rand(0,60);
		$expires = time()+$max_age;
//		header('Content-type: text/html; charset=utf-8');
		header('Cache-Control: max-age='. $cache['max_age']);
		header('Expires: '.date("D, d M Y H:i:s", $cache['expires']));
		header('ETag: '.$etag);
		header('Pragma: cache');		
	}
	if($response)
	{
		$data = response_pack($response,$type);	
		if($cache)
		{
			if(!is_dir($cache_base))
			{
				mkdir($cache_base);
			}
			$fp = fopen($cache_base.$cache['file'],'w+');
			fwrite($fp,$data);
			fclose($fp);
		}
		echo $data;
	}
	die();
}
?>