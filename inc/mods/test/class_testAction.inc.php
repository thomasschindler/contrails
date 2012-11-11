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
			case 'step0':			$this->step0();							break;
			case 'step1':			$this->step1();							break;
			case 'step2':			$this->step2();							break;
		}		
	}

	/**
	*	step2 of the creation process
	*	add the currency
	*/

	function step2()
	{
		// clone the currency
		$r = $this->CRUD->load('tajapa_currency','id',$this->data['tajapa_currency']);
		$c = $r->r();
		$c['template'] = 0;
		unset($c['id']);
		$tajapa_currency = $this->CRUD->create('tajapa_currency',$c);
		// add it to the marketplace
		$this->CRUD->update('tajapa_marketplace','id',$this->data['tajapa_marketplace'],array('tajapa_currency'=>$tajapa_currency));
		// go to step3
		$this->set_view('step3');

	}

	/**
	*	store the current marketplace
	*	for the current user
	*/

	function step1()
	{
		$this->data['uid'] = $this->CLIENT->usr['id'];
		$this->data['created'] = time();
		$this->data['tajapa_currency'] = 0;
		if(($tajapa_marketplace = $this->form('tajapa_marketplace')) != false)
		{
			$this->OPC->lnk_add('data[tajapa_marketplace]',$tajapa_marketplace->id());
			return $this->set_view('step2');
		}
		return $this->set_view('step1');
	}

	function step0()
	{
		$f = new FORMS('step0');
		if(!$f->valid())
		{
			$this->OPC->error(e::o('step0_form_error'));
			return $this->set_view('step0');
		}
		$this->OPC->success(e::o('step0_form_success'));
		// create the user
		$this->MC->call('usradmin','usr_create',array
		(
			'info' => array
			(
				'usr' => $this->data['usr'],
				'pwd' => $this->data['pwd'],
				'email' => $this->data['email'],
			),
			'grp' => array
			(
				68,66
			),
			'login' => true,
		));
		$this->set_view('step1');
	}
}
?>
