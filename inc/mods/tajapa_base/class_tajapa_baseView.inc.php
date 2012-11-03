<?
class tajapa_base_view extends modView 
{
	var $mod_name = 'tajapa_base';
	/**
	*	view method distribution
	*/
	function main($vid, $method_name) 
	{
		$this->vid = $vid;
		switch(strtolower($method_name)) 
		{
			case 'tajapa_base':			
			default:						$this->tajapa_base();					break;
		}
	}
	/**
	*	start here to learn how to work with contrails
	*/
	function tajapa_base()
	{

		//$b = &$this->MOF->obtain('tajapa_currency',array('uid'=>12,'label'=>'booya'));
		
		/*		
		MC::debug($b);

		return;

		if($this->access('tajapa_base'))
		{
			$this->set_var('link',$this->lnk(array('event' => 'tajapa_base')));
		}
		$f = new forms('tajapa_base');
		$this->set_var('form',$f->show());
		$this->show('tajapa_base');
		*/
	}
}
?>
