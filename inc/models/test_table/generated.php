<?
class generated_test_table extends model
{
	var $_fields = array();
	var $_table = 'test_table';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11)','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => 'auto_increment',),'field1' => array('Field' => 'field1','Type' => 'varchar(45)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'field2' => array('Field' => 'field2','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),);
	}

	function id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['id'] = $d;
			return true;
		}
		return $this->_fields['id'];
	}

	function field1($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',45))
			{
				return false;
			}
			$this->_fields['field1'] = $d;
			return true;
		}
		return $this->_fields['field1'];
	}

	function field2($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['field2'] = $d;
			return true;
		}
		return $this->_fields['field2'];
	}
}
?>