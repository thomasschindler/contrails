<?
/**
*	class handling the remote call sequence
*/
class remote_connection
{
	var $OPC; // our local opc
	var $BURC; // a burc link handler
	var $called_vids = array(); // array containing all vids we called in a call_action
	var $remote_map = array();
	/**
	*	constructor
	*/
	function remote_connection($server=null)
	{
		if(!$server)
		{
			$server = CONF::get("content_syndication_server");
		}
		
		$this->server = $server;
		
		$this->OPC = &OPC::singleton();
		$this->BURC = new BURC();

		if(!is_dir(CONF::inc_dir()."/etc/system/connections/".CONF::project_name()."/"))
		{
			mkdir(CONF::inc_dir()."/etc/system/connections/".CONF::project_name()."/",0755);
		}
	}
	/**
	*	call_view checks whether we need a remote call to the current module
	*	if so it checks whether it exists
	*	if not - it connects to the remote system
	*/
	function call_view($vid,$mod,$view)
	{
		if(strlen($vid) > 32)
		{
			return false;
		}
		
		$this->remote_map[$mod][$vid] = $this->check_for_connection($vid,$mod);

		if(!is_array($this->remote_map[$mod][$vid]))
		{
			return false;
		}
		if($this->called_vids[$vid])
		{
			return false;
		}	
		$remote_vars = $this->remote_map[$mod][$vid];

		// connect to the remote system via soap
		$client = new soapclientw($remote_vars['remote_oos']."/services/remote_module.php");
				
		$result = $client->call
		(
			'call_view', 
			array
			(
				'usr'=>$remote_vars['remote_usr'],
				'pwd'=>$remote_vars['remote_pwd'],
				'pid' => $remote_vars['remote_pid'],
				'vid'=>$remote_vars['remote_vid'],
				'mod' => $mod,
				'view'=>$view,
			)
		);
		

		
#		$result = unserialize(stripslashes(urldecode($result)));
		$result = $this->decode($result);
		
		
		if(!is_array($result))
		{
			return false;
		}
		// now we collect the remote session and replace it with our own
		// rewrite everything in the vars to our needs
		// ( especially stuff like the links and burc! )

		$result = UTIL::array_replace($remote_vars['remote_vid'],$vid,$result);
				
		if(is_array($result['links']))
		{
			foreach($result['links'] as $link => $values)
			{
				// create a new link with the values
				$values['SESSION'] = session_id();
				$values['vid'] = $vid;
				$values['pid'] = $this->OPC->pid();
				//$new_link = $this->BURC->lnk(array_merge($values));
				$new_link = $this->BURC->lnk($values);
				$nl = explode(".html?",$new_link);
				$ol = explode(".html?",$link);
				$result['vars'] = UTIL::array_replace(strval(trim($ol[0])),strval(trim($nl[0])),$result['vars']);
			}
		}
		$result = UTIL::array_replace($result['session'],session_id(),$result);
		$result = UTIL::array_replace($result['session_name'],$this->OPC->SESS->name,$result);
		
		$result = UTIL::array_replace(strval($remote_vars['remote_pid']),strval($this->OPC->pid()),$result); // <- not too good
		
//		$this->OPC->_vars['__remote'][$vid] = $result['vars'];
		$this->OPC->vars_set('__remove',$vid,$result['vars']);
		$this->called_vids[$vid] = true;
		return true;
		
	}
	/**
	*	call_action 
	*	is used in the mc->call_action process
	*	checks to see whether it is necessary to call a remote action 
	*/
	function call_action($action,$params=null)
	{
		/*
		if(!$this->check_for_connection($action['mod']))
		{
			// no need to check further
			return false;
		}
		*/
		// the vid in question can be in two places
		// 	get_post
		//	params
		// 	params has priority
		if(is_array($params))
		{
			foreach($params as $local_vid)
			{
				$remote_vars = $this->check_for_connection($local_vid,$action['mod']);
				if(is_array($remote_vars))
				{
					continue;
				}
			}
		}
		if(!is_array($remote_vars))
		{
			$local_vid = UTIL::get_post('vid');
			if(!empty($local_vid))
			{
				$remote_vars = $this->check_for_connection($local_vid,$action['mod']);
			}
		}

		// no remote values -> we can continue locally
		if(!is_array($remote_vars))
		{
			return false;
		}
		
		if($this->called_vids[$local_vid])
		{
			return false;
		}

		$this->called_vids[$local_vid] = true;
		// do the remote call procedure
		$remote_get_post = UTIL::oos_array_merge($_GET,$_POST);
		
		unset($remote_get_post['pid']);
		unset($remote_get_post['vid']);
		unset($remote_get_post['file']);
		unset($remote_get_post['burc']);
		unset($remote_get_post['SESSION']);
		
		$remote_get_post['pid'] = $remote_vars['remote_pid'];
		$remote_get_post['vid'] = $remote_vars['remote_vid'];
		
		$MYCLIENT = &CLIENT::singleton();
		
		// connect to the remote system via soap
		$client = new soapclientw($remote_vars['remote_oos']."/services/remote_module.php");
		$result = $client->call
		(
			'call_action', 
			array
			(
				'usr'=>$remote_vars['remote_usr'],
				'pwd'=>$remote_vars['remote_pwd'],
				'action'=>serialize($action),
				'params'=>serialize($params),
				'get_post'=>urlencode(addslashes(serialize($remote_get_post))),
				'vid'=>$remote_vars['remote_vid'],
				'local_usr' => $MYCLIENT->usr['usr']."@".substr(CONF::baseurl(),7)
			)
		);
				
#		$result = unserialize(stripslashes(urldecode($result)));
		$result = $this->decode($result);
		if(!is_array($result))
		{
			return false;
		}
		// now we collect the remote session and replace it with our own
		// now we collect the remote session and replace it with our own
		// rewrite everything in the vars to our needs
		// ( especially stuff like the links and burc! )
		$result = UTIL::array_replace($remote_vars['remote_vid'],$local_vid,$result);
		foreach($result['links'] as $link => $values)
		{
			// create a new link with the values
			$values['SESSION'] = session_id();
			$values['vid'] = $local_vid;
			$values['pid'] = $this->OPC->pid();
			//$new_link = $this->BURC->lnk(array_merge($values));
			$new_link = $this->BURC->lnk($values);
			$nl = explode(".html?",$new_link);
			$ol = explode(".html?",$link);
			$result['vars'] = UTIL::array_replace(strval(trim($ol[0])),strval(trim($nl[0])),$result['vars']);
		}
		$result = UTIL::array_replace($result['session'],session_id(),$result);
		$result = UTIL::array_replace($result['session_name'],$this->OPC->SESS->name,$result);
		$result = UTIL::array_replace(strval($remote_vars['remote_pid']),strval($this->OPC->pid()),$result); // <- not too good
		
		// put the vars into the local system
		$this->OPC->set_view(
			$local_vid,
			$action['mod'],
			$result['view']
		);
//		$this->OPC->_vars['__remote'][$local_vid] = $result['vars'];
		$this->OPC->vars_set('__remote',$local_vid,$result['vars']);
		// set remote to true for jumping local action
		return true;
	}
	/**
	*	check for connection files
	*	if only asked for a module
	*		returns true or false if connections exist for the specified module
	*	if asked for module and vid
	*		returns null or an array of values
	*/
	function subscribed($vid,$mod)
	{
		$file = CONF::inc_dir()."/etc/system/connections/".CONF::project_name()."/".$vid.".".$mod.".sub";
		if(@is_file($file))
		{
			$this->get_connection($vid,$mod);
			$file = file($file);

			if($file[0] === '0')
			{
				return true;
			}
			return $file[0];
		}
		return false;
	}

