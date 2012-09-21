<?php
        /**
        * action-klasse
        *
        * wird (wohl) von MC::call_action() instanziert
		* 
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		mods
*/
       

        class acladmin_action extends modAction {

                var $action;

				var $mod_name = 'acladmin';
				var $vid = '';

				var $tbl_usr_ar = 'mod_page_mod_usr_ar';
				var $tbl_grp_ar = 'mod_page_mod_grp_ar';

				var $tbl_grp    = 'mod_usradmin_grp';
				var $tbl_usr    = 'mod_usradmin_usr';
				
				var $tbl_acl    = 'mod_page_acl';
				
				var $mod_tbl = 'mod_page';
				
				function acladmin_action() {
					$this->OPC = &OPC::singleton();
				}
				
                /*
                * 'verteiler-funktion' wird von MC::call_action() aufgerufen
                *
                *@param        array        $action
                *@return
                */
                function main($action, $params) {                	
                	
					// da wir keine seite brauchen, setzen wir den start-view
					$this->set_start_view('acl_list');

					$this->vid = UTIL::get_post('vid');

					$event = (is_array($action)) ? $action['event'] : $action;
					
					switch($event) {
						case 'acl_add':		$this->action_acl_add(); 									break;
						case 'acl_del':		$this->action_acl_del(); 									break;
						case 'acl_save':	$this->action_acl_save(); 									break;
						
						case 'acl_sql':		return $this->action_acl_sql($params); 									break;
						case 'acl_check':	return $this->action_acl_check($params); 									break;
						case 'acl_list':	$this->set_start_view('acl_list');										break;
						default:
					
					}

                }
	// funktionen

				/**
				* benutzer/gruppen wurden entfernt
				*	deprecated since 2004 08 03
				*/
				function action_acl_del() {
					$data = UTIL::get_post('data');

					//-- uebertragen der checkboxen in das info-array (post->info)
					$data = $this->_ar_post2info($data);
										
					//-- in data[action] steht komma-separiert key und id
					list($key, $id) = explode(',', $data['action']);
					unset($data['info'][$key][$id]);
					
					$this->set_var('data', $data);
					
					$this->set_start_view('acl_list');

				}
				
				/**
				* benutzer/gruppen wurden hinzugefuegt
				*/
				function action_acl_add() {
					$data = UTIL::get_post('data');
					

					//-- uebertragen der checkboxen in das info-array (post->info)
					$data = $this->_ar_post2info($data);
					
					//-- in form steht serialisiertes array der tabbelen und gewaehlten ids
					$add  = unserialize(stripslashes($data['action']));
					
					//-- fuer alle hinzugefuegten gruppen/benutzer, das form (info-feld) erweitern
					foreach($add as $table => $ids) {
						$key = '';
						switch($table) {
							case 'mod_usradmin_grp': $key = 'grp'; $name_field = 'name'; break;
							case 'mod_usradmin_usr': $key = 'usr'; $name_field = 'usr'; break;
						}
						foreach($ids as $id) {
							// nur hinzufuegen, wenn nicht schon da
							if (is_array($data['info'][$key][$id])) continue;
							$res_name = $this->DB->query('SELECT '.$name_field.' AS name FROM '.$this->{'tbl_'.$key}.' WHERE id='.(int)$id);
							$data['info'][$key][$id] = array(
								'name' => $res_name->f('name'),
								'ar'   => 0,
							);
						}
					}
							
					$this->set_var('data', $data);
					
					$this->set_start_view('acl_list');
				}
				
				
				/**
				* zugriffsrechte abspeichern
				*/
				function action_acl_save() {
					$data = UTIL::get_post('data');
					
					$edit_id = (int)UTIL::get_post('edit_id');

					//-- uebertragen der checkboxen in das info-array (post->info)
					$data = $this->_ar_post2info($data);

					$table = $data['tbl'];

					$table_conf = $this->MC->table_config($table);
				
					
					$is_tree    = (bool)$table_conf['table']['tree'];
					$acl_table  = $table_conf['table']['name'].'_acl';

					//-- vererben wir nach unten, haben wir eine liste von datensaetzen,
					//	fuer die wir das gleiche eintragen
					$update_pid = array();
					$inherit = (int)$data['inherit'];	// anzahl der ebenen die nach unten vererbt wird
					if ($is_tree && $inherit > 0) {
						$set = new NestedSet();
						$set->set_table_name($table);
						$sub = $set->getSubtree($edit_id, 'id,name');
						
						// alle pid's unter uns je level sammeln
						$level = array();
						foreach($sub as $node) $level[$node['level']][] = $node['id'];

						// nur die pid's der ersten X level nehmen
						$cnt = 0;
						foreach($level as $lev => $ids) {
							$update_pid = array_merge($update_pid, $ids);
							if (++$cnt > $inherit) break;
						}
					}
					else {
						$update_pid = array($edit_id);
					}
					
					//-- erstmal alle alten rechte dieses moduls auf der seite antfernen
					/**changed 20040801: only delete the ones that actually get changed!**/
#					$sql = 'DELETE FROM '. $acl_table .' WHERE id IN ('. implode(',', $update_pid) .')';
#					$this->DB->query($sql);
					//collect the groups to change
					// moved down
					/**end of changes**/					

					if (is_array($data['info']['grp'])) {
						
						$chg_grp = array(0);
						foreach($data['info']['grp'] as $key => $val){
							if($val['chg']){
								$chg_grp[] = $key;
							}
							if($val['del']){
								$chg_grp[] = $key;
							}
						}
						$chg_grp = array_unique($chg_grp);	

						$sql = 'DELETE FROM '. $acl_table .' WHERE id IN ('. implode(',', $update_pid) .') AND type = '.JIN_ACL_TYPE_GROUP.' AND aid IN ('.implode(',',$chg_grp).')';
						$this->DB->query($sql);

						$grp_sql = 'INSERT INTO ' . $acl_table . ' (id, type, aid, ar, inherit_pid) VALUES ';
						$grp_val = array();
						foreach($data['info']['grp'] as $gid => $info) {
							// only change what has to be changed!
							if(!$info['chg']){
								continue;
							}
							$gid = (int)$gid;
							$ar  = (int)$info['ar'];
							if ($ar == 0) continue;		// null-werte nicht speichern
							foreach($update_pid as $pid) $grp_val[] = "($pid, ".JIN_ACL_TYPE_GROUP.", $gid, $ar, $edit_id)";
						}
						if (count($grp_val) > 0) {
							$grp_sql .= implode(', ', $grp_val);
							$this->DB->query($grp_sql);
						}
					}

					#if (is_array($data['info']['usr']) AND is_array($data['ar']['usr'])) {
					if (is_array($data['info']['usr'])) {
																		
						$chg_usr = array(0);
						foreach($data['info']['usr'] as $key => $val){
							if($val['chg']){
								$chg_usr[] = $key;
							}
							if($val['del']){
								$chg_usr[] = $key;
							}
						}
						$chg_usr = array_unique($chg_usr);
						
						$sql = 'DELETE FROM '. $acl_table .' WHERE id IN ('. implode(',', $update_pid) .') AND type = '.JIN_ACL_TYPE_USER.' AND aid IN ('.implode(',',$chg_usr).')';
						$this->DB->query($sql);
						
						$usr_sql = 'INSERT INTO ' . $acl_table . ' (id, type, aid, ar, inherit_pid) VALUES ';
						$usr_val = array();
						foreach($data['info']['usr'] as $uid => $info) {
							// only change what has to be changed!
							if(!$info['chg']){
								continue;
							}
							$uid = (int)$uid;
							$ar  = (int)$info['ar'];
							if ($ar == 0) continue;		// null-werte nicht speichern
							foreach($update_pid as $pid) $usr_val[] = "($pid, ".JIN_ACL_TYPE_USER.", $uid, $ar, $edit_id)";
						}
						if (count($usr_val) > 0) {
							$usr_sql .= implode(', ', $usr_val);
							$this->DB->query($usr_sql);
						}
					}
					$this->set_var('msg', e::o('a_save_success').UTIL::get_js('refresh_opener()'));
				}

				
				
				/**
				* liefert acl-sql fuer eine tabelle
				*/
				function action_acl_sql($params) {
					$usr = &CLIENT::singleton();
					$uid = (int)$usr->usr['id'];
					$gid = array_keys($usr->usr['groups']);

					$orig = $params['table'];
					$tbl  = $orig.'_acl';
					
					$ret = array(
						'fields'   => ' BIT_OR('.$tbl.'.ar) AS ar',
						'join'    => ' LEFT JOIN '.$tbl.' ON '.
										$orig.'.id='.$tbl.'.id '.
										' AND ( ('.$tbl.'.type='.JIN_ACL_TYPE_USER.' AND aid='.$uid.') '.
											'OR ('.$tbl.'.type='.JIN_ACL_TYPE_GROUP.' AND aid IN ('.implode(',',$gid).') ) )',
						'group_by' => ' GROUP BY id',
					);
						
					return $ret;
				}
				
				/**
				* prueft, ob ein bestimmtes recht gesetzt ist
				*/
				function action_acl_check($params) 
				{
					$table      = $params['table'];
					$right      = $params['right'];
					$ar         = $params['ar'];
					$allow_empty = (int)$params['allow_empty'];

					if ($allow_empty && $ar == 0) return true;

					$conf = $this->MC->acl_config($table);
					
					$nr = $conf['rights'][$right];
					
					
					$tmp = $ar & $nr;
					
					return ($ar & $nr) == $nr;
				}
				
				
				/**
				* uebertragen der checkboxen in das info-array (post->info)
				*
				*@access	private
				*/
				function _ar_post2info($data) {
					if($this->CLIENT->usr['id'] == $this->CLIENT->__root()){
						if (is_array($data['ar']['grp'])) {
							foreach($data['ar']['grp'] as $gid => $ar) {
								$data['info']['grp'][$gid]['ar'] = array_sum($ar);
							}
						}
						if (is_array($data['ar']['usr'])) {
							foreach($data['ar']['usr'] as $uid => $ar) {
								$data['info']['usr'][$uid]['ar'] = array_sum($ar);
							}
						}
					
						return $data;			
					}
					// make sure the user cannot add rights he hasnt got
					$access_conf = $this->MC->acl_config($data['tbl']);
					$strp_ar = array();
					foreach($access_conf['rights'] as $key => $ar){
						if(!$this->MC->acl_access($data['tbl'],$key,$this->pid)){
							$strp_ar[] = $key;
						}
					}
															

					
					if (is_array($data['ar']['grp'])) {
						foreach($data['ar']['grp'] as $gid => $ar) {
														
							$current = (int)$data['info']['grp'][$gid]['ar'];
														
							$data['info']['grp'][$gid]['ar'] = 0;
							
							foreach($ar as $key => $list_ar){
								if(!UTIL::in_array($key,$strp_ar)){
									$data['info']['grp'][$gid]['ar'] += (int)$list_ar;
								}
							}

							if(!$this->MC->acl_access($data['tbl'],$key,$this->pid)){
								$data['info']['grp'][$gid]['ar'] = $current;
							}
						}
					}
					
					
					if (is_array($data['ar']['usr'])) {
						foreach($data['ar']['usr'] as $uid => $ar) {
						
							$current = (int)$data['info']['usr'][$uid]['ar'];
														
							$data['info']['usr'][$uid]['ar'] = 0;
																				
							foreach($ar as $key => $list_ar){
								if(!UTIL::in_array($key,$strp_ar)){
									$data['info']['usr'][$uid]['ar'] += (int)$list_ar;
								}
							}
							
							if(!$this->MC->acl_access($data['tbl'],$key,$this->pid)){
								$data['info']['usr'][$uid]['ar'] = $current;
							}
							
						}
					}

					return $data;
				}
				
        }

?>
