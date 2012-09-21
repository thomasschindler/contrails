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

class page_action extends modAction {

	/**
	 *	Dict with 'action' and 'event' keys? (CHECKME)
	 *	@var string[string]
	 */

	public $action;

	/**
	 *	Name of this module
	 *	@var string
	 */

	public $mod_name = 'page';

	/**
	 *	View ID, hash (CHECKME)
	 *	@var string
	 */

	public $vid = '';

	/**
	 *	Name of table holding user access rights (CHECKME)
	 *
	 *	Table has structure:
	 *
	 *		pid			int(10)		ref: mod_page.id	A page
	 *		uid			int(10)		ref: mod_usradmin_usr	A user
	 *		mid			int(10)		(DOCUMENTME) module id? maps where?
	 *		ar			int(32)		(DOCUMENTME) access right?
	 *		inherit_pid	int(10)		(DOCUMENTME)
	 *
	 *	@var string
	 */

	private $tbl_usr_ar = 'mod_page_mod_usr_ar';

	/**
	 *	Name of table holding group access rights (CHECKME)
	 *
	 *	Table has structure:
	 *
	 *		pid			int(10)		ref: mod_page.id
	 *		gid			int(10)		ref: mod_usradmin_grp (CHECKME)
	 *		mid			int(10)		(DOCUMENTME)
	 *		ar			int(32)		(DOCUMENTME) access right?
	 *		inherit_pid	int(10)		(DOCUMENTME)
	 *
	 *	@var string
	 */

	private $tbl_grp_ar = 'mod_page_mod_grp_ar';

	/**
	 *	(DOCUMENTME)
	 *
	 *	Table has structure:
	 *
	 *		id				int(10)			Pri Key
	 *		pid				int(10)			ref: mod_page.id (see below)
	 *		name			varchar(255)	(DOCUMENTME) appears to be a group name
	 *		lang			int(2)			(DOCUMENTME) where is this mapped?
	 *		sys_trashcan	tinyint(1)		always false? (CHECKME)
	 *
	 *	Observed group names:
	 *		Registered, Guest, Editor, PSP, karneval, Admin, Backoffice, Customer, Customer Support
	 *
	 *	@var string
	 */

	private $tbl_grp = 'mod_usradmin_grp';

	/**
	 *	(DOCUMENTME)
	 *	@var string
	 */

	private $tbl_usr = 'mod_usradmin_usr';

	/**
	 *	Access control List for this module? (CHECKME)
	 *
	 *	Table has structure:
	 *		id		int(10)		Pri Key
	 *		type		int(1)		(DOCUMENTME)
	 *		aid		int(10)		(DOCUMENTME)
	 *		ar		int(32)		(DOCUMENTME)
	 *		inherit_pid	int(10)		(DOCUMENTME)
	 *
	 *	@var string
	 */

	private $tbl_acl = 'mod_page_acl';

	/**
	 *	Name of table holding page definitions
	 *
	 *	Table has structure:
	 *
	 *		id					int(10)				Pri Key, (CHECKME) same as PID used elsewhere?
	 *		name				varchar(255)		name of page
	 *		url					text				relative to http://mysite.com/
	 *		title				varchar(255)		page title
	 *		description			text				(DOCUMENTME)
	 *		keywords			text				SEO? (CHECKME)
	 *		lft					int(10)				(DOCUMENTME) page to left od this one?
	 *		rgt					int(10)				(DOCUMENTME) page to right of this one?
	 *		root_id				int(10)				(DOCUMENTME) pages exist in a tree?
	 *		parent_id			int(11)				(DOCUMENTME) pages exist in a tree?
	 *		set_ignore			tinyint(1)			(DOCUMENTME)
	 *		template_name		varchar(255)		(DOCUMENTME) how is this resolved / run
	 *		rights				int(10)				(DOCUMENTME) always seems to be 0
	 *		structure			text				(DOCUMENTME) serialized PHP array
	 *		lost_mods			text				(DOCUMENTME) 
	 *		sys_trashcan		smallint(1)			(DOCUMENTME)
	 *		sys_date_created	int(10)				timestamp? (CHECKME)
	 *		sys_date_changed	int(10)				timestamp? (CHECKME)
	 *
	 *	@var string
	 */

	private $mod_tbl = 'mod_page';

	/**
	 *	Constructor
	 *	@return	void
	 */

