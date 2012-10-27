<?
class test_action extends modAction 
{
	var $mod_name = 'test';
	/**
	*	event distribution
	*/
	function main($action, $p = null)
	{
		switch(strtolower($action['event'])) 
		{
			case 'load':			$this->load_model();					break;
			case 'test':			$this->test();							break;
		}		
	}
	/**
	*	sample event
	*/
	function test()
	{
		//MC::debug($this->data,'the data');
		$this->set_view('test');
		$f = new forms('test');
		if(!$f->valid())
		{
			return $this->OPC->error(e::o('test_form_error'));
		}
		// write the data 
		return;
	}

	function load_model(){
		$MF = MC::singleton();
		$instance = $MF->obtain('test_table', 1);
		var_dump($instance);
	}
}
?>
