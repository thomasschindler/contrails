<?php
/**
*	view class for grid
*
*	@author 		Thomas Schindler <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		mods
*/

	class grid_view extends modView 
	{
		var $mod_name = "grid";
		var $tbl = "mod_grid";
		var $exclude_mod = array
		(
			'grid' => 1,
			'mdb' => 1,
			'wysiwyg' => 1,
			'page'=>1,
			'syscheck' => 1,
			'acladmin' => 1,
			'objbrowser' => 1,
			'podcast' => 1,
		);
		/**
		*	constructor
		*/
		function grid_view() {}
		/**
		*	view distribution
		*/
		function main($vid, $method_name) 
		{				
			$this->vid = $vid;
			$this->OPC->lnk_add('mod',$this->mod_name);
			$this->OPC->lnk_add('vid',$vid);
			
			//$this->tpl_sub_dir = CONF::web_dir()."/tpl/oos/grid/".CONF::project_name();

			$this->tpl_sub_dir = CONF::inc_dir()."/mods/grid/assets/".CONF::project_name();
			
			if(!is_dir($this->tpl_sub_dir))
			{
				mkdir($this->tpl_sub_dir,0755);
				// initialise the common contents
				$this->MC->call_action(array("mod"=>"grid","event"=>"init"));
			}

			$msg = $this->alert_msg();
			if($msg)
			{
				$this->set_var("err",&$msg);
			}
			
			switch(strtolower($method_name)) 
			{	
				case 'edit':								$this->view_edit();							break;
				case 'online':
				default:										$this->view_default();						break;
			}
		}
		
		
		/**
		*	show the article or a message that there is no article
		*/
		function view_default()
		{

			if($this->access('edit'))
			{
				//$this->set_var('button',$this->OPC->create_button('edit',e::o('edit_button'),'edit'));
				#echo '<div style="position:absolute;z-index:6000;width:150px;left:200px;">'.$this->OPC->create_button('edit',e::o('edit_button'),'edit').'</div>';
				#$this->set_var('edit_bar',);

				$form_edit = '<form action="'.$this->OPC->action().'" method="post" name="edit">
					<button>'.e::o('edit_button').'</button>
					'.$this->OPC->lnk_hidden(array(
						'event' => 'edit',
						'mod' => 'grid'
					)).'
				</form>';
				
				$mod_list = $this->get_mod_list();
				$form .= '<form action="'.$this->OPC->action().'" method="post" name="transport">';
					$form .= '<select name="data[new_mod]">';
					foreach($mod_list as $mod)
					{
						if(!$this->exclude_mod[$mod['mod_name']])
						{
							$form .= '<option value="'.$mod['mod_name'].'"> '.$mod['label'].'</option>';
						}
					}
					$form .= '</select>';

					$form .= '<button>'.e::o('v_new').'</button>		
					<input type="hidden" name="event" value="new_box">					
					<input type="hidden" name="mod" value="'.$this->mod_name.'">
					<input type="hidden" name="data[init]" value="true">
					<input type="hidden" name="data[sx]" value="300">
					<input type="hidden" name="data[sy]" value="300">
					<input type="hidden" name="data[px]" value="100">
					<input type="hidden" name="data[py]" value="100">
					'.$this->OPC->lnk_hidden().'
				</form>';				
				
				$this->set_var('edit_toggle',&$form_edit);
				$this->set_var('edit_bar',&$form);
				$this->generate_view('edit_bar.php',true);
			}
			
			
			
			
			if(!is_file($this->tpl_sub_dir."/".$this->vid.".php"))
			{
				$this->create_tpl();
			}
			$this->generate_view($this->tpl_sub_dir."/".$this->vid.'.php');
			return;
		}
		/**
		*	edit the article
		*/
		function view_edit()
		{

			#$this->set_var('button',$this->OPC->create_button('online',e::o('online_button'),'online'));
			// set the mods we have
			$form .= $this->alert_msg();
			$select = "SELECT * FROM ".$this->tbl." WHERE id = '".$this->vid."'";
			$mods = $this->DB->query($select,false);

			$this->set_var('mods',&$mods);
			// create the interface
			while($mods->next())
			{
				if(!is_error($this->MC->instance_config($mods->f('mod'))) AND strlen($mods->f('view')) == 0)
				{
					
					
					$fields['default'] = 'default';
					$tmp = $this->MC->instance_config($mods->f('mod'));
					foreach($tmp['methods'] as $f => $v)
					{
						$fields[$f] = $v;
					}
					
					$conf = array
					(
						'fields' => array
						(
							'view'	 => array
							(
								'label' => 'Choose',
								'cnf' => array
								(
									'type' => 'select',
									'items' => $fields
								)
							)
						)
					);
					
					$f = MC::create("form");					
					$f->init($this->mod_name,$conf);
					$f->add_hidden("data[id]",$mods->f('id'));
					$f->add_hidden("data[vid]",$mods->f('vid'));
					$f->add_hidden("data[mod]",$mods->f('mod'));
					$f->add_button("event_instance_view_set","Speichern");

					$choose[$mods->f('vid')] = $f->show();
					
					$serialized .= 'ID['.$mods->f('vid').']'.$mods->f('px').'px:'.$mods->f('py').'px:'.$mods->f('sx').'px:'.$mods->f('sy').'px:'.$mods->f('z_index');
					
				}
				else
				{
					$serialized .= 'ID['.$mods->f('vid').']'.$mods->f('px').'px:'.$mods->f('py').'px:'.$mods->f('sx').'px:'.$mods->f('sy').'px:'.$mods->f('z_index');
				}
			}
			$this->set_var('choose',&$choose);
			$mods->reset();
			// if we're trying to create a module that can have different views, we have to choose
			if($this->get_var('choose_instance'))
			{
				$instance_config = $this->MC->instance_config($this->data['new_mod']);
				$form .= '<form action="'.$this->OPC->action().'" method="post" name="transport">';
					
					$form .= '<select name="data[new_mod_view]">';
					$form .= '<option value="default">default</option>';
					foreach($instance_config['methods'] as $method => $label)
					{
						$form .= '<option value="'.$method.'">'.$label.'</option>';
					}
					$form .= '</select>';
					$form .= '<input type="submit" value="'.e::o('save').'" name="event_new_box">
					<input type="hidden" name="serialized" value="'.$serialized.'">
					'.$this->OPC->lnk_hidden(array(
						'data[new_mod]' => $this->data['new_mod'],
						'data[label]' => $this->data['label']
					)).'
				</form>';			
			}
			elseif($this->get_var("set_vid"))
			{
				$form .= '<form action="'.$this->OPC->action().'" method="post" name="transport">';
					$form .= 'VID: <input type="text" name="data[set_vid]">';
					$form .= '<input type="submit" value="'.e::o('save').'" name="event_new_box">
					<input type="hidden" name="serialized" value="'.$serialized.'">
					'.$this->OPC->lnk_hidden(array(
						'data[new_mod]' => $this->data['new_mod'],
						'data[label]' => $this->data['label']
					)).'
				</form>';			
			}
			else
			{
				//get the mod list 
				$mod_list = $this->get_mod_list();
				$form .= '<form action="'.$this->OPC->action().'" method="post" name="transport">';
					$form .= '<select name="data[new_mod]">';
					foreach($mod_list as $mod)
					{
						if(!$this->exclude_mod[$mod['mod_name']])
						{
							$form .= '<option value="'.$mod['mod_name'].'"> '.$mod['label'].'</option>';
						}
					}
					$form .= '</select>';
					#$form .= ' <input type="text" name="data[label]" value="'.($this->data['label'] ?  $this->data['label'] : e::o('v_mod_label')).'">';
					
					
					          
					//<input type="image" src="/template/oos/img/button/'.e::lang($this->CLIENT->usr['lang']).'/module_new.gif" title="'.e::o('v_new').'" name="event_new_box">
					
					$form .= '
					<button>'.e::o('v_new').'</button>
					<input type="hidden" name="event" value="new_box">
					<input type="hidden" name="serialized" value="'.$serialized.'">
					<input type="hidden" name="data[sx]" value="300">
					<input type="hidden" name="data[sy]" value="300">
					<input type="hidden" name="data[px]" value="100">
					<input type="hidden" name="data[py]" value="100">
					'.$this->OPC->lnk_hidden().'
				</form>';				
			}
			
				$changed = UTIL::get_post('changed') ? true : false;
				/*
$js = '<script language="Javascript">
			function do_confirm()
			{
				var result = false;
				result = confirm("booo");
				return document.online.do_save.value = result;
			}
</script>';

				$form_online = $js.'<form action="'.$this->OPC->action().'" method="post" name="online">					
					<input type="image" src="'.$this->OPC->show_icon('online_red',true).'" onClick="do_confirm();"><span>'.e::o('v_online_view').'</span>
					<input type="hidden" name="serialized" value="'.$serialized.'">
					'.$this->OPC->lnk_hidden(array(
						'event' => 'online_view',
						'changed'=>$changed,
						'do_save'=>false
					)).'
				</form>';
*/
/*
				$form_online = $js.'<form action="'.$this->OPC->action().'" method="post" name="online">					
					<input type="image" src="'.$this->OPC->show_icon('online_red',true).'" onClick="if(document.online.changed.value == 1){if(confirm(\''.e::o('v_confirm_changes').'\')){document.online.do_save.value = 1;}}"><span>'.e::o('v_online_view').'</span>
					<input type="hidden" name="serialized" value="'.$serialized.'">
					'.$this->OPC->lnk_hidden(array(
						'event' => 'online_view',
						'changed'=>$changed,
						'do_save'=>false
					)).'
				</form>';
*/                                                                                                                          
//					<input type="image" src="/template/oos/img/button/'.e::lang($this->CLIENT->usr['lang']).'/module_save.gif" title="'.e::o('v_online_view').'" >
				$form_online = $js.'<form action="'.$this->OPC->action().'" method="post" name="online">					
					<button>'.e::o('v_online_view').'</button>
					<input type="hidden" name="event" value="'.$serialized.'">
					<input type="hidden" name="serialized" value="'.$serialized.'">
					'.$this->OPC->lnk_hidden(array(
						'event' => 'online_view',
						'changed'=>$changed,
						'do_save'=>1
					)).'
				</form>';
			
			
			/*
				get the pages on which the user has the right to the page and to grid
				create a dropdown with those pages 
			*/

			$select = "SELECT * FROM sys_vid WHERE mod_name = 'grid'";
			$r = $this->DB->query($select);
			while($r->next())
			{
				$pids[$r->f('pid')]++;
			}
			
			$p = $this->CLIENT->get_my_pages();
			
			foreach($p as $pid => $data)
			{
				if($this->MC->access("grid","edit",$pid) == true AND $pid != $this->pid AND $pids[$pid])
				{
					$pages[$pid] = $data['name'];
				}
			}
			
			$cnf = array
			(
				'fields' => array
				(
					'pid' => array
					(
						'label' => '',
						'cnf' => array
						(
							'type' => 'select',
							'items' => $pages
						)
					)
				)
			);
			
			/*
			$f = MC::create("form");
			$f->init("choose_form",$cnf);
			$f->add_button("event_copy_module","Kopieren");
			$move_interface = $f->start().$f->fields().$f->end();
			*/
			

			
			$this->set_var('copy_form_cnf',&$cnf);
			
			$this->set_var('interface',&$form);
			$this->set_var('online',&$form_online);
			// create the view
			return $this->generate_view('edit.php',true);
		}
		/**
		*	create an empty tpl
		*/
		function create_tpl()
		{
			// do we have a template with a wrong name for this page???
			// this might happen in new versions with the opc bug fixed.
			
			// check for id with the current pid.
			// if we have a file with this id, we update the db and rename the file
			
			$select = "SELECT * FROM ".$this->tbl." WHERE pid = ".$this->pid;
			$r = $this->DB->query($select);
			
			if($r->nr() == 1)
			{
				$update = "UPDATE ".$this->tbl." SET id = '".$this->vid."' WHERE id = '".$r->f('id')."'";
				$this->DB->query($update);
				$cmd = "mv ".$this->tpl_sub_dir."/".$r->f('id').".php ".$this->tpl_sub_dir."/".$this->vid.".php";
				exec($cmd);
			}
			else
			{
				$file = fopen($this->tpl_sub_dir."/".$this->vid.".php","w+");
				$str = "";
				fwrite($file,$str);
				fclose($file);
			}
			return;
		}
		/**
		*	get the list of possible modules
		*/
		function get_mod_list()
		{
			$ret = array();
			// get all mods
			$select = "SELECT * FROM sys_module order by label";
			$mods = $this->DB->query($select);
			// get the rights
			$rights = $this->MC->get_access_rights();
			// filter the modlist against rights
			$ret[] = array
			(
				'mod_name' => -1, 
				'label' => e::o("choose_mod")
			);
			while($mods->next())
			{
				if(isset($rights[$mods->f('id')]) OR ($this->CLIENT->usr['id'] == CLIENT::__root()))
				{
					$l = 20;
					$label = $mods->f('label');
					if(strlen($label) > $l)
					{
						$label = substr($label,0,$l).'...';
					}
					$ret[] = array('id'=>$mods->f('id'),'label'=>$label,'mod_name'=>$mods->f('modul_name'));
				}
			}
			return $ret;
		}
	}
	
?>