	public function __construct() {
		$this->OPC = &OPC::singleton();
	}

	/**
	 *	(TRANSLATEME)
	 *	[de] 'verteiler-funktion' wird von MC::call_action() aufgerufen
	 *
	 *	@param	string[string]	$action		dict containing 'event' key
	 *	@param	mixed		$params		additional arguments to event
	 *	@return	mixed
	 */

	public function main($action,$params) 
	{
		
		MC::log($action);
		
		$this->vid = UTIL::get_post('vid');		//	(TODO) sanitize this
		switch (strtolower($action['event']))
		{
			case 'get_start_page':	$this->get_start_page();			break;
			case 'get_my_path':		$this->get_my_path(); 				break;

			/* page-admin ----------------------------------------------------------------------- */
			case 'pa_edit':			$this->set_start_view('pa_edit');	break;
			case 'pa_enter':		$this->set_start_view('pa_enter');	break;
			case 'pa_save':			return $this->action_pa_save();		break;
			case 'pa_delete':		$this->action_pa_delete();			break;
			case 'pa_move':			$this->action_pa_move();			break;

			/* modul-access-rights -------------------------------------------------------------- */
			case 'pa':				$this->set_start_view('pa'); 		break;
			case 'pa_type':			$this->set_start_view('pa_type');	break;
			case 'pa_ar':			$this->set_start_view('pa_ar');		break;
			case 'pa_ar_add':		$this->action_pa_ar_add();			break;
			case 'pa_ar_del':		$this->action_pa_ar_del();			break;
			case 'pa_ar_cancel':	$this->set_view('pa_type');			break;
			case 'pa_ar_save':		$this->action_pa_ar_save();			break;

			/* called by opc when restructuring a page ------------------------------------------ */
			case 'callback_lost_mods_save':	$this->callback_lost_mods_save($params);break;
			case 'callback_lost_mods_get':	return $this->callback_lost_mods_get($params);break;

			case 'view':
			default:
				//	(TODO) handle this case
		}

	}

	/**
	 *	returns the default pid
	 * 	hard for now - should have a switch for admin
	 *
	 *	@return	string
	 */

	function get_start_page()
	{
		return "1";
	}

	/**
	 *	Create a new page / update an existing page
	 *
	 *	navigation_1 might listen to this!!!!
	 *
	 * 	[de] neue seite anlegen / vorhandene seite updaten
	 *
	 *	(TODO) have this return -1 on failure, rather than FALSE, check for type-unsafe comparisons
	 *	
	 *	@return	mixed	PID or false
	 */

