<?
class generated_mod_usradmin_usr extends model
{
	var $_fields = array();
	var $_table = 'mod_usradmin_usr';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'show_id' => array('Field' => 'show_id','Type' => 'varchar(16)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'usr' => array('Field' => 'usr','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'pwd' => array('Field' => 'pwd','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'name' => array('Field' => 'name','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'email' => array('Field' => 'email','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'tel' => array('Field' => 'tel','Type' => 'varchar(40)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'fax' => array('Field' => 'fax','Type' => 'varchar(40)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'street' => array('Field' => 'street','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'num' => array('Field' => 'num','Type' => 'varchar(10)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'zip' => array('Field' => 'zip','Type' => 'varchar(10)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'city' => array('Field' => 'city','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'country' => array('Field' => 'country','Type' => 'char(2)','Null' => 'NO','Key' => '','Default' => 'de','Extra' => '',),'lang' => array('Field' => 'lang','Type' => 'tinyint(2)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'lang_default' => array('Field' => 'lang_default','Type' => 'tinyint(1)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'type' => array('Field' => 'type','Type' => 'tinyint(1)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'register_key' => array('Field' => 'register_key','Type' => 'varchar(64)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'accept' => array('Field' => 'accept','Type' => 'tinyint(1)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_trashcan' => array('Field' => 'sys_trashcan','Type' => 'smallint(1) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_date_created' => array('Field' => 'sys_date_created','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_date_changed' => array('Field' => 'sys_date_changed','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_date_lastlogin' => array('Field' => 'sys_date_lastlogin','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
	}


	function id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['id'] = $d;
			return true;
		}
		return $this->_fields['id'];
	}

	function show_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',16))
			{
				return false;
			}
			$this->_fields['show_id'] = $d;
			return true;
		}
		return $this->_fields['show_id'];
	}

	function pid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['pid'] = $d;
			return true;
		}
		return $this->_fields['pid'];
	}

	function usr($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['usr'] = $d;
			return true;
		}
		return $this->_fields['usr'];
	}

	function pwd($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['pwd'] = $d;
			return true;
		}
		return $this->_fields['pwd'];
	}

	function name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['name'] = $d;
			return true;
		}
		return $this->_fields['name'];
	}

	function email($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['email'] = $d;
			return true;
		}
		return $this->_fields['email'];
	}

	function tel($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',40))
			{
				return false;
			}
			$this->_fields['tel'] = $d;
			return true;
		}
		return $this->_fields['tel'];
	}

	function fax($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',40))
			{
				return false;
			}
			$this->_fields['fax'] = $d;
			return true;
		}
		return $this->_fields['fax'];
	}

	function street($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['street'] = $d;
			return true;
		}
		return $this->_fields['street'];
	}

	function num($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',10))
			{
				return false;
			}
			$this->_fields['num'] = $d;
			return true;
		}
		return $this->_fields['num'];
	}

	function zip($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',10))
			{
				return false;
			}
			$this->_fields['zip'] = $d;
			return true;
		}
		return $this->_fields['zip'];
	}

	function city($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['city'] = $d;
			return true;
		}
		return $this->_fields['city'];
	}

	function country($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'char',2))
			{
				return false;
			}
			$this->_fields['country'] = $d;
			return true;
		}
		return $this->_fields['country'];
	}

	function lang($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',2))
			{
				return false;
			}
			$this->_fields['lang'] = $d;
			return true;
		}
		return $this->_fields['lang'];
	}

	function lang_default($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->_fields['lang_default'] = $d;
			return true;
		}
		return $this->_fields['lang_default'];
	}

	function type($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->_fields['type'] = $d;
			return true;
		}
		return $this->_fields['type'];
	}

	function register_key($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',64))
			{
				return false;
			}
			$this->_fields['register_key'] = $d;
			return true;
		}
		return $this->_fields['register_key'];
	}

	function accept($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->_fields['accept'] = $d;
			return true;
		}
		return $this->_fields['accept'];
	}

	function sys_trashcan($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'smallint',1))
			{
				return false;
			}
			$this->_fields['sys_trashcan'] = $d;
			return true;
		}
		return $this->_fields['sys_trashcan'];
	}

	function sys_date_created($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['sys_date_created'] = $d;
			return true;
		}
		return $this->_fields['sys_date_created'];
	}

	function sys_date_changed($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['sys_date_changed'] = $d;
			return true;
		}
		return $this->_fields['sys_date_changed'];
	}

	function sys_date_lastlogin($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['sys_date_lastlogin'] = $d;
			return true;
		}
		return $this->_fields['sys_date_lastlogin'];
	}
}
?>