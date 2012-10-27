<?
class generated_uftp_fucks extends model
{
	var $_table = 'uftp_fucks';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),'1' => array('type' => 'KEY','fields' => array('0' => 'user_id','1' => 'location_id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => 'auto_increment',),'title' => array('Field' => 'title','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'description' => array('Field' => 'description','Type' => 'text','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'image' => array('Field' => 'image','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'user_id' => array('Field' => 'user_id','Type' => 'int(11)','Null' => 'YES','Key' => 'MUL','Default' => NULL,'Extra' => '',),'location_id' => array('Field' => 'location_id','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'created_at' => array('Field' => 'created_at','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'updated_at' => array('Field' => 'updated_at','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),);
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

	public function title($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('title'=>$d));
			$this->_fields['title'] = $d;
			return true;
		}
		return $this->_fields['title'];
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

	public function image($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('image'=>$d));
			$this->_fields['image'] = $d;
			return true;
		}
		return $this->_fields['image'];
	}

	public function user_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('user_id'=>$d));
			$this->_fields['user_id'] = $d;
			return true;
		}
		return $this->_fields['user_id'];
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