<?
class generated_uftp_resources extends model
{

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),'1' => array('type' => 'KEY','fields' => array('0' => 'unfuck_id','1' => 'location_id','2' => 'avail_from','3' => 'avail_to',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => 'auto_increment',),'unfuck_id' => array('Field' => 'unfuck_id','Type' => 'int(11)','Null' => 'YES','Key' => 'MUL','Default' => NULL,'Extra' => '',),'location_id' => array('Field' => 'location_id','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'description' => array('Field' => 'description','Type' => 'text','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'unit' => array('Field' => 'unit','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'quantity' => array('Field' => 'quantity','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'avail_from' => array('Field' => 'avail_from','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'avail_to' => array('Field' => 'avail_to','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'created_at' => array('Field' => 'created_at','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'updated_at' => array('Field' => 'updated_at','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),);
	}

	public function table_name()
	{
		return 'uftp_resources';
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

	public function unfuck_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('unfuck_id'=>$d));
			$this->_fields['unfuck_id'] = $d;
			return true;
		}
		return $this->_fields['unfuck_id'];
	}

	public function location_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('location_id'=>$d));
			$this->_fields['location_id'] = $d;
			return true;
		}
		return $this->_fields['location_id'];
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

	public function unit($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('unit'=>$d));
			$this->_fields['unit'] = $d;
			return true;
		}
		return $this->_fields['unit'];
	}

	public function quantity($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('quantity'=>$d));
			$this->_fields['quantity'] = $d;
			return true;
		}
		return $this->_fields['quantity'];
	}

	public function avail_from($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('avail_from'=>$d));
			$this->_fields['avail_from'] = $d;
			return true;
		}
		return $this->_fields['avail_from'];
	}

	public function avail_to($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('avail_to'=>$d));
			$this->_fields['avail_to'] = $d;
			return true;
		}
		return $this->_fields['avail_to'];
	}

	public function created_at($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('created_at'=>$d));
			$this->_fields['created_at'] = $d;
			return true;
		}
		return $this->_fields['created_at'];
	}

	public function updated_at($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('updated_at'=>$d));
			$this->_fields['updated_at'] = $d;
			return true;
		}
		return $this->_fields['updated_at'];
	}
}
?>