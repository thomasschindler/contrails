<?

class MF{

	private $_instances = array();

	public function singleton() {
		static $instance;
			
		if (!is_object($instance)) { $instance = new MF(); }
			
		return $instance;
	}

	public function obtain($table, $key){
		if(!isset($this->_instances[$table])){
			$this->_instances[$table] = array();
		}

		if(isset($this->_instances[$table][$key])){
			return $this->_instances[$table][$key];
		}

		$obj = $this->CRUD()->load($table, 'id', $key);

		if($obj->nr() <= 0){
			return null;
		}

		$this->_instances[$table][$key] = new $table();
		if(!$this->_instances[$table][$key]->load($obj->r())){
			return null;
		}

		return $this->_instances[$table][$key];
	}

	private function store_states(){

	}

	private function do_delete($table, $keys){

	}

	private function do_update($table, $keys, $data){

	}

	private function do_create($table, $data){

	}

	private function crud()
	{
		return DB_CRUD::singleton();
	}


}