<?
class uftp_fuck_action extends modAction 
{
	var $mod_name = 'uftp_fuck';
	/**
	*	event distribution
	*/
	function main($action, $p = null)
	{
		switch(strtolower($action['event'])) 
		{
			case 'uftp_fuck':			$this->uftp_fuck();							break;
		}		
	}
	/**
	*	sample event
	*/
	function uftp_fuck()
	{
		//MC::debug($this->data,'the data');
		$this->set_view('uftp_fuck');
		$f = new forms('uftp_fuck');
		if(!$f->valid())
		{
			return $this->OPC->error(e::o('uftp_fuck_form_error'));
		}
		// write the data 
		return;
	}
}
?>
