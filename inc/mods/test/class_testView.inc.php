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
