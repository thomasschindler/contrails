<?
class generated_sys_log extends model
{

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11)','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'time' => array('Field' => 'time','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'project' => array('Field' => 'project','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'url' => array('Field' => 'url','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'pid' => array('Field' => 'pid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'mod' => array('Field' => 'mod','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'event' => array('Field' => 'event','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'files' => array('Field' => 'files','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'post' => array('Field' => 'post','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'get' => array('Field' => 'get','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'ip' => array('Field' => 'ip','Type' => 'varchar(30)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'session' => array('Field' => 'session','Type' => 'varchar(40)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'browser' => array('Field' => 'browser','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'uid' => array('Field' => 'uid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'name' => array('Field' => 'name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'referer' => array('Field' => 'referer','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
	}

	public function table_name()
	{
		return 'sys_log';
	}


	public function id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('id'=>$d));
			$this->_fields['id'] = $d;
			return true;
		}
		return $this->_fields['id'];
	}

	public function time($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('time'=>$d));
			$this->_fields['time'] = $d;
			return true;
		}
		return $this->_fields['time'];
	}

	public function project($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('project'=>$d));
			$this->_fields['project'] = $d;
			return true;
		}
		return $this->_fields['project'];
	}

	public function url($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('url'=>$d));
			$this->_fields['url'] = $d;
			return true;
		}
		return $this->_fields['url'];
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

	public function mod($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('mod'=>$d));
			$this->_fields['mod'] = $d;
			return true;
		}
		return $this->_fields['mod'];
	}

	public function event($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('event'=>$d));
			$this->_fields['event'] = $d;
			return true;
		}
		return $this->_fields['event'];
	}

	public function files($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('files'=>$d));
			$this->_fields['files'] = $d;
			return true;
		}
		return $this->_fields['files'];
	}

	public function post($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('post'=>$d));
			$this->_fields['post'] = $d;
			return true;
		}
		return $this->_fields['post'];
	}

	public function get($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('get'=>$d));
			$this->_fields['get'] = $d;
			return true;
		}
		return $this->_fields['get'];
	}

	public function ip($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',30))
			{
				return false;
			}
			$this->push_update(array('ip'=>$d));
			$this->_fields['ip'] = $d;
			return true;
		}
		return $this->_fields['ip'];
	}

	public function session($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',40))
			{
				return false;
			}
			$this->push_update(array('session'=>$d));
			$this->_fields['session'] = $d;
			return true;
		}
		return $this->_fields['session'];
	}

	public function browser($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('browser'=>$d));
			$this->_fields['browser'] = $d;
			return true;
		}
		return $this->_fields['browser'];
	}

	public function uid($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('uid'=>$d));
			$this->_fields['uid'] = $d;
			return true;
		}
		return $this->_fields['uid'];
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

	public function referer($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('referer'=>$d));
			$this->_fields['referer'] = $d;
			return true;
		}
		return $this->_fields['referer'];
	}
}
?>