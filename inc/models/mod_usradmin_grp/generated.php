<?
class generated_mod_usradmin_grp extends model
{
	var $_table = 'mod_usradmin_grp';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'name' => array('Field' => 'name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'lang' => array('Field' => 'lang','Type' => 'int(2)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_trashcan' => array('Field' => 'sys_trashcan','Type' => 'tinyint(1) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
	}

<<<<<<< HEAD
	function id($d=null)
=======
	public function id($d=null)
>>>>>>> master
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('id'=>$d));
			$this->_fields['id'] = $d;
			return true;
		}
		return $this->_fields['id'];
	}

	public function pid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('pid'=>$d));
			$this->_fields['pid'] = $d;
			return true;
		}
		return $this->_fields['pid'];
	}

	public function name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('name'=>$d));
			$this->_fields['name'] = $d;
			return true;
		}
		return $this->_fields['name'];
	}

	public function lang($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',2))
			{
				return false;
			}
			$this->push_update(array('lang'=>$d));
			$this->_fields['lang'] = $d;
			return true;
		}
		return $this->_fields['lang'];
	}

	public function sys_trashcan($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->push_update(array('sys_trashcan'=>$d));
			$this->_fields['sys_trashcan'] = $d;
			return true;
		}
		return $this->_fields['sys_trashcan'];
	}
}
?>