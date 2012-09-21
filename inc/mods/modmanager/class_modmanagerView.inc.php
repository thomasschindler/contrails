<?php
	
/**
* we need 
* - migrations
* - packing
* - installing 
*/
	
	class modmanager_view extends modView 
	{
	
		var $mod_name = "modmanager";
		var $pid;
		var $tbl = "mod_modmanager";
		
		function modmanager_view() {
			
		}
/**
*	view method decision switch and global settings
*/
	
		function main($vid, $method_name) {
			
			$this->vid = $vid;
			$this->pid = $this->OPC->get_pid();
			
			$this->OPC->lnk_add('mod',$this->mod_name);
			$this->OPC->lnk_add('pid',$this->pid);
			$this->OPC->lnk_add('vid',$this->vid);
			
			$this->set_var('vid',$this->vid);
			
			switch(strtolower($method_name)) {
				default:
					return $this->modmanager();
			}
			
		}
		
		/**
		*	show the navigation
		*/
		function modmanager()
		{                    
			// get all mods from the table
			$select = "SELECT * FROM sys_module order by label";
			$r = $this->DB->query($select);
			while($r->next())
			{
				$m[$r->f('modul_name')] = $r->r();
			}
			// make a loop through the inc/mods dir
			$d = dir(CONF::inc_dir().'/mods/');            
			$c = '<table>';
			while(false !== ($e = $d->read()))
			{                                                           
				$tmp = array();
				if(preg_match('/\./',$e))
				{
					continue;
				}         
				// virtual means: you cannot clone it - it's part of the system
				$tmp[] = $e;
				$tmp[] = (!isset($m[$e]) AND $m[$e]['virtual']==0) ? '<button name="event_activate">'.e::o('Activate').'</button>' : ((isset($m[$e]) AND $m[$e]['virtual']==0) ? '<button  name="event_deactivate">'.e::o('Deactivate').'</button>' : '&nbsp;');
				$tmp[] = '<input type="text" name="data[name]"><button name="event_clone" >'.e::o('Clone').'</button>';
				$this->OPC->lnk_add('data[mod]',$e);
				$c .= '<tr><form action="'.$this->OPC->action().'" method="POST"><td>'.implode('</td><td>',$tmp).'</td>'.$this->OPC->lnk_hidden().'</form></tr>';
			}
			$c .= '</table>';
			// multidim-array to table!!!
			
 			$this->set_var('headline','modmanager');
 			$this->set_var('content',&$c);
			return $this->generate_view('main.php',true);
		}                                                 
	}
?>
