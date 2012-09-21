<?php
/**
*	action class for grid
*	
*	@author 		Thomas Schindler <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		mods
*/
	
	class grid_action extends modAction
	{
	
		var $mod_name  = "grid";
		var $tbl = "mod_grid";
		
		function grid_action() {}
		
		/*
		* event distribution
		*
		*	@param	array	$action
		*	@return void, method return value
		*/
		function main($action,$params = '') 
		{
			switch(strtolower($action['event'])) 
			{
				case 'instance_view_set':	$this->instance_view_set();	break;
				case 'copy_module';	$this->copy_module();			break;
				case 'move_module';	$this->move_module();			break;
				case 'init':  $this->init();	break;
				case 'online_view':	$this->check_online_view();			break;
				case 'edit':		$this->set_view('edit');				break;
				case 'new_box':	$this->action_new_box();				break;
				case 'save_state':	$this->action_save_state();			break;
				case 'get_mods':	return $this->action_get_mods($params);	break;
			}
		}
		
		function instance_view_set()
		{
			#$this->action_save_state();
			$this->set_view("edit");
			#MC::debug($this->data);
			$update = "UPDATE ".$this->tbl." SET 
				view = '".$this->data['view']."'
			WHERE id = '".$this->data['id']."' AND vid = '".$this->data['vid']."'";
			$this->DB->query($update);
		}
		
		function move_module()
		{
			$this->copy_module(true);
		}
		
		function copy_module($rm=false)
		{
			// get the target vid for the page - this works on the assumption that we only use one grid per page!!
			$select = "SELECT id, z_index FROM ".$this->tbl." WHERE pid = ".$this->data['pid']." ORDER BY z_index DESC LIMIT 0,1";
			$r = $this->DB->query($select);
				
			if($r->nr() != 1)
			{
				$select = "SELECT vid FROM sys_vid WHERE pid = ".$this->data['pid']." AND mod_name = 'grid'";
				$r = $this->DB->query($select);
				if($r->nr() != 1)
				{
					$this->add_msg(e::o("err_copy_module_grid"));
					return;
				}
				$id = $r->f('vid');
				$z_index = 5000;
			}
			else
			{
				$id = $r->f('id');
				$z_index = $r->f('z_index')+1;			
			}
			
			// is this module on the target page already?
			$select = "SELECT id FROM ".$this->tbl." WHERE vid = '".$this->data['vid']."' AND pid = ".$this->data['pid']."";
			$check = $this->DB->query($select);	
			
			if($check->nr() != 0)
			{
				$this->add_msg(e::o("err_copy_module_double"));
				return;
			}
			
			// get the data for this mod from the db
			$select = "SELECT * FROM ".$this->tbl." WHERE vid = '".$this->data['vid']."'";
			$data = $this->DB->query($select);
			
			// put it into the db for the target page
			$insert = "INSERT INTO ".$this->tbl." (
				".$this->tbl.".id,
				".$this->tbl.".vid,
				".$this->tbl.".pid,
				".$this->tbl.".label,
				".$this->tbl.".mod,
				".$this->tbl.".view,
				".$this->tbl.".sx,
				".$this->tbl.".sy,
				".$this->tbl.".px,
				".$this->tbl.".py,
				".$this->tbl.".z_index
			) VALUES (
				'".$id."',
				'".$this->data['vid']."',
				'".$this->data['pid']."',
				'".$data->f('label')."',
				'".$data->f('mod')."',
				'".$data->f('view')."',
				'".$data->f('sx')."',
				'".$data->f('sx')."',
				'".$data->f('px')."',
				'".$data->f('py')."',
				'".$z_index."'
			)";
		
			$this->DB->query($insert);
			// remove it from the original page
			
			if($rm)
			{
				$delete = "DELETE FROM ".$this->tbl." WHERE vid = '".$this->data['vid']."' AND pid = ".$this->pid;
				$this->DB->query($delete);
				$this->create_tpl();
			}
			
			// change location to the target page with activated grid
			$lnk = $this->OPC->lnk(array(
				'pid' => $this->data['pid'],
				'mod' => 'grid',
				'event' => 'edit',
				'vid' => $id
			));
			header("Location: ".CONF::baseurl()."/".$lnk);
			die;
		}
		/**
		*	check for changes and ask the user if he wants to save
		*/
		function check_online_view()
		{
			$do_save = (bool)UTIL::get_post("do_save");
			if($do_save == 1)
			{
				$this->action_save_state();
			}
			return $this->set_view('online');
		}
		/**
		*	create a new box
		*/
		function action_new_box()
		{
			if($this->data['new_mod'] < 0)
			{
				return;
			}
			if(!$this->data['init'])
			{
				$this->action_save_state(); // save the position and size of the current boxes
			}
			
			if(!$this->data['sx'])
			{
				$sx = 600;
				$sy = 300;
				$px = 400;
				$py = 400;
			}
			else
			{
				$sx = $this->data['sx'];
				$sy = $this->data['sy'];
				$px = $this->data['px'];
				$py = $this->data['py'];				
			}      
			$this->set_view('edit');
			$val = &regex::singleton();
			if(!$val->validate($this->data['label'],$val->text()))
			{
				return $this->add_msg(e::o('a_err_label'));
			}
			// does the new module have an instance file?
			/*
			
			this is only right for standard layout!
			
			if(is_error($this->MC->instance_config($this->data['new_mod'])) != 1 AND !$this->data['new_mod_view'])
			{
				$instance = $this->MC->instance_config($this->data['new_mod']);
				if(sizeof($instance['methods']) > 0)
				{
					$this->set_var('choose_instance',true);
					return;
					$ret = 1;
				}
			}
			*/
			
			/*
			if($this->access("set_vid"))
			{
				if(is_error($this->MC->instance_config($this->data['new_mod'])) != 1 AND !$this->data['set_vid'])
				{
					$instance = $this->MC->instance_config($this->data['new_mod']);
					if($instance['set_vid'])
					{
						$this->set_var('set_vid',true);
						return;
					}
				}
			}
			*/

			
			// create the new box
			$select = "SELECT z_index FROM ".$this->tbl." WHERE id = '".$this->vid."' ORDER BY z_index DESC LIMIT 0, 1";
			$z_index = $this->DB->query($select);

			if($z_index->nr() == 0)
			{
				$z_index = 5000;
			}
			else
			{
				$z_index = $z_index->f('z_index')+1;
			}

			$vid = $this->data['set_vid'] ? $this->data['set_vid'] : $this->OPC->set_vid('grid_child');
			
			$vid = strtolower($vid);
			
			$insert = "INSERT INTO ".$this->tbl." (
				id,
				vid,
				pid,
				sx,
				sy,
				px,
				py,
				z_index,
				".$this->tbl.".mod,
				view,
				label
			) VALUES (
				'".$this->vid."',
				'".$vid."',
				'".$this->pid."',
				".$sx.",
				".$sy.",
				".$px.",
				".$py.",
				'".$z_index."',
				'".$this->data['new_mod']."',
				'".$this->data['new_mod_view']."',
				'".$this->data['label']."'
			)";
						
			$this->DB->query($insert);
			// set the changed parameter to true
			$_GET['changed'] = true;
			return;
		}
		/**
		*	save the box states
		*/
		function action_save_state()
		{
			$this->set_view('edit');
			$serialized = UTIL::get_post('serialized');
			
			/*
			if(strlen($serialized) == 0)
			{
				MC::debug("cleanup");
				$this->cleanup();
				return;			
			}
			*/
			$vids = array();
			// parse the serialized string
			$elements = explode('ID[',$serialized);

			foreach($elements as $element)
			{
				$info = explode(']',$element);
				// vid should be $info[0]
				$vid = $info[0];
				$vids[] = $info[0];
				$info = explode(':',$info[1]);
				

				
				$update = "UPDATE ".$this->tbl." SET 
					px = '".(substr(trim($info[0]),-2) == 'px' ? substr(trim($info[0]),0,-2) : trim($info[0]))."',
					py = '".(substr(trim($info[1]),-2) == 'px' ? substr(trim($info[1]),0,-2) : trim($info[1]))."',
					sx = '".(substr(trim($info[2]),-2) == 'px' ? substr(trim($info[2]),0,-2) : trim($info[2]))."',
					sy = '".(substr(trim($info[3]),-2) == 'px' ? substr(trim($info[3]),0,-2) : trim($info[3]))."',
					z_index = '".trim($info[4])."'
				WHERE vid = '".$vid."' AND id = '".$this->vid."'";
				$this->DB->query($update);
			}
			// cleanup function 
			$this->cleanup($vids);
			$this->create_tpl();
			return;
		}
		/**
		*	cleanup checks for deleted boxes and tries to call a cleanup function in the corresponding module
		*/
		function cleanup($vids = array())
		{
			$select = "SELECT * FROM ".$this->tbl." WHERE vid NOT IN ('".implode("','",$vids)."') AND id = '".$this->vid."'";

			$deleted = $this->DB->query($select);
			
			#MC::Debug($deleted);
			/*
            [0] => Array
                (
                    [id] => 49b55498cc4ad2ade841fa55d0f3a77a
                    [vid] => c569c94db9fea7e670c6a3fe9f3f14ab
                    [pid] => 349
                    [label] => Name
                    [mod] => article
                    [view] => 
                    [sx] => 400
                    [sy] => 400
                    [px] => 0
                    [py] => 120
                    [z_index] => 5002
                )
			*/
			
			$vids = array();
			
			while($deleted->next())
			{
				// only delete ones that dont exist on a second page
				$select = "SELECT id FROM ".$this->tbl." WHERE vid = '".$deleted->f('vid')."'";
				$r = $this->DB->query($select);
				
				$vids[] = $deleted->f('vid');
				
				if($r->nr() == 0)
				{
					$this->MC->call_action
					(
						array
						(
							'mod'=>$deleted->f('mod'),
							'event'=>'garbage_collection'
						),
						array
						(
							'vid' => $deleted->f('vid')
						)
					);
				}
			}
			
			$delete = "DELETE FROM ".$this->tbl." WHERE vid IN ('".implode("','",$vids)."') AND id = '".$this->vid."'";
			
			$this->DB->query($delete);
			return;
		}
		/**
		*	return a list of pages with the according names
		*	as a db result object
		*/
		function action_get_mods($params)
		{
			if(isset($params['pid']))
			{
				if(is_array($params['pid']))
				{
					$where = "WHERE pid IN ('".implode("','",$params['pid'])."')";
				}
				else
				{
					$where = "WHERE pid = '".$params['pid']."'";
				}
			}
			$select = "SELECT pid, label,vid,mod,view FROM ".$this->tbl." ".$where." ORDER BY pid ASC";
			$list = $this->DB->query($select);
			return $list;
		}
		/**
		*	create the tpl file
		*/
		function create_tpl($vid=null)
		{

			// check if we have a content_syndication_client
			// if so - put it in!
			if($this->MC->get_modul_id("content_syndication_client"))
			{
				$csc = true;				
			}
			
			if($vid)
			{
				$this->vid = $vid;
			}
			
			// 
			
			$select = "SELECT * FROM ".$this->tbl." WHERE id = '".$this->vid."'";
			$boxes = $this->DB->query($select,false);
			
			
			$content = ' ';
			while($boxes->next())
			{
				/**
					spare boxes with an instance file and no view
				*/
				if(!is_error($this->MC->instance_config($boxes->f('mod'))) AND strlen($boxes->f('view')) == 0)
				{
					continue;
				}
				
				if($csc)
				{					
					$content .= '<div style="position:absolute;width:'.$boxes->f('sx').'px;height:15px;left:'.$boxes->f('px').'px;top:'.($boxes->f('py')-15).'px;z-index:'.($boxes->f('z_index')+100).';">';
					$content .= '
					<?
						$OPC->call_view("'.$boxes->f('vid').'.'.$boxes->f('mod').'","content_syndication_client","",1);
					?>';
					$content .= '</div>';
				}

				$width[] = $boxes->f('sx')+$boxes->f('px'); // width+left
				$height[] = $boxes->f('py')+$boxes->f('sy');
				
				$content .= '<div style="overflow:auto;position:absolute;width:'.$boxes->f('sx').'px;height:'.$boxes->f('sy').'px;left:'.$boxes->f('px').'px;top:'.$boxes->f('py').'px;z-index:'.$boxes->f('z_index').';">';
				$content .= '
				<?
					$OPC->set_var("'.$boxes->f('mod').'","_width","'.$boxes->f('sx').'");
					$OPC->set_var("'.$boxes->f('mod').'","_height","'.$boxes->f('sy').'");
					$OPC->set_var("'.$boxes->f('mod').'","_left","'.$boxes->f('px').'");
					$OPC->set_var("'.$boxes->f('mod').'","_top","'.$boxes->f('py').'");
					$OPC->call_view("'.$boxes->f('vid').'","'.$boxes->f('mod').'","'.$boxes->f('view').'",1);
				?>';
				$content .= '</div>';
			}	
						
			
			$max_w = 0;
			if($width)
			{
			foreach($width as $w)
			{
				if($w > $max_w)
				{
					$max_w = $w;
				}
			}
			}
			
			$max_h = 0;
			if($height)
			{
			foreach($height as $h)
			{
				if($h > $max_h)
				{
					$max_h = $h;
				}
			}
			}
			//
			if($c = CONF::get('sbcx'))
			{
				$max_w -= $c;	
			}
			if($c = CONF::get('sbcy'))
			{
				$max_h -= $c;	
			}
			// now we could create the relative box
			
			$content .= '<div id="grid_squeezebox" style="position:relative;height:'.$max_h.'px;width:'.$max_w.'px;" >&nbsp;</div>';


			$file = fopen(CONF::inc_dir()."/mods/".$this->mod_name."/assets/".CONF::project_name()."/".$this->vid.".php","w+");
			fwrite($file,$content);
			fclose($file);
			return;
		}
		
		function init()
		{
			$select = "SELECT id FROM ".$this->tbl."";
			$boxes = $this->DB->query($select);
			while($boxes->next())
			{
				$this->create_tpl($boxes->f("id"));
			}
		}
		
	}
	
?>
