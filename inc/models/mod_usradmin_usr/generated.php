<?
class generated_mod_usradmin_usr extends model
{
	var $_fields = array();
	var $_table = 'mod_usradmin_usr';

	protected function _primary()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
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