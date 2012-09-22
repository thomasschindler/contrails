<?
class begin_view extends modView 
{
	var $mod_name = 'begin';
	/**
	*	view method distribution
	*/
	function main($vid, $method_name) 
	{
		$this->vid = $vid;
		switch(strtolower($method_name)) 
		{
			case 'begin':			
			default:						$this->begin();					break;
		}
	}
	/**
	*	start here to learn how to work with contrails
	*/
	function begin()
	{
		if($this->access('begin'))
		{
			$this->set_var('link',$this->lnk(array('event' => 'begin')));
		}
		$f = new forms('begin');
		$this->set_var('form',$f->show());
		$this->show('begin');
	}
}
?>
