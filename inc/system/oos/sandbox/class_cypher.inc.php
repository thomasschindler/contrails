<?php
/**
*	encryption
*	decripton
*	helpclass
*
*	only holds a dummy version for now
*	later implementation should use mcrypt or something similar	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		util
*/
class cypher{
	/**
	*	vars
	*/
	var $salt; // used for spicing up encryption
	var $pepper;
	/**
	*	constructor
	*	takes an option parameter to spice up encryption
	*/
	function cypher($salt = "rocknroll is not better than funk"){
		$this->salt = crc32($salt) % 11 + 2;
		$this->pepper = 13 - $this->salt;
	}
	/**
	*	singleton, make sure we only have one instance of this
	*/
	function &singleton() {
		static $instance;	
		if (is_object($instance)) {
			return $instance;
		}		
		return  new cypher();
	}
	/**
	*	switch between different enctypes
	*/
	function encode($string, $type = "basic"){
		$func = "type_".$type."_en";
		return $this->$func($string);
	}
	/**
	*	switch between different decode types
	*/
	function decode($string, $type = "basic"){
		$func = "type_".$type."_de";
		return $this->$func($string);
	}
	/**
	*	encode basic
	*	takes a string as parameter
	*	returns encoded string
	*/
	function type_mcrypt_en($string){
		$iv_size = mcrypt_get_iv_size(MCRYPT_SAFER128, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   		$key = "superdau";
		return $crypttext = mcrypt_encrypt(MCRYPT_SAFER128, $key, $string, MCRYPT_MODE_ECB, $iv);
	}
	/**
	*	decode basic
	*	takes a string as parameter
	*	returns decoded version of the string
	*/
	function type_mcrypt_de($string){
		$iv_size = mcrypt_get_iv_size(MCRYPT_SAFER128, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   		$key = "superdau";
		return $crypttext = mcrypt_decrypt(MCRYPT_SAFER128, $key, $string, MCRYPT_MODE_ECB, $iv);
	}
	/**
	*	encode basic
	*	takes a string as parameter
	*	returns encoded string
	*	@deprecated 13 05 2004
	*/
	function type_basic_en($string){
		$string = strtolower($string);

		for($i=0; $i<strlen($string); $i++) {
			$j = ord($string[$i]);
			
			$j = $j - $this->salt + ($i * $this->pepper) % 26;
			
			$ret .= chr($j);
		}
		return $ret;
	}
	/**
	*	decode basic
	*	takes a string as parameter
	*	returns decoded version of the string
	*	@deprecated 13 05 2004
	*/
	function type_basic_de($string){
		$string = strtolower($string);

		for($i=0; $i<strlen($string); $i++) {
			$j = ord($string[$i]);
			
			$j = $j + $this->salt - ($i * $this->pepper) % 26;
			
			$ret .= chr($j);
		}
		return $ret;
	}
}
?>