	function action_pa_save() 
	{
		$data     = UTIL::get_post('data');						//	(TODO) sanitize this
		$edit_pid = (int)UTIL::get_post('edit_pid');

		$this->OPC->lang_page_start($this->mod_name);			//	(DOCUMENTME) lang_page_start
		$validator = MC::create('validator', $this->mod_tbl);

		$save_mode = ($edit_pid) ? 'edit' : 'enter';			//	(DOCUMENTME)
		$form_view = ($edit_pid) ? 'pa_edit' : 'pa_enter';		//	(DOCUMENTME)

		if (!$validator->is_valid($data, $save_mode)) 
		{
			$this->set_var('error', $validator->get_error_fields());
			$this->set_var('data_' . $this->data['form_cnt'], $data);
			$this->set_start_view($form_view);
			$this->OPC->lang_page_end();
			$this->OPC->error("Please review the errors");
			return false;
		}
		$this->OPC->lang_page_end();

		//	[en] Everything ok, save, and, do not edit_pid->insert else update
		//	[de] alles ok, und speichern; keine edit_pid->insert sonst update
		$set = new NestedSet();
		$set->set_table_name($this->mod_tbl);

		if (!$edit_pid) {




			//	[en] Create new page in module, and inherit access rights of parent page
			//	[de] bei 'neu anlegen', modul und zugriffsrechte von eltern-seite erben
			$parent_pid = (int)$data['parent_pid'];
			$redirect_to = (int)$data['redirect_to'];
			$new_id = $set->nodeNew($parent_pid);

			$_GET['new_id'] = $new_id;

			$set->setNode(
				$new_id,
				array(
					'parent_id' => $parent_pid,
					'name' => $data['name'],
					'url' => $data['url'],
					'title' => $data['title'],
					'keywords' => $data['keywords'],
					'description' => $data['description'],
					'template_name' => addslashes($data['template_name']),
					'cookie_name' => $data['cookie_name'],
					'cookie_value' => $data['cookie_value'],
					'cookie_lifetime' => (int)$data['cookie_lifetime'],
					'redirect_to' => $redirect_to
				)
			);
			


			
			//	[en] user module right / permission
			//	[de] user-modulrechte
			$sql = 'SELECT * FROM '.$this->tbl_usr_ar.' WHERE pid='.$parent_pid;
			$res = $this->DB->query($sql);
			$val = array();

			while($res->next()) 
			{
				$val[] = '('. $new_id .','. $res->f('uid') .','. $res->f('mid') .','. $res->f('ar') .','. $parent_pid .')';
			}

			if($this->CLIENT->usr['id'] != CONF::su())
			{
				if(sizeof($val) >= 1)
				{
					$new_sql = "INSERT INTO ".$this->tbl_usr_ar." (pid, uid, mid, ar, inherit_pid) VALUES ".implode(',', $val);
					$this->DB->query($new_sql);
				}
				/*
				foreach($val as $_v)
				{
					$new_sql = "INSERT INTO ".$this->tbl_usr_ar." (pid, uid, mid, ar, inherit_pid) VALUES ".$_v;
					$this->DB->query($new_sql);
				}
				*/
			}

			//	[en] group module permissions
			//	[de] gruppen-modulrechte
			$sql = 'SELECT * FROM '.$this->tbl_grp_ar.' WHERE pid='.$parent_pid;
			$res = $this->DB->query($sql);
			$val = array();
			while($res->next()) {
				$val[] = '('. $new_id .','. $res->f('gid') .','. $res->f('mid') .','. $res->f('ar') .','. $parent_pid .')';
			}
			if(sizeof($val) != 0)
			{
				$new_sql = ''
				 . 'INSERT INTO ' . $this->tbl_grp_ar . ' (pid, gid, mid, ar, inherit_pid) '
				 . 'VALUES ' . implode(',', $val);
				$this->DB->query($new_sql);
			}

			//	[en] acl rights / permissions
			//	[de] acl-rechte
			$sql = 'SELECT * FROM ' . $this->tbl_acl . ' WHERE id=' . $parent_pid;
			$res = $this->DB->query($sql);
			$val = array();
			while($res->next())
			{
				$val[] = '('. $new_id .','. $res->f('type') .','. $res->f('aid') .','. $res->f('ar') .','. $parent_pid .')';
			}

			if (sizeof($val) != 0)
			{
				$new_sql = ''
				 . 'INSERT INTO ' . $this->tbl_acl . ' (id, type, aid, ar, inherit_pid) '
				 . 'VALUES '.implode(',', $val);
				$this->DB->query($new_sql);
			}

			// send 
			$this->set_var('msg', e::o('a_page_create_success').UTIL::get_js('refresh_opener()'));
			$this->set_start_view('pa_enter');
			#return $new_id;
			$page_id = $new_id;
		}
		else 
		{
			//	[en] update only if the page has changed
			//	[en] remove the old page structure
			//	[de] nur update und nur, wenn das template geändert wurde!!

			$select = "SELECT template_name FROM ".$this->mod_tbl." WHERE id = ".$edit_pid;
			$tpl_name = $this->DB->query($select);
			if ($tpl_name->f('template_name') != $data['template_name'])
			{
				$update = "UPDATE ".$this->mod_tbl." SET structure = '' WHERE id = ".$edit_pid;
				#$this->DB->query($update);		//	found commented out 2012-05-02
			}
			$redirect_to = (int)$data['redirect_to'];
			$set->setNode(
				$edit_pid,
				array(
					'name' => $data['name'],
					'title' => $data['title'],
					'url' => $data['url'],
					'keywords' => $data['keywords'],
					'description'=>$data['description'],
					'template_name' => addslashes($data['template_name']),
					'cookie_name' => $data['cookie_name'],
					'cookie_value' => $data['cookie_value'],
					'cookie_lifetime' => (int)$data['cookie_lifetime'],
					'redirect_to' => $redirect_to
				)
			);

			$this->set_var('msg', e::o('a_page_change_success').UTIL::get_js('refresh_opener();'));
			$this->set_start_view('pa_edit');
			#return $edit_pid;
			$page_id = $edit_pid;
		}

		if ($_FILES)						//	(TODO) type-safe comparison
		{
			$path = CONF::asset_dir()."/mods/page/";
			if (!is_dir($path)) { UTIL::mkdir_recursive($path); }
		}

		if ($_FILES['img_active'])			//	(TODO) type-safe comparison
		{
			copy($_FILES['img_active']['tmp_name'], $path . $page_id . "_active.gif");
		}

		if ($_FILES['img_inactive'])		//	(TODO) type-safe comparison
		{
			copy($_FILES['img_inactive']['tmp_name'], $path . $page_id . "_inactive.gif");
		}

		return $page_id;
	}

