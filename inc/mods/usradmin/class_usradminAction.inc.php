<?php
/**
*	usradmin is the core module for user and group handling
*	it is responsible for user interaction with their home
*	it is responsible for user registration
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
	
	class usradmin_action extends modAction {
	
		var $action;
		
		
		var $view_name = '';
		var $mod_name  = 'usradmin';
		
		var $tbl_usr        = 'mod_usradmin_usr';
		var $tbl_grp        = 'mod_usradmin_grp';
		var $tbl_usr_grp    = 'mm_usradmin_usr_grp';
		var $tbl_usr_online = 'mod_usradmin_usr_online';
		var $tbl_usr_profile = 'mod_usradmin_usr_profile';
		
		var $def_usr_online_pid;
		var $def_usr_home;
		
		
		
		function usradmin_action() {
			
			$def_pages = CONF::default_pages();
			
		}
		
		/*
		* 'verteiler-funktion' wird von MC::call_action() aufgerufen
		*
		*@param	array	$action
		*@return 
		*/
		function main($action, $params = '') {		

			$this->action    = $action;

			switch(strtolower($action['event'])) 
			{
				// login
				case 'login':					$this->login();							break;
				case 'logout':					$this->logout();						break;

				// add a user from outside
				case 'usr_create':				return $this->usr_create($params);						break;
				//
				case 'fields':	return $this->action_fields();		break;
				case 'edit_registerform': 		$this->set_view("edit_registerform");		break;
				case 'do_edit_registerform': 		$this->action_do_edit_registerform();		break;

				case 'edit_profileform': 		$this->set_view("edit_profileform");		break;
				case 'do_edit_profileform': 		$this->action_do_edit_profileform();		break;
				
				case 'usr_new':    $this->action_usr_new();    break;
				case 'usr_save':   $this->action_usr_save();   break;
				case 'usr_edit':   $this->action_usr_edit();   break;
				case 'usr_delete': $this->action_usr_delete(); break;
				
				case 'usr_visit':	$this->set_start_view('usr_visit',true);		break;

				case 'grp_new':    $this->action_grp_new();    break;
				case 'grp_save':   $this->action_grp_save();   break;
				case 'grp_edit':   $this->action_grp_edit();   break;
				case 'grp_delete': $this->action_grp_delete(); break;
				case 'get_groups':	return $this->action_get_groups($params); break;
				
				case 'register_form':	$this->set_view('register');	break;
				case 'register_sent':	$this->action_register_sent();	break;
				case 'register_ok':		$this->action_register_ok();	break;
				
				case 'usr_validate':	return $this->action_usr_validate($params);	break;
				case 'get_default_usr':	return $this->action_get_default_usr(); 	break;
				case 'get_users':		return $this->action_get_users($params);	break;
				
				case 'usr_home': 	$this->set_view('usr_home'); break;
				case 'usr_profile':	$this->set_var('force_action', 'usr_profile'); $this->set_view('usr_profile'); break;
				
				case 'profile_save':			$this->action_profile_save();			break;				
				case 'find_pwd':		return $this->action_find_pwd($params);	break;
				
				case 'callback_get_link': return $this->callback_get_link($params); break;
				
				case 'do_usr_search':	return $this->action_do_usr_search();		break;
				case 'usr_exists':	return $this->action_usr_exists($params);		break;
				case 'usr_email_exists':	return $this->usr_email_exists($params);		break;
				
				case 'get_file':		return $this->get_file($params);	break;
				case 'get_files':		return $this->get_files($params);	break;
				
				case 'du':			$this->du();	break;
			}
			
		}
		

		/**
		*	logs the user out
		*/
		function logout($params)
		{
			if($_COOKIE[CONF::project_name()])
			{
				setcookie(CONF::project_name(),"",-3600,"/",substr(CONF::baseurl(),7));
			}
			$this->CLIENT->unset_auth();
			$delete = "DELETE FROM ".$this->tbl_online." WHERE uid = ".$this->CLIENT->usr['id'];
			$this->DB->query($delete);
			if(!$params['quiet'])
			{
				header("Location:".CONF::baseurl());
			}
		}
		/**
		*	try and log the user on
		*/
		function login() 
		{
			$usr = trim($this->data['usr']);
			$pwd = trim($this->data['pwd']);			

			//-- login fuer usr
			$check = $this->MC->call_action(array('mod' => 'usradmin', 'event' => 'usr_validate'), $this->data);
			
			if (is_error($check)) 
			{
				forms::set_error_fields(array('usr'=>true,'pwd'=>true));
				$this->OPC->error($check->txt);
				return $this->set_start_view('login_show');
			}
			else 
			{
				setcookie(CONF::project_name(),md5($check['usr']),time()+86400,"/",substr(CONF::baseurl(),7));
				$this->CLIENT->set_auth($check);

				// we need to refresh the window.location of the opener

				// the user may have requested a specific page before logging in, so we send her there,
			   	
				$url = $this->SESS->get('after_login','url');
				if($url)
				{
					header('Location:'.$url);
					die;
				}

				$p = CONF::default_pages();
				
				// this page is not allowed to registered users usually - so we redirect them
				if($this->pid == $p['usr_register'])
				{
					if($p['after_login'])
					{         
						header('Location:'.CONF::baseurl().$this->OPC->lnk(array('pid'=>$p['after_login'])));
						//header('Location:'.CONF::baseurl()."/page_".$p['after_login'].".html?".$this->SESS->name."=".$this->SESS->id);
						die;
					}
					else
					{
						header('Location:'.CONF::baseurl().$this->OPC->lnk(array('pid'=>CONF::pid())));
						die;
					}
				}
				elseif($p['after_login'])
				{             
					header('Location:'.CONF::baseurl().$this->OPC->lnk(array('pid'=>$p['after_login'])));
					//header('Location:'.CONF::baseurl()."/page_".$p['after_login'].".html?".$this->SESS->name."=".$this->SESS->id);
					die;
				}
				else
				{
					header('Location:'.CONF::baseurl().$this->OPC->lnk(array('pid'=>$this->pid)));
					die;
				}
				ob_end_flush();
			}
		}

		function usr_create($p)
		{
			
			$v = &regex::singleton();
			if($p['optin'])
			{
				$token = md5(time().uniqid());
				if(!$p['info']['email'])
				{
					return false;
				}
				if(!$v->validate($p['info']['email'],$v->email()))
				{
					return false;
				}
			}
			
			if(!$p['info']['usr'])
			{
				if($p['info']['email'])
				{
					$p['info']['usr'] = $p['info']['email'];
				}
				else
				{
					return false;
				}
			}
			
			$select = "SELECT id FROM mod_usradmin_usr WHERE usr = '".$p['info']['usr']."' OR email = '".$p['info']['email']."'";
			$r = $this->DB->query($select);
			if($r->nr() != 0)
			{
				return;
			}
			
			$pwd = $p['info']['pwd'];
			$p['info']['pwd'] = md5($p['info']['pwd']);
			
			foreach($p['info'] as $k=>$v)
			{
				$key[] = $k;
				$val[] = "'".mysql_real_escape_string($v)."'";
			}
			
			$insert = "INSERT INTO mod_usradmin_usr (
				".implode(",",$key).",
				accept,
				register_key,
				lang,
				sys_date_created,
				sys_date_changed
			) VALUES (
				".implode(",",$val).",
				1,
				'".$token."',
				".(isset($p['lang'])?$p['lang']:1).", 
				".time().",
				".time()."
			)";

			$this->DB->query($insert);
			$uid = $this->DB->insert_id();
			
			if(is_array($p['grp']))
			{
				$insert = "INSERT INTO ".$this->tbl_usr_grp." (local_id, foreign_id) VALUES ( ".$uid.", ".implode(" ), ( ".$uid.", ",$p['grp'])." )";
				$this->DB->query($insert);
			}
			
			$usr = $this->action_usr_validate(array(
				'usr' => $p['info']['usr'],
				'pwd' => $p['info']['pwd'],
				'__force' => true,
				'__encoded' => true
			));
			
			if(is_error($usr))
			{
				return false;
			}
			// add the user to the groups
			

			
			if($p['optin'])
			{
				// send email
			}
			if($p['login'])
			{
				//$usr['register_key'] = '';
				//$this->CLIENT->set_auth($usr);
				$usr = $p['info']['usr'];
			
				$check = $this->MC->call_action(array('mod' => 'usradmin', 'event' => 'usr_validate'), $this->data);
			
				if (is_error($check)) 
				{
					forms::set_error_fields(array('usr'=>true,'pwd'=>true));
					$this->OPC->error($check->txt);
					return $this->set_start_view('login_show');
				}
				else 
				{
					setcookie(CONF::project_name(),md5($check['usr']),time()+86400,"/",substr(CONF::baseurl(),7));
					$this->CLIENT->set_auth($check);
				}
			}
			return true;
		}
		
		function du()
		{
			if($this->CLIENT->usr['du'])
			{
				// change back to me
				unset($this->CLIENT->usr['du']);
				$this->CLIENT->set_auth($this->CLIENT->usr);
			}
			else
			{
				// change to default
				$this->CLIENT->usr['du'] = true;
				$this->CLIENT->set_auth($this->CLIENT->usr);
			}
		}
		
		/**
			returns the displayable fields for users as an assoc array
		*/
		function action_fields()
		{
			$ignore = array
			(
			 	'groups' => true,
    			'lang' => true,
    			'lang_default' => true
			);
			if(is_file(CONF::inc_dir()."/etc/form/".$this->tbl_usr.".".CONF::project_name().".form"))
			{
				$tbl = $this->tbl_usr.".".CONF::project_name();
			}
			else
			{
				$tbl = $this->tbl_usr;
			}
			
			$conf = $this->MC->table_config($tbl);
			foreach($conf['fields'] as $field => $c)
			{
				if(!$ignore[$field] AND $c['cnf']['type'] != 'file')
				{
					$r[$field] = $c['label'];
				}
			}
			return $r;
			
		}
		
		function action_do_edit_registerform()
		{
			CONF::set("lang_".$this->vid,$this->data['lang']);
			CONF::set("grp_".$this->vid,implode(",",$this->data['groups']));
			CONF::set("pid_".$this->vid,$this->data['welcome_page']);
			
			$this->set_view("edit_registerform");
			
		}
		
		function action_do_edit_profileform()
		{
			CONF::set("pid_".$this->vid,$this->data['welcome_page']);	
			$this->set_view("edit_profileform");
		}
		
