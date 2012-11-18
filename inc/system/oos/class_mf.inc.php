<?

class MF{

	private $_instances = array();

	public function singleton() {
		static $instance;
			
		if (!is_object($instance)) { $instance = new MF(); }
			
		return $instance;
	}

	public function &obtain($table, $data = null){
		if(is_null($data)){
			if(!class_exists($table)){
				log::err("Attempted to load an unexisting class in the MF::obtain method '$table'");
				return null;
			}

			return new $table();
		}

		if(is_array($data) === false){
			return $this->load_record($table, $data); 
		}

		$key = $this->create_record($table, $data);
		return $this->load_record($table, $key);

	}

	public function &register($instance)
	{



		$status = $instance->pull();


		/*if($status['action'] !== factory_actions::Create){
			log::err("Instance already exists or is not in the created status.");
			return false;
		}*/
		
		$table = $instance->table_name();


		
		$key = $this->create_record($table, $status['data']);


		if(is_null($key))
		{
			log::err("Insertion failed");
			return null;
		}

		if(!isset($this->_instances[$table]))
		{
			$this->_instances[$table] = array();
		}

		$instance->clear_status();
		
		if(!$instance->load($status['data'])){
			log::err("Failed to load data from status");
		}

		// SET THE ID
		$mdl = new $table();
		$key_columns = $mdl->primary_key();
		$instance->{$key_columns}($key);
		
		$this->_instances[$table][$key] = $instance;
		return $this->_instances[$table][$key];
	}

	private function &load_record($table, $key){
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

	private function create_record($table, $data)
	{
		if(!class_exists($table))
		{
			log::err("Attempted to load an unexisting class in the MF::obtain method '$table'");
			return null;
		}
		
		$key = $this->do_create($table, $data);
		if($key == -1){
			MC::log("Failed to insert the record into the Database  " . $this->crud()->err_msg);
			return null;
		}

		$class = new $table();

		$key = (isset($data[$class->primary_key()]) ? $data[$class->primary_key()] : $key);

		return $key;						
	}

	private function do_delete($table, $keys){

	}

	private function do_update($table, $key_column ,$key, $data){
		return $this->crud()->update($table, $key_column, $key, $data);
	}

	private function do_create($table, $data){
		return $this->crud()->create($table,$data,array());
	}

	public function flush(){
		foreach($this->_instances as $table => $rows){
			foreach($rows as $id => $instance){
				$status = $instance->pull();
				switch($status['action']){
					case factory_actions::Delete:
						$this->do_delete($table, $keys);
						break;
					case factory_actions::Update:
						$this->do_update($table, $instance->primary_key(), $id, $status['data']);
						break;
					case factory_actions::Create:
						//Not applicable anymore
						break;
				}
			}
		}
	}

	private function crud()
	{
		return DB_CRUD::singleton();
	}


}