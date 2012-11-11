<?
class generated_tajapa_marketplace extends model
{

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => 'auto_increment',),'label' => array('Field' => 'label','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => '','Extra' => '',),'description' => array('Field' => 'description','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'uid' => array('Field' => 'uid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'created' => array('Field' => 'created','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'tajapa_currency' => array('Field' => 'tajapa_currency','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),);
	}

	public function table_name()
	{
		return 'tajapa_marketplace';
	}


	public function id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('id'=>$d));
			$this->_fields['id'] = $d;
			return true;
		}
		return $this->_fields['id'];
	}

	public function label($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('label'=>$d));
			$this->_fields['label'] = $d;
			return true;
		}
		return $this->_fields['label'];
	}

	public function description($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('description'=>$d));
			$this->_fields['description'] = $d;
			return true;
		}
		return $this->_fields['description'];
	}

	public function uid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('uid'=>$d));
			$this->_fields['uid'] = $d;
			return true;
		}
		return $this->_fields['uid'];
	}

	public function created($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('created'=>$d));
			$this->_fields['created'] = $d;
			return true;
		}
		return $this->_fields['created'];
	}

	public function tajapa_currency($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('tajapa_currency'=>$d));
			$this->_fields['tajapa_currency'] = $d;
			return true;
		}
		return $this->_fields['tajapa_currency'];
	}
}
?>