	function subscribe($vid,$mod,$unpublish="0")
	{
		
		if(!$unpublish)
		{
			$unpublish = "0";
		}

		$f = fopen(CONF::inc_dir()."/etc/system/connections/".CONF::project_name()."/".$vid.".".$mod.".sub","w+");
		fwrite($f,$unpublish);
		fclose($f);
	}
	
	function unsubscribe($vid,$mod)
	{
		UTIL::delete_file(CONF::inc_dir()."/etc/system/connections/".CONF::project_name()."/".$vid.".".$mod.".sub");
	}
	
	function published($vid,$mod)
	{
		return is_file(CONF::inc_dir()."/etc/system/connections/".CONF::project_name()."/".$vid.".".$mod.".pub");
	}

	function publish($vid,$mod)
	{
		$f = fopen(CONF::inc_dir()."/etc/system/connections/".CONF::project_name()."/".$vid.".".$mod.".pub","w+");
		fwrite($f,time());
		fclose($f);
	}
	
	function unpublish($vid,$mod)
	{
		UTIL::delete_file(CONF::inc_dir()."/etc/system/connections/".CONF::project_name()."/".$vid.".".$mod.".pub");
	}
	
	function check_for_connection($vid=null,$mod=null)
	{
		if(!$vid)
		{
			return is_dir(CONF::inc_dir()."/etc/system/connections/".CONF::project_name());
		}
		if($this->subscribed($vid,$mod))
		{
			return $this->get_connection($vid,$mod);
		}
		return null;
	}
	
	function get_connection($vid,$mod)
	{
			$client = new soapclientw($this->server."/services/content_syndication_server.php");
			$result = $client->call
			(
				'get_connection', 
				array
				(
					'vid'=>$vid,
					'host'=>substr(CONF::baseurl(),7)
				)
			);
			$res = $this->decode($result);
			
			if($res === false)
			{
				$this->unsubscribe($vid,$mod);
			}
			
			if($res['unpublish'] !== 0)
			{
				$this->subscribe($vid,$mod,$res['unpublish']);
			}
			
			return $res;	
	}
	
	/**
	*	make sure we only have one instance of this class
	*/
	function &singleton($server=null) 
	{
		static $instance;
		if (!is_object($instance)) 
		{
			$instance = new remote_connection($server);
		}
		return $instance;
	}
	
	function encode($trsp)
	{
		return urlencode(addslashes(serialize($trsp)));
	}
	function decode($trsp)
	{
		return unserialize(stripslashes(urldecode($trsp)));
	}
}
?>