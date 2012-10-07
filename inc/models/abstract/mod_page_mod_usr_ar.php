<?
class models_abstract_mod_page_mod_usr_ar extends model
{
	var $_fields = array();
	var $_table = 'mod_page_mod_usr_ar';

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

	function uid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['uid'] = $d;
			return true;
		}
		return $this->_fields['uid'];
	}

	function mid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['mid'] = $d;
			return true;
		}
		return $this->_fields['mid'];
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