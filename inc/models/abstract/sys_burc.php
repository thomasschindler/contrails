<?
class models_abstract_sys_burc extends model
{
	var $_fields = array();
	var $_table = 'sys_burc';

	function burc($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',20))
			{
				return false;
			}
			$this->_fields['burc'] = $d;
			return true;
		}
		return $this->_fields['burc'];
	}

	function pid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['pid'] = $d;
			return true;
		}
		return $this->_fields['pid'];
	}

	function data($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['data'] = $d;
			return true;
		}
		return $this->_fields['data'];
	}

	function sys_date_created($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['sys_date_created'] = $d;
			return true;
		}
		return $this->_fields['sys_date_created'];
	}

	function permanent($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->_fields['permanent'] = $d;
			return true;
		}
		return $this->_fields['permanent'];
	}
}
?>