<?php
/**
*	
* klasse zur session verwaltung
*
* url basiert, keine cookies. 
* wir arbeiten hier nur mit der superglobalen variable $_SESSION
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		system
*/

	class SESS {
		
		/**
		* name der session
		*
		* kann als param bei construktor uebergeben werden, 
		* oder kommt aus CONF
		*@access	public
		*/
		var $name = 'sess';
		
		/**
		* session-id (md5-hash)
		*@access	public
		*/
		var $id;
		
		/**
		* name der session-variablen (array) in der daten gespeichert werden
		*
		* kann auch in der CONF angegeben werden
		*@access	private
		*/
		var $data_name = '_MY_SESSION_DATA_ARRAY';
		
		/**
		* flag, dass anzeigt, ob session gestartet wurde
		*@access	private
		*/
		var $started = false;
		
		/**
		*werden cookies verwendet
		*@access	private
		*/
		var $use_cookies = 0;
		
		/**
		* konstruktor
		*
		*@access	public
		*/
		function sess() {
			$conf = CONF::session_options();

			if (isset($conf['name'])) $this->name = $conf['name'];
			if (isset($conf['data_name'])) $this->data_name = $conf['data_name'];
			if (isset($conf['use_cookies'])) {
				$this->use_cookies = ($conf['use_cookies'] ? '1' : '0');
			}
						
		}
		
		/**
		* startet die session
		*
		*@access	public
		*/
		function start() 
		{
			

		if(!isset($_SERVER['argv']))
		{
			if(!isset($_GET[$this->name]))
			{
				ini_set('session.use_cookies', 1);
			}
			else
			{
				session_id($_GET[$this->name]);
				ini_set('session.use_cookies', 0);
			}
		}
		else
		{
			if(!isset($_GET[$this->name]))
			{
				ini_set('session.use_cookies', 1);
			}
			else
			{
				// if we use the same session as the web-user we run into problems 
				// when executing scripts using the same session
				session_id(md5($_GET[$this->name]));
				ini_set('session.use_cookies', 0);
			}
		}
			
			
			session_name($this->name);

			session_start();
			
			$this->started = true;
			$this->id = session_id();

			// session-id in allen urls/forms mitschleifen
			$opc = &OPC::singleton();
			$opc->lnk_add($this->name, $this->id);

		}
		
		
		/**
		* setzen eines wertes in session
		*
		* ein wert hat neben dem (variablen)namen auch den modul-namen 
		* bspl.: $sess->set('news', 'counter', $c);
		*
		*@access	public
		*@param	string	$modul	modul-name
		*@param	string	$key	schluessel-name
		*@param	mixed	$value	zu speichernder wert
		*@access	public
		*/
		function set($modul, $key, $value) {
			$_SESSION[$this->data_name][$modul][$key] = $value;
		}
		
		/**
		* lesen eines wertes aus session
		*
		*@param	string	$modul	modul-name
		*@param	string	$key	schluessel-name
		*@return	mixed	gespeicherter wert
		*@access	public
		*/
		function get($modul, $key) {
			return @$_SESSION[$this->data_name][$modul][$key];
		}
		
		/**
		* prueft, ob eine variable gesetzt ist
		*
		*@param	string	$modul	modul-name
		*@param	string	$key	schluessel-name
		*@return	bool	true, wenn die variable gesetzt ist, false sonst
		*@access	public
		*/
		function is_set($modul, $key) {
			return isset($_SESSION[$this->data_name][$modul][$key]);
		}
		
		/**
		* variable leeren
		*
		* wird dann auch aus session entfernt
		*
		*@param	string	$modul	modul-name
		*@param	string	$key	schluessel-name
		*@access	public
		*/
		function remove($modul, $key=null) 
		{	
			if(!$key)
			{
				unset($_SESSION[$this->data_name][$modul]);		 
				return;   	
			}
			unset($_SESSION[$this->data_name][$modul][$key]);
		}
		
		
		/**
		* instanzieren 
		*
		* session-objekte sollten nur ueber diese funktion instanziert werden: 
		* $sess = &SESS::singleton();	// wichtig: das '&' zeichen ! 
		* sie sorgt dafuer, dass es immer nur eine  
		* instanz gibt, mit der alle programme arbeiten. 
		*
		*@access	public
		*@return	object	zeiger auf session-objekt
		*/
		function &singleton() {
			static $instance;
		
			if (!is_object($instance)) {
				$instance = new SESS();
			}
		
			return $instance;
		}

	}
	

?>
