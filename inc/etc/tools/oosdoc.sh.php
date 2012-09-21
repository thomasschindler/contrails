#!/usr/bin/php -q
<?
chdir(substr(dirname(__FILE__),0,-13));
if(is_dir('web/doc/'))
{
	$d = dir('web/doc/');
	while(false == ($e = $d->read()))
	{
		if(substr($e,-5)==".html")
		{
			delete('web/doc/'.$e);
		}
	}
}
else
{
	exec("mkdir web/doc");
}
if(!is_file('web/doc/.htaccess'))
{
	$d = 'RewriteEngine off';
	$f = fopen('web/doc/.htaccess','w+');
	fwrite($f,$d);
	fclose($f);
}
/**
	This script parses an oOS installation for documentation
	implemented
	- parse base-classes in inc/system
	- interpret javadoc comments
	- parse module classes
	not implemented
	- parse access, lang, instance, etc
*/

$bucket = array();

$dir = 'inc/mods/';
$d = dir($dir);
while(false !== ($e = $d->read()))
{
	if(substr($e,0,1)==".")
	{
		continue;
	}
	mod_parse_view($e,&$bucket);
}

function mod_parse_view($name,$bucket)
{
	// we can use class_parse for the basic operation
	// the main function needs to be handled seperately

	$tmp = class_parse('inc/mods/'.$name.'/class_'.$name.'View.inc.php');
	$tmp['data']['events'] = class_parse_for_events('inc/mods/'.$name.'/class_'.$name.'View.inc.php');
				
	$bucket['mods'][$name]['view'] = $tmp['data'];

	$tmp = class_parse('inc/mods/'.$name.'/class_'.$name.'Action.inc.php');	
	$tmp['data']['events'] = class_parse_for_events('inc/mods/'.$name.'/class_'.$name.'Action.inc.php');
	$bucket['mods'][$name]['action'] = $tmp['data'];
}


