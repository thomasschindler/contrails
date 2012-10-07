<?
class models_abstract_mod_page_acl extends model
{
	var $_fields = array();
	var $_table = 'mod_page_acl';

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

	function type($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',1))
			{
				return false;
			}
			$this->_fields['type'] = $d;
			return true;
		}
		return $this->_fields['type'];
	}

	function aid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['aid'] = $d;
			return true;
		}
		return $this->_fields['aid'];
	}

	function ar($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',32))
			{
				return false;
			}
			$this->_fields['ar'] = $d;
			return true;
		}
		return $this->_fields['ar'];
	}

	function inherit_pid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['inherit_pid'] = $d;
			return true;
		}
		return $this->_fields['inherit_pid'];
	}
}
?>