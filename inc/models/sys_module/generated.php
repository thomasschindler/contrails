<?
class generated_sys_module extends model
{
	var $_fields = array();
	var $_table = 'sys_module';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'modul_name' => array('Field' => 'modul_name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'label' => array('Field' => 'label','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_trashcan' => array('Field' => 'sys_trashcan','Type' => 'smallint(1) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'virtual' => array('Field' => 'virtual','Type' => 'tinyint(1) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
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

	function modul_name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['modul_name'] = $d;
			return true;
		}
		return $this->_fields['modul_name'];
	}

	function label($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['label'] = $d;
			return true;
		}
		return $this->_fields['label'];
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

	function virtual($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->_fields['virtual'] = $d;
			return true;
		}
		return $this->_fields['virtual'];
	}
}
?>