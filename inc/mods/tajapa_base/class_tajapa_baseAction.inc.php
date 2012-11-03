<?
class tajapa_base_action extends modAction 
{
	var $mod_name = 'tajapa_base';
	/**
	*	event distribution
	*/
	function main($action, $p = null)
	{
		switch(strtolower($action['event'])) 
		{
			case 'tajapa_base':			$this->tajapa_base();							break;
		}		
	}
	/**
	*	sample event
	*/
	function tajapa_base()
	{
		//MC::debug($this->data,'the data');
		$this->set_view('tajapa_base');
		$f = new forms('tajapa_base');
		if(!$f->valid())
		{
			return $this->OPC->error(e::o('tajapa_base_form_error'));
		}
		// write the data 
		return;
	}
}
?>
