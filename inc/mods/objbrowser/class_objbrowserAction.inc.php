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
	
	class objbrowser_action extends modAction{
	
		var $action;
		
		var $view_name = '';
		var $mod_name  = 'objbrowser';
		
		
		function objbrowser_action() {
		}
		
		/*
		* 'verteiler-funktion' wird von MC::call_action() aufgerufen
		*
		*@param	array	$action
		*@return 
		*/
		function main($action) {
			
			// da wir keine seite brauchen, setzen wir den start-view auf uns
			$this->set_start_view('admin_list');
			
			$this->action    = $action;
			$this->vid = UTIL::get_post('vid');
			
			switch(strtolower($action['event'])) 
			{
				case 'admin_list':	$this->set_start_view('admin_list');	break;
				case 'open':		$this->action_open();					break;
				case 'close':		$this->action_close();					break;
				case 'add':			$this->action_add();					break;
				case 'remove':		$this->action_remove();					break;
				case 'clear':		$this->action_clear();					break;
				case 'finish':		$this->action_finish();					break;
			}
			
		}
		
		/**
		* tabelle oeffnen/anzeigen
		*/
		function action_close() {	
			$tbl = UTIL::get_post('data');
			$tbl = $tbl['close'];
			$this->SESS->set('objbrowser', 'close:'.$tbl, true);
		}
		/**
		* tabelle schliessen/nicht anzeigen
		*/
		function action_open() {	
			$tbl = UTIL::get_post('data');
			$tbl = $tbl['open'];
			$this->SESS->remove('objbrowser', 'close:'.$tbl);
		}
		
		
		/**
		* element zu auswahlliste hinzufuegen
		*/
		function action_add() {
			$data  = UTIL::get_post('data');
			$id    = (int)$data['add_id'];
			$table = $data['add_table'];
			
			$choosen = $this->SESS->get('objbrowser', 'choosen');
			$choosen[$table][$id] = $id;
			$this->SESS->set('objbrowser', 'choosen', $choosen);
		}
		
		
		/*
		* element von 'ausgewhlte objekte' entfernen
		*/
		function action_remove() {
			$data = UTIL::get_post('data');
			$id    = (int)$data['rem_id'];
			$table = $data['rem_table'];
			
			$choosen = $this->SESS->get('objbrowser', 'choosen');
			unset($choosen[$table][$id]);
			if (count($choosen[$table]) == 0) unset($choosen[$table]);
			
			$this->SESS->set('objbrowser', 'choosen', $choosen);
		}
		
		/**
		* alle elemente aus 'ausgewhlte objekte' entfernen
		*/
		function action_clear() {
			$this->SESS->remove('objbrowser', 'choosen');
		}
		
		/**
		* auswaehlen button geclickt
		*/
		function action_finish() 
		{
		
			$mod = $this->SESS->get("objbrowser","mod_callback");
			$event = $this->SESS->get("objbrowser","event_callback");
			

			if($mod AND $event)
			{
				$this->MC->call_action(array(
					'mod'=>$mod,'event'=>$event
				),
				array_merge($this->SESS->get('objbrowser', 'choosen'),array('vid'=>$this->SESS->get("objbrowser","vid_callback")))
				);
			}
			$this->set_var('finish', true);
			$this->set_var('choosen', $this->SESS->get('objbrowser', 'choosen'));
			$this->SESS->remove('objbrowser', 'choosen');
			$this->SESS->remove('objbrowser', 'mod_callback');
			$this->SESS->remove('objbrowser', 'event_callback');
			$this->SESS->remove('objbrowser', 'vid_callback');
		}
	}
	
?>
