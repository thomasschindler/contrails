<?
class generated_sys_burc extends model
{
	var $_fields = array();
	var $_table = 'sys_burc';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'burc',),),);
	}

	protected function _fields()
	{
		return array('burc' => array('Field' => 'burc','Type' => 'varchar(20)','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'data' => array('Field' => 'data','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_date_created' => array('Field' => 'sys_date_created','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'permanent' => array('Field' => 'permanent','Type' => 'tinyint(1)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
	}

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