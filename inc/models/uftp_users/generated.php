<?
class generated_uftp_users extends model
{
	var $_table = 'uftp_users';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => 'auto_increment',),'firstname' => array('Field' => 'firstname','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'lastname' => array('Field' => 'lastname','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'gender' => array('Field' => 'gender','Type' => 'enum(\'M\',\'F\')','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'email' => array('Field' => 'email','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'auth_provider' => array('Field' => 'auth_provider','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'auth_token' => array('Field' => 'auth_token','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'auth_uid' => array('Field' => 'auth_uid','Type' => 'double','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'created_at' => array('Field' => 'created_at','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'updated_at' => array('Field' => 'updated_at','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),);
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

	public function firstname($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('firstname'=>$d));
			$this->_fields['firstname'] = $d;
			return true;
		}
		return $this->_fields['firstname'];
	}

	public function lastname($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('lastname'=>$d));
			$this->_fields['lastname'] = $d;
			return true;
		}
		return $this->_fields['lastname'];
	}

	public function gender($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'enum',0))
			{
				return false;
			}
			$this->push_update(array('gender'=>$d));
			$this->_fields['gender'] = $d;
			return true;
		}
		return $this->_fields['gender'];
	}

	public function email($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('email'=>$d));
			$this->_fields['email'] = $d;
			return true;
		}
		return $this->_fields['email'];
	}

	public function auth_provider($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('auth_provider'=>$d));
			$this->_fields['auth_provider'] = $d;
			return true;
		}
		return $this->_fields['auth_provider'];
	}

	public function auth_token($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('auth_token'=>$d));
			$this->_fields['auth_token'] = $d;
			return true;
		}
		return $this->_fields['auth_token'];
	}

	public function auth_uid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'double'))
			{
				return false;
			}
			$this->push_update(array('auth_uid'=>$d));
			$this->_fields['auth_uid'] = $d;
			return true;
		}
		return $this->_fields['auth_uid'];
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