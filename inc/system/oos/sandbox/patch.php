<?

//	not relevant to testing, strix 2012-05-11
/*
	$redirect = false;
	if(!CONF::get('patch_270807_ts_opc_call_view'))
	{
			$m = get_class_methods('CONF');
			if(!in_array('patch',$m))
			{
				$f = file(CONF::inc_dir().'/etc/system/oos.cnf');				
				for($i=sizeof($f)-1;$i>0;$i--)
				{
					if(preg_match('/}/',$f[$i]))
					{
						break;
					}
				}
				$f[$i-1] .= "\nfunction patch(\$p){return false;}\n";
				$fp = fopen(CONF::inc_dir().'/etc/system/oos.cnf','w');
				fwrite($fp,implode("",$f));
				fclose($fp);
			}
		CONF::set('patch_270807_ts_opc_call_view',true);
		$redirect = true;
	}
	
	// adding automatic cache refresh to CONF::set

	if(!CONF::get('patch_090408_ts_conf_set_d'))
	{
		$c = UTIL::file_get_contents(CONF::inc_dir().'/etc/system/oos.cnf');
		if(!preg_match("/patch_040408_ts_conf_set/",$c))
		{
	$r = '	
	function set($cnf,$val)
	{
		$cnf_dir = CONF::cnf_dir();
		// format val
		// open the file for writing no matter what it contains or whether it exists
		$cnf_file = fopen($cnf_dir."/".$cnf,"w+");
		fwrite($cnf_file,serialize($val));
		fclose($cnf_file);
		// patch_040408_ts_conf_set : auto refresh cache
		CONF::get($cnf,true);
		return true;
	}';
			UTIL::function_replace(CONF::inc_dir().'/etc/system/oos.cnf','set',$r);
	
			$redirect = true;
		}
		CONF::set('patch_090408_ts_conf_set_d',true);
	}

	if($redirect)
	{
		header('Location: '.CONF::baseurl().$_SERVER['REQUEST_URI']);
		die;
	}

*/
?>
