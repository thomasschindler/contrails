<?
class generated_sys_vid extends model
{
	var $_fields = array();
	var $_table = 'sys_vid';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'vid',),),'1' => array('type' => 'KEY','fields' => array('0' => 'pid',),),);
	}

	protected function _fields()
	{
		return array('vid' => array('Field' => 'vid','Type' => 'varchar(32)','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'mod_name' => array('Field' => 'mod_name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(11)','Null' => 'NO','Key' => 'MUL','Default' => NULL,'Extra' => '',),);
	}

	function vid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',32))
			{
				return false;
			}
			$this->_fields['vid'] = $d;
			return true;
		}
		return $this->_fields['vid'];
	}

	function mod_name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['mod_name'] = $d;
			return true;
		}
		return $this->_fields['mod_name'];
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
}
?>