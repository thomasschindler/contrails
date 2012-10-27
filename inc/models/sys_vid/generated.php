<?
class generated_sys_vid extends model
{
	var $_table = 'sys_vid';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'vid',),),'1' => array('type' => 'KEY','fields' => array('0' => 'pid',),),);
	}

	protected function _fields()
	{
		return array('vid' => array('Field' => 'vid','Type' => 'varchar(32)','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'mod_name' => array('Field' => 'mod_name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(11)','Null' => 'NO','Key' => 'MUL','Default' => NULL,'Extra' => '',),);
	}

<<<<<<< HEAD
	function vid($d=null)
=======
	public function vid($d=null)
>>>>>>> master
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',32))
			{
				return false;
			}
			$this->push_update(array('vid'=>$d));
			$this->_fields['vid'] = $d;
			return true;
		}
		return $this->_fields['vid'];
	}

<<<<<<< HEAD
	function mod_name($d=null)
=======
	public function pid($d=null)
>>>>>>> master
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
<<<<<<< HEAD
			$this->_fields['mod_name'] = $d;
=======
			$this->push_update(array('pid'=>$d));
			$this->_fields['pid'] = $d;
>>>>>>> master
			return true;
		}
		return $this->_fields['mod_name'];
	}

<<<<<<< HEAD
	function pid($d=null)
=======
	public function mod_name($d=null)
>>>>>>> master
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
<<<<<<< HEAD
			$this->_fields['pid'] = $d;
=======
			$this->push_update(array('mod_name'=>$d));
			$this->_fields['mod_name'] = $d;
>>>>>>> master
			return true;
		}
		return $this->_fields['pid'];
	}
}
?>