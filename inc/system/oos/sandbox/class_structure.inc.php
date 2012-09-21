<?
class structure{
	
	function find_assoc($arr,$find_key,$find_val,$coll = array('path'=>array(),'success'=>false),$depth = 0){
		foreach($arr as $key => $val){
			if(is_array($val) AND !$coll['success']){
				$coll = $this->find_assoc($val,$find_key,$find_val,$coll, $depth + 1);
				if($coll['success']){
					$coll['path'][$depth] = $key;
				}
			}
			if($key == $find_key AND $val == $find_val){
				$coll['success'] = true;
			}
		}
		return $coll;
	}
	
	function path($path){
		return implode('][',array_reverse($path['path']));
	}
	
	function get($haystack,$needle_name,$needle_val){
		$coll = $this->find_assoc($haystack,$needle_name,$needle_val);
		eval(chr(36).'ret = '.chr(36).'haystack['.$this->path($coll).'];');
		return $ret;
	}
	
	function set($haystack,$needle_name,$needle_val,$insert){		
		eval(chr(36).'haystack['.$this->path($this->find_assoc($haystack,$needle_name,$needle_val)).'] = '.chr(36).'insert;');
		return $haystack;
	}
	
	function &singleton() {
		static $instance;	
		if (is_object($instance)) {
			return $instance;
		}		
		return  new structure();
	}
}
?>
