<?
class generated_sys_mc_listeners extends model
{
	var $_fields = array();
	var $_table = 'sys_mc_listeners';

	protected function _keys()
	{
		return array('0' => array('type' => 'KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11) unsigned','Null' => 'NO','Key' => 'MUL','Default' => NULL,'Extra' => '',),'mod_shout' => array('Field' => 'mod_shout','Type' => 'varchar(200)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'event_shout' => array('Field' => 'event_shout','Type' => 'varchar(200)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'mod_listen' => array('Field' => 'mod_listen','Type' => 'varchar(200)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'event_listen' => array('Field' => 'event_listen','Type' => 'varchar(200)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'att_listen' => array('Field' => 'att_listen','Type' => 'text','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'start' => array('Field' => 'start','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'stop' => array('Field' => 'stop','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => '2147483647','Extra' => '',),'pre' => array('Field' => 'pre','Type' => 'tinyint(1) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
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

	function mod_shout($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',200))
			{
				return false;
			}
			$this->_fields['mod_shout'] = $d;
			return true;
		}
		return $this->_fields['mod_shout'];
	}

	function event_shout($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',200))
			{
				return false;
			}
			$this->_fields['event_shout'] = $d;
			return true;
		}
		return $this->_fields['event_shout'];
	}

	function mod_listen($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',200))
			{
				return false;
			}
			$this->_fields['mod_listen'] = $d;
			return true;
		}
		return $this->_fields['mod_listen'];
	}

	function event_listen($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',200))
			{
				return false;
			}
			$this->_fields['event_listen'] = $d;
			return true;
		}
		return $this->_fields['event_listen'];
	}

	function att_listen($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['att_listen'] = $d;
			return true;
		}
		return $this->_fields['att_listen'];
	}

	function start($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['start'] = $d;
			return true;
		}
		return $this->_fields['start'];
	}

	function stop($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['stop'] = $d;
			return true;
		}
		return $this->_fields['stop'];
	}

	function pre($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->_fields['pre'] = $d;
			return true;
		}
		return $this->_fields['pre'];
	}
}
?>