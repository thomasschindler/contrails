<?
class begin_action extends modAction 
{
	var $mod_name = 'begin';
	/**
	*	event distribution
	*/
	function main($action, $p = null)
	{
		switch(strtolower($action['event'])) 
		{
			case 'begin':			$this->begin();							break;
		}		
	}
	/**
	*	sample event
	*/
	function begin()
	{
		//MC::debug($this->data,'the data');
		$this->set_view('begin');
		$f = new forms('begin');
		if(!$f->valid())
		{
			return $this->OPC->error(e::o('begin_form_error'));
		}
		// write the data 
		return;
	}
}
?>
