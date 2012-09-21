<?
/**
* 	Best Uniform Resource Connector
*	
*	this helper class is used when djinni linktype is set to BURC
*	BURC should only be used statically: BURC::get() / BURC::set()
*
*	TODO: cleanup - long and short term memory!!!

22082007
moved burc from file-system to db:

CREATE TABLE `sys_burc` (
`burc` VARCHAR( 20 ) NOT NULL ,
`data` TEXT NOT NULL ,
`sys_date_created` INT( 11 ) NOT NULL ,
`permanent` TINYINT( 1 ) NOT NULL DEFAULT '0',
PRIMARY KEY ( `burc` )
) TYPE = MYISAM ;


*/
class SCS
{
	var $cache = array();
	var $conf = array('dir'=>'');

	function SCS()
	{
		$this->DB = DB::singleton();
	}
	
	function cleanup()
	{
#		exec('find '.$this->burc_dir.'/ -amin +120 -exec rm {} ";" ');
	}
	
	function rm($burc=null)
	{
		if(!$burc)
		{
			$burc = $this->_b;
		}
		else
		{
			$burc = $this->burc_dir."/".$burc.".burc";
		}
		$this->DB->query('DELETE FROM sys_burc WHERE burc = \''.$burc.'\'');
		#UTIL::delete_file($burc);
	}
	
	/**
	*	parse an url for a burc and return the key on success
	*/
	function parse($url)
	{         
		
		$p = explode("-",$url);
		$p = explode(".",$p[count($p)-1]);
		$burc = $p[0];
		
//		$burc = UTIL::get_post('ACTION');

		$select = "SELECT * FROM sys_burc WHERE burc ='".$burc."'";
		$r = $this->DB->query($select);     
		if($r->nr() == 1)
		{
			$this->_b = $burc;
			return $this->_b;
		}
		return false;
	}
	/**
	*	return the pid
	*/
	function pid($burc)
	{
		// so it still might be a valid burc
		$burc = explode("_",$burc);
		$burc = explode(".",$burc[1]);
		// init the cache for this 
		if(!$burc[0])
		{
			$burc[0] = CONF::pid();
		}
		return $burc[0];
		
		$select = "SELECT burc FROM sys_burc WHERE pid = ".(int)$burc[0]."";
		$r = $this->DB->query($select);
		while($r->next())
		{
			$this->cache[$r->f('burc')] = true;
		}
		return $burc[0];
	}
	/**
	*	create a link
	*	takes an array of the form of lnk_add from opc
	*/
	function lnk($burc,$anchor=null,$filetype="html",$permanent=0)
	{
		// unset the session and add it to the link afterwards
		// attention: some mods use a session replacement:
		
		$sess_info = CONF::session_options();
		
		if($burc[$sess_info['name']] AND $burc[$sess_info['name']] != $sess_info['name'].'_replace')
		{
			$sess_info['current'] = '&'.$sess_info['name'].'='.$burc[$sess_info['name']];
			unset($burc[$sess_info['name']]);
		}
		
		if($anchor)
		{
			return $this->set($burc,$permanent).".".$filetype.$sess_info['current']."#".$anchor;
		}
		
		return $this->set($burc,$permanent);//.$sess_info['current'];//.".".$filetype.$sess_info['current'];
	}
	/**
	*	get returns the the lnk array that was set by set 
	*	if no resource of the specified name could be found it returns an empty array
	*/
	function get($burc)
	{
		$select = "SELECT * FROM sys_burc WHERE burc ='".$burc."'";
		$r = $this->DB->query($select);
		if($r->nr() == 1)
		{
			$burc = unserialize($r->f('data'));
			$burc['burc']['error'] = false;
			return $burc;
		}
		elseif($this->conf['dir'])
		{
			include $this->conf['dir'].'/'.$burc.'.burc';
			$burc['burc']['error'] = false;
			return $burc;
		}
		return array('burc'=>array('error'=>true));
	}
	/**
	*	create a proper array from the input
	*/
	function rectify_array($burc){
		foreach($burc as $key => $val){
			if($val == 'seen'){
				unset($burc[$key]);
			}
			if(preg_match('/\[/',$key)){
				unset($burc[$key]);
				$parts = explode('[',$key);
				// how many parts are there?
				$e_str = "\$burc";
				foreach($parts as $part){
					$e_str .= "[".preg_replace("/]/","",$part)."]";
				}
				$val = empty($val) ? 0 : $val;
				$e_str .= is_int($val) ? " = ".$val.";" : " = '".$val."';";
				eval($e_str);
			}
		}
		return $burc;
	}
	/**
	*	read the array, clean it and return something that can be written to a file
	*/
	function read_array($burc){
		$burc = $this->rectify_array($burc);
		return serialize($burc);
	}
	/**
	*	write to a file if necessary
	*/
	function set($burc,$permanent=0)
	{
		$pid = $burc['pid'] ? $burc['pid'] : 1;
		$burc_ = (int)crc32(serialize($burc));
		$burc_  = $burc_< 0 ? 'm'.substr($burc_,1) : 'p'.$burc_;
		#$burc_ .= time().'_'.$pid;
		$burc_ .= '_'.$pid;
		if(@$this->cache[$burc_])
		{
			return $burc_;
		}
		// make sure, we need to set it
		$select = "SELECT burc FROM sys_burc WHERE burc = '".$burc_."'";
		$r = $this->DB->query($select,false);
		if($r->nr()!=0)
		{
			$this->cache[$burc_] = true;
			return $burc_;
		}
		$insert = "INSERT INTO sys_burc (
		burc,
		pid,
		data,
		sys_date_created,
		permanent
		) VALUES (
		'".$burc_."',
		".UTIL::get_post('pid').",
		'".mysql_real_escape_string(serialize($this->rectify_array($burc)))."',
		".time().",
		".$permanent."
		)";	

		$this->DB->query($insert);
		$this->cache[$burc_] = true;
		return $burc_;
	}

}
?>
