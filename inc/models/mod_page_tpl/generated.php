<?
class generated_mod_page_tpl extends model
{
	var $_table = 'mod_page_tpl';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'tpl_name' => array('Field' => 'tpl_name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'label' => array('Field' => 'label','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_trashcan' => array('Field' => 'sys_trashcan','Type' => 'smallint(1) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
	}

	public function id($d=null)
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

	public function tpl_name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('tpl_name'=>$d));
			$this->_fields['tpl_name'] = $d;
			return true;
		}
		return $this->_fields['tpl_name'];
	}

	public function label($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('label'=>$d));
			$this->_fields['label'] = $d;
			return true;
		}
		return $this->_fields['label'];
	}

	public function sys_trashcan($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'smallint',1))
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