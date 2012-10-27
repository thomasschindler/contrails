<?
class generated_mod_page_mod_grp_ar extends model
{
	var $_fields = array();
	var $_table = 'mod_page_mod_grp_ar';

	protected function _keys()
	{
		return array('0' => array('type' => 'KEY','fields' => array('0' => 'pid','1' => 'gid','2' => 'mid',),),);
	}

	protected function _fields()
	{
		return array('pid' => array('Field' => 'pid','Type' => 'int(10) unsigned','Null' => 'NO','Key' => 'MUL','Default' => NULL,'Extra' => '',),'gid' => array('Field' => 'gid','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'mid' => array('Field' => 'mid','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'ar' => array('Field' => 'ar','Type' => 'int(32) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'inherit_pid' => array('Field' => 'inherit_pid','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
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

	function gid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['gid'] = $d;
			return true;
		}
		return $this->_fields['gid'];
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