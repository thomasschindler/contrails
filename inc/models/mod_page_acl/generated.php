<?
class generated_mod_page_acl extends model
{
	var $_fields = array();
	var $_table = 'mod_page_acl';

	protected function _keys()
	{
		return array('0' => array('type' => 'KEY','fields' => array('0' => 'id','1' => 'aid',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => 'MUL','Default' => NULL,'Extra' => '',),'type' => array('Field' => 'type','Type' => 'int(1) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'aid' => array('Field' => 'aid','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'ar' => array('Field' => 'ar','Type' => 'int(32) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'inherit_pid' => array('Field' => 'inherit_pid','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
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