// parse the system classes
// for now we ignore everything that doesn't start with class_
$dir = 'inc/system/';
$d = dir($dir);
while(false !== ($e = $d->read()))
{
	if(substr($e,0,6) == 'class_')
	{
		$tmp = class_parse($dir.$e,$bucket);
		$bucket['system'][$tmp['name']] = $tmp['data'];
	}
}
function class_parse_for_events($f)
{
	$a = file($f);
	$ret = array();
	foreach($a as $l)
	{
		$l = trim($l);
		if(substr($l,0,8)=='function' AND preg_match('/main/',$l))
		{
			$start = true;
			continue;
		}
		if($start)
		{
			$collect .= $l;
		}
		if($start AND substr($l,0,8)=='function')
		{
			$atom = '_7987_';
			$collect = preg_replace('/"/',$atom,$collect);
			$collect = preg_replace("/'/",$atom,$collect);	
			$tmp = explode("case",$collect);
			unset($tmp[0]);
			foreach($tmp as $i)
			{
				$t = explode($atom,$i);
				$event = trim($t[1]);
				$t = explode("\$this->",$t[2]);
				$t = explode("(",$t[1]);			
				$method = trim($t[0]);
				if(strlen($event)==0 OR strlen($method)==0)
				{
					continue;
				}
				$ret[$method] = array
				(
					'name' => $method,
					'event' => $event,
				);
				if(preg_match('/return/',$i))
				{
					$ret[$method]['api'] = true;
				}
				if(preg_match("/default:/",$i))
				{
					$ret[$method]['default'] = true;
				}
			}

			return $ret;

		}
	}
	return $ret;
}
//handler funtion for parsing system classes
function class_parse($f)
{
	$bucket = array();
	// get the class name
	// see if it extends another class
	// get all methods
	// get their parameters
	$a = file($f);
	$class = null;
	$javadoc = null;
	$abstract = false;
	foreach($a as $l)
	{
		// start grabbing javadoc
		if(preg_match("/\/\*\*/",$l))
		{
			$javadoc = null;
			$jd_started = true;
		}
		if($jd_started)
		{
			$javadoc .= $l;
		}
		if($jd_started AND preg_match("/\*\//",$l))
		{
			$javadoc = preg_replace("/\*/","",$javadoc);
			$javadoc = preg_replace("/\//","",$javadoc);
			$jd_started = false;
		}
		if($jd_started)
		{
			continue;
		}
		// start collecting other data
		$_m = null;
		$l = trim($l);
		if(strlen($l)==0)
		{
			continue;
		}
		// catch abstract class
		if(substr($l,0,8)=='abstract' AND !$class)
		{
			$abstract = true;
			$l = preg_replace("/abstract /","",$l);
		}
		// catch method types
		if(substr($l,0,8)=='abstract' AND $class)
		{
			$_m['abstract'] = true;
			$l = preg_replace("/abstract /","",$l);
		}
		if(substr($l,0,7)=='private' AND $class)
		{
			$_m['private'] = true;
			$l = preg_replace("/private /","",$l);
		}
		if(substr($l,0,6)=='public' AND $class)
		{
			$_m['public'] = true;
			$l = preg_replace("/public /","",$l);
		}
		if(substr($l,0,9)=='protected' AND $class)
		{
			$_m['protected'] = true;
			$l = preg_replace("/protected /","",$l);
		}
		
		if(substr($l,0,5)=='class' AND !$class)
		{
			$tmp = explode('class',$l);
			$tmp = explode(' ',trim($tmp[1]));
			$class = trim($tmp[0]);
			$class = preg_replace("/{/","",$class);
			$class = trim($class);
			$bucket_data = array();
			$bucket_data['file'] = $f;
			$bucket_data['name'] = $class;
			if($javadoc)
			{
				$bucket_data['javadoc'] = $javadoc;				
				$javadoc = null;
			}
			if($abstract)
			{
				$bucket_data['abstract'] = true;
			}
			if(preg_match('/extends/',$l))
			{
				$tmp = explode('extends',$l);
				$tmp = explode(' ',trim($tmp[1]));
				$tmp[0] = preg_replace("/{/","",$tmp[0]);
				$bucket_data['extends'] = trim($tmp[0]);
			}
		}
		elseif(substr($l,0,8)=='function')
		{
			// get the method
			$tmp = explode("function",$l);
			$tmp = explode("(",trim($tmp[1]));
			$function = trim($tmp[0]);
			if($_m)
			{
				$bucket_data['method'][$function]['info'] = $_m;
			}
			$bucket_data['method'][$function]['function'] = preg_replace("/function /","",$l);
			if($javadoc)
			{
				$bucket_data['method'][$function]['javadoc'] = $javadoc;				
				$javadoc = null;
			}
			// get the arguments
			$tmp = explode("(",$l);
			$tmp = explode(")",trim($tmp[1]));
			$tmp = explode(",",trim($tmp[0]));
			if(is_array($tmp))
			{
				foreach($tmp as $argument)
				{
					$tmp_argument = explode("=",$argument);
					$argument = substr(trim($tmp_argument[0]),1);
					$value = trim($tmp_argument[1]);
					$bucket_data['method'][$function]['arguments'][$argument] = $value;
				}
			}
		}
		elseif(substr($l,0,6)=='return')
		{
			$bucket_data['method'][$function]['return'][] = preg_replace("/;/","",trim(preg_replace("/return /","",$l)));
		}
		
	}
	
	return array
	(
		'name' => $class,
		'data' => $bucket_data
	);

}

// prepare the navigation
foreach($bucket as $type => $data)
{
	$nav['main'][$type] = strtolower($type).".html";
	foreach($data as $k => $v)
	{
		if(strlen($k)==0)
		{
			continue;
		}
		$nav['sub'][$type][$k] = strtolower($k).'.html';
	}
	asort($nav['sub'][$type]);
}
// create the files - main nav first
foreach($nav['main'] as $type => $file)
{
	$html = html_head($type);
	$html .= html_nav($type,'main',$nav['main']);
	$html .= html_nav(null,'sub',$nav['sub'][$type],($type=="system"?'system_':'mods_action_'));
	$html .= html_foot();
	html_write($file,$html);
	// create the files for the subtypes
	foreach($nav['sub'][$type] as $k => $v)
	{
		switch($type)
		{
			case 'system':
				$html = html_head($k);
				$html .= html_nav($type,'main',$nav['main']);
				$html .= html_nav($k,'sub',$nav['sub'][$type],'system_');
				$html .= html_content(html_parsed_class($bucket[$type][$k]));
				$html .= html_foot();
				html_write("system_".$v,$html);	
			break;
			case 'mods':
				
				$tmpnav = array
				(
					'Action' => 'mods_action_'.$k.'.html',
					'View' => 'mods_view_'.$k.'.html'
				);
				
				$html = html_head($k." - VIEW");
				$html .= html_nav($type,'main',$nav['main']);
				$html .= html_nav($k,'sub',$nav['sub'][$type],'mods_action_');
				$html .= html_content(html_nav('View','meta',$tmpnav).html_parsed_class($bucket[$type][$k]['view']));				
				$html .= html_foot();
				html_write("mods_view_".$v,$html);	
				
				$html = html_head($k." - ACTION");
				$html .= html_nav($type,'main',$nav['main']);
				$html .= html_nav($k,'sub',$nav['sub'][$type],'mods_action_');
				$html .= html_content(html_nav('Action','meta',$tmpnav).html_parsed_class($bucket[$type][$k]['action']));								
				$html .= html_foot();
				html_write("mods_action_".$v,$html);	
			break;
		}
		$html .= html_foot();
		html_write($v,$html);	
	}
}

