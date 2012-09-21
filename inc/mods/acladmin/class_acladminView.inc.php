<?php

        /**
        * klasse zum generieren eines views fuer verwaltung von acl-listen
		*
		* acl-listen beschraenken die zugriffsrechte auf einzelne datensaetze einer tabelle
		* ist die tabelle sind die datansaetze in der tabelle in einer baumstruktur organisiert,
		* ist eine vererbung moeglich 
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		mods
*/

        class acladmin_view extends modView{

			var $view;
			
			var $tpl_dir;
			var $tpl_dir_change_dir;
			
			
			var $mod_name = 'acladmin';
			
			var $mod_tbl = 'mod_page';
			
			var $tbl_module = 'sys_module';
			var $tbl_usr_ar = 'mod_page_mod_usr_ar';
			var $tbl_grp_ar = 'mod_page_mod_grp_ar';
			var $tbl_grp    = 'mod_usradmin_grp';
			var $tbl_usr    = 'mod_usradmin_usr';
			var $tbl_acl    = 'mod_page_acl';
			
			var $vid = '';
			
			/**
			* root-seiten-id des baumes
			*/
			var $root_id = 1;
			
			function acladmin_view() {
				$this->tpl_dir = CONF::inc_dir() . '/mods/acladmin/tpl/';
			}
			


			function main($vid, $method_name) 
			{
				$this->vid = $vid;
				$this->set_var('vid', $vid);

				switch(strtolower($method_name)) {
					case 'acl_list':	$this->acl_list();	break;
				}
				
			}
				
				
			
			/**
			* editieren der zugriffsrechte eines datensatzes einer tabelle
			*
			* was brauchen wir dazu:
			*	- tabellenname
			*	- id des satzes (name des id feldes ?)
			*/
			function acl_list() 
			{

				$edit_id  = (int)UTIL::get_post('edit_id');

				if(!$edit_id)
				{
					$edit_id = $this->data['pid'];
				}
				// page select
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
				$f->use_layout("red");
				$f->set_values(array("pid"=>$edit_id));
				$f->init("pidlist",$conf);
				$f->add_hidden("mod",$this->mod_name);
				$f->add_hidden("data[tbl]",$this->data['tbl']);
				$f->add_hidden("event","pa_type");
				$content = $f->show();

				$this->set_var("page_select",&$content);

				$data      = $this->get_var('data');		// data wird von action-klasse gesetzt
				#$edit_id   = (int)UTIL::get_post('edit_id');
				$data_form = UTIL::get_post('data');

				if (!$edit_id) return 'no edit-id';
				$this->set_var('edit_id', $edit_id);
				
				// welche tabelle
				$table = $data_form['tbl'];
				$this->set_var('tbl', $table);
				
				$table_conf = $this->MC->table_config($table);
				
				
				$is_tree    = (bool)$table_conf['table']['tree'];
				$acl_table  = $table_conf['table']['name'].'_acl';
				
				
				$inheritet_from = '';
				
				//-- wurden wir 'frisch' aufgerufen, lesen wir die rechte aus db
				if (!is_array($data)) {
					//-- vorhandene rechte lesen 
					$sql = 'SELECT * FROM '. $acl_table.' WHERE id='.$edit_id;

					$acl = $this->DB->query($sql);



					//-- wurden die rechte von andererm datensatz geerbet ?
					if ($is_tree) {
						$inherit_pid = (int)$acl->f('inherit_pid');
						if ($inherit_pid > 0 && $inherit_pid != $edit_pid) $inheritet_from = $this->_get_path_print($table, $inherit_pid);
					}
					

					$known_ids = array();
					while($acl->next()) {
						switch($acl->f('type')) {
							case JIN_ACL_TYPE_GROUP: $key = 'grp'; break;
							case JIN_ACL_TYPE_USER:  $key = 'usr'; break;
						}
						$known_ids[$key][] = $acl->f('aid');
						$data['info'][$key][$acl->f('aid')] = array(
							'name' => '',
							'ar'   => $acl->f('ar'),
						);
					}
					
					// noch die namen zusammenklauben
					if (is_array($known_ids['grp'])) {
						$grp = $this->DB->query('SELECT id, name FROM '.$this->tbl_grp.' WHERE id IN ('.implode(',', $known_ids['grp']).')');
						while($grp->next()) {
							$data['info']['grp'][$grp->f('id')]['name'] = $grp->f('name');
						}
					}
					if (is_array($known_ids['usr'])) {
						$usr = $this->DB->query('SELECT id, usr FROM '.$this->tbl_usr.' WHERE id IN ('.implode(',', $known_ids['usr']).')');
						while($usr->next()) {
							$data['info']['usr'][$usr->f('id')]['name'] = $usr->f('usr');
						}
					}

				}
				
				$this->set_var('inheritet_from', $inheritet_from);
				
				if ($is_tree) 
				{
					//-- select zum 'runter-vererben'/'anwenden auf' bauen
					// dazu lesen wir den subtree ab (und inkl.) des datensatzes ein
					// wir muessen 'per hand' schauen, wieviele ebenen tief es geht, 
					// und wieviele datensaetze jeweils pro ebene existieren
					$set = new NestedSet();
					$set->set_table_name($table);
					$sub = $set->getSubtree($edit_id, 'id');
					
					$level = array();
					foreach($sub as $node) $level[$node['level']]++;
					
					// wir wissen jetzt folgendes:
					// (zu beachten ist, dass der aktuelle datensatz in diesem baum auch drin ist (zu oberst))
					// die anzahl der ebenen-1 ergibt die anzahl der darunterliegenden ebenen
					// die anzahl der seiten, die sich aendern wird auf-addiert
					$select_array = array();
					$cnt = $page_cnt = 0;
					foreach($level as $lev => $nr) 
					{
						$val = '';
						switch($cnt) 
						{
							case '0': $val = e::o('v_this_entry'); break;
							case '1': $page_cnt += $nr; $val = e::o('v_this_entry').e::o('v_level_below');  break;
							default:  $page_cnt += $nr; $val = e::o('v_this_entry').e::o('v_level_below',array('%cnt%'=>$cnt),true);  break;
						}
						if ($cnt > 0) {
							$val .= e::o('v_entries_changed',array('%page_cnt%'=>$page_cnt),(($page_cnt > 1) ? true : false));
						}
						$select_array[$cnt] = $val;
						$cnt++;
					}
				}
					
				
				//-- acl-config-datei auslesen
				$acl_conf = $this->MC->acl_config($table);

				if (is_error($acl_conf)) 
				{
					echo e::o('v_no_acl_found',array('%table%',$table));
					return;
				}

				//-- werte fuer template setzen
				

				$this->set_var('data', $data);
				$this->set_var('select_array', $select_array);
				$this->set_var('acl_conf', $acl_conf);
				$this->set_var('table_conf', $table_conf);
				$this->set_var('is_tree', $is_tree);
				
				$this->show('acl_list');

				//$this->OPC->generate_view($this->tpl_dir . 'acl_list.php');
			}
			

			
			/**
			* stellt den kompletten pfad zur seite dar (view)
			*/
			function show_path($pid) {
				$this->set_var('page_path', $this->_get_path($pid));
				$this->OPC->generate_view($this->tpl_dir . 'pa_path.php');
			}
			
			/**
			* liefert den pfad ab einem datensatz  nach oben als array
			*
			*@param	int	$pid
			*@return	array
			*@access	private
			*/
			function _get_path($table_name, $id) {
				$set = new NestedSet();
				$set->set_table_name($table_name);
				return $set->getPath($id);
			}
			
			/**
			* liefert (fuer hierarschiche datensaetze) den pfad ab einer satz nach oben als string
			*
			*@param	int	$pid
			*@return	string
			*@access	private
			*/
			function _get_path_print($table_name, $id) {
				$path = $this->_get_path($table_name, $id);
				if (is_error($path)) return $path->txt;
				
				$ret = '';
				foreach($path as $node) $ret .= '\\'.$node['name'];
				return $ret;
			}

        }
?>
