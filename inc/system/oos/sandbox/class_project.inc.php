<?

class PROJECT
{
	/**
		returns all config files
	*/
	function get_config_files()
	{
		static $get_config_files;
		if(!$get_config_files)
		{
			$idx = 2;
			$dir = CONF::inc_dir()."/etc/config";
			$d = dir($dir);
			while(false !== ($entry = $d->read()))
			{
				if(($entry != ".") AND ($entry != "..") AND !is_dir($dir."/".$entry) AND (substr($entry,-3) == "cnf" ) AND ($entry != '_default.cnf'))
				{
					$get_config_files[$idx] = $dir."/".$entry;
					$idx++;
				}
			}
		}
		return $get_config_files;
	}
	/**
		returns all project names
	*/
	function get_projects()
	{
		static $get_projects;
		if(!$get_projects)
		{
			$cnf_files = PROJECT::get_config_files();
			$get_projects = array();
			// include them all
			foreach($cnf_files as $idx => $file)
			{
				$class_name = PROJECT::get_class_name($idx);
				$host_config = UTIL::file_get_contents($file);
				if(preg_match('/header/i',$host_config))
				{
					continue;
				}
				$host_config = preg_replace("/HOST_CONF/i",$class_name,$host_config);
				eval('#?#>'.$host_config);
				$host_c = "\$get_projects[".$idx."] = ".$class_name."::project_name();";
				eval($host_c);
			}
		}
		$tmp = array();

		foreach($get_projects as $idx => $name)
		{
			if($tmp[$name])
			{
				unset($get_projects[$idx]);
			}
			$tmp[$name] = $idx;
		}

		asort($get_projects);	
		return $get_projects;	
	}
	function get_project_id($project)
	{
		static $get_project_id;
		if(!$get_project_id)
		{
			$get_projects = PROJECT::get_projects();
			foreach($get_projects as $id => $name)
			{
				$get_project_id[$name] = $id;
			}
		}
		if($get_project_id[$project])
		{
			return $get_project_id[$project];
		}
		return false;
	}
	/**
		returns the config file for an id 
	*/
	function get_project_file($id)
	{
		static $get_project_file;
		if(!$get_project_file[$id])
		{
			$get_projects = PROJECT::get_config_files();
			$get_project_file[$id] = $get_projects[$id];
		}
	}
	/**
		returns the value of a configuration $conf for project $project
	*/
	function conf($project,$conf)
	{
		if(!$project OR !$conf)
		{
			return false;
		}
		$project_id = PROJECT::get_project_id($project);
		if(!$project_id)
		{
			return false;
		}
		static $__conf;
		if(!$__conf)
		{
			$__conf = array();
		}
		if(!$__conf[$project][$conf])
		{
			$host_config = UTIL::file_get_contents(PROJECT::get_project_file($project_id));
			$class_name = PROJECT::get_class_name($project_id);
			$host_config = preg_replace("/HOST_CONF/i",$class_name,$host_config);
			eval('#?#>'.$host_config);
			$host_c = "\$__conf[\"".$project."\"][\"".$conf."\"] = ".$class_name."::".$conf."();";
			eval($host_c);
		}
		return $__conf[$project][$conf];
	}
	/**
	
	*/
	function get_class_name($idx)
	{
		static $get_class_name;
		if(!$get_class_name[$idx])
		{
			$get_class_name[$idx] = "HOST_CONF_".$idx;
		}
		return $get_class_name[$idx];
	}
}

?>
