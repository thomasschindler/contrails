<?
class generated_sys_log extends model
{
	var $_fields = array();
	var $_table = 'sys_log';

	protected function _primary()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
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