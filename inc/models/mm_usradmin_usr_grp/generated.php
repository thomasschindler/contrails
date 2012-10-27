<?
class generated_mm_usradmin_usr_grp extends model
{
	var $_table = 'mm_usradmin_usr_grp';

	protected function _keys()
	{
		return array('0' => array('type' => 'KEY','fields' => array('0' => 'local_id','1' => 'foreign_id',),),);
	}

	protected function _fields()
	{
		return array('local_id' => array('Field' => 'local_id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => 'MUL','Default' => NULL,'Extra' => '',),'foreign_id' => array('Field' => 'foreign_id','Type' => 'int(10) unsigned','Null' => 'NO','Key' => '','Default' => NULL,'Extra' => '',),);
	}

	public function local_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('local_id'=>$d));
			$this->_fields['local_id'] = $d;
			return true;
		}
		return $this->_fields['local_id'];
	}

	public function foreign_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->push_update(array('foreign_id'=>$d));
			$this->_fields['foreign_id'] = $d;
			return true;
		}
		return $this->_fields['foreign_id'];
	}
}
?>