<?
class generated_sys_vid extends model
{
	var $_fields = array();
	var $_table = 'sys_vid';
	var $_relations = array
	(
		'mod_page' => array
		(
			'field' => 'vid',
		),
		'mod_usradmin_usr' => array
		(
			'table' => 'mm_some_relational_table',
			'field' => 'm_vid',
		),
	);

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'vid','1' => 'pid',),),'1' => array('type' => 'KEY','fields' => array('0' => 'test',),),);
	}

	protected function _fields()
	{
		return array('vid' => array('Field' => 'vid','Type' => 'varchar(32)','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => '','Extra' => '',),'mod_name' => array('Field' => 'mod_name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
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
}
?>