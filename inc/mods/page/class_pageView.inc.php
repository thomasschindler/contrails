<?php

/**
*	page - handling
*	very important central module
*	
*	creates the surrounding for vids, used by opc and all nested modules
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		mods
*/

        class page_view extends modView
        {

			var $view;
			
			var $tpl_dir;
			var $tpl_dir_change_dir;
			
			var $default_pid = 1;
			
			var $mod_name = 'page';
			
			var $mod_tbl = 'mod_page';
			
			var $tbl_module = 'sys_module';
			var $tbl_usr_ar = 'mod_page_mod_usr_ar';
			var $tbl_grp_ar = 'mod_page_mod_grp_ar';
			var $tbl_grp    = 'mod_usradmin_grp';
			var $tbl_usr    = 'mod_usradmin_usr';
			var $tbl_acl    = 'mod_page_acl';
			var $tbl_tpl = 'mod_page_tpl';
			var $vid = '';
			
			/**
			* root-seiten-id des baumes
			*/
			var $root_id = 1;
			
			function page_view() {}
			
			/**
			*	view method decision switch and global settings
			*/

			function main($vid, $method_name) 
			{   	 
					
				// is the user allowed to see this page?
				/*
				if(isset($this->pid))
				{
					if(!in_array($this->pid,$this->CLIENT->get_allowed_pages()))
					{
						// add the desired url to the session, so we can redirect the user after login
						// create a new session
						$this->SESS->set('after_login','url',CONF::baseurl()."/".$_SERVER['REQUEST_URI']);
						header("Location: ".CONF::baseurl()."/".$this->OPC->lnk(array('pid'=>CONF::pid())));
						
					}
				}
				*/

				$this->templates_dir = CONF::inc_dir() . '/mods/page/assets/';								
				$this->vid = $vid;
				$this->set_var('vid', $vid);

				switch(strtolower($method_name)) 
				{
					//-- common
					case 'tree':		return $this->show_tree();	break;
					case 'admin_panel':	$this->admin_panel();		break;
					case 'show_path':	$this->show_path();			break;
					//-- page-admin
					case 'pa':			$this->pa();				break;
					case 'pa_enter':	$this->template_dir_refresh();
										$this->pa_enter();			break;
					case 'pa_type':		$this->pa_type();			break;
					case 'pa_edit':		$this->pa_edit();			break;
					case 'pa_ar':		$this->pa_ar();				break;
					case 'pa_edit':		$this->pa_edit();			break;

					case 'view':
					default:			return $this->generate_page();
				}
				
			}
				
			/**
			*	refresh the templates
			*	reads through the templates dir 
			*	and updates the db if necessary
			*	saves a hash for better performance
			*/
			function template_dir_refresh()
			{
				$d = dir($this->templates_dir);
				while(false !== ($e = $d->read()))
				{
					if(substr($e,-4)=='.php')
					{
						$tpl[md5($e)] = $e;
					}
					elseif(substr($e,-4)=='.tpl')
					{
						$tpl[md5($e)] = $e;
					}
				}
				$h = CONF::get('template_dir_refresh');
				$hash = md5(serialize($tpl));
				if($h==$hash)
				{
					return;
				}
				// remove old
				$update  = "UPDATE ".$this->tbl_tpl." SET sys_trashcan = 1 WHERE tpl_name NOT IN ('".implode("','",$tpl)."')";
				$r = $this->DB->query($update);				
				// add new 
				$select  = "SELECT * FROM ".$this->tbl_tpl." WHERE !sys_trashcan";
				$r = $this->DB->query($select);
				while($r->next())
				{
					unset($tpl[md5($r->f('tpl_name'))]);
				}
				foreach($tpl as $t)
				{
					$insert[] = "('".$t."','".preg_replace("/_/"," ",strtoupper(substr($t,0,-4)))."')";
				}
				if($insert)
				{
					$insert = "INSERT INTO ".$this->tbl_tpl." ( tpl_name, label ) VALUES ".implode(",",$insert);
					$this->DB->query($insert);
				}
				CONF::set('template_dir_refresh',$hash);
			}
			
			
			/**
			* seite zum bearbeiten auswhlen
			*/
			function pa() {
				$edit_pid = (int)UTIL::get_post('edit_pid');
				//-- haben wir eine edit-id, koennen wir direkt zur auswahl gehen
				if ($edit_pid) return $this->pa_type();
				//-- seitenbaum auslesen
				$root_id = (int)$this->root_id;
				$set = new NestedSet();
				$set->set_table_name($this->mod_tbl);
				$nodes = $set->getNodes($root_id, '*', $this->DB->table_restriction($this->mod_tbl));
				$this->set_var('page_nodes', $nodes);
				$this->set_var('title', e::o('v_title_1'));
				$this->OPC->generate_view($this->tpl_dir . 'pa.php');
				return;
			}
			
			
			function pa_type()
			{
				// choose page
				$pid = (int)UTIL::get_post('edit_pid');
				if(!$pid)
				{
					$pid = $this->data['pid'];
					if(!$pid)
					{
						$pid = CONF::pid();
					}
				}
				$edit_pid = &$pid;
				
				$this->pid = $edit_pid;
				$this->OPC->pid_set($edit_pid);
				
				$base_lnk = array
				(
					'mod' => 'page',
					'edit_pid' => $edit_pid ,
					'vid' => $vid,
					'pid' => $edit_pid
				);
	
				$common_lnk = array_merge($base_lnk, array('event' => 'pa_edit'));
				$del_lnk    = array_merge($base_lnk, array('event' => 'pa_delete'));
	
				$pages = $this->CLIENT->get_my_pages();
				foreach($pages as $p)
				{
					$pid_list[$p['id']] = str_repeat("&nbsp;&nbsp;&nbsp;",($p['level']-1)).$p['name'];
				}	
				$conf = array
				(
					'fields' => array
					(
						'pid' => array
						(
							'label' => '',
							'cnf' => array
							(
								'type' => 'select',
								'items' => $pid_list,
								'js' => 'onChange="submit();"'
							)
						)
					)
				);
				$f = MC::create("form");
				$f->use_layout("none");
				$f->set_values(array("pid"=>$pid));
				$f->init("pidlist",$conf);
				$f->add_hidden("mod",$this->mod_name);
				$f->add_hidden("event","pa_type");
				$content = $f->show();
				// edit
				$form = MC::create('form');
				$form->init('page', 'mod_page');
				$form->add_hidden('edit_pid', $pid);
				$form->add_hidden('mod', $this->mod_name);
				// hatten wir fehler
				if ($this->get_var('error')) 
				{
					$form->set_values($this->get_var('data'));
					$form->set_error_fields($this->get_var('error'));
				}	
				else 
				{
					// frisch aus db lesen
					$sql  = 'SELECT * FROM '.$this->mod_tbl.' WHERE id='.$pid;
					$res  = $this->DB->query($sql);
					$data = $res->r();
					$form->set_values($data);
				}
				
				//$this->OPC->register_icon("oos_save","/template/oos/img/button/".e::lang($this->CLIENT->usr['lang'])."/save.gif",e::o('save'));
				//$form->add_image("oos_save",'event_pa_save');

				$form->button('event_pa_save',e::o('Save'));

				$content .= $form->show();
				
				// move

				
				$move_select = '<form action="'.$this->OPC->action().'" method="POST"><button class="btn">'.e::o('Move').'</button>'.
				'<select name="data[move_type]">'.
				'<option value="child">'.e::o('pa_type_one_level_under').'</option>'.
				'<option value="brother">'.e::o('pa_type_same_level_before').'</option>'.
				'</select> '.
				'<select name="data[move_to]">';
/*
	$move_select = '<form action="'.$OPC->action().'" method="POST">'.
				'<input type="radio" name="data[move_type]" value="child">'.e::o('pa_type_one_level_under').' - '.
				'<input type="radio" name="data[move_type]" value="brother">'.e::o('pa_type_same_level_before').'<br>'.
				'<select name="data[move_to]">';
				
	*/
				$root_id = (int)$this->root_id;
				$set = new NestedSet();
				$set->set_table_name($this->mod_tbl);
				$nodes = $set->getNodes($root_id, '*', $this->DB->table_restriction($this->mod_tbl));
				foreach($nodes as $node) 
				{
					$style = '';
		
					if ($node['id'] == $edit_pid) 
					{
						$no_move_lft = $node['lft'];
						$no_move_rgt = $node['rgt'];
					}
		
					if ($no_move_lft > 0 && $node['lft'] <= $no_move_rgt && $node['lft'] >= $no_move_lft) 
					{
						continue;
					}
		
					$move_select .= '<option value="'.$node['id'].'"'.$style.'>'.str_repeat('&nbsp;', ($node['level']-1)*4).$node['name'].'</option>';
				}
				/*
				$move_select .= '</select> <input type="submit" name="ok" value="'.e::o('pa_type_ok').'">'.$this->OPC->lnk_hidden().
					'<input type="hidden" name="mod" value="page">'.
					'<input type="hidden" name="event" value="pa_move">'.
					'<input type="hidden" name="edit_pid" value="'.$edit_pid.'">'.
					'</form>';
				*/
				$move_select .= '

					</select>
					 '.$this->OPC->lnk_hidden().
					'<input type="hidden" name="mod" value="page">'.
					'<input type="hidden" name="event" value="pa_move">'.
					'<input type="hidden" name="edit_pid" value="'.$edit_pid.'">'.
					'</form>';
				$content .= $move_select;
				// delete 
				
				if($this->access('ar_delete') AND $edit_pid != CONF::pid()) 
				{
					//$content .= '<div class="oos_pa_box"><a href="'.$this->OPC->lnk($del_lnk).'" onClick="return confirm(\''.e::o('pa_type_delete_confirm_page').'\');"><img src="/template/oos/img/button/'.e::lang($this->CLIENT->usr['lang']).'/delete.gif" alt="'.e::o('pa_type_delete').'" title="'.e::o('pa_type_delete').'" border="0" /></a></div>';
					$content .= '<div><a href="'.$this->OPC->lnk($del_lnk).'" class="btn" onClick="return confirm(\''.e::o('pa_type_delete_confirm_page').'\');">'.e::o('pa_type_delete').'</a></div>';
				}
				// new
				
				$this->set_var("content",&$content);
				$this->set_var("headline",e::o('pa_type_headline'));
				$this->OPC->generate_view($this->tpl_dir . 'main.php');
				
			}
			
			/**
			* waehlen, was editiert werden soll (name, zugriffsrechte etc.)
			*/
			function pa_type_DEP_130707() 
			{
				$edit_pid = (int)UTIL::get_post('edit_pid');
				//-- haben wir keine edit-id, gibts eine seiten-tabelle zur auswahl
				if (!$edit_pid) return $this->pa();
				$this->set_var('edit_pid', $edit_pid);
				//-- der seitenpfad erscheint immer
				$this->set_var('show_path',$this->show_path($edit_pid));
				//-- vorhandene module lesen
				$mods = $this->MC->get_mods();
				//-- auslesen, fuer welche module rechte vergeben wurden
				$known_mods = $this->MC->get_page_mods($edit_pid);
				//-- seitenbaum auslesen
				$root_id = (int)$this->root_id;
				$set = new NestedSet();
				$set->set_table_name($this->mod_tbl);
				$nodes = $set->getNodes($root_id, '*', $this->DB->table_restriction($this->mod_tbl));
				$this->set_var('page_nodes', $nodes);
				$this->set_var('known_mods', $known_mods);
				$this->set_var('mod_list', $mods);
				$this->set_var('table_name', $this->mod_tbl);
				$this->set_var('title',e::o('v_title_2'));
				$this->OPC->generate_view($this->tpl_dir . 'pa_type.php');
			}
			
			
			/**
			* editieren vojn allgemeinen angaben der seite (name, template etc.)
			*/
			function pa_edit() {
				$edit_pid = (int)UTIL::get_post('edit_pid');
				
				//-- haben wir keine edit-id, gibts eine seiten-tabelle zur auswahl
				if (!$edit_pid) {
					echo 'error: no pid';
					exit;
				}
				
				$form = MC::create('form');
				$form->init('page', 'mod_page');
				$form->add_hidden('edit_pid', $edit_pid);
				
				// hatten wir fehler
				if ($this->get_var('error')) {
					$form->set_values($this->get_var('data'));
					$form->set_error_fields($this->get_var('error'));
				}	
				else {
					// frisch aus db lesen
					$sql  = 'SELECT * FROM '.$this->mod_tbl.' WHERE id='.$edit_pid;
					$res  = $this->DB->query($sql);
					$data = $res->r();
					$form->set_values($data);
				}

				$this->set_var('path', $this->_get_path_print($edit_pid));
				$this->set_var('form', $form);
				
				$this->OPC->generate_view($this->tpl_dir.'pa_edit.php');

			}
			
			/*
				we need two of three parameters:
				mid
				pid
				uid
				
				
				
			*/
			function pa_ar() 
			{
				
				// get the page dropdown
				// get the module dropdown
				// get the group dropdown
				// get the user add interface
				
				$pages = $this->CLIENT->get_my_pages();
				$pid[0] = "---";
				foreach($pages as $p)
				{
					$pid[$p['id']] = str_repeat("&nbsp;&nbsp;&nbsp;",($p['level']-1)).$p['name'];
				}
				
				$mods = $this->MC->get_mods();
				$mid[0] = "---";
				foreach($mods as $m)
				{
					if(is_file(CONF::inc_dir().'/mods/'.$m['modul_name'].'/etc/access.php'))
					{
						$mid[$m['id']] = $m['label'];
					}
				}
							
				$grps = $this->MC->call("usradmin","get_groups");
				$gid[0] = "---";
				while($grps->next())
				{
					if($grps->f('trashcan'))
					{
						continue;
					}
					$gid[$grps->f('id')] = $grps->f('name');
				}
				
				$js = '
				function empty(id)
				{
					var elem = document.getElementById(id);
					if(id == "gid")
					{
						elem.value = 0;
					}
					else
					{
						elem.value = "";						
					}
				}
				';
				
				$content .= UTIL::get_js($js);
				
				$conf = array
				(
					'fields' =>  array
					(
						'pid' => array
						(
							'label' => ' <b>Seite</b> ',
							'cnf' => array
							(
								'type' => 'select',
								'items' => $pid
							)
						),
						'mid' => array
						(
							'label' => ' <b>Modul</b> ',
							'cnf' => array
							(
								'type' => 'select',
								'items' => $mid
							)
						),
						'gid' => array
						(
							'label' => ' <b>Gruppe</b> ',
							'cnf' => array
							(
								'type' => 'select',
								'items' => $gid,
								'js' => 'onFocus="empty(\'data[usr]\');"'
							)
						),
						
						'usr' =>  array
						(
							'label' => ' <b>oder User</b> ',
							'cnf' => array
							(
								'type' => 'input',
								'js' => 'onFocus="empty(\'gid\');"'
							)
						)
					)
				);
				

				if($this->data['pid'])
				{
					$action['pid'] = $this->data['pid'];
					$action['type'] += 2;
				}
				if($this->data['mid'])
				{
					$action['mid'] = $this->data['mid'];
					$action['type'] += 4;
				}
				if($this->data['gid'])
				{
					$action['gid'] = $this->data['gid'];
					$action['type'] += 8;
				}
				elseif($this->data['usr'])
				{
					// get the usr id
					$select = "SELECT * FROM mod_usradmin_usr WHERE usr = '".mysql_real_escape_string($this->data['usr'])."'";
					$r = $this->DB->query($select);
					if($r->nr() == 1)
					{
						$action['uid'] = $r->f('id');
						$action['type'] += 16;
					}
					else
					{
						$this->data['usr']	= e::o("this_usr_doesnt_exist");
					}
				}
/*
				MC::debug($action);
				MC::debug($this->data);
				MC::debug($_GET);
				MC::debug($_POST);
*/
				$f = MC::create("form");
				$f->init($this->mod_name,$conf);

				$f->set_values($this->data);
				$f->add_hidden("mod",$this->mod_name);

				$f->button("pa_ar",e::o("Show"));
				$content .= $f->show();
				
				
				switch($action['type'])
				{
					case 6: // pid mid
						$content .= $this->interface_form_get($this->interface_ar_data_get($action['pid'],$action['mid']),$action['pid'],$action['mid']);
					break;
					case 10: // pid gid
						foreach($mods as $m)
						{
							if($tmp = $this->interface_form_get($this->interface_ar_grp_data_get($action['gid'],$action['pid'],$m['id']),$action['pid'],$m['id'],array("mid"=>true)))
							{
								$content .= '<div style="margin-top:15px;font-weight:bold;border:thin solid black;padding:4px;">'.$m['label'].'<br>'.$tmp.'</div>';
							}							
						}
					break;
					case 12: // mid gid
						foreach($pages as $page)
						{
							if($tmp = $this->interface_form_get($this->interface_ar_grp_data_get($action['gid'],$page['id'],$action['mid']),$page['id'],$action['mid'],array("pid"=>true)))
							{
								$content .= '<div style="margin-top:15px;font-weight:bold;border:thin solid black;padding:4px;">'.$page['name'].'<br>'.$tmp.'</div>';
							}
						}					
					break;
					case 14: // pid mid gid
						$content .= $this->interface_form_get($this->interface_ar_grp_data_get($action['gid'],$action['pid'],$action['mid']),$action['pid'],$action['mid']);
					break;
					case 18: // pid usr
						foreach($mods as $m)
						{
							if($tmp = $this->interface_form_get($this->interface_ar_usr_data_get($action['uid'],$action['pid'],$m['id']),$action['pid'],$m['id'],array("mid"=>true)))
							{
								$content .= '<div style="margin-top:15px;font-weight:bold;border:thin solid black;padding:4px;">'.$m['label'].'<br>'.$tmp.'</div>';
							}
						}
					break;
					case 20: // mid usr
						foreach($pages as $page)
						{
							if($tmp = $this->interface_form_get($this->interface_ar_usr_data_get($action['uid'],$page['id'],$action['mid']),$page['id'],$action['mid'],array("pid"=>true)))
							{
								$content .= '<div style="margin-top:15px;font-weight:bold;border:thin solid black;padding:4px;">'.$page['name'].'<br>'.$tmp.'</div>';
							}
						}
					break;	
					case 22: // pid mid usr
						$content .= $this->interface_form_get($this->interface_ar_usr_data_get($action['uid'],$action['pid'],$action['mid']),$action['pid'],$action['mid']);
					break;
					default:
						$content .= '<b>'.e::o('min_two_elements').'</b>';
					break;
				}

				$this->set_var("content",&$content);
				$this->set_var("headline",e::o("pa_ar_headline"));
				$this->show('pa_ar_new');
				
				return;
			}
			
			
			function interface_form_get($data,$pid,$mid,$ignore=array())
			{
				$form_name = 'arform_'.++$this->form_cnt;
		
				if($this->get_var('data_'.$this->form_cnt))
				{
					$data = $this->get_var('data_'.$this->form_cnt);
				}
				$access_conf = $this->MC->access_config($this->MC->get_modul_name($mid));

				$ret .= '<table class="table table-striped">';

				$form = $this->MC->create('form');
				$form->init('page');
				
				if(!$ignore['pid'])
				{
					$form->add_hidden('data[pid]',$pid);
				}
				else
				{
					$form->add_hidden('data[pid_hidden]',$pid);
				}
				if(!$ignore['mid'])
				{
					$form->add_hidden('data[mid]',$mid);
				}
				else
				{
					$form->add_hidden('data[mid_hidden]',$mid);
				}
				$form->add_hidden('data[usr]',$this->data['usr']);
				$form->add_hidden('data[gid]',$this->data['gid']);
	
				$form->add_hidden('edit_pid',  $pid);
				$form->add_hidden('vid',       $vid);

				$form->add_hidden('mod','page');
				$form->add_hidden('event', '');
				$form->add_hidden('data[action]','');		// infos fuer hinzufuegen/loeschen etc.
				$form->add_hidden("data[form_cnt]",$this->form_cnt);

				$ret .= $form->start($form_name);

				
				if(is_object($access_conf))
				{
					return;
				}
				else
				{
					$max = count($access_conf['rights']);
				}

				//-- horizontal -> zugriffsrechte
				$ret .=  '<tr>';
				$ret .=  '<th style="width:120px;">&nbsp;</th>';	// fuergruppen/user
				$ret .=  '<th style="width:70px;">'.e::o('delete').'</th>';	// for editthis checkbox 
				$ret .=  '<th style="width:70px;background-color:#66c266;" >'.e::o('change').'</th>';	// for editthis checkbox 
				foreach($access_conf['labels'] as $ar => $label) 
				{
					$ret .=  '<th>'.htmlspecialchars($label).'</th>';
				}
				$ret .=  '</tr>';
	
				//$icon_base   = CONF::img_url().'icons/';
				//$icon_delete = '<img src="'.$icon_base.'icon_delete.gif" width="16" height="16" alt="'.e::o('delete').'" border="0">';

				$cnt = 0;



				foreach(array('grp', 'usr') as $key) 
				{
					//$my_icon = $icon_base . 'icon_mod_usradmin_'.$key.'.gif';
		
					if (!is_array($data['info'][$key]))
					{
						continue;
					}

					foreach($data['info'][$key] as $id => $info) 
					{
						$no_rights = false;
			
						// vorhandene rechte als hidden-fields
						$ret .=  '<input type="hidden" name="data[info]['.$key.']['.$id.'][name]" value="'.$info['name'].'">';
						$ret .=  '<input type="hidden" name="data[info]['.$key.']['.$id.'][ar]" value="'.$info['ar'].'">';
			
						$css_class = $counter++%2 == 0 ? 'even' : 'odd';
		
						$ret .=  '<tr>';

					
						$ret .=  '<th style="text-align:left"><img src="'.$my_icon.'">&nbsp;';
						if ( ($key == 'usr' && $id == $CLIENT->usr['id']) || ($key == 'grp' && isset($CLIENT->usr['groups'][$id])) ) 
						{
							$ret .=  '<u>'. $info['name'] .'</u>';
						}
						else 
						{
							$ret .=  $info['name'];
						}
						$ret .=  '</th>';
			
						$ret .=  '<th><input type="checkbox" name="data[info]['.$key.']['.$id.'][del]" value="1"></th>';
			
						$ret .=  '<th  style="background-color:#66c266;"><input type="checkbox" name="data[info]['.$key.']['.$id.'][chg]" value="1"></th>';
						
						// get access conf
						
												
						foreach($access_conf['rights'] as $name => $number) 
						{
								$ret .=  '<td align="center"><input type="checkbox" name="data[ar]['.$key.']['.$id.']['.$name.']" value="'.$number.'"'.(($info['ar'] & $number) ? ' checked' : '').'></td>';
						}
						$ret .=  '</tr>';
			
						$cnt++;		
					}
				}
				
				if ($no_rights) 
				{
					$ret .= '<tr><th colspan="3" align="center">'.e::o('no_grp_usr_selection').'</th><td colspan="'.$max.'">&nbsp;</td>	</tr>';
				}
	
				$add_param = array
				(
					'mod' => 'objbrowser',
					'pid' => '1', #161
					'event' => 'admin_list',
					'data[tbl][0]' => 'mod_usradmin_usr',
					'data[tbl][1]' => 'mod_usradmin_grp',
					'data[callback]' => 'page_objbrowser_callback_'.$this->form_cnt,
					'data[return_type]' => 'ser',
				);


				$lnk_add = $this->OPC->lnk($add_param);

				$ret .= '</table>';

				$ret .= '
				<div class="oos_pa_box">

				<a href="'.$lnk_add.'" id="add_user" class="btn popup">Add a user or a group</a>

				</a></div>
				<script language="JavaScript">
				<!--
					function page_objbrowser_callback_'.$this->form_cnt.'(result) 
					{
						var fo = document.forms[\''.$form_name.'\'];
						fo.elements[\'data[action]\'].value = result;
						fo.elements[\'event\'].value     = \'pa_ar_add\';
						fo.submit();
					}
					function page_del_ar(result) 
					{
						var fo = document.forms[\''.$form_name.'\'];
						fo.elements[\'data[action]\'].value = result;
						fo.elements[\'event\'].value     = \'pa_ar_del\';
						fo.submit();		
					}
				//-->
				</script>
				';

				$ret .= '</table>';
				
				$ret .= $this->interface_ar_inherit_get($pid);
				
				$ret .= $form->end();

				return $ret;
				
			}
			
			
			function interface_ar_usr_data_get($uid,$pid,$mid)
			{
				//-- vorhandene rechte von usern lesen (mit join, um namen zu kriegen)
				$sql = 'SELECT ar.*, usr.usr as name FROM '. $this->tbl_usr_ar.' AS ar, '.$this->tbl_usr.' AS usr  WHERE ar.mid='.$mid.' AND ar.pid = '.$pid.' AND ar.uid=usr.id AND usr.id = '.$uid;
				$usr_ar = $this->DB->query($sql);
				//-- wurden die rechte von anderer seite geerbet ?
				$inherit_pid = (int)$usr_ar->f('inherit_pid');
				if ($inherit_pid > 0 && $inherit_pid != $pid)
				{
					$inheritet_from = $this->_get_path_print($inherit_pid);
				}	
				while($usr_ar->next()) 
				{
					$data['info']['usr'][$usr_ar->f('uid')] = array
					(
						'name' => $usr_ar->f('name'),
						'ar'   => $usr_ar->f('ar'),
					);
				}
				return $data;
			}
			

			function interface_ar_grp_data_get($gid,$pid,$mid)
			{
				//-- vorhandene rechte von usern lesen (mit join, um namen zu kriegen)
				$sql = 'SELECT ar.*, usr.usr as name FROM '. $this->tbl_usr_ar.' AS ar, '.$this->tbl_usr.' AS usr  WHERE ar.mid='.$mid.' AND ar.pid = '.$pid.' AND ar.uid=usr.id AND usr.id = '.$uid;
				
					$sql = 'SELECT ar.*, grp.name FROM '. $this->tbl_grp_ar.' AS ar, '.$this->tbl_grp.' AS grp '.
								' WHERE ar.mid='.$mid.' AND ar.pid='.$pid.' AND ar.gid=grp.id AND grp.id = '.$gid;
				
				$usr_ar = $this->DB->query($sql);
				//-- wurden die rechte von anderer seite geerbet ?
				$inherit_pid = (int)$usr_ar->f('inherit_pid');
				if ($inherit_pid > 0 && $inherit_pid != $pid)
				{
					$inheritet_from = $this->_get_path_print($inherit_pid);
				}	
				while($usr_ar->next()) 
				{
					$data['info']['usr'][$usr_ar->f('uid')] = array
					(
						'name' => $usr_ar->f('name'),
						'ar'   => $usr_ar->f('ar'),
					);
				}
				return $data;
			}
			
			function interface_ar_data_get($pid=null,$mid=null)
			{

				if (!$pid || !$mid) return;

				//-- vorhandene rechte von usern lesen (mit join, um namen zu kriegen)
				$sql = 'SELECT ar.*, usr.usr as name FROM '. $this->tbl_usr_ar.' AS ar, '.$this->tbl_usr.' AS usr  WHERE ar.mid='.$mid.' AND ar.pid='.$pid.' AND ar.uid=usr.id';

				$usr_ar = $this->DB->query($sql);
				
#				MC::Debug($usr_ar,$sql);
				
				//-- wurden die rechte von anderer seite geerbet ?
				$inherit_pid = (int)$usr_ar->f('inherit_pid');
				if ($inherit_pid > 0 && $inherit_pid != $pid)
				{
					$inheritet_from = $this->_get_path_print($inherit_pid);
				}
					
				while($usr_ar->next()) 
				{
					$data['info']['usr'][$usr_ar->f('uid')] = array
					(
						'name' => $usr_ar->f('name'),
						'ar'   => $usr_ar->f('ar'),
					);
				}

				//-- vorhandene rechte von gruppen lesen (mit join, um namen zu kriegen)
				$sql = 'SELECT ar.*, grp.name FROM '. $this->tbl_grp_ar.' AS ar, '.$this->tbl_grp.' AS grp  WHERE ar.mid='.$mid.' AND ar.pid='.$pid.' AND ar.gid=grp.id';
				$grp_ar = $this->DB->query($sql);

				//-- wurden die rechte von anderer seite geerbet ?
				$inherit_pid = (int)$grp_ar->f('inherit_pid');
				if ($inheritet_from == '' && $inherit_pid > 0 && $inherit_pid != $edit_pid)
				{
					$inheritet_from = $this->_get_path_print($inherit_pid);
				}
					
				while($grp_ar->next()) 
				{
					$data['info']['grp'][$grp_ar->f('gid')] = array
					(
						'name' => $grp_ar->f('name'),
						'ar'   => $grp_ar->f('ar'),
					);
				}
				
				//-- modul-info lesen
				
				return $data;
				
			}

			function interface_ar_inherit_get($pid=null)
			{
				if(!$pid)
				{
					return;
				}
				//-- select zum 'runter-vererben'/'anwenden auf' bauen
				// dazu lesen wir den subtree ab (und inkl.) der seite ein
				// wir muessen 'per hand' schauen, wieviele ebenen tief es geht, 
				// und wieviele seiten jeweils pro ebene existieren
				$set = new NestedSet();
				$set->set_table_name($this->mod_tbl);
				$sub = $set->getSubtree($pid, 'id');
				
				$level = array();
				foreach($sub as $node) $level[$node['level']]++;
				
				// wir wissen jetzt folgendes:
				// (zu beachten ist, dass die aktuelle seite in diesem baum auch drin ist (zu oberst))
				// die anzahl der ebenen-1 ergibt die anzahl der darunterliegenden ebenen
				// die anzahl der seiten, die sich aendern wird auf-addiert
				$select_array = array();
				$cnt = $page_cnt = 0;

				foreach($level as $lev => $nr) 
				{
					$val = '';
					switch($cnt) 
					{
						case '0': $val = e::o('v_pa_1'); break;
						case '1': $page_cnt += $nr; $val = e::o('v_pa_2');  break;
						default:  $page_cnt += $nr; $val = e::o('v_pa_3',array('%cnt%'=>$cnt));  break;
					}
					if ($cnt > 0) 
					{
						$pl_sl = $page_cnt > 1 ? true : false;
						$val .= e::o('v_pa_4',array('%cnt%'=>$page_cnt),$pl_sl);
					}
					$select_array[$cnt] = $val;
					$cnt++;
				}

#							<td colspan="2"><input type="button" name="_save" value="'.e::o('save_for').'" onClick="this.form.event.value=\'pa_ar_save\';this.form.submit();"></td>
				
				$ret .= '
					<table class="table">
						<tr>
							<td colspan="2"><input type="button" name="_save" value="'.e::o('save_for').'" onClick="this.form.event.value=\'pa_ar_save\';this.form.submit();"></td>
							<td colspan="2">
								<button onClick="this.form.event.value=\'pa_ar_save\';this.form.submit();" class="btn btn-primary">'.e::o('save_for').'</button>
							<td>
								<select name="data[inherit]" size="1">';
								foreach($select_array as $key => $val) 
								{
									$ret .= '<option value="'.$key.'">'.htmlspecialchars($val).'</option>';
								}
								$ret .= '</select>
							</td>
						</tr>
					</table>
				';	
				
				return $ret;
			}
			
			/**
			* editieren der zugriffsrechte fuer ein modul 
			*/
			function pa_ar_DEP() {


				$edit_pid  = (int)UTIL::get_post('edit_pid');
				$data_form = UTIL::get_post('data');

				$mid      = (int)$data_form['mid'];
				
				$data     = $this->get_var('data');
				#UTIL::debug();
				//-- haben wir keine edit-id oder modul-name, gibts eine seiten-tabelle zur auswahl
				if (!$edit_pid || !$mid) return $this->pa();
				
				$this->set_var('edit_pid', $edit_pid);
				$this->set_var('mid', $mid);
				
				$inheritet_from = '';
				
								
				//-- wurden wir 'frisch' aufgerufen, lesen wir die rechte aus db
				if (!is_array($data)) {

					//-- vorhandene rechte von usern lesen (mit join, um namen zu kriegen)
					$sql = 'SELECT ar.*, usr.usr as name FROM '. $this->tbl_usr_ar.' AS ar, '.$this->tbl_usr.' AS usr '.
								' WHERE ar.mid='.$mid.' AND ar.pid='.$edit_pid.' AND ar.uid=usr.id';
					$usr_ar = $this->DB->query($sql);

					//-- wurden die rechte von anderer seite geerbet ?
					$inherit_pid = (int)$usr_ar->f('inherit_pid');
					if ($inherit_pid > 0 && $inherit_pid != $edit_pid) $inheritet_from = $this->_get_path_print($inherit_pid);
					
					while($usr_ar->next()) {
						$data['info']['usr'][$usr_ar->f('uid')] = array(
							'name' => $usr_ar->f('name'),
							'ar'   => $usr_ar->f('ar'),
						);
					}

					//-- vorhandene rechte von gruppen lesen (mit join, um namen zu kriegen)
					$sql = 'SELECT ar.*, grp.name FROM '. $this->tbl_grp_ar.' AS ar, '.$this->tbl_grp.' AS grp '.
								' WHERE ar.mid='.$mid.' AND ar.pid='.$edit_pid.' AND ar.gid=grp.id';
					$grp_ar = $this->DB->query($sql);

					//-- wurden die rechte von anderer seite geerbet ?
					$inherit_pid = (int)$grp_ar->f('inherit_pid');
					if ($inheritet_from == '' && $inherit_pid > 0 && $inherit_pid != $edit_pid) $inheritet_from = $this->_get_path_print($inherit_pid);
					
					while($grp_ar->next()) {
						$data['info']['grp'][$grp_ar->f('gid')] = array(
							'name' => $grp_ar->f('name'),
							'ar'   => $grp_ar->f('ar'),
						);
					}
				}
				
				$this->set_var('inheritet_from', $inheritet_from);
				
				//-- modul-info lesen
				$mod_info = $this->DB->query('SELECT * FROM '.$this->tbl_module.' WHERE id='.$mid);

				
				//-- select zum 'runter-vererben'/'anwenden auf' bauen
				// dazu lesen wir den subtree ab (und inkl.) der seite ein
				// wir muessen 'per hand' schauen, wieviele ebenen tief es geht, 
				// und wieviele seiten jeweils pro ebene existieren
				$set = new NestedSet();
				$set->set_table_name($this->mod_tbl);
				$sub = $set->getSubtree($edit_pid, 'id');
				
				$level = array();
				foreach($sub as $node) $level[$node['level']]++;
				
				// wir wissen jetzt folgendes:
				// (zu beachten ist, dass die aktuelle seite in diesem baum auch drin ist (zu oberst))
				// die anzahl der ebenen-1 ergibt die anzahl der darunterliegenden ebenen
				// die anzahl der seiten, die sich aendern wird auf-addiert
				$select_array = array();
				$cnt = $page_cnt = 0;
/*				foreach($level as $lev => $nr) {
					$val = '';
					switch($cnt) {
						case '0': $val = 'diese Seite'; break;
						case '1': $page_cnt += $nr; $val = 'diese Seite + 1 Ebene tiefer';  break;
						default:  $page_cnt += $nr; $val = 'diese Seite + '.$cnt.' Ebenen tiefer';  break;
					}
					if ($cnt > 0) {
						$val .= ' ('.$page_cnt.' '.(($page_cnt > 1) ? 'Seiten' : 'Seite').' betroffen)';
					}
					$select_array[$cnt] = $val;
					$cnt++;
				}*/
				foreach($level as $lev => $nr) {
					$val = '';
					switch($cnt) {
						case '0': $val = e::o('v_pa_1'); break;
						case '1': $page_cnt += $nr; $val = e::o('v_pa_2');  break;
						default:  $page_cnt += $nr; $val = e::o('v_pa_3',array('%cnt%'=>$cnt));  break;
					}
					if ($cnt > 0) {
						$pl_sl = $page_cnt > 1 ? true : false;
						$val .= e::o('v_pa_4',array('%cnt%'=>$page_cnt),$pl_sl);
					}
					$select_array[$cnt] = $val;
					$cnt++;
				}
				
				//-- werte fuer template setzen
				$this->set_var('data', $data);
				$this->set_var('mod_info', $mod_info);
				$this->set_var('select_array', $select_array);
				$this->set_var('path', $this->_get_path_print($edit_pid));
				
				$this->OPC->generate_view($this->tpl_dir . 'pa_ar.php');
			}
			
			
			
			/**
			* neue seite anlegen
			*/
			function pa_enter() {
				$form = MC::create('form');
				$form->init('page', 'mod_page');
				
				// damit wir felder 'vorbelegen' koennen, mergen wir data von get/post dazu
				$a = UTIL::get_post('data');
				$b = $this->get_var('data');
				if(is_array($a)&&is_array($b))
				{
					$data = array_merge($a,$b);
				}
				elseif(is_array($a))
				{
					$data = $a;
				}
				elseif(is_array($b))
				{
					$data = $b;
				}
				
				$form->set_values($data);
				
				// hatten wir fehler
				if ($this->get_var('error')) 
				{
					$form->set_error_fields($this->get_var('error'));
				}

				// seitenbaum-select 'per hand'
				$set = new NestedSet();
				$set->set_table_name($this->mod_tbl);
				$items = array();
				foreach($set->getNodes($this->root_id) as $node) {
					$items[$node['id']] = str_repeat('&nbsp;', ($node['level']-1)*4).$node['name'];
				}
				
				$form->add_field('parent_pid', e::o('v_subpage_of'), array(
											'type'  => 'select', 
											'empty' => false,
											'size'  => 1,
											'multi' => false,
											'min'   => 1,
											'items' => $items,
											'descr' => e::o('v_inherit_desc'),
				));
				
				$this->set_var('form', $form);
				
				$this->show('pa_enter');

			}

			
			/**
			* stellt den kompletten pfad zur seite dar (view)
			*/
			function show_path($pid = null) {
				if ($pid === null) $pid = $this->pid;
				$this->set_var('page_path', $this->_get_path($pid));
				
				$path_array = $this->_get_path($pid);
				$path = e::o('pa_path_edit_page');

				if (is_array($path_array)) {
					foreach($path_array as $node) {
						$path .=  '/' . $node['name'];
					}
				}
				return $path;
				
				#$this->OPC->generate_view($this->tpl_dir . 'pa_path.tpl');
			}
			
			/**
			* liefert den pfad ab einer seite nach oben als array
			*
			*@param	int	$pid
			*@return	array
			*@access	private
			*/
			function _get_path($pid) {
				$set = new NestedSet();
				$set->set_table_name($this->mod_tbl);
				return $set->getPath($pid);
			}
			
			/**
			* liefert den pfad ab einer seite nach oben als string
			*
			*@param	int	$pid
			*@return	string
			*@access	private
			*/
			function _get_path_print($pid) {
				$path = $this->_get_path($pid);
				if (is_error($path)) return $path->txt;
				
				$ret = '';
				foreach($path as $node) $ret .= '/'.$node['name'];
				return $ret;
			}
					
					
			/**
			*	admin panel
			*/
			function admin_panel() 
			{
				if(!$this->MC->acl_access('mod_page','edit',$this->pid))
				{
					return;
				}
				return $this->show('pap');
			}
			
			
				
				/**
				*	show_tree is a switch to different trees
				*/
				function show_tree(){
					switch(LINK_TYPE){
						case "static":
							return $this->show_tree_static();
						break;
						case "burc":
						case "dynamic":
						default:
							return $this->show_tree_dynamic();
					}
				}
				
/**
*	dynamic tree
*/
				
				function show_tree_dynamic() {
					include_once(CONF::inc_dir().'/system/nested_sets.inc.php');
					
					$root_id = (int)$this->root_id;
					
					$set = new NestedSet();
					$set->set_table_name($this->mod_tbl);
					$nodes = $set->getNodes($root_id);
					
					if (is_error($nodes)) {
						$tree = 'FEHLER:'.$nodes->txt;
					}
					else {
						$tree = $nodes;
					}

					$uid  = $this->CLIENT->usr['id'];
					$gids = array_keys($this->CLIENT->usr['groups']);

					$sql = 'SELECT id FROM '.$this->tbl_acl.
							' WHERE ((aid='.$uid.' AND type='.JIN_ACL_TYPE_USER.') OR (aid IN ('.implode(',', $gids).') AND type='.JIN_ACL_TYPE_GROUP.') )'.
							' AND (ar & 1) = 1';
					$res = $this->DB->query($sql);
					
					$allowed_ids = array();
					while($res->next()) $allowed_ids[(int)$res->f('id')] = true;

					// seiten entfernen, die nicht erlaubt sind
					foreach($nodes as $nid => $node) {
						if ($allowed_ids[$node['id']] !== true) unset($tree[$nid]);
					}
					
					
					$this->OPC->set_var('page', 'tree', $tree);
					
					$this->OPC->generate_view($this->tpl_dir.'tree.php');

				}
				/**
					static tree call
				*/
				
				function show_tree_static() {
					include_once(CONF::inc_dir().'/system/nested_sets.inc.php');
					
					$root_id = (int)$this->root_id;
					
					$set = new NestedSet();
					$set->set_table_name($this->mod_tbl);
					$nodes = $set->getNodes($root_id);
					
					if (is_error($nodes)) {
						$tree = e::o('v_error').$nodes->txt;
					}
					else {
						$tree = $nodes;
					}
					//-- mal sehen, ob der user alle seiten sehen darf ...
					$uid  = $this->CLIENT->usr['id'];
					$gids = array_keys($this->CLIENT->usr['groups']);

					$sql = 'SELECT id FROM '.$this->tbl_acl.
							' WHERE ((aid='.$uid.' AND type='.JIN_ACL_TYPE_USER.') OR (aid IN ('.implode(',', $gids).') AND type='.JIN_ACL_TYPE_GROUP.') )'.
							' AND (ar & 1) = 1';
					$res = $this->DB->query($sql);
					
					$allowed_ids = array();
					while($res->next()) $allowed_ids[(int)$res->f('id')] = true;

					// seiten entfernen, die nicht erlaubt sind
					foreach($nodes as $nid => $node) {
						if ($allowed_ids[$node['id']] !== true){
							unset($tree[$nid]);
						}
						else{
							//get the path
							$paths[$node['id']] = $this->_get_path_print($node['id']);
						}
					}

					$this->OPC->set_var('page', 'tree', $tree);

					//before we call the template react on link_type

					$this->OPC->set_var('paths', 'tree', $paths);

					$this->OPC->generate_view($this->tpl_dir.'tree_static.php');

				}
				
				//-------------------------------------//
				/**
				*	generate view
				*/
                function generate_page($pid = null) {
					if ($pid === null) $pid = (int)UTIL::getPost('pid');

						if (!(int)$pid) $pid = $this->default_pid;

                        $db = &DB::singleton();

                        $res = $db->query("SELECT * FROM " . $this->mod_tbl . 
											" WHERE id = ".(int)$pid);
						
						if(is_error($res) || $res->nr() == 0 || $res->f('template_name') == '') {
							$template_name = 'fehler.tpl';
						}else{
							$template_name = $res->f('template_name');
							
							$_GET['pid'] = $pid;	//_LOOK
							$this->OPC->set_pid($pid);
							
							
							$this->OPC->set_var('page','pid_',$pid);
							
							$this->OPC->lnk_add('pid', $pid);
							//$this->OPC->struct() = unserialize($res->f('structure'));
							$this->OPC->set_struct(unserialize($res->f('structure')));
						}
						
						#$this->OPC->set_var('page', 'title', $res->f('name').' ['.$res->f('template_name').']');                                                                  

						$this->OPC->set_var('page', 'title', $res->f('title'));
						$this->OPC->set_var('page','cookie_name',$res->f('cookie_name'));
						$this->OPC->set_var('page','cookie_value',$res->f('cookie_value'));
						$this->OPC->set_var('page','cookie_lifetime',$res->f('cookie_lifetime'));
						$this->OPC->set_var('page','redirect_to',$res->f('redirect_to'));
						$this->OPC->set_var('page','template_name',$res->f('template_name'));
						$this->OPC->set_var('page', 'keywords', $res->f('keywords'));
						$this->OPC->set_var('page', 'description', $res->f('description'));
						
	                	if ($res->f('cookie_name') && $res->f('cookie_value'))
						{
							if ( ! $_COOKIE[$res->f('cookie_name')] )											
							{
								$c_value = $res->f('cookie_value');							
								$c_name = $res->f('cookie_name');
								$c_lifetime = time()+($res->f('cookie_lifetime')*24*60*60);
								setcookie($c_name,$c_value,$c_lifetime,"/",substr(CONF::baseurl(),7));
							}
						}
						
						//redirect if necessary
						$redirect_to = $res->f('redirect_to');
						if ( $redirect_to != '' && $redirect_to != 1 && $redirect_to != 0)
						{
							header("HTTP/1.1 301 Moved Permanently");
							header("Location: ".CONF::baseurl().$this->OPC->lnk(array('pid'=>$redirect_to)));
							header("Connection: close");
							die;
						}
						
						//return $this->OPC->generate_view($this->tpl_dir . $template_name);
						
						
						$this->OPC->generate_view($this->templates_dir . $template_name);
						
						// write the new struct back to page if changes have ocurred
												
						if(serialize($this->OPC->struct()) != $res->f('structure')){
							$this->OPC->clean_struct($this->OPC->struct());
							$update = "UPDATE ".$this->mod_tbl." SET structure = '".serialize($this->OPC->struct())."' WHERE id = '$pid'";
							$db->query($update);
						}
						
						return;

                }
				
        }
?>
