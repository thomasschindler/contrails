<?
class generated_mod_page extends model
{
	var $_table = 'mod_page';

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => '',),'name' => array('Field' => 'name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'url' => array('Field' => 'url','Type' => 'text','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'title' => array('Field' => 'title','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'description' => array('Field' => 'description','Type' => 'text','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'keywords' => array('Field' => 'keywords','Type' => 'text','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'lft' => array('Field' => 'lft','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'rgt' => array('Field' => 'rgt','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'root_id' => array('Field' => 'root_id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'parent_id' => array('Field' => 'parent_id','Type' => 'int(11) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'set_ignore' => array('Field' => 'set_ignore','Type' => 'tinyint(1)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'template_name' => array('Field' => 'template_name','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'rights' => array('Field' => 'rights','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'structure' => array('Field' => 'structure','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'lost_mods' => array('Field' => 'lost_mods','Type' => 'text','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_trashcan' => array('Field' => 'sys_trashcan','Type' => 'smallint(1) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_date_created' => array('Field' => 'sys_date_created','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'sys_date_changed' => array('Field' => 'sys_date_changed','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'cookie_name' => array('Field' => 'cookie_name','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'cookie_lifetime' => array('Field' => 'cookie_lifetime','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'cookie_value' => array('Field' => 'cookie_value','Type' => 'varchar(255)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),'redirect_to' => array('Field' => 'redirect_to','Type' => 'int(11)','Null' => 'YES','Key' => '','Default' => NULL,'Extra' => '',),);
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

	public function url($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('url'=>$d));
			$this->_fields['url'] = $d;
			return true;
		}
		return $this->_fields['url'];
	}

	public function title($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('title'=>$d));
			$this->_fields['title'] = $d;
			return true;
		}
		return $this->_fields['title'];
	}

	public function description($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('description'=>$d));
			$this->_fields['description'] = $d;
			return true;
		}
		return $this->_fields['description'];
	}

	public function keywords($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('keywords'=>$d));
			$this->_fields['keywords'] = $d;
			return true;
		}
		return $this->_fields['keywords'];
	}

	public function lft($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('lft'=>$d));
			$this->_fields['lft'] = $d;
			return true;
		}
		return $this->_fields['lft'];
	}

	public function rgt($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('rgt'=>$d));
			$this->_fields['rgt'] = $d;
			return true;
		}
		return $this->_fields['rgt'];
	}

	public function root_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('root_id'=>$d));
			$this->_fields['root_id'] = $d;
			return true;
		}
		return $this->_fields['root_id'];
	}

	public function parent_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('parent_id'=>$d));
			$this->_fields['parent_id'] = $d;
			return true;
		}
		return $this->_fields['parent_id'];
	}

	public function set_ignore($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->push_update(array('set_ignore'=>$d));
			$this->_fields['set_ignore'] = $d;
			return true;
		}
		return $this->_fields['set_ignore'];
	}

	public function template_name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('template_name'=>$d));
			$this->_fields['template_name'] = $d;
			return true;
		}
		return $this->_fields['template_name'];
	}

	public function rights($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('rights'=>$d));
			$this->_fields['rights'] = $d;
			return true;
		}
		return $this->_fields['rights'];
	}

	public function structure($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('structure'=>$d));
			$this->_fields['structure'] = $d;
			return true;
		}
		return $this->_fields['structure'];
	}

	public function lost_mods($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'text'))
			{
				return false;
			}
			$this->push_update(array('lost_mods'=>$d));
			$this->_fields['lost_mods'] = $d;
			return true;
		}
		return $this->_fields['lost_mods'];
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

	public function sys_date_created($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('sys_date_created'=>$d));
			$this->_fields['sys_date_created'] = $d;
			return true;
		}
		return $this->_fields['sys_date_created'];
	}

	public function sys_date_changed($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('sys_date_changed'=>$d));
			$this->_fields['sys_date_changed'] = $d;
			return true;
		}
		return $this->_fields['sys_date_changed'];
	}

	public function cookie_name($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('cookie_name'=>$d));
			$this->_fields['cookie_name'] = $d;
			return true;
		}
		return $this->_fields['cookie_name'];
	}

	public function cookie_lifetime($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('cookie_lifetime'=>$d));
			$this->_fields['cookie_lifetime'] = $d;
			return true;
		}
		return $this->_fields['cookie_lifetime'];
	}

	public function cookie_value($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'varchar',255))
			{
				return false;
			}
			$this->push_update(array('cookie_value'=>$d));
			$this->_fields['cookie_value'] = $d;
			return true;
		}
		return $this->_fields['cookie_value'];
	}

	public function redirect_to($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('redirect_to'=>$d));
			$this->_fields['redirect_to'] = $d;
			return true;
		}
		return $this->_fields['redirect_to'];
	}
}
?>