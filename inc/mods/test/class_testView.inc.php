<?
class test_view extends modView 
{
	var $mod_name = 'test';
	/**
	*	view method distribution
	*/
	function main($vid, $method_name) 
	{
		$this->vid = $vid;
		switch(strtolower($method_name)) 
		{
			case 'test':			
			default:						$this->test();					break;
		}
	}
	/**
	*	start here to learn how to work with contrails
	*/
	function test()
	{

		
		$b = &$this->MF->obtain('sys_burc','p108071938_348');

		$b->permanent(1);

		//$c = $m->obtain('test_table', null, array('field2' => 23, 'field1' => 'Something or another'));

		///$b->permanent(1);

		//$b->save();

		//$b = new sys_burc();

		//$b->load('p108071938_348');

		MC::debug($b);

		return;

<<<<<<< HEAD
/*
		$this->facebook = new Facebook(array(
		  'appId'  => '492651730746756',
		  'secret' => '789e7719ff2478bf2ce48c107c7d7da0',
		));	
		$this->facebook->setAccessToken('AAAHAEGtovYQBAFsLi6elFySwMnIsZBRyhUNyHktRU1rGYWiikukIK5KniOcr3CsWgcEsAX0jZB9wm9cQmvEk6gTWnKvCCUmZA7x9MZCSyQZDZD');
		$this->facebook->getUser();

		MC::debug($this->user);

*/

		$this->facebook = new Facebook(array(
		  'appId'  => '492651730746756',
		  'secret' => '789e7719ff2478bf2ce48c107c7d7da0',
		));	

		$this->user = $this->facebook->getUser();

		if(!$this->user)
		{
			echo '<a href="'.$this->facebook->getLoginUrl(array('scope'=>'publish_stream','user_location','friends_location')).'">'.e::o('login').'</a>';
		}
		else
		{
			echo '<a href="'.$this->facebook->getLogoutUrl().'">'.e::o('logout').'</a>';
		}


		MC::debug($this->user);
		MC::debug($this->facebook->getAccessToken());
		if($this->facebook->setExtendedAccessToken() == true)
		{
			MC::debug("YES");
		}
		else
		{
			MC::debug("NO");
		}
		MC::debug($this->facebook->getAccessToken());
		
		/*

=======
>>>>>>> master
		if($this->access('test'))
		{
			$this->set_var('link',$this->lnk(array('event' => 'test')));
		}
		$f = new forms('test');
		$this->set_var('form',$f->show());
		$this->show('test');
		*/
	}
}
?>
