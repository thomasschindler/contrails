<?
class generated_sys_log extends model
{
	var $_fields = array();
	var $_table = 'sys_log';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11)','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'time' => array('Field' => 'time','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'project' => array('Field' => 'project','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'url' => array('Field' => 'url','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'mod' => array('Field' => 'mod','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'event' => array('Field' => 'event','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'files' => array('Field' => 'files','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'post' => array('Field' => 'post','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'get' => array('Field' => 'get','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'ip' => array('Field' => 'ip','Type' => 'varchar(30)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'session' => array('Field' => 'session','Type' => 'varchar(40)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'browser' => array('Field' => 'browser','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'uid' => array('Field' => 'uid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'name' => array('Field' => 'name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'referer' => array('Field' => 'referer','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
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

	function time($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['time'] = $d;
			return true;
		}
		return $this->_fields['time'];
	}

	function project($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['project'] = $d;
			return true;
		}
		return $this->_fields['project'];
	}

	function url($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['url'] = $d;
			return true;
		}
		return $this->_fields['url'];
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

	function mod($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['mod'] = $d;
			return true;
		}
		return $this->_fields['mod'];
	}

	function event($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->_fields['event'] = $d;
			return true;
		}
		return $this->_fields['event'];
	}

	function files($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['files'] = $d;
			return true;
		}
		return $this->_fields['files'];
	}

	function post($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['post'] = $d;
			return true;
		}
		return $this->_fields['post'];
	}

	function get($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['get'] = $d;
			return true;
		}
		return $this->_fields['get'];
	}

	function ip($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',30))
			{
				return false;
			}
			$this->_fields['ip'] = $d;
			return true;
		}
		return $this->_fields['ip'];
	}

	function session($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',40))
			{
				return false;
			}
			$this->_fields['session'] = $d;
			return true;
		}
		return $this->_fields['session'];
	}

	function browser($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['browser'] = $d;
			return true;
		}
		return $this->_fields['browser'];
	}

	function uid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->_fields['uid'] = $d;
			return true;
		}
		return $this->_fields['uid'];
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

	function referer($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->_fields['referer'] = $d;
			return true;
		}
		return $this->_fields['referer'];
	}
}
?>