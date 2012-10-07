<?
class models_abstract_sys_mc_listeners extends model
{
	var $_fields = array();
	var $_table = 'sys_mc_listeners';

	function id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['id'] = $d;
			return true;
		}
		return $this->_fields['id'];
	}

	function mod_shout($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',200))
			{
				return false;
			}
			$this->_fields['mod_shout'] = $d;
			return true;
		}
		return $this->_fields['mod_shout'];
	}

	function event_shout($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',200))
			{
				return false;
			}
			$this->_fields['event_shout'] = $d;
			return true;
		}
		return $this->_fields['event_shout'];
	}

	function mod_listen($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',200))
			{
				return false;
			}
			$this->_fields['mod_listen'] = $d;
			return true;
		}
		return $this->_fields['mod_listen'];
	}

	function event_listen($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',200))
			{
				return false;
			}
			$this->_fields['event_listen'] = $d;
			return true;
		}
		return $this->_fields['event_listen'];
	}

	function att_listen($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['att_listen'] = $d;
			return true;
		}
		return $this->_fields['att_listen'];
	}

	function start($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['start'] = $d;
			return true;
		}
		return $this->_fields['start'];
	}

	function stop($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['stop'] = $d;
			return true;
		}
		return $this->_fields['stop'];
	}

	function pre($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->_fields['pre'] = $d;
			return true;
		}
		return $this->_fields['pre'];
	}
}
?>