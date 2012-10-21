<?
class generated_mm_usradmin_usr_grp extends model
{
	var $_fields = array();
	var $_table = 'mm_usradmin_usr_grp';

	protected function _primary()
	{
		return array('0' => array('type' => 'KEY','fields' => array('0' => 'local_id','1' => 'foreign_id',),),);
	}

	function local_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['local_id'] = $d;
			return true;
		}
		return $this->_fields['local_id'];
	}

	function foreign_id($d=null)
	{
		if($d !== null)
		{
			if(!$this->_valid($d,'int',10))
			{
				return false;
			}
			$this->_fields['foreign_id'] = $d;
			return true;
		}
		return $this->_fields['foreign_id'];
	}
}
?>