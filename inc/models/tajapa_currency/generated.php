<?
class generated_tajapa_currency extends model
{

	protected function _keys()
	{
		return array('0' => array('type' => 'PRIMARY KEY','fields' => array('0' => 'id',),),);
	}

	protected function _fields()
	{
		return array('id' => array('Field' => 'id','Type' => 'int(11) unsigned','Null' => 'NO','Key' => 'PRI','Default' => NULL,'Extra' => 'auto_increment',),'uid' => array('Field' => 'uid','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'label' => array('Field' => 'label','Type' => 'varchar(255)','Null' => 'NO','Key' => '','Default' => '','Extra' => '',),'interest_rate' => array('Field' => 'interest_rate','Type' => 'int(3)','Null' => 'NO','Key' => '','Default' => '0','Extra' => '',),'interest_term' => array('Field' => 'interest_term','Type' => 'enum(\'DAY\',\'WEEK\',\'MONTH\',\'YEAR\')','Null' => 'NO','Key' => '','Default' => 'YEAR','Extra' => '',),'source' => array('Field' => 'source','Type' => 'enum(\'COMMUNITY\',\'BANK\')','Null' => 'NO','Key' => '','Default' => 'COMMUNITY','Extra' => '',),'created' => array('Field' => 'created','Type' => 'int(11)','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),'template' => array('Field' => 'template','Type' => 'tinyint(1)','Null' => 'NO','Key' => '','Default' => '0','Extra' => '',),'parent' => array('Field' => 'parent','Type' => 'tinyint(1)','Null' => 'YES','Key' => '','Default' => '0','Extra' => '',),);
	}

	public function table_name()
	{
		return 'tajapa_currency';
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

	public function interest_rate($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',3))
			{
				return false;
			}
			$this->push_update(array('interest_rate'=>$d));
			$this->_fields['interest_rate'] = $d;
			return true;
		}
		return $this->_fields['interest_rate'];
	}

	public function interest_term($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'enum',0))
			{
				return false;
			}
			$this->push_update(array('interest_term'=>$d));
			$this->_fields['interest_term'] = $d;
			return true;
		}
		return $this->_fields['interest_term'];
	}

	public function source($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'enum',0))
			{
				return false;
			}
			$this->push_update(array('source'=>$d));
			$this->_fields['source'] = $d;
			return true;
		}
		return $this->_fields['source'];
	}

	public function created($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',11))
			{
				return false;
			}
			$this->push_update(array('created'=>$d));
			$this->_fields['created'] = $d;
			return true;
		}
		return $this->_fields['created'];
	}

	public function template($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->push_update(array('template'=>$d));
			$this->_fields['template'] = $d;
			return true;
		}
		return $this->_fields['template'];
	}

	public function parent($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'tinyint',1))
			{
				return false;
			}
			$this->push_update(array('parent'=>$d));
			$this->_fields['parent'] = $d;
			return true;
		}
		return $this->_fields['parent'];
	}
}
?>