	/**
	 *	[en] store access rights for modules
	 *	[de] zugriffsrechte fuer module abspeichern
	 *	@return	void
	 */

	function action_pa_ar_save() 
	{


		$data = UTIL::get_post('data');



		$edit_pid = (int)UTIL::get_post('edit_pid');

		if (!$edit_pid) { $pid = $data['pid_hidden']; }		//	(DOCUMENTME)
		$mid = $data['mid'];

		if(!$mid)
		{
			$mid = $data['mid_hidden'];
		}

		//	[en] carry over the checkboxes in the info-array (post->info) (CLARIFY)
		//	[de] uebertragen der checkboxen in das info-array (post->info)
		$data = $this->_ar_post2info($data);

		//	[en] (TRANSLATEME)
		//	[de] vererben wir nach unten, haben wir eine liste von seiten,
		//	[de] fuer die wir das gleiche eintragen
		$update_pid = array();
		$inherit = (int)$data['inherit'];	// anzahl der ebenen die nach unten vererbt wird
		if ($inherit > 0)
		{
			$set = new NestedSet();
			$set->set_table_name($this->mod_tbl);
			$sub = $set->getSubtree($edit_pid, 'id,name');

			//	[en] collect all PIDs on the same level (CHECKME)(CLARIFY)
			//	[de] alle pid's unter uns je level sammeln
			$level = array();
			foreach ($sub as $node) $level[$node['level']][] = $node['id'];

			//	[en] pid's take the first level X (CLARIFY)
			//	[de] nur die pid's der ersten X level nehmen
			$cnt = 0;
			foreach ($level as $lev => $ids)
			{
				$update_pid = array_merge($update_pid, $ids);
				if (++$cnt > $inherit) break;
			}
		}
		else
		{
			$update_pid = array($edit_pid);
		}

		//	same changes as in acladmin???
		//	[en] first all old right antfernen this module on the website
		//	[de] erstmal alle alten rechte dieses moduls auf der seite antfernen
		#$sql = 'DELETE FROM '. $this->tbl_usr_ar .' WHERE pid IN ('. implode(',', $update_pid) .') AND mid='.$mid;
		#MC::debug($sql);
		#$this->DB->query($sql);

		#$sql = 'DELETE FROM '. $this->tbl_grp_ar .' WHERE pid IN ('.implode(',', $update_pid).') AND mid='.$mid;
		#MC::debug($sql);
		#$this->DB->query($sql);
		# moved down
		/**end of changes**/

		if (is_array($data['info']['grp']))
		{
			$chg_grp = array(0);
			foreach ($data['info']['grp'] as $key => $val)
			{
				if($val['chg']) { $chg_grp[] = $key; }
				if ($val['del']) { $chg_grp[] = $key; }
			}

			$chg_grp = array_unique($chg_grp);

			$sql = ''
			 . 'DELETE FROM ' . $this->tbl_grp_ar
			 . ' WHERE pid IN (' . implode(',', $update_pid) . ')'
			 . ' AND mid=' . $mid
			 . ' AND gid IN (' . implode(',', $chg_grp) . ')';

			$this->DB->query($sql);

			$grp_sql = 'INSERT INTO ' . $this->tbl_grp_ar . ' (pid, gid, mid, ar, inherit_pid) VALUES ';
			$grp_val = array();


			foreach($data['info']['grp'] as $gid => $info) 
			{
				// only change what has to be changed!
				if (!$info['chg']) { continue; }

				$gid = (int)$gid;
				$ar  = (int)$info['ar'];
				if ($ar == 0) { continue; }		// do not store null values
				foreach ($update_pid as $pid) { $grp_val[] = "($pid, $gid, $mid, $ar, $edit_pid)"; }
			}

			if (count($grp_val) > 0) {
				$grp_sql .= implode(', ', $grp_val);
				//MC::debug($grp_sql);
				$this->DB->query($grp_sql);
			}
		}
	

		if (is_array($data['info']['usr']))
		{
			$chg_usr = array(0);
			foreach($data['info']['usr'] as $key => $val)
			{
				if($val['chg']) { $chg_usr[] = $key; }
				if($val['del']) { $chg_usr[] = $key; }
			}

			$chg_usr = array_unique($chg_usr);

			$sql = ''
			 . 'DELETE FROM ' . $this->tbl_usr_ar
			 . ' WHERE pid IN (' . implode(',', $update_pid) . ')'
			 . ' AND mid=' . $mid
			 . ' AND uid IN (' . implode(',', $chg_usr) . ')';


			$this->DB->query($sql);

			$usr_val = array();
			$usr_sql = ''
			 . 'INSERT INTO ' . $this->tbl_usr_ar . ' (pid, uid, mid, ar, inherit_pid) VALUES ';

			foreach($data['info']['usr'] as $uid => $info)
			{
				// only change what has to be changed!
				if (!$info['chg']) { continue; }
				$uid = (int)$uid;
				$ar = (int)$info['ar'];
				if (0 == $ar) { continue; }		// do not store null values
				foreach ($update_pid as $pid) { $usr_val[] = "($pid, $uid, $mid, $ar, $edit_pid)"; }
			}

			if (count($usr_val) > 0) {
				$usr_sql .= implode(', ', $usr_val);
				//MC::debug($usr_sql);
				$this->DB->query($usr_sql);
			}
		}

		$this->set_start_view('pa_ar');
		$this->set_var('msg', e::o('a_access_rights_success').UTIL::get_js('refresh_opener();'));
	}

