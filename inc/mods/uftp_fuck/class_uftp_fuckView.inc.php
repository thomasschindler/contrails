<?
class uftp_fuck_view extends modView 
{
	var $mod_name = 'uftp_fuck';
	/**
	*	view method distribution
	*/
	function main($vid, $method_name) 
	{
		$this->vid = $vid;
		switch(strtolower($method_name)) 
		{
			case 'uftp_fuck':			
			default:						$this->uftp_fuck();					break;
		}
	}
	/**
	*	start here to learn how to work with contrails
	*/
	function uftp_fuck()
	{
		if($this->access('uftp_fuck'))
		{
			$this->set_var('link',$this->lnk(array('event' => 'uftp_fuck')));
		}
		$f = new forms('uftp_fuck');
		$this->set_var('form',$f->show());
		$this->show('uftp_fuck');
	}
}
?>