//-- BENUTZER ----------------------------------------------------------------
		/**
		* returns the default user
		* default user hardcoded for now - should be able to be administered
		*/
		function action_get_default_usr() {
			
			$cnf_methods = get_class_methods("CONF");
			
			$select = "SELECT usr, pwd FROM ".$this->tbl_usr." WHERE id = ".CONF::guest();

			$default = $this->DB->query($select);

			$params = $default->r();
			$params['__encoded'] = true;

			$usr = $this->action_usr_validate($params);
			
			$usr['is_default'] = true;
			
			return $usr;
		}
		/**
		*	get users
		*	params[group] - array of group ids
		*	params[where] - where clause for filtering
		*/
		function action_get_users($params=null){
			
			if(is_array($params))
			{
				$where = "WHERE id IN ('".implode("','",$params)."')";
			}
			
			$select = "SELECT * FROM ".$this->tbl_usr." ".$where;
			return $this->DB->query($select);
		}
		
		function action_get_groups($params){
			if($params)
			{
				$select = "SELECT * FROM ".$this->tbl_grp." WHERE id IN (".implode(",",$params).")";				
			}
			else
			{
				$select = "SELECT * FROM ".$this->tbl_grp;
			}
			return $this->DB->query($select);
		}
		
		/**
		*	create a new user
		*/
		function action_usr_new() 
		{
			$this->set_view('usr_form');
		}
		/**
		*	edit a user
		*/

		function action_usr_edit() 
		{
			$ids = UTIL::get_post('ids');
			if (!is_array($ids)) 
			{
				$this->set_var('error', e::o('a_please_choose_usr'));
				$this->set_view('usr_list');
				return;
			}
			
			$this->set_var('edit_id', $ids[0]);
			$this->set_view('usr_form');
		}
		/**
		*	save a user
		*/
		function action_usr_save() 
		{
			$data = UTIL::get_post('data');
			$this->OPC->lang_page_start($this->mod_name);
			
			if(is_file(CONF::inc_dir()."/etc/form/".$this->tbl_usr.".".CONF::project_name().".form"))
			{
				$tbl = $this->tbl_usr.".".CONF::project_name();
			}
			else
			{
				$tbl = $this->tbl_usr;
			}
			
			$validator = MC::create('validator', $tbl);
			
			$edit_id = (int)$data['edit_id']; 
			// schauen ob insert oder update
			if ($edit_id) 
			{
				$save_mode = 'edit';
				// edit_id mitschleifen, fuer den fehlerfall
				$this->set_var('edit_id', $edit_id);
			}
			else 
			{
				$save_mode = 'enter';
			}

			if (!$validator->is_valid($data, $save_mode)) 
			{
				$this->set_var('error', $validator->get_error_fields());
				$this->set_var('data', $data);
				$this->set_view('usr_form');
				$this->OPC->lang_page_end();
				return;
			}
			$this->OPC->lang_page_end();

			$db = &DB::singleton();
			
			// do we have another user with the same language set to default?
			// if yes, we ask the user if he is sure
			if($this->data['lang_default'])
			{
				$select = "SELECT id FROM ".$this->tbl_usr." WHERE lang = '".$data['lang']."' AND lang_default";
				$def = $db->query($select);
				// brutal method.
				// we delete all defaults for current language and set the new one
				$del = array();
				while($def->next())
				{
					$del[] = $def->f('id');
				}
				if(sizeof($del) >= 1)
				{
					$update = "UPDATE ".$this->tbl_usr." SET lang_default = 0 WHERE id IN (".implode(",",$del).")";
					$db->query($update);
				}
			}
			// continue
			// schauen ob insert oder update
			if ($save_mode == 'edit') 
			{
				$id = (int)$data['edit_id'];
				$select = "SELECT pwd FROM ".$this->tbl_usr." WHERE id = '".$id."'";
				$res = $this->DB->query($select);
				if($res->nr() != 1)
				{
					return;
				}
				if($data['pwd'] != $res->f('pwd'))
				{
					$data['pwd'] = md5($data['pwd']);
				}
				$this->DB->update($tbl, $data, array('id' => $id));
			}
			else 
			{
				$data['pid'] = $this->pid;
				$data['pwd'] = md5($data['pwd']);
				$id = $db->insert($tbl, $data);
			}
			if(!is_dir($this->usr_file_path."/".$id))
			{
				mkdir($this->usr_file_path."/".$id,0777);
			}
			$this->handle_files($id);
		}
		/**
		*	delete a user
		*/
		function action_usr_delete() {
			$ids = UTIL::get_post('ids');
			if (!is_array($ids)) {
				$this->set_var('grp_error', e::o('a_please_choose_usr'));
				$this->set_view('usr_list');
				return;
			}
			// is this a default usr?
			$default = $this->action_get_default_usr();
			if(in_array($default['id'],$ids))
			{
				$this->add_msg(e::o('cannot_delete_default_usr'));
			}
			else
			{
				$this->DB->delete($this->tbl_usr, array('id' => $ids));
				$delete = "DELETE FROM ".$this->tbl_usr_grp." WHERE local_id IN (".implode(",",$ids).")";
				$this->DB->query($delete);
			}
		}

		/**
		* gibt es einen gueltigen user mit dem uebergebenen usrname/pwd
		*
		*@param	array	$param	ass.array ('usr' => '...', 'pwd' => '...')
		*@return	array	komplette 'zeile' mit user-daten (inkl ass.array der gruppen-ids 'id' => 'name')
		*/
		function action_usr_validate($params) 
		{
			$usr = trim($params['usr']);
			$pwd = trim($params['pwd']);
			
			if($params['__encoded'])
			{
				$sql = 'SELECT * FROM '.$this->tbl_usr.
					' WHERE usr=\''.addslashes($usr).'\' AND pwd=\''.addslashes($pwd).'\' AND NOT sys_trashcan';
			}
			elseif($params['uid'])
			{
				$sql = 'SELECT * FROM '.$this->tbl_usr.' WHERE id='.$params['uid'].' AND NOT sys_trashcan';
				
			}
			else
			{
				$sql = 'SELECT * FROM '.$this->tbl_usr.
					' WHERE usr=\''.addslashes($usr).'\' AND pwd=\''.addslashes(md5($pwd)).'\' AND NOT sys_trashcan';
			}
			
			
			$res = $this->DB->query($sql);
			
			if(@$params['uid'])
			{
				$usr = $res->f('usr');
				$pwd = $res->f('pwd');
				$params['__encoded'] = true;
			}


			if (is_error($res)) {
				return new Error(e::o('a_err_pwd'));
			}

			$found_usr = null;
			while($res->next()) 
			{
				if ((string)$res->f('usr') === (string)$usr &&  ( (string)$res->f('pwd') === (string)$pwd OR (string)$res->f('pwd') === (string)md5($pwd) ) ) {
					
					if(!@$params['__force'])
					{
						// ist er 'freigeschaltet'
						if (trim($res->f('register_key')) != '') return new Error(e::o('a_usr_not_free_yet'));
					}
					
					$found_usr = $res->r();
					$uid = (int)$found_usr['id'];
					//-- zusaetzlich noch die zugehoerigen gruppen auslesen
					$sql = 'SELECT grp.id, grp.name '.
							'FROM '.$this->tbl_grp.' AS grp, '.$this->tbl_usr_grp.' AS mm '.
							'WHERE mm.local_id='.$uid.' AND mm.foreign_id=grp.id';
					$res = $this->DB->query($sql);
					$groups = array();
					while($res->next()) $groups[$res->f('id')] = $res->f('name');
					$found_usr['groups'] = $groups;
				}
			}
			if ($found_usr === null) {
				return new Error(e::o('a_err_pwd'));
			}
			else 
			{
				return $found_usr;
			}
		}

