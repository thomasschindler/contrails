<?

class MF{

	private $_instances = array();

	public function store(&$object){
		$this->_instances[] = $object;
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

		$this->_instances[$table][$key] = new $table($obj->r());
		
		return &$this->_instances[$table][$key];
	}

	private function store_states(){

	}

	private function do_delete($table, $keys){

	}

	private function do_update($table, $keys, $data){

	}

	private function do_create($table, $data){

	}


}