	/**
	 *	[en] users / groups were removed (CHECKME)
	 *	[de] benutzer / gruppen wurden entfernt
	 *
	 *	(DOCUMENTME) expected structure of POST args
	 *
	 *	@return	void
	 */

	function action_pa_ar_del() 
	{
		$data = UTIL::get_post('data');			//	unsanitized

		//	[en] carry over the checkboxes in the POSTed info array
		//	[de] uebertragen der checkboxen in das info-array (post->info)
		$data = $this->_ar_post2info($data);

		//	[en] is in data[action] separated by commas and key id
		//	[de] in data[action] steht komma-separiert key und id
		list($key, $id) = explode(',', $data['action']);
		unset($data['info'][$key][$id]);

		$this->set_var('data', $data);
		$this->set_start_view('pa_ar');
	}

	/**
	 *	[en] users / groups were added
	 *	[de] benutzer / gruppen wurden hinzugefuegt
	 *
	 *	(DOCUMENTME) expected structure of $_POST
	 *
	 *	@return	void
	 */

	function action_pa_ar_add()
	{
		$data = UTIL::get_post('data');		//	unsanitized

		//	[en] carry over the checkboxes in the info array (post --> info)
		//	[de] uebertragen der checkboxen in das info-array (post --> info)
		$data = $this->_ar_post2info($data);

		//	[en] (TRANSLATEME)
		//	[de] in form steht serialisiertes array der tabbelen und gewaehlten ids
		$add  = unserialize(stripslashes($data['action']));

		//	[en] (TRANSLATEME)
		//	[de] fuer alle hinzugefuegten gruppen/benutzer, das form (info-feld) erweitern
		foreach($add as $table => $ids) 
		{
			$key = '';
			switch($table) 
			{
				case 'mod_usradmin_grp':	$key = 'grp';	$name_field = 'name';	break;
				case 'mod_usradmin_usr':	$key = 'usr';	$name_field = 'usr';	break;
			}

			foreach($ids as $id) 
			{
				//	[en] add only if not already present
				//	[de] nur hinzufuegen, wenn nicht schon da
				if (is_array($data['info'][$key][$id])) { continue; }

				//	(TODO) remove variable property
				$sql = ''
				 . 'SELECT ' . $name_field . ' AS name FROM ' . $this->{'tbl_'.$key}
				 . ' WHERE id=' . (int)$id;

				$res_name = $this->DB->query($sql);
				$data['info'][$key][$id] = array
				(
					'name' => $res_name->f('name'),
					'ar'   => 0,
				);
			}
		}

		$this->set_var('data_' . $this->data['form_cnt'], $data);
		$this->set_start_view('pa_ar');								//	(DOCUMENTME)
	}

