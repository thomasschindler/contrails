<?php
	
/**
*	object browser allows 'dragging and dropping' objects from different dbtables
*	
*	@author 		Joachim Klinkhammer <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		mods
*/
	
	class objbrowser_view extends modView {
		
		var $tpl_dir = '';
		
		var $view_name = '';
		var $mod_name  = 'objbrowser';
		
		
		/**
		* konstruktor
		*/
		function objbrowser_view() {
			$this->tpl_dir = CONF::inc_dir() . 'mods/' . $this->mod_name . '/tpl/';
		}
		/**
		*	view distribution
		*/
		
		function main($view_name, $method_name) {
			
			$this->view_name = $view_name;
			
			switch(strtolower($method_name)) {
				case 'form':		return $this->view_form();		break;
				case 'admin_list':
				default:			return $this->view_list(); 		break;
			}
			
		}
		
		/**
		* finish
		*
		* javascript ausgeben, um die gewaehlten werte der eltern seite zukommen zu lassen
		*/
		function finish() {
			$this->OPC->generate_view($this->tpl_dir . 'finish.php');
			return;
		}
		/**
		*	show the list
		*/
		function view_list() 
		{
			$this->OPC->lnk_add("mod","objbrowser");
			
				
			if ($this->get_var('finish') === true) 
			{
				return $this->finish();
			}
			
			//-- welche objekte (db-tabellen) sollen dargestellt werden
			$pid  = (int)UTIL::get_post('pid');

			$base_lnk = array();
			
			$data = UTIL::get_post('data');
			
			//-- namen der javascript-funktion, der wir am ende das ergebnis liefern
			$callback = trim($data['callback']);
			if ($callback) 
			{
				$this->SESS->set('objbrowser', 'callback', $callback);
			}
			if($this->data['action'])
			{
				$this->SESS->set('objbrowser', 'mod_callback', $this->data['action']['mod']);
				$this->SESS->set('objbrowser', 'event_callback', $this->data['action']['event']);
				$this->SESS->set('objbrowser', 'vid_callback', $this->data['action']['vid']);
			}
			
			//-- art der rueckgabe (serialisiert, komma-separiert)
			if (!$this->SESS->get('objbrowser', 'return_type')) 
			{
				$return_type = ($data['return_type']) ? $data['return_type'] : 'ser';
				$this->SESS->set('objbrowser', 'return_type', $return_type);
			}

			//-- welche tabellen darstellen
			$tables = $data['tbl'];
			$offset = $data['offset'];
			if(UTIL::get_post("set_filter"))
			{
				$filter = $data['filter'];
			}
			else
			{
				$filter = UTIL::get_post("filter");
			}
			$this->set_var("filter",$filter);
			/*
			if (!is_array($tables)) {
				$tables = array('mod_usradmin_usr', 'mod_usradmin_grp', 'mod_news');
			}
			*/
			/*
			if (is_array($data['key']))
			{
				$this->SESS->set('objbrowser', 'key', $data['key']);
			}
			if (is_array($data['value'])) $this->SESS->set('objbrowser', 'value', $data['value']);
			*/
			if ($data['key'])
			{
				$this->SESS->set('objbrowser', 'key', $data['key']);
			}	
			if ($data['value'])
			{
				$this->SESS->set('objbrowser', 'value', $data['value']);
			}	
			$this->set_var('tables', $tables);
			
			$data = array();
			$base_lnk['mod'] = 'objbrowser';
			$base_lnk['filter'] = $filter;
			
			$tbl_cnt = 0;
			foreach($tables as $tbl) {
			
				$this->OPC->lnk_add('data[tbl][]',$tbl);
				
				
				$conf = $this->MC->table_config($tbl);
				
				$tbl = $conf['table']['name'];
				
				//$icon = ($conf['table']['icon']) ? $conf['table']['icon'] : 'icon_empty.gif';
				//$icon = '<img src="icons/'.$icon.'" width="16" height="16" alt="'.$conf['table']['label'].'">';
				
				$base_lnk['data[tbl]['.$tbl_cnt.']'] = $tbl;
				$base_lnk['data[offset]['.$tbl.']'] = $offset[$tbl]; // add the offset to everything
				$tbl_cnt ++;
				
				
				$data[$tbl] = array();
				$data[$tbl]['label'] = $conf['table']['label'];
				//$data[$tbl]['icon']  = $icon;
				
				//-- sind wir auf/zu
				if ($this->SESS->get('objbrowser', 'close:'.$tbl) == true) {

					$data[$tbl]['header'] = false;
					continue;
				}
				
				$header = explode(',', $conf['table']['title']);
				
				//-- aus db lesen
				// limit to a number ( conf? ) and let user page through
				
#				$filter = "oot";				
				$lim = 10;
				$from = $offset[$tbl] * $lim;
				$to = $lim + 1;
				
				$sql = 'SELECT id, '.$conf['table']['title'].' FROM ' . $tbl . ' WHERE 1=1' . $this->DB->table_restriction($tbl)." ".(isset($filter) ? ' AND '.$conf['table']['title'].' LIKE "%'.$filter.'%"' : '' )." LIMIT ".$from.", ".$to."";
				$res = $this->DB->query($sql);
				
				$next = (int)$res->nr()-$lim;
				if($next == 1)
				{
					$res->rm($res->nr()-1);
				}

				//-- header bestimmen
				$data[$tbl]['header'] = array();
				foreach($header as $field) {
					$data[$tbl]['header'][$field] = $conf['fields'][$field]['label'];
				}
				
				//-- daten eintragen
				$data[$tbl]['data'] = array();
				$cnt = 0;
				while($res->next()) {

					$data[$tbl]['data'][$cnt]['id'] = htmlspecialchars($res->f('id'));
					foreach($conf['fields'] as $field => $info) {
						$data[$tbl]['data'][$cnt][$field] = htmlspecialchars($res->f($field));
					}
					
					$cnt++;
				}
				
#				$data[$tbl]['nav'] = $offset[$tbl] == 0 ? "" : $this->OPC->create_icon('online_red','',array('data[offset]['.$tbl.']'=>($offset[$tbl]-1)));
#				$data[$tbl]['nav'] .= ( $next == 1 )  ? $this->OPC->create_icon('arrow_red','',array('data[offset]['.$tbl.']'=>($offset[$tbl]+1))) : "";
	
				$data[$tbl]['nav']['up'] = $offset[$tbl] == 0 ? null : ($offset[$tbl]-1);
				$data[$tbl]['nav']['down'] = ( $next == 1 )  ? ($offset[$tbl]+1) : null;
				$base_lnk[$tbl]['offset']['current'] = $offset[$tbl];
	
				$this->OPC->lang_page_end();
			}
						
			$this->set_var('base_lnk', $base_lnk);
			$this->set_var('data', $data);
			
			$this->show('list');

			//$this->OPC->generate_view($this->tpl_dir . 'list.php');
			
		}
		
		/**
		*	create the form
		*/
		function view_form() {
			$form = MC::create('form');
			$form->init('news', 'mod_news');

			$form->add_hidden('mod',  $this->mod_name);
			$form->add_hidden('area', $this->view_name);
			
			// bei fehler, diese und daten in form setzen
			if ($this->get_var('error')) {
				$form->set_values($this->get_var('data'));
				$form->set_error_fields($this->get_var('error'));
			}

			// wenn edit_id gesetzt und kein fehler, daten aus db laden
			if ($id = (int)$this->get_var('edit_id')) {
				if (!$this->get_var('error')) {
					$sql = 'SELECT * FROM '. $this->tbl_news .' WHERE id='.$id;
					$res = $this->DB->query($sql);
					$form->set_values($res->r());
				}
				$form->add_hidden_pre('edit_id', $id);
			}
			

			$this->set_var('form', &$form);
			$this->set_var('headline', 'Neue Nachricht eingeben');
			
			$this->OPC->generate_view($this->tpl_dir . 'form.php');
		}
		
		
	}
?>
