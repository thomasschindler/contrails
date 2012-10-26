<?
class generated_uftp_locations extends model
{
	var $_fields = array();
	var $_table = 'uftp_locations';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),'1' => array('type' => 'KEY','fields' => array('0' => 'lat','1' => 'lon',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => 'auto_increment',),'name' => array('Field' => 'name','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'lat' => array('Field' => 'lat','Type' => 'float','Null' => 'YES','Key' => 'MUL','Default' => NULL,'Extra' => '',),'lon' => array('Field' => 'lon','Type' => 'float','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'created_at' => array('Field' => 'created_at','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'updated_at' => array('Field' => 'updated_at','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),);
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

	function name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['name'] = $d;
			return true;
		}
		return $this->_fields['name'];
	}

	function lat($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'float'))
			{
				return false;
			}
			$this->_fields['lat'] = $d;
			return true;
		}
		return $this->_fields['lat'];
	}

	function lon($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'float'))
			{
				return false;
			}
			$this->_fields['lon'] = $d;
			return true;
		}
		return $this->_fields['lon'];
	}

	function created_at($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['created_at'] = $d;
			return true;
		}
		return $this->_fields['created_at'];
	}

	function updated_at($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['updated_at'] = $d;
			return true;
		}
		return $this->_fields['updated_at'];
	}
}
?>