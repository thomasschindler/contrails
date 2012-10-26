<?

abstract class model{
	private $_error_messages = array();

	private $_state = array(
				mstack::Delete 	=> null,
				mstack::Load 	=> null,
				mstack::Update 	=> null,
				mstack::Current => null,
				//mstack::Final 	=> null
			);

	public function exists(){
		return !is_null($this->_state[mstack::Load]);
	}

	public function changed(){
		return !is_null($this->_state[mstack::Update]);
	}

	public function deleted(){
		return !is_null($this->_state[mstack::Delete]);	
	}

	protected function push_load($data){
		$this->_state[mstack::Load] = $data;
	}

	protected function push_update($data){
		$this->validate_input($data);

		if($this->changed() === false){
			$this->_state[mstack::Update] = $data;
			return return::success;		
		}

		if($this->deleted()){
			$this->err("Cannot update a model that is marked for deletion.");
			return return::error;
		}

		foreach($data as $key => $value){
			$this->_state[mstack::Update][$key] = $value;
		}

		return return::success;
	}

	protected function push_create($data){
		$this->validate_input($data);

		if($this->exists()){
			$this->err("Cannot reinsert an object on the database. If you need to clone a row, use the clone method instead.");
			return return::error;
		}

		$this->_state[mstack::Update] = $data;

		return return::success;
	} 
	
	protected function push_delete(){
		if($this->deleted()){
			$this->warn("Object has already been deleted.");
			return return::no_change;
		}

		if($this->changed() || $this->exists() === false){
			$this->warn("Deleting an updated with pending changes will stop these changes from being written to the Database.");
		}

		$this->_state[mstack::Delete] = true;

		return return::success;
	}

	protected function err($msg){
		$this->msg($msg, error_message_level::error)
	}

	protected function warn($msg){
		$this->msg($msg, error_message_level::warning)
	}

	protected function dbg($msg){
		$this->msg($msg, error_message_level::debug)
	}

	protected function msg($msg, $level){
		$logpath = CONF::logpath() . CONF::project_name() . "/" . date("Ymd") . "/" . session_id();
		mkdir($logpath, "0777", true);

		switch ($level) {
			case error_message_level::error:
				//@todo: Decide on a message body!
				mail(CONF::notification(), CONF::project_name() . ' - Error (Session: ' . session_id(), $msg);
			case error_message_level::warning:
			case error_message_level::debug:
				$logpath = CONF::log_path() . date("") . "/" . session_id();
				mkdir($logpath, '0777', true);
				MC::log("Message >> Level " . error_message_level::s($level) . " >> $msg", "messages.log" , $logpath);
				break;
		}


	}


}