// create an index
exec('cp web/doc/system.html web/doc/index.html');

function html_parsed_class($data)
{
	
	// add some output in wiki formatting
	$out[] = 'h1. '.$data['name']."\n";
	$out[] = $data['file'];
	
	if(count($data['events'])>0)
	{
		$out[] = "\nh2. Events\n";
		foreach($data['events'] as $event => $info)
		{
			$out[] = "\nh3. ".$event."\n";
			$out[] = "name ".$info['name'];
			$out[] = "event ".$info['event'];
		}	
	}
	
	if(count($data['method'])>0)
	{
		$out[] = "\nh2. Methods"."\n";
		foreach($data['method'] as $method => $info)
		{
			$out[] = "\nh3. ".$method."\n";
			if(strlen($info['javadoc'])>0)
			{
				$out[] = "<pre>".$info['javadoc']."</pre>";	
			}
		}
	}
	wiki_write($data['name'].".txt",implode("\n",$out));
	$out = null;
	
	// output as html
	$out .= '<div class="oc_name"><a name="top">'.$data['name'].'</a></div>';
	$out .= '<div class="oc_location">Location: '.$data['file'].'</div>';
	if($data['javadoc'])
	{
		$out .= '<div class="oc_alert">'.nl2br($data['javadoc']).'</div>';		
	}
	if($data['extends'])
	{
		$out .= '<div class="oc_attention">Extends: <a href="'.strtolower($data['extends']).'.html">'.$data['extends'].'</a></div>';
	}
	if($data['abstract'])
	{
		$out .= '<div class="oc_attention">Abstract!</div>';
	}
	if($data['method']['&singleton'])
	{
		$out .= '<div class="oc_attention">Has singleton!</div>';	
	}
	// set anchors for jumping
	asort($data['method']);
	$out .= '<div id="oc_internal_nav">';

	if(sizeof($data['events'])>0)
	{
		$out .= '<table><tr><th>Method</th><th>Event</th><th>API</th><th>Default</th></tr>';
		foreach($data['method'] as $m => $v)
		{
			$out .= '<tr><td><a href="#'.$m.'">'.$m.'</a></td><td>'.$data['events'][$m]['event'].'</td><td>'.(isset($data['events'][$m]['api'])?"YES":'&nbsp;').'</td><td>'.(isset($data['events'][$m]['default'])?"YES":'&nbsp;').'</td></tr>';
		}	
		$out .= '</table>';
	}
	else
	{
		foreach($data['method'] as $m => $v)
		{
			$out .= '<a href="#'.$m.'">'.$m.'</a>';
		}
	}
	$out .= '</div>';
	foreach($data['method'] as $m => $v)
	{
		$out .= '<fieldset class="oc_method"><legend><a name="'.$m.'">'.$m.'</a>  <a href="#top">top</a></legend>';
		$out .= '<div class="oc_box">CALL: '.$v['function'].'</div>';		
		if($v['javadoc'])
		{
			$out .= '<div class="oc_alert">'.nl2br($v['javadoc']).'</div>';		
		}
		if($v['info'])
		{
			foreach($v['info'] as $vk => $vv)
			{
				$out .= '<div class="oc_alert">'.$vk.'</div>';
			}
		}
		if($v['arguments'] AND !isset($v['arguments'][0]))
		{
			$out .= '<fieldset><legend>Arguments</legend><table><tr><th>Name</th><th>Default</th></tr>';
			
			foreach($v['arguments'] as $vk => $vv)
			{
				$out .= '<tr><td>'.$vk.'</td><td>'.$vv.'</td></tr>';
			}			
			$out .= '</table></fieldset>';
		}
		if($v['return'])
		{
			$out .= '<fieldset><legend>Returnvalues</legend>';
			
			foreach($v['return'] as $vk => $vv)
			{
				$out .= $vv.'<br/>';
			}			
			$out .= '</fieldset>';
		}
		$out .= '</fieldset>';
	}
	return $out;
}

