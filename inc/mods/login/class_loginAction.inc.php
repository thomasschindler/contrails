<?php
/**
* action-klasse
*
* @version	0.1.0 14.01.04
* @author	Oli Blum <development@hundertelf.com>
* @package	mods
* @access	public
* wird von MC::call_action() instanziert
*
* Enthaelt alle Funktionen zum abhandeln des Logins für Benutzer sowie die Registrierung neuer Benutzer.
* Fehlermeldungen etc.
*
*
*/

	class login_action extends modAction {
	
		var $action;
		var $mod_name = 'login';

		function login_action() {}
		
		/*
		* 'verteiler-funktion' wird von MC::call_action() aufgerufen
		*
		*@param        array        $action
		*@return
		*/
		function main($action,$params=null) 
		{
			switch(strtolower($action['event'])) 
			{
				case 'login_show':				$this->set_start_view('login_show');			break;
				case 'login':					$this->login_sent();							break;
				case 'logout':					$this->logout();								break;
				case 'pwd_retrieve_start':		$this->set_view('pwd_retrieve');				break;
				case "find_pwd":				$this->action_find_pwd();						break;
			}
		
		}

		/**
		*	try and find the password by username and / or emailadress
		*	and send the user an email with the pwd
		*/
		function action_find_pwd(){
			$this->set_var('pwd_retrieve','true');
			$this->data = array(
				'email' => $this->data['email'],
				'usr' => $this->data['uname']
			);
			$found = $this->MC->call_action(array('mod' => 'usradmin', 'event' => 'find_pwd'), $this->data);
			return $this->set_var('found',$found);
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
		function login_sent() 
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

		
	}
?>