//-- GRUPPEN ----------------------------------------------------------------
		/**
		*	create a new group
		*/
		function action_grp_new() {
			$this->set_view('grp_form');
		}
		/**
		*	edit a group
		*/
		function action_grp_edit() {
			$ids = UTIL::get_post('ids');
			if (!is_array($ids)) {
				$this->set_var('grp_error', e::o('a_please_choose_grp'));
				$this->set_view('grp_list');
				return;
			}
			
			$this->set_var('edit_id', $ids[0]);
			$this->set_view('grp_form');
		}
		
		/**
		*	save a group
		*/
		function action_grp_save() {

			$data = UTIL::get_post('data');
			$this->OPC->lang_page_start($this->mod_name);
			$validator = MC::create('validator', 'mod_usradmin_grp');
			
			$edit_id = (int)$data['edit_id'];
			// schauen ob insert oder update
			if ($edit_id) {
				$save_mode = 'edit';
				// edit_id mitschleifen, fuer den fehlerfall
				$this->set_var('edit_id', $edit_id);
			}
			else {
				$save_mode = 'enter';
			}

			if (!$validator->is_valid($data, $save_mode)) {
				$this->set_var('grp_error', $validator->get_error_fields());
				$this->set_var('data', $data);
				$this->set_view('grp_form');
				$this->OPC->lang_page_end();
				return;
			}
			$this->OPC->lang_page_end();
			
			// schauen ob insert oder update
			if ($save_mode == 'edit') {
				$sql = 'UPDATE '. $this->tbl_grp .' '.
					'SET name=\''.addslashes($data['name']).'\', 
					lang = '.$data['lang'].' '.
					'WHERE id='.(int)$data['edit_id'];
			}
			else {
				$sql = 'INSERT INTO '. $this->tbl_grp .' (pid, name, lang) '.
						'VALUES ('.$this->pid.', \''.addslashes($data['name']).'\', \''.$data['lang'].'\')';
			}
			
			$db = &DB::singleton();
			$db->query($sql);
		}
		/**
		*	delete group
		*/
		function action_grp_delete() {
			$ids = UTIL::get_post('ids');
			if (!is_array($ids)) {
				$this->set_var('grp_error', e::o('a_please_choose_grp'));
				$this->set_view('usr_list');
				return;
			}
			
			$this->DB->delete($this->tbl_grp, array('id' => $ids));
		}

		
