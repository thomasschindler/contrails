<?
class generated_mod_page extends model
{
	var $_fields = array();
	var $_table = 'mod_page';

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

	function name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['name'] = $d;
			return true;
		}
		return $this->_fields['name'];
	}

	function url($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['url'] = $d;
			return true;
		}
		return $this->_fields['url'];
	}

	function title($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['title'] = $d;
			return true;
		}
		return $this->_fields['title'];
	}

	function description($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['description'] = $d;
			return true;
		}
		return $this->_fields['description'];
	}

	function keywords($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['keywords'] = $d;
			return true;
		}
		return $this->_fields['keywords'];
	}

	function lft($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['lft'] = $d;
			return true;
		}
		return $this->_fields['lft'];
	}

	function rgt($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['rgt'] = $d;
			return true;
		}
		return $this->_fields['rgt'];
	}

	function root_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['root_id'] = $d;
			return true;
		}
		return $this->_fields['root_id'];
	}

	function parent_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['parent_id'] = $d;
			return true;
		}
		return $this->_fields['parent_id'];
	}

	function set_ignore($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->_fields['set_ignore'] = $d;
			return true;
		}
		return $this->_fields['set_ignore'];
	}

	function template_name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['template_name'] = $d;
			return true;
		}
		return $this->_fields['template_name'];
	}

	function rights($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['rights'] = $d;
			return true;
		}
		return $this->_fields['rights'];
	}

	function structure($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['structure'] = $d;
			return true;
		}
		return $this->_fields['structure'];
	}

	function lost_mods($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['lost_mods'] = $d;
			return true;
		}
		return $this->_fields['lost_mods'];
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

	function cookie_name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['cookie_name'] = $d;
			return true;
		}
		return $this->_fields['cookie_name'];
	}

	function cookie_lifetime($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['cookie_lifetime'] = $d;
			return true;
		}
		return $this->_fields['cookie_lifetime'];
	}

	function cookie_value($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['cookie_value'] = $d;
			return true;
		}
		return $this->_fields['cookie_value'];
	}

	function redirect_to($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['redirect_to'] = $d;
			return true;
		}
		return $this->_fields['redirect_to'];
	}
}
?>