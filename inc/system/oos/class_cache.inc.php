<?

class CACHE
{
	var $file = false;
	var $base = "/tmp/oospagecache";
	var $ignore = false;
	/**
	*	if the linktype is scs and there are no parameters in the url and the function cache exists in the configuration 
	*	and the respective file exists for the current user (and testgroup)!
	*	then output the file
	*/
	function CACHE($min=false)
	{
		if($min==false)
		{
			$this->base .= "_".md5(CONF::baseurl());
			$c = CLIENT::singleton();
			if(!$c->usr['is_default'])
			{
				return;
			}	
			if(!@$_COOKIE['AB'])
			{
				return;
			}
			// linktype has to be scs
			if(CONF::linktype() != "scs")
			{
				return;
			}
			// cache function
			if(!method_exists("conf","cache"))
			{
				return;
			}
			// analyze url
			if(method_exists("conf","cache_check"))
			{
				$o = &OPC::singleton();
				if(CONF::cache_check($o->pid()) == false)
				{
					if(preg_match("/[a-zA-Z0-9/]*-[p|m]{1}[0-9]{6,14}_[0-9]{3}.html/",$_REQUEST['file']))
					{
						return;
					}
					if(preg_match("/form_[a-zA-Z0-9_]*.html/",$_REQUEST['file']))
					{
						return;
					}	
				}
			}
			else
			{
				if(preg_match("/[a-zA-Z0-9/]*-[p|m]{1}[0-9]{6,14}_[0-9]{3}.html/",$_REQUEST['file']))
				{
					return;
				}
				if(preg_match("/form_[a-zA-Z0-9_]*.html/",$_REQUEST['file']))
				{
					return;
				}
			}
			// make sure we have the folder
			if(!is_dir($this->base))
			{
				UTIL::mkdir_recursive($this->base);
			}
			$cache_hash = '';
			if(method_exists("conf","cache_hash"))
			{
				$cache_hash = CONF::cache_hash();
			}
			$this->file = $this->base."/".md5($_REQUEST['file'].$cache_hash);
			if(!is_file($this->file))
			{
				return;
			}
			if(filemtime($this->file)+CONF::cache()<time())
			{
				return UTIL::delete_file($this->file);
			}
			readfile($this->file);
			$LOG = &LOG::singleton();
			$LOG->end();
			die;
		}
		return;
	}
	/**
	*	
	*/
	function write()
	{
		if($this->ignore)
		{
			return;
		}
		if($this->file)
		{
			$f = fopen($this->file,"w+");
			fwrite($f,ob_get_contents());
			fclose($f);	
		}
	}
	
	function ignore()
	{
		$this->ignore = true;
	}
	
	function &singleton($min=false) 
	{
		static $instance;	
		if (!is_object($instance)) 
		{
			$instance = new CACHE($min);
		}
		return $instance;
	}
	
}


?>