function wiki_write($file,$data)
{
	if(!is_dir('web/wiki'))
	{
		mkdir('web/wiki');
	}
	$f = fopen('web/wiki/'.$file,'w+');
	fwrite($f,$data);
	fclose($f);
}

function html_write($file,$data)
{
	if(!is_dir('web/doc'))
	{
		mkdir('web/doc');
	}
	$f = fopen('web/doc/'.$file,'w+');
	fwrite($f,$data);
	fclose($f);
}

function html_content($data)
{
	return '<div id="oc_content">'.$data.'</div>';
}

function html_nav($a,$type,$nav,$lnk_prefix=null)
{
	$out = '<div id="oc_'.$type.'_nav">';
	foreach($nav as $k=>$v)
	{
		 $out .= '<a href="'.$lnk_prefix.$v.'" '.($k==$a?'class="active"':'').'>'.strtolower($k).'</a>';
	}
	$out .= '</div>';
	return $out;
}

function html_head($type)
{
	return '<html><head><style>
	body
	{
		font-family:Trebuchet MS;
	}
	table
	{
		width:100%;
	}
	th
	{
		border:thin solid silver;
		margin:2px;
		font-weight:bold;
		color:silver;
	}	
	td
	{
		border:thin solid silver;
		margin:2px;
	}
	fieldset
	{
		border: thin solid silver;
	}
	legend
	{
		font-weight:bold;
		padding:4px;
	}
	.oc_location
	{
		width:680;
		padding:10px;
		background-color:yellow;
		color:black;
		margin-bottom:3px;
	}
	.oc_name
	{
		width:680;
		padding:10px;
		background-color:silver;
		color:yellow;
		font-weight:bold;
		margin-bottom:3px;
	}
	.oc_attention
	{
		width:680;
		padding:10px;
		background-color:red;
		color:black;
		margin-bottom:3px;
	}
	.oc_alert
	{
		padding:10px;
		border:thin solid red;
		color:black;		
		margin-bottom:3px;
	}
	.oc_box
	{
		padding:10px;
		color:black;		
		margin-bottom:3px;
		font-weight:bold;
	}	
	#oc_main_nav
	{
		position:absolute;
		top:10px;
		left:10px;
		background-color:silver;
		padding:10px;
		width:1026px;
	}
	#oc_main_nav a
	{
		font-weight:bold;
		color:black;
		text-decoration:none;
		margin-right:10px;
	}
	#oc_main_nav .active
	{
		color:yellow;
	}
	#oc_main_nav a:hover
	{
		color:yellow;
	}	
	#oc_sub_nav
	{
		position:absolute;
		top:50px;
		left:10px;
		padding:10px;
		width:300px;
		background-color:white;
		border:thin solid silver;
	}
	#oc_sub_nav a
	{
		display:block;
		font-weight:bold;
		color:silver;
		text-decoration:none;
		padding:2px;
	}
	#oc_sub_nav a:hover
	{
		color:yellow;
		background-color:silver;
	}
	#oc_sub_nav .active
	{
		color:yellow;
		background-color:silver;
	}
	#oc_meta_nav
	{
		width:680;
		padding:10px;
		background-color:silver;
		color:black;
		margin-bottom:3px;
	}
	#oc_meta_nav a 
	{
		color:black;
		font-weight:bold;
		text-decoration:none;
		margin-right:10px;
	}
	#oc_meta_nav a:hover
	{
		color:yellow;
	}
	#oc_meta_nav .active
	{
		color:yellow;
	}
	#oc_internal_nav
	{
		width:680;
		padding:10px;
		margin-bottom:3px;
		background-color:white;
		border:thin solid silver;
	}
	#oc_internal_nav a
	{
		display:block;
		font-weight:bold;
		color:silver;
		text-decoration:none;
		padding:2px;
	}
	#oc_internal_nav a:hover
	{
		color:yellow;
		background-color:silver;
	}
	#oc_content
	{
		position:absolute;
		top:50px;
		left:334px;
		padding:10px;
		width:700px;
		background-color:white;
		border:thin solid silver;
	}
	</style><title>oOSdoc ['.$type.']</title></head><body>';
}
function html_foot()
{
	return '</body></html>';
}

?>