	/**
	 *	[en] delete page, including all children
	 *	[de] seite loeschen, inklusiver aller kinder (kein muelleimer hier !)
	 *	@return void
	 */

	function action_pa_delete() {
		$pid = (int)UTIL::getPost('edit_pid');						//	unsanitized
		if (!$pid) { return; }										//	(TODO) type safety

		if ($pid == CONF::pid()) { return; }						//	(DOCUMENTME)

		$set = new NestedSet();
		$set->set_table_name($this->mod_tbl);

		//	[en] identify all children
		//	[de] alle kinder ermitteln
		$childs = $set->getSubtree($pid, 'id');
		$del_pid = array();
		foreach($childs as $c) { $del_pid[] = $c['id']; }

		//	[en] delete nodes
		//	[de] knoten loeschen
		$set->nodeDel($pid, '1');

		//	[en] delete all access rights to the page and child pages
		//	[de] auch alle zugriffsrechte die seite betreffend loeschen
		$sql = 'DELETE FROM '. $this->tbl_usr_ar .' WHERE pid IN ('. implode(',', $del_pid) .')';
		$this->DB->query($sql);

		$sql = 'DELETE FROM '. $this->tbl_grp_ar .' WHERE pid IN ('.implode(',', $del_pid).')';
		$this->DB->query($sql);

		$sql = 'DELETE FROM '. $this->tbl_acl .' WHERE id IN ('.implode(',', $del_pid).')';
		$this->DB->query($sql);

		//	(CHECKME) why is this commented out?  can it be removed?
		#$this->set_var('msg', e::o('a_page_del_success').UTIL::get_js('refresh_opener_close();'));
		$this->set_var('msg', e::o('a_page_del_success'));

		unset($_POST['edit_pid'], $_GET['edit_pid']);		//	(DOCUMENTME) why do this?
		$_GET['edit_pid'] = CONF::pid();					//	""
		$_GET['pid'] = CONF::pid();							//	""
		$this->set_start_view("pa_type");					//	(DOCUMENTME) what view is this?
	}

	/**
	 *	move a page
	 *
	 *	This assumes that $_POST['edit_pid'] is set, and that $data will contain 'move_to' and
	 *	'move_type'.
	 *
	 *	[de] seite verschieben
	 *
	 *	@return	void
	 */

	function action_pa_move() {
		$data = UTIL::get_post('data');			//	unsanitized
		$move_to = (int)$data['move_to'];		//	assumption?
		$move_id = (int)$_POST['edit_pid'];		//	assumption?
		$move_type = $data['move_type'];		//	assumption?

		if (!$move_to || !$move_id) { return; }

		$set = new NestedSet();
		$set->set_table_name($this->mod_tbl);

		switch (strtolower($move_type)) {
			case 'brother':
				$broth   = $set->getNode($move_to);
				$order   = $broth['lft'];
				$move_to = $broth['parent_id'];
				break;		//..............................................

			case 'child':	//	(CHECKME) deliberate fallthrough?
			default:
				$order = -1;
		}

		#MC::debug($order, 'NEW LEFT');	
		#MC::debug($move_to, 'PARENT_ID');
		#MC::debug($move_to);
		#MC::debug($_POST);

		$set->MoveNode($move_id, $move_to, $order);
		$this->set_start_view('pa_type');
	}

	/**
	 *	[en] carry over the checkboxes in the info array (post --> info)
	 *	[de] uebertragen der checkboxen in das info-array (post --> info)
	 *
	 *	@param	mixed	$data	(DOCUMENTME)
	 */