//-- online registrierung
		/**
		* anmelde formular wurde gesendet
		*/
		function action_register_sent() 
		{
			   
			if(is_file(CONF::inc_dir()."/etc/form/".$this->tbl_usr_online.".".CONF::project_name().".form"))
			{
				$tbl = $this->tbl_usr_online.".".CONF::project_name();
			}
			else
			{
				$tbl = $this->tbl_usr_online;
			}

			$data = UTIL::get_post('data');
			$this->OPC->lang_page_start($this->mod_name);
			$validator = MC::create('validator', $tbl);
			
			if (!$validator->is_valid($data, $enter)) 
			{
				$this->set_var('error', $validator->get_error_fields());
				$this->set_var('data', $data);
				$this->set_view('register_form');
				$this->OPC->lang_page_end();
				return;
			}
			$this->OPC->lang_page_end();

			$db = &DB::singleton();

			//$data['pid'] = $this->pid;
			$data['pid']           = (int)$this->def_usr_online_pid;
			$data['register_key'] = md5(uniqid(rand()));
			$__tmp_pwd = $data['pwd'];
			$data['pwd'] = md5($data['pwd']);
			if($l = CONF::get("lang_".$this->vid))
			{                                       
				$data['lang'] = CONF::get("lang_".$this->vid);
			}
			// user abspeichern
			
			$grp = CONF::get("grp_".$this->vid);
			
			$data['groups'] = explode(",",$grp);
			
			$uid = $db->insert($tbl, $data);
			
			// neuen datei-ordner fuer user anlegen
			if(!is_dir(CONF::usr_dir()."/".$uid)){
				mkdir(CONF::usr_dir()."/".$uid,0777);
			}
			$data['pwd'] = $__tmp_pwd;
			$this->set_var('data', $data);
			
				$pid = CONF::get("pid_".$this->vid);
				
				if(!$pid)
				{
					$p = CONF::default_pages();
					$pid = $p['usr_register'];
				}
			
			$this->set_var("pid",$pid);
			

			
			$this->OPC->set_var("usradmin","registered",true);
			
			$this->set_view('register_sent');
		}
		/**
		*	user sent the key back
		*/
		function action_register_ok()
		{	
			$register_key = UTIL::get_post('register_key');
			$update = "UPDATE ".$this->tbl_usr." SET register_key = null WHERE register_key = '".$register_key."'";
			$this->DB->query($update);
			$this->set_view('register_ok');
			$this->set_var('force_action','register_ok');
		}
		
		function handle_files($uid)
		{
			if(!$uid)
			{
				return;
			}
			$path = CONF::asset_dir()."/usr_home/".$uid;
			
			if(is_file(CONF::inc_dir()."/etc/form/".$this->tbl_usr_profile.".".CONF::project_name().".form"))
			{
				$tbl = $this->tbl_usr_profile.".".CONF::project_name();
			}
			else
			{
				$tbl = $this->tbl_usr_profile;
			}
			
			$conf = $this->MC->table_config($tbl);
			
			$files = $this->get_files(array('uid'=>$uid));
			
			foreach($_FILES as $n => $f)
			{
				if($f['error'])
				{
					continue;
				}
				if($files[$n])
				{
					UTIL::delete_file(CONF::asset_dir()."/usr_home/".$uid."/".$files[$n]);
				}
				
				$parts = pathinfo($f['name']);
				
				$file = $path."/".$n.".".strtolower($parts['extension']);
				copy($f['tmp_name'],$file);
				if($conf['fields'][$n]['cnf']['modify'])
				{
					switch($conf['fields'][$n]['cnf']['modify']['type'])
					{
						case 'image':
							$cnf = $conf['fields'][$n]['cnf']['modify'];
							//	function resize($file_in,$file_out,$x_size=null,$y_size=null)
							$inf = getimagesize($file);

							if(isset($cnf['width']))
							{
								if($inf[0] != $cnf['width'] OR $inf[1] != $cnf['height'] )
								{
									if($cnf['keep_aspect'])
									{
										if($inf[0] > $inf[1])
										{
											$width = $cnf['width'];
											$height = ceil($width/($inf[0]/$inf[1]));
										}
										else
										{
											$height = $cnf['height'];
											$width = ceil($height/($inf[1]/$inf[0]));
										}
									}
									elseif($cnf['padding'])
									{
										
										
										
										$in = &$file;

										$p = pathinfo($in);	
										
										$max_image = $cnf['width'];
										$i = getimagesize($in); // 1 height
										

										
										$m = &magick::singleton();
										$tmp = $p['dirname']."/tmp1.gif";
										$target = $p['dirname']."/tmp2.gif";
										$canvas = CONF::inc_dir()."//tmp/".time().".gif";

										if($i[0]<$max_image||$i[1]<$max_image)
										{
											if($i[0] > $i[1])
											{
												$width = $max_image;
												$height = $width/($i[0]/$i[1]);
											}
											else
											{
												$height = $max_image;
												$width = $height/($i[1]/$i[0]);
											}
											$m->resize($in,$in,$width,$height);
											$i = getimagesize($in); // 1 height
											unset($height);
											unset($width);
										}

										#UTIL::delete_file($in);
												
											if($i[0] > $i[1])
											{
												$m->resize($in,$tmp,$max_image);
												$m->create_canvas($max_image,(( $max_image - ( ( $i[1] * $max_image ) / $i[0] ) ) / 2),'white',$canvas);
												$m->append_image_list(array(
													$canvas,
													$tmp,
													$canvas
												),$target);
											}
											elseif($i[0] < $i[1])
											{
												$m->resize($in,$tmp,(($max_image * $i[0])  / $i[1]),$max_image);
												$m->create_canvas((( $max_image - ( ( $i[0] * $max_image ) / $i[1] ) ) / 2),$max_image,'white',$canvas);
												$m->append_image_list(array(
													$canvas,
													$tmp,
													$canvas
												),$target,"horizontal");
											}	
											else
											{
												$m->resize($in,$target,$max_image,$max_image);
											}
											
											UTIL::delete_file($canvas);
											UTIL::delete_file($tmp);
											UTIL::delete_file($file);
											exec("mv ".$target." ".$p['dirname']."/image.gif");
											
											// create the thumb
#											$m->resize($target,$p['dirname']."/thumb.gif",$max_thumb,$max_thumb);
											$file = $p['dirname']."/image.gif";
									}
									else
									{
										$width = $cnf['width'];
										$height = $cnf['height'];
									}
									if($width AND $height)
									{
										$m = &magick::singleton();
										$m->resize($file,$file,$width,$height);
									}
									if($cnf['thumb'])
									{
										$p = pathinfo($file);	
										$m->resize($file,$p['dirname']."/thumb.gif",$cnf['thumb']['width'],$cnf['thumb']['height']);
									}
								}
							}
							
						break;
					}
				}
			}
		}
		
		function get_files($p)
		{
			if(!is_array($this->__get_files))
			{
				$path = CONF::asset_dir()."/usr_home/".$p['uid']."/";
				$out = array();
				exec("ls ".$path,$out);
				foreach($out as $f)
				{
					$parts = explode(".",$f);
					unset($parts[sizeof($parts)-1]);
					$this->__get_files[implode(".",$parts)] = $f;
				}
			}
			return $this->__get_files;
		}
		
		function get_file($p)
		{
			if(!$p['name'])
			{
				return;
			}
			if(!$p['uid'])
			{
				$p['uid'] = $this->CLIENT->usr['id'];
			}
			
			$files = $this->get_files($p);
			
			if($files[$p['name']])
			{
				return CONF::asset_url()."/usr_home/".$p['uid']."/".$files[$p['name']];
			}
			return;
		}
		
		/**
		*	let the user change parts of the profile
		*/
		function action_profile_save()
		{
			if($this->CLIENT->usr['is_default'])
			{
				$this->MC->call_action(array('event'=>'logout','mod'=>'login'));
				return;
			}
			
			if(!$this->data['staylogged'])
			{
				$this->data['staylogged'] = (bool)$this->OPC->get_var("usradmin","staylogged");
			}
			
			$this->set_view('usr_profile');			

			if(is_file(CONF::inc_dir()."/etc/form/".$this->tbl_usr_profile.".".CONF::project_name().".form"))
			{
				$tbl = $this->tbl_usr_profile.".".CONF::project_name();
			}
			else
			{
				$tbl = $this->tbl_usr_profile;
			}
			
			$this->OPC->lang_page_start($this->mod_name);
			$validator = MC::create('validator', $tbl);
			if(!$validator->is_valid($this->data, 'edit'))
			{
				$this->OPC->lang_page_end();
				return $this->set_var('error', $validator->get_error_fields());
			}
			$this->OPC->lang_page_end();

			$this->handle_files($this->CLIENT->usr['id']);
			
			if(!$this->data['staylogged'])
			{
				$register_key = (string)md5(uniqid(rand(), true));
				$this->data['register_key'] = $register_key;
			}

			if($this->data['pwd'] != $this->CLIENT->usr['pwd'])
			{
				$pwd = $this->data['pwd'];
				$this->data['pwd'] = md5($this->data['pwd']);
			}

			$this->DB->update($tbl,$this->data,array("id"=>$this->CLIENT->usr['id']));
						
			if($this->data['staylogged'] != true)
			{
				// send the mail
				$pid = CONF::get("pid_".$this->vid);
				
				if(!$pid)
				{
					$p = CONF::default_pages();
					$pid = $p['usr_register'];
				}

				$register_link = $this->OPC->lnk(array(
					'mod' => 'usradmin', 
					'event' => 'register_ok', 
					'register_key' => $register_key,
					'pid'=>$pid),null,'strict');
					
				$mail_subject = e::o('a_m_1_subject');
				$mail_to      = $this->data['email'];
				$mail_body = e::o
				(
					'a_m_1_body',
					array
					(
						'%link%' => CONF::baseurl().'/'.$register_link,
						'%username%' => $this->CLIENT->usr['usr'],
						'%password%' => $pwd
					)
				);
				/*				
				$url = substr(CONF::baseurl(),7);
				if(substr($url,0,3) == "www")
				{
					$url = substr($url,4);
				}
				if(substr($url,-1) == "/")
				{
					$url = substr($url,0,-1);
				}
				
				$headers = "From: noreply@".$url." \r\n";
				$headers .= "Content-Type: text/plain; charset=UTF-8; format=flowed\r\n";	
				
				mail($mail_to, $mail_subject, $mail_body, $headers);
				*/
				
				new oos_mail($mail_to,null, $mail_subject, $mail_body);
				
				unset($_GET);
				unset($_POST);
			
				$this->MC->call_action(array('event'=>'logout','mod'=>'login'));
			}
			else
			{
				// update client data
				$data['usr'] = $this->CLIENT->usr['usr'];
				$data['pwd'] = $this->data['pwd'];
				$data['__encoded'] = true;
				$check = $this->action_usr_validate($data);
				$this->SESS->set('client', 'usr', $check);
				$this->CLIENT->set_auth($check);
				#header("Location: ".CONF::baseurl()."/page_".$this->pid.".html?".$this->SESS->name."=".$this->SESS->id);
			}
			return;
		}
		/**
		*	try and find the password
		*	return the password on success
		*	false on error
		*/
		function action_find_pwd($params){
		
			if(strlen($params['usr']) != 0 AND strlen($params['email']) != 0){
				//both - check if that combination exists
				$select = "SELECT id, usr, pwd, email FROM ".$this->tbl_usr." WHERE usr = '".$params['usr']."' AND email='".$params['email']."'";
			}
			elseif(strlen($params['usr']) != 0){
				// dig out the email adress
				$select = "SELECT id, usr, pwd, email FROM ".$this->tbl_usr." WHERE usr = '".$params['usr']."'";
			}
			elseif(strlen($params['email']) != 0){
				// does a user exist with that emailadress
				$select = "SELECT id, usr, pwd, email FROM ".$this->tbl_usr." WHERE email='".$params['email']."'";
			}
			$res = $this->DB->query($select);
			if($res->nr() == 1){
				
				// generate a  new password
				for($i=0;$i<8;$i++)
				{
					$pwd .= chr(rand(97,122));
				}
				
				$update = "UPDATE ".$this->tbl_usr." SET pwd = '".md5($pwd)."' WHERE id = '".$res->f('id')."'";
				$this->DB->query($update);
				

				
				$mail_body = e::o('a_m_3_body',array('%username%'=>$res->f('usr'),'%password%'=>$pwd));
				$mail_subject = e::o('a_m_3_subject',array("%url%"=>$url));
				/*
				$url = substr(CONF::baseurl(),7);
				if(substr($url,0,3) == "www")
				{
					$url = substr($url,4);
				}
				if(substr($url,-1) == "/")
				{
					$url = substr($url,0,-1);
				}
				
				$headers = "From: password@".$url."\r\n";
				$headers .= "Content-Type: text/plain; charset=UTF-8; format=flowed\r\n";	
				
				@mail($res->f('email'), $mail_subject, $mail_body,$headers);
				*/
				new oos_mail($res->f('email'),null, $mail_subject, $mail_body);
				return 1;
			}
			return -1;
		}
		/**
		* link zu uns generieren
		*/
		function callback_get_link($params) 
		{
			$url = '';
			switch($params['type']) 
			{
				case 'usr_home':
					//-- link zum home eines users; user kann ueber id oder name angegeben werden
					
					$def = CONF::default_pages();
					
					if($params['uid'] == $this->CLIENT->usr['id'] OR !$params['uid'])
					{
						$p = array('mod' => 'usradmin', 'event' => 'usr_home', 'pid' => $def['usr_home']);	
					}
					else
					{
						$p = array('mod' => 'usradmin', 'event' => 'usr_home', 'pid' => $def['usr_home_visit'],'data[uid]'=>$params['uid']);
					}
					if(!$p)
					{
						if ($params['uname']) 
						{
							$p['data[uname]'] = $params['uname'];
						}
						else 
						{
							$p['data[uid]'] = ($params['uid']) ? $params['uid'] : $this->CLIENT->usr['id'];
						}
					}
					
					$url = $this->OPC->lnk($p, null, array('__sv[mod]', '__sv[method]', '__sv[vid]'));
					
					if ($url != '') return ($params['full']) ? '<a href="'.$url.'" target="mainframe">'.($params['uname']?$params['uname']:'UserHome').'</a>' : $url;		
					
				break;
					
				case 'usr_visit':
										
					if(!$params['uid'])
					{
						return;
					}
					
					if($params['img'])
					{
						$f = $this->get_file(array('name'=>$params['img'],'uid'=>$params['uid']));
						$txt[] = '<img src="'.$f.'" border="0">';
						
					}
					if($params['uname'])
					{
						$select ="SELECT usr FROM ".$this->tbl_usr." WHERE id = ".$params['uid'];
						$r = $this->DB->query($select);
						if($r->nr() != 1)
						{
							return;
						}
						$txt[] = $r->f('usr');
					}
					if($params['txt'])
					{
						$txt[] = $params['txt'];
					}
					if(!$txt)
					{
						$select ="SELECT usr FROM ".$this->tbl_usr." WHERE id = ".$params['uid'];
						$r = $this->DB->query($select);
						if($r->nr() != 1)
						{
							return;
						}
						$txt[] = $r->f('usr');
					}
					
					$lnk = $this->OPC->lnk(array(
						'mod'=>'usradmin',
						'event'=>'usr_visit',
						'data[uid]' => $params['uid'],
					));
					

					
					return '<a href="'.$lnk.'" target="_blank" onClick="return popup(\''.$lnk.'\');">'.implode(" ",$txt).'</a>';
					
				break;
					
			}
			
		}

		/**
		*	search for users
		*/
		function action_do_usr_search()
		{
			$val = &regex::singleton();
			if(!$val->validate($this->data['usr_search'],$val->text()))
			{
				return $this->set_var('search_result',array('err'=>'Fehler bei der Eingabe.'));
			}
			$this->set_view('usr_search');
			$select = "SELECT usr,id FROM ".$this->tbl_usr." WHERE usr LIKE '%".$this->data['usr_search']."%'";
			return $this->set_var('search_result',array('res'=>$this->DB->query($select))); 
		}
		/**
		*	returns true if the user in params['uname'] exists
		*/
		function action_usr_exists($params)
		{
			$cache = isset($params['cache']) ? $params['cache'] : true;
			$select = 'SELECT id FROM '.$this->tbl_usr.' WHERE usr = "'.$params['usr'].'"';
			$res = $this->DB->query($select,$cache);
			if($res->nr() == 1){
				return $res->f('id');
			}
			return false;
		}
		/**
		*	returns true if the user in params['uname'] exists
		*/
		function usr_email_exists($params)
		{
			$cache = isset($params['cache']) ? $params['cache'] : true;
			$select = 'SELECT id FROM '.$this->tbl_usr.' WHERE email = "'.$params['email'].'"';
			$res = $this->DB->query($select,$cache);
			if($res->nr() == 1){
				return $res->f('id');
			}
			return false;
		}
	}
	
?>
