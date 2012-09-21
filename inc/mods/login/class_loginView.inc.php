<?php

/**
* klasse verwaltet das login und weist grund-gruppen-zugeh�rigkeit zu 
*
* alle view klassen erben von ihr, z.b. OPC, DB etc. 
*
*@version	0.1.0	13.01.04
*@author	Joachim Klinkhammer <development@hundertelf.com>
*@access	public
*@package	mods
*/

	class login_view extends modView {
		
		var $view;
		
		var $tpl_dir;
		
		var $mod_name = 'login';
		function login_view() {
//			$this->tpl_dir = CONF::inc_dir() . 'mods/login/tpl/';
		}
		
		
		function main($vid, $method_name) 
		{
			switch(strtolower($method_name)) 
			{
				case 'retrieve':		$this->retrieve();					break;
				case 'login_show':		$this->login_show();				break;
				default:				$this->button();					break;
			}
		}
		/**
		*	view for password retrieval
		*/
		function retrieve()
		{
			$switch = $this->get_var('found');
			switch($switch){
				case 1:
					$this->set_var('msg',e::o('v_sent_u_pwd'));
				break;
				case -1:
					$this->set_var('msg',e::o('v_no_match')."<br>");
					$this->set_var('form','true');
				break;
				default:
					$this->set_var('msg',e::o('v_retrieve_txt'));
					$this->set_var('form','true');
			}
			
			$this->generate_view('pwd_retrieve.php',true);
		}

		function login_show() 
		{
            $f = new forms();
            $f->create('login');
            $f->button('login',e::o('login'));
            $f->hidden('mod','login');
            $this->set_var('content',$f->show());
            $this->show('login');
		}

		function button()
		{
			if($this->CLIENT->usr['is_default'])
			{
			$f = new forms();
            $f->create('login');
            $f->button('login',e::o('login'));
            $f->hidden('mod','login');

				echo '<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Login<strong class="caret"></strong></a><div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">'.$f->show().'</div></li>';
				return;
			}
			echo '<li><a href="'.$this->OPC->lnk(array('event'=>'logout','mod'=>'login')).'">Logout [ '.$this->CLIENT->usr['usr'].' ]</a></li>';
		}
	}

?>