	private function _ar_post2info($data)
	{
		if($this->CLIENT->usr['id'] == $this->CLIENT->__root())
		{

			if (is_array($data['ar']['grp']))
			{
				foreach($data['ar']['grp'] as $gid => $ar)
				{
					$data['info']['grp'][$gid]['ar'] = array_sum($ar);
				}
			}

			if (is_array($data['ar']['usr']))
			{
				foreach($data['ar']['usr'] as $uid => $ar)
				{
					$data['info']['usr'][$uid]['ar'] = array_sum($ar);
				}
			}

			return $data;
		}

		$mod = $this->MC->get_modul_name($data['mid']);

		//	make sure the user cannot add rights he does not have
		$access_conf = $this->MC->access_config($mod);

		if(!is_error($access_conf))
		{
			foreach($access_conf['rights'] as $key => $ar)
			{
				if(!$this->MC->access($mod,$key,$this->pid)){ $strp_ar[] = $key; }
			}
		}

		//	(CHECKME) found commented out 2012-05-02
		#$mine = $data['info']['usr'][$this->CLIENT->usr['id']]['ar'];

		if (is_array($data['ar']['grp']))
		{
			foreach($data['ar']['grp'] as $gid => $ar) 
			{
				$current = (int)$data['info']['grp'][$gid]['ar'];
				$data['info']['grp'][$gid]['ar'] = 0;

				foreach($ar as $key => $list_ar)
				{
					if (!UTIL::in_array($key,$strp_ar))
					{
						$data['info']['grp'][$gid]['ar'] += (int)$list_ar;
					}
				}

				#if(((int)abs($data['info']['grp'][$gid]['ar']-$current) & $mine) === 0){
				if(!$this->MC->access($mod,$key,$this->pid))
				{
					$data['info']['grp'][$gid]['ar'] = $current;
				}
			}
		}

		if (is_array($data['ar']['usr']))
		{
			foreach($data['ar']['usr'] as $uid => $ar)
			{

				$current = (int)$data['info']['usr'][$uid]['ar'];
				$data['info']['usr'][$uid]['ar'] = 0;

				foreach($ar as $key => $list_ar)
				{
					if(!UTIL::in_array($key,$strp_ar))
					{
						$data['info']['usr'][$uid]['ar'] += (int)$list_ar;
					}
				}

				#if(((int)abs($data['info']['usr'][$uid]['ar']-$current) & $mine ) === 0){
				if(!$this->MC->access($mod,$key,$this->pid))
				{
					$data['info']['usr'][$uid]['ar'] = $current;
				}
			}
		}

		return $data;
	}

	/**
	 *	returns the path to the current page
	 *	consists of the names of the pages above
	 *
	 *	(DOCUMENTME) what calls this?
	 *	TODO: adding session and all the rest
	 *
	 *	@return	void
	 */

	function get_my_path()
	{
		include_once(CONF::inc_dir() . 'system/nested_sets.inc.php');

		$set = new NestedSet();
		$set->set_table_name('page');

		$path = $set->getPath($this->action['pid']);

		foreach($path as $piece) { $my_path = $piece["name"] . "/"; }

		$this->OPC->set_var("page", "my_path", CONF::baseurl() . $my_path . "index.html");

		echo CONF::baseurl() . $my_path . "index.html";
	}

	/**
	 *	called only? by opc during page restructuring
	 *	@return	void
	 */

	function callback_lost_mods_save($struct)
	{
		// collect the instances from the current page
		$select = "SELECT lost_mods FROM ".$this->mod_tbl." WHERE id = '".$this->pid."'";
		$res = $this->DB->query($select);
		if ($res->nr() > 1) { return; }

		$lost_mods = unserialize($res->f('lost_mods'));

		if (!is_array($lost_mods)) { $lost_mods = $struct; }
		else
		{
			//	translate lost mods to an array consisting only vids
			$lost_mods_vids = array();
			foreach($lost_mods as $item) { $lost_mods_vids[] = $item['vid']; }

			//	loop through the array and decide which ones to add
			foreach ($struct as $key => $item)
			{
				// attention: php 4.2!!
				if (!in_array($item['vid'],$lost_mods_vids)) { $lost_mods[] = $item; }
			}
		}

		//	update the instances
		$update = ''
		 . "UPDATE " . $this->mod_tbl
		 . " SET lost_mods = '" . serialize($lost_mods) . "'"
		 . " WHERE id = '" . $this->pid . "'";

		$this->DB->query($update);
		return;
	}

	/**
	 *	get the array of lost mods
	 *	(DOCUMENTME) how do mods get lost?
	 *
	 *	@param	string[string]	$params		dict containing 'pid' key
	 *	@return	string	(DOCUMENTME)
	 */

	function callback_lost_mods_get($params)
	{
		$select = ''
		 . "SELECT lost_mods FROM " . $this->mod_tbl
		 . " WHERE id = '" . $params['pid'] . "'";
		$res = $this->DB->query($select);

		return unserialize($res->f('lost_mods'));
	}

}

?>
