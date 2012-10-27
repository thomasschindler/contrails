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

		$u = &$this->MOF->obtain('uftp_fucks');
		
		$u->title('title');
		$u->description('description');
		$u->image('image');
		$u->user_id(1);
		$u->location_id(1);
		$u->created_at(time());
		$u->updated_at(time());

		return;
		
		$b = &$this->MF->obtain('sys_burc','p108071938_348');

		$b->permanent(1);

		//$c = $m->obtain('test_table', null, array('field2' => 23, 'field1' => 'Something or another'));

		///$b->permanent(1);

		//$b->save();

		//$b = new sys_burc();

		//$b->load('p108071938_348');

		MC::debug($b);

		return;


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

		
		// pull data 
		$b1 = &$this->MOF->obtain('sys_burc','p108071938_348');
		// modify data
		$b1->permanent(1);
	
		// create a new object
		$b2 = &$this->MOF->obtain('sys_burc',array
			(
				'burc' =>'test',
				'pid' => 500,
				'permanent'=>1,
				'data' => 'da',
				'sys_date_created' => 1348350503
			));
		
		// create a new empty object
		$b3 = &$this->MOF->obtain('sys_burc');
		
		$b3->permanent(1);
		$b3->burc(time());
		$b3->sys_date_created(time());
		$b3->pid(555);
		$b3->data(time());


		$this->MOF->register($b3);
		
		$b3->burc('whatever');

		MC::debug($b3);

		/*


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
