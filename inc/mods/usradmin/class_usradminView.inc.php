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
	
	class usradmin_view extends modView {
		
		var $tpl_dir   = '';
		
		var $view_name = '';
		var $mod_name  = 'usradmin';
		
		var $tbl_usr        = 'mod_usradmin_usr';
		var $tbl_grp        = 'mod_usradmin_grp';
		var $tbl_usr_grp    = 'mm_usradmin_usr_grp';
		var $tbl_usr_online = 'mod_usradmin_usr_online'; 
		var $tbl_usr_profile = 'mod_usradmin_usr_profile'; 
		
		var $vid;
		
		/**
		* konstruktor
		*/
		function usradmin_view() 
		{

		}
		/**
		*	view method distribution
		*/
		
		function main($vid, $method_name) {

			$this->vid = $vid;
			$this->set_var('vid', $vid);
			#MC::debug($method_name, 'VIEW');
			#MC::debug($this->vid);
			
			# taken out on 10.8.7 because of new admin-interface: double call problem....
			# could cause problems with register_ok. -> solution: use a special page with normal content for registration ok display
			#			if ($f = $this->OPC->get_var('usradmin', 'force_action')) $method_name = $f;
			
			switch(strtolower($method_name)) 
			{
				case 'loginlogout':				$this->loginlogout();						break;
				case 'navigation':				$this->navigation();						break;
				//
				case 'du':								$this->du();											break;
				case 'grp_list':						return $this->view_grp_list();				break;
				case 'grp_form':					return $this->view_grp_form();			break;
				case "edit_registerform":		$this->view_edit_registerform();			break;
				case "edit_profileform":		$this->view_edit_profileform();			break;
				case 'register_form':			return $this->view_register_form(); 		break;
				case 'register_sent':			return $this->view_register_sent(); 		break;
				case 'register_ok':				return $this->view_register_ok();			break;
				case 'usr_home':					return $this->view_usr_home();			break;
				case 'usr_profile':				return $this->view_usr_profile();			break;
				case 'usr_visit':					return $this->view_usr_visit();				break;
				case 'usr_headline':				$this->view_usr_headline();					break;
				case 'usr_search':				return $this->view_usr_search();			break;
				case 'usr_form':					return $this->view_usr_form();				break;
				case 'usr_list':
				default:									return $this->view_usr_list();
			}
			
		}
		
		function loginlogout()
		{
			if($this->CLIENT->usr['is_default'])
			{
			$f = new forms();
            $f->create('login');
            $f->button('login',e::o('login'));
            $f->hidden('mod','usradmin');

				echo '<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Login<strong class="caret"></strong></a><div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">'.$f->show().'</div></li>';
				return;
			}
			echo '<li><a href="'.$this->OPC->lnk(array('event'=>'logout','mod'=>'usradmin')).'">Logout [ '.$this->CLIENT->usr['usr'].' ]</a></li>';
		}

		/**
		*	create the view for the top navigation
		*/
		function navigation()
		{
			return $this->show('navigation');
		}

		function du()
		{
			echo '<a class="linkbutton" href="'.$this->OPC->lnk(array('event'=>'du','mod'=>$this->mod_name,'rand'=>rand(0,99999999))).'">'.($this->CLIENT->usr['du']?e::o("alt_du_off",null,null,"page"):e::o("alt_du_on",null,null,"page")).'</a>';   
		}
		
		function view_usr_headline()
		{
			if($this->data['uid'] AND ($this->data['uid'] != $this->CLIENT->usr['id']))
			{
				$select = "SELECT usr FROM mod_usradmin_usr WHERE id = ".$this->data['uid'];
				$r = $this->DB->query($select);
				if($r->nr() != 1)
				{
					$usr = 'Hallo '.$this->CLIENT->usr['usr'];					
				}
				else
				{
					$usr = 'Home von '.$r->f('usr');
				}
			}
			else
			{
				$usr = 'Hallo '.$this->CLIENT->usr['usr'];
			}
			$this->set_var("headline",&$usr);
			$this->generate_view("headline.tpl",true);
		}
		
		/**
		*	find usr by name
		*/
		function view_usr_search()
		{
			
			$this->OPC->lnk_add('mod','usradmin');
			$this->OPC->lnk_add('event','do_usr_search');
			
			$content = '<form action="'.$this->OPC->action().'" method="post">
				<input type="text" name="data[usr_search]" value="'.$this->data['usr_search'].'">
				<input type="submit" name="event_do_usr_search" value="'.e::o('v_b_search').'">
				'.$this->OPC->lnk_hidden(array('vid'=>$this->vid)).'
			</form>';
			
			$this->set_var('content',&$content);
			$this->generate_view('usr_search.php',true);
//			$this->OPC->generate_view($this->tpl_dir.'usr_search.tpl');
			return;
		}
//-- BENUTZER -------------------------------------------
		/**
		*	show user list
		*/
		function view_usr_list() {
			echo $this->alert_msg();
			//-- benutzer auslesen
			#$sql = 'SELECT * FROM '. $this->tbl_usr .' WHERE pid=' . $this->pid.' AND NOT sys_trashcan';
			$sql = 'SELECT * FROM '. $this->tbl_usr .' WHERE NOT sys_trashcan';
			$usr = $this->DB->paging_query($sql);
			
			$this->set_var('usr_liste', $usr);
			
			//-- formular anlegen
			$form = MC::create('form');
			$form->add_hidden('vid', $this->vid);
			$form->add_hidden('mod', $this->mod_name);
			$this->set_var('usr_form', &$form);
			
			
			//-- icon ?
			$conf = $this->MC->table_config('mod_usradmin_usr');
			$icon = '&nbsp;';
			if ($conf['table']['icon']) {
				$icon = '<img src="'.CONF::img_url().'icons/'.$conf['table']['icon'].'" alt="'.$conf['table']['label'].'">';
			}
			$this->set_var('tbl_icon', $icon);
			$this->set_var('vid', &$this->vid);
			
			$this->generate_view('usr_list.php',true);
//			$this->OPC->generate_view($this->tpl_dir . 'usr_list.tpl');
			
		}
		
		/**
		*	show user form
		*/
		function view_usr_form() {
		
			$form = MC::create('form');
			
			if(is_file(CONF::inc_dir()."/etc/form/".$this->tbl_usr.".".CONF::project_name().".form"))
			{
				$tbl = $this->tbl_usr.".".CONF::project_name();
			}
			else
			{
				$tbl = $this->tbl_usr;
			}
			
			$form->init($this->mod_name, $tbl);

			$form->add_hidden('mod', $this->mod_name);
			$form->add_hidden('vid', $this->vid);
			
			
			// wenn edit_id gesetzt und kein fehler, aus db laden
			if ($id = (int)$this->get_var('edit_id')) {
				if (!$this->get_var('error')) {
					$sql = 'SELECT * FROM '. $this->tbl_usr .' WHERE id='.$id;
					$res = $this->DB->query($sql);
					$data = $res->r();
					
					$sql = 'SELECT * FROM '. $this->tbl_usr_grp .' WHERE local_id='.$id;
					$res = $this->DB->query($sql);
					$groups = array();
					while ($res->next()) {
						$groups[] = $res->f('foreign_id');
					}
					$data['groups'] = $groups;
					$form->set_values($data);
				}

				
				$form->add_hidden_pre('edit_id', $id);
				$this->set_var('headline', e::o('v_edit_user'));
			}
			else {
				$this->set_var('headline', e::o('v_new_user'));
			}

			// bei fehler, fehler und daten in form setzen
			if ($this->get_var('error')) {
				$form->set_values($this->get_var('data'));
				$form->set_error_fields($this->get_var('error'));
			}

			
			$this->set_var('usr_form', &$form);
			
			$this->generate_view('usr_form.php',true);
//			$this->OPC->generate_view($this->tpl_dir . 'usr_form.tpl');
		}
		
//-- GRUPPEN ---------------------------------------
		/**
		*	show group list
		*/
		function view_grp_list() {
			//-- benutzer auslesen
			#$sql = 'SELECT * FROM '. $this->tbl_grp .' WHERE pid=' . $this->pid . ' AND NOT sys_trashcan';
			$sql = 'SELECT * FROM '. $this->tbl_grp .' WHERE NOT sys_trashcan';
			$grp = $this->DB->query($sql);
			$this->set_var('grp_liste', $grp);
			
			//-- formular anlegen
			$form = MC::create('form');

			$form->add_hidden('vid', $this->vid);
			$form->add_hidden('mod', $this->mod_name);
			$this->set_var('grp_form', &$form);
			
			//-- icon ?
			$conf = $this->MC->table_config('mod_usradmin_grp');
			$icon = '&nbsp;';
			if ($conf['table']['icon']) {
				$icon = '<img src="'.CONF::img_url().'icons/'.$conf['table']['icon'].'" alt="'.$conf['table']['label'].'">';
			}
			$this->set_var('tbl_icon', $icon);
			
			$this->generate_view('grp_list.tpl',true);
			
//			$this->OPC->generate_view($this->tpl_dir . 'grp_list.tpl');
			
		}
		/**
		*	show group form
		*/
		function view_grp_form() {

			$form = MC::create('form');
			$form->init($this->mod_name, $this->tbl_grp);

			$form->add_hidden('mod', $this->mod_name);
			$form->add_hidden('vid', $this->vid);
			
			
			// wenn edit_id gesetzt und kein fehler, aus db laden
			if ($id = (int)$this->get_var('edit_id')) {
				if (!$this->get_var('grp_error')) {
					$sql = 'SELECT * FROM '. $this->tbl_grp .' WHERE id='.$id;
					$res = $this->DB->query($sql);
					$form->set_values($res->r());
				}
				
				$form->add_hidden_pre('edit_id', $id);
				$this->set_var('headline', e::o('v_edit_grp'));
			}
			else {
				$this->set_var('headline', e::o('v_new_grp'));
			}

			// bei fehler, fehler und daten in form setzen
			if ($this->get_var('grp_error')) {
				$form->set_values($this->get_var('data'));
				$form->set_error_fields($this->get_var('grp_error'));
			}

			
			$this->set_var('grp_form', &$form);
			
			$this->generate_view('grp_form.php',true);
			
//			$this->OPC->generate_view($this->tpl_dir . 'grp_form.tpl');
		}
	
		
		function view_edit_registerform()
		{
			$this->OPC->lnk_add("mod",$this->mod_name);
			$this->OPC->lnk_add("vid",$this->vid);
				
			echo $this->OPC->create_button("online","Online");
			
			
			$lang = CONF::get("lang_".$this->vid);
			$grp = CONF::get("grp_".$this->vid);
			$pid = CONF::get("pid_".$this->vid);
			
			$conf = $this->MC->table_config($this->tbl_usr_online);
			
			if($lang)
			{
				$conf['fields']['lang']['cnf']['default'] = $lang;
			}

			if($grp)
			{
				$conf['fields']['groups']['cnf']['default'] = $grp;
			}
			
			$vals = array
			(
				'groups' => explode(",",$conf['fields']['groups']['cnf']['default']),
				'lang' => $conf['fields']['lang']['cnf']['default'],
				'welcome_page' => $pid,
			);
			
			$form = MC::create('form');
			$form->init($this->mod_name, $conf);

			$form = MC::create('form');
			$form->init($this->mod_name,"mod_usradmin_edit_registerform");

			$form->set_values($vals);
			
			$form->add_button("event_do_edit_registerform",e::o('save'));
			
			$content = $form->start().$form->fields().$form->end();
				
			$this->set_var("content",&$content);
			$this->generate_view("default.tpl",true);
		}


		function view_edit_profileform()
		{
			$this->OPC->lnk_add("mod",$this->mod_name);
			$this->OPC->lnk_add("vid",$this->vid);
				
			echo $this->OPC->create_button("online","Online");
			
			$pid = CONF::get("pid_".$this->vid);
			
			$conf = $this->MC->table_config($this->tbl_usr_online);
			
			$vals = array
			(
				'welcome_page' => $pid,
			);
			
			$form = MC::create('form');
			$form->init($this->mod_name, $conf);

			$form = MC::create('form');
			$form->init($this->mod_name,"mod_usradmin_edit_profileform");

			$form->set_values($vals);
			
			$form->add_button("event_do_edit_profileform",e::o('save'));
			
			$content = $form->start().$form->fields().$form->end();
				
			$this->set_var("content",&$content);
			$this->generate_view("default.tpl",true);
		}		
		/**
		* anmelde-formular
		
		changed 27.04.06
		now editable which groups and language the user has when registering
		
		*/
		function view_register_form() 
		{			
			if($this->access("edit_registerform"))
			{
				$this->OPC->lnk_add("mod",$this->mod_name);
				$this->OPC->lnk_add("vid",$this->vid);
				echo $this->OPC->create_button("edit","Edit","edit_registerform");
			}
			
			$lang = CONF::get("usradmin_register_lang");
			$grp = CONF::get("usradmin_register_grp");
			
			
			$tbl = CONF::inc_dir()."/etc/form/".$this->tbl_usr_online.".".CONF::project_name();
			
			if(is_file($tbl.".php"))
			{
				$conf = $this->MC->table_config($this->tbl_usr_online.".".CONF::project_name(),$this->mod_name);
			}
			else
			{
				$conf = $this->MC->table_config($this->tbl_usr_online,$this->mod_name);
			}
			
			if($lang)
			{
				$conf['fields']['lang']['cnf']['default'] = $lang;
			}

			if($grp)
			{
				$conf['fields']['groups']['cnf']['default'] = $grp;
			}
			
			$vals = array
			(
				'groups' => $conf['fields']['groups']['cnf']['default'],
				'lang' => $conf['fields']['lang']['cnf']['default'],
			);
			
			$form = MC::create('form');
			
			$form->init($this->mod_name, $conf);
			
			
			$form->add_hidden('mod', $this->mod_name);
			$form->add_hidden('vid', $this->vid);
			// bei fehler, fehler und daten in form setzen
			if ($this->get_var('error')) {
				$form->set_values($this->get_var('data'));
				$form->set_error_fields($this->get_var('error'));
			}
			
			$this->set_var('register_form', $form);
			$this->set_var('log_error', $this->data['log_error']);
			
			$this->generate_view('register_form.php',true);
			
//			$this->OPC->generate_view($this->tpl_dir . 'register_form.tpl',false);
		}

		/**
		* benutzer wurde gespeichert
		*/
		function view_register_sent() {
			// mail wird ueber view/template gesendet
			$this->OPC->lnk_add('vid',$this->vid);
			$this->generate_view('register_sentmail.php',true);
//			$this->OPC->generate_view($this->tpl_dir . 'register_sentmail.tpl',false);
			
			// bestaetigungstext
			$this->generate_view('register_senttext.php',true);
//			$this->OPC->generate_view($this->tpl_dir . 'register_senttext.tpl',false);
		}
		/**
		*	registration completed
		*/
		function view_register_ok()
		{
			$this->generate_view('register_complete.php',true);
//			$this->OPC->generate_view($this->tpl_dir . 'register_complete.tpl',false);
		}

		
		/**
		* benutzer-home-seite, profil etc.
		*/
		function view_usr_home() {
			$data = UTIL::get_post('data');
				if($this->CLIENT->usr['is_default']){
					$tpl = "usr_home_guest.php";
					$this->generate_view($tpl,true);
//					$this->OPC->generate_view($this->tpl_dir . $tpl, false);
					return;
				}
				
			$uid = (int)$data['uid'];
			if (!$uid) {
				// haben wir keine id, versuchen wirs mal mit nem namen
				$uname = trim($data['uname']);
				if ($uname == '') {
					$uid = $this->CLIENT->usr['id'];
				}
				else {
					$res = $this->DB->query('SELECT id FROM '.$this->tbl_usr.' WHERE usr=\''.addslashes($uname).'\'');
					$uid = ($res->nr() > 0) ? (int)$res->f('id') : $this->CLIENT->usr['id'];
				}
				
			}
			
			//-- template wahl -> soll home des aktuellen users gezeigt werden

			if ($uid == $this->CLIENT->usr['id']) {
				// die daten nimmt das template dann aus CLIENT
				$tpl = 'usr_home_self_new.tpl';
			}
			else {
				if($this->CLIENT->usr['is_default']){
					$tpl = "usr_home_guest.tpl";
					$this->generate_view($tpl,true);
//					$this->OPC->generate_view($this->tpl_dir . $tpl, false);
					return;
				}
				$tpl = 'usr_home_others_new.tpl';
				// wir lesen die daten aus db
				$res = $this->DB->query('SELECT * FROM '.$this->tbl_usr.' WHERE id='.$uid);
				if ($res->nr() == 0) {
					echo e::o('v_no_usr_found');
					return;
				}
				$this->set_var('usr_data', $res->r());
			}
			
			$this->set_var('uid', $uid);
			$this->set_var('usr_file_url', $this->usr_file_url);
			$this->set_var('usr_file_path', $this->usr_file_path);
			$this->generate_view($tpl,true);
//			$this->OPC->generate_view($this->tpl_dir . $tpl, false);
		}

		/**
		* profil-formular		
		*/
		/**
		* anmelde-formular
		*/

		function view_usr_profile() 
		{
			if($this->access("edit_registerform"))
			{
				$this->OPC->lnk_add("mod",$this->mod_name);
				$this->OPC->lnk_add("vid",$this->vid);
				echo $this->OPC->create_button("edit","Edit","edit_profileform");
			}
			
			$form = MC::create('form');
			if(is_file(CONF::inc_dir()."/etc/form/".$this->tbl_usr_profile.".".CONF::project_name().".form"))
			{
				$form->init($this->mod_name, $this->tbl_usr_profile.".".CONF::project_name());
			}
			else
			{
				$form->init($this->mod_name, $this->tbl_usr_profile);
			}

			$form->add_hidden('mod', $this->mod_name);
			$form->add_hidden('vid', $this->vid);

			// bei fehler, fehler und daten in form setzen
			if ($this->get_var('error')) 
			{
				$form->set_values($this->data);
				$form->set_error_fields($this->get_var('error'));
			}
/*			
			$icn = $this->MC->call('usradmin','get_file',array('name'=>'icon','uid'=>$this->CLIENT->usr['id']));
			$img = $this->MC->call('usradmin','get_file',array('name'=>'image','uid'=>$this->CLIENT->usr['id']));
			
			if($icn)
			{
				$content .= '<img src="'.$icn.'"><br>';
			}
			if($img)
			{
				$content .= '<img src="'.$img.'">';
			}			
*/		
			$content .= '<hr>'.$this->MC->call('usradmin','callback_get_link',array('type'=>'usr_visit','uid'=>$this->CLIENT->usr['id'],'img'=>'icon'));
			
			$this->set_var('content', &$content);
			$this->set_var('profile_form', $form);
			$this->set_var('usr_file_url', $this->usr_file_url);
			$this->set_var('usr_file_path', $this->usr_file_path);
			$this->generate_view('usr_profile_new.php',true);
//			$this->OPC->generate_view($this->tpl_dir . 'usr_profile_new.tpl',false);
		}
		
		function view_usr_visit()
		{
			$select = "SELECT * FROM ".$this->tbl_usr." WHERE id = ".$this->data['uid'];
			$res = $this->DB->query($select);
			$this->set_var("uid",&$this->data['uid']);
			$this->set_var("content",&$res);
			$this->generate_view("usr_visit.php",true);
		}
		
	}
?>
