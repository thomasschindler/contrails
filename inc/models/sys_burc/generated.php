<?
class generated_sys_burc extends model
{

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'burc',),),);
	}

	protected function _fields()
	{
		return array('burc' => array('Field' => 'burc','Type' => 'varchar(20)','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'data' => array('Field' => 'data','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_date_created' => array('Field' => 'sys_date_created','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'permanent' => array('Field' => 'permanent','Type' => 'tinyint(1)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
	}

	public function table_name()
	{
		return 'sys_burc';
	}


	public function burc($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',20))
			{
				return false;
			}
			$this->push_update(array('burc'=>$d));
			$this->_fields['burc'] = $d;
			return true;
		}
		return $this->_fields['burc'];
	}

	public function pid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('pid'=>$d));
			$this->_fields['pid'] = $d;
			return true;
		}
		return $this->_fields['pid'];
	}

	public function data($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('data'=>$d));
			$this->_fields['data'] = $d;
			return true;
		}
		return $this->_fields['data'];
	}

	public function sys_date_created($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('sys_date_created'=>$d));
			$this->_fields['sys_date_created'] = $d;
			return true;
		}
		return $this->_fields['sys_date_created'];
	}

	public function permanent($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->push_update(array('permanent'=>$d));
			$this->_fields['permanent'] = $d;
			return true;
		}
		return $this->_fields['permanent'];
	}
}
?>