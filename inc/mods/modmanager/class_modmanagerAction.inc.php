<?php

	class modmanager_action extends modAction
	{
	
		var $action;
		var $OPC;	// zeiger auf globalen OPC
		var $mod_name  = "modmanager";
		var $tbl = "mod_modmanager";
		
		
		function modmanager_action() {
			$this->OPC = &OPC::singleton();
		}
		
		/*
		* 'verteiler-funktion' wird von MC::call_action() aufgerufen
		*
		*@param	array	$action
		*@return 
		*/
		function main($action, $params = null) 
		{
			$this->action = $action;
			
			switch(strtolower($action['event'])) 
			{                                                                                     
				case 'deactivate':	   			$this->deactivate();       					break;				
				case 'activate':	   			$this->activate();		   					break;
				case 'clone':					$this->doclone();								break;
			}
			
		}
		
		function activate()
		{
			$insert = "INSERT INTO sys_module (modul_name, label) VALUES ('".$this->data['mod']."','".$this->data['mod']."')";
			$this->DB->query($insert);
		}
		
		function deactivate()
		{
			$delete = "DELETE FROM sys_module WHERE modul_name = '".$this->data['mod']."'";
			//$sql[] = "DELETE FROM mod_page_mod_grp_ar WHERE mid = '".$mod_id."'";
			//$sql[] = "DELETE FROM mod_page_mod_usr_ar WHERE mid = '".$mod_id."'";
			$this->DB->query($delete);
		}
		
		function doclone()
		{
			// copy the source module to a tmp location with the target name
			// remove all SVN
			// replace all occurencies of the module name in the files
			// replace all occurencies of the module name in the filenames
			if(!$this->data['name'])
			{
				return $this->OPC->error(e::o('Please specify a name'));
			}          
			if(is_dir(CONF::inc_dir().'/mods/'.$this->data['name']))
			{
				return $this->OPC->error(e::o('There is already a module with that name.'));
			}                                                                           
			exec('cp -R '.CONF::inc_dir().'/mods/'.$this->data['mod']." ".CONF::inc_dir().'/mods/'.$this->data['name']);
			$this->remove_svn(CONF::inc_dir().'/mods/'.$this->data['name']);
			$this->rename_mod(CONF::inc_dir().'/mods/'.$this->data['name'],$this->data['mod'],$this->data['name']);
			$this->install($this->data['name']);
		}
		/*
		  installs the mod: looks at the version folder and executes the sql files by number.
		  and remembers the last sql it executed
		*/
		function install($mod=null)
		{
			$dir = dir(CONF::inc_dir().'/mods/'.$mod.'/version');
			while(false !== ($e = $dir->read()))
			{                                                 
				if(substr($e,-3)=='sql')
				{
					//$this->DB->query($this->DB->escape(UTIL::file_get_contents(CONF::inc_dir().'/mods/'.$mod.'/version/'.$e)));
					/*
					$query = implode("",file(CONF::inc_dir().'/mods/'.$mod.'/version/'.$e));
					$query =  implode("",explode("\n",$query));
					$query =  implode("",explode("\r",$query));
					*/                                                                    
					$f = file(CONF::inc_dir().'/mods/'.$mod.'/version/'.$e);
					foreach($f as $l)
					{
						$query .= trim($l);
					}                      
					$query = preg_replace("/;/","",$query);
					$query = preg_replace("/\n/","",$query);
					$query = preg_replace("/\r/","",$query);
					$query = preg_replace("/\t/","",$query);
					  
					$f = fopen('/Users/lilith/Desktop/test.log','a+');
					fwrite($f,$query);
					fclose($f);
					
					$query = trim($query);
					
					$queryc = 'CREATE TABLE `mod_test13` (`vid` varchar(40) NOT NULL,`headline` varchar(250) NOT NULL,`content` text NOT NULL,UNIQUE KEY `vid` (`vid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8';
					           
					$query = 'CREATE TABLE `mod_test13` (`vid` varchar(40) NOT NULL,`headline` varchar(250) NOT NULL,`content` text NOT NULL,UNIQUE KEY `vid` (`vid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ';
					
					$this->DB->query($query,false);
					
					
//					if(md5($queryc) != md5($query))
					if($queryc != $query)
					{            
						MC::debug($queryc);
						MC::debug($query);
						die('1');
					}
					
				}
			}			   
		}
		
		function remove_svn($d)
		{
			$dir = dir($d);
			while(false !== ($e = $dir->read()))
			{
				if($e == "." OR $e == "..")
				{
					continue;
				}            
				if($e == ".svn")
				{
					exec("rm -R ".$d."/".$e);                               
				}                            
				if(is_dir($d."/".$e))
				{
					$this->remove_svn($d."/".$e);
				}
			}
		}
		
		function rename_mod($d,$from,$to)
		{
			$dir = dir($d);
			while(false !== ($e = $dir->read()))
			{
				if($e == "." OR $e == "..")
				{
					continue;
				}            
				if(is_dir($d."/".$e))
				{
					$this->rename_mod($d."/".$e,$from,$to);
				}            
				if(is_file($d."/".$e))
				{   
					// change contents
					$c = UTIL::file_get_contents($d."/".$e);
					$c = preg_replace("/".$from."/",$to,$c);
					UTIL::file_put_contents($d."/".$e,$c);
					// change filename
					if(preg_match("/".$from."/",$e))
					{
						exec('mv '.$d.'/'.$e.' '.$d.'/'.preg_replace("/".$from."/",$to,$e));
					}
				}
			}
		}
		
	}
	
?>
