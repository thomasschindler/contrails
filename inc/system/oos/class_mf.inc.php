<?

class MF{

	private $_instances = array();

	public function singleton() {
		static $instance;
			
		if (!is_object($instance)) { $instance = new MF(); }
			
		return $instance;
	}

	public function obtain($table, $key = null, $data = null){
		if(is_null($key) === false){
			return $this->load_record($table, $key);
		}

		if(is_null($data) === false){
			return $this->create_record($table, $data);
		}

		return null;
	}

	private function load_record($table, $key){
		if(!class_exists($table)){
			log::err("Attempted to load an unexisting class in the MF::obtain method '$table'");
			return null;
		}

		if(!isset($this->_instances[$table])){
			$this->_instances[$table] = array();
		}

		if(isset($this->_instances[$table][$key])){
			return $this->_instances[$table][$key];
		}

		$mdl = new $table();
		$key_columns = $mdl->primary_key();


		$obj = $this->CRUD()->load($table, $key_columns, $key);

		if(!$obj || $obj->nr() <= 0){
			return null;
		}

		$this->_instances[$table][$key] = $mdl;
		if(!$this->_instances[$table][$key]->load($obj->r())){
			return null;
		}

		return $this->_instances[$table][$key];
	}

	private function create_record($table, $data){
		MC::log("Table: $table Data: " .print_r($data, true));
		if(!class_exists($table)){
			log::err("Attempted to load an unexisting class in the MF::obtain method '$table'");
			return null;
		}

		$key = $this->do_create($table, $data);

		if($key == -1){
			MC::log("Failed to insert the record into the Database  " . $this->crud()->err_msg);
			return null;
		}


		MC::log("Returned $key");
		return $this->obtain($table, $key);						
	}

	private function store_states(){

	}

	private function do_delete($table, $keys){

	}

	private function do_update($table, $key_column ,$key, $data){
		return $this->crud()->update($table, $key_column, $key, $data);
	}

	private function do_create($table, $data){
		return $this->crud()->create($table,$data,array());
	}

	private function crud()
	{
		return DB_CRUD::singleton();
	}


}