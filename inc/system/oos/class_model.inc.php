<?

abstract class model{
	protected $_fields;
	private $form;

	private $_state = array(
				mstack::Delete 	=> null,
				mstack::Load 	=> null,
				mstack::Update 	=> null,
				mstack::Current => null,
				mstack::Done 	=> null
			);

	public function __construct(){} 

	/**
	*	set receives an array of values and tries to set them
	*/
	function set($a)
	{
		foreach($a as $k => $v)
		{
			$this->{$k}($v);
		}
	}

	/**
	*	returns a form configuration to be used with the form and validator classes 
	*/
	public function form()
	{
		if(!$this->form)
		{
			$f = $this->_fields();

			$i = array();
			if(method_exists($this,'form_ignore'))
			{
				$i = $this->form_ignore();
			}
			
			foreach($f as $field => $data)
			{
				if(in_array($field, $i))
				{
					continue;
				}
				if($fld = $this->form_parse_cnf($data))
				{
					$this->form['fields'][$field] = $fld;
				}
				
			}
		}
		return $this->form;
	}

	private function form_parse_cnf($data)
	{
		$conf = array();
		if($data['Key'] == 'PRI')
		{
			// this should only be carried through, if it has a value
			if($this->{strtolower($data['Field'])}() == false)
			{
				return;
			}
			$conf['cnf']['type'] = 'hidden';
			$conf['cnf']['unique'] = 'true';
			$conf['cnf']['err_unique'] = e::o('err_unique_generic');
		}
		else
		{
			$conf['label'] = strtoupper($data['Field']);
			// extract the type
			$tmp = explode("(",$data['Type']);
			$type = strtolower($tmp[0]);
			$length = NULL;
			if(isset($tmp[1]))
			{
				$tmp = explode(")",$tmp[1]);
				$conf['cnf']['max'] = $tmp[0];

			}
			switch($type)
			{
				case 'enum':
					$conf['cnf']['type'] = 'select';
					// @todo: items
				break;
				case 'text':
					$conf['cnf']['type'] = 'input';
					$conf['cnf']['format'] = 'text';
					$conf['cnf']['err_format'] = e::o('err_text_generic');
				break;
				default:
					if(preg_match("/pass/i",$data['Field']) || preg_match("/pwd/i",$data['Field']) )
					{
						$conf['cnf']['type'] = 'password';
					}
					else
					{
						$conf['cnf']['type'] = 'input';
					}
					// validations
					if(preg_match("/mail/i",$data['Field']))
					{
						$conf['cnf']['format'] = 'email';
						$conf['cnf']['err_format'] = e::o('err_email_generic');
					}
					else
					{
						switch($type)
						{
							case 'smallint':
							case 'tinyint':
							case 'int':
								$conf['cnf']['format'] = 'int';
								$conf['cnf']['err_format'] = e::o('generic_form_error_int',null,null,null,true);
							break;
							default:
								$conf['cnf']['format'] = 'text';
								$conf['cnf']['err_format'] = e::o('generic_form_error_text',null,null,null,true);
						}
					}
				break;
			}
		}
		// add the value
		$conf['cnf']['value'] = $this->{strtolower($data['Field'])}();
		// is it allowed to be emtpy?
		if($data['Null'] == 'NO')
		{
			$conf['cnf']['empty'] = false;
			$conf['cnf']['err_empty'] = e::o('generic_form_error_empty',null,null,null,true);
		}
		return $conf;
	}

	public function primary_key(){
		$keys = $this->_keys();
		return $keys['0']['fields']['0'];
	}

	public function load($data){
		if($this->loaded()){
			log::err("Attempting to load an already existing instance.");
			return null;
		}

		$this->_fields = $data;
		if($this->push_load($this->_fields) !== exit_status::success)
		{
			return null;	
		}

		return true;
	}

	public static function fetch($class, $data){
		if(!class_exists($class)){
			log::err("Attempted to load an unexisting class in the fetch method '$class'");
			return null;
		}

		$table_name = $class::table_name();
		$CRUD = db_crud::singleton();
		$rows = $CRUD->load_range($table_name, array('id'), $data);
		$return = array();

		if(!$rows || $rows->nr() <= 0){
			return null;
		}

		while($rows->next()){
			$instance = $this->MF()->obtain($table_name, $rows->f('id'));
			$return[] = $instance;
		}

		return $return;
	}

	public function copy(){
		/**
		 *	@todo
		 */
	}

	/**
	 *	Return the final state of the object, ready for being saved in the Database. Will return a
	 *	structure with the following data:
	 *		$return $ array(
	 *			'action' => factory_actions::Update/Create/Delete/Unchanged
	 *			'data' => array containing the data that will be passed to the database
	 * 		)
	 *
	 */
	public function pull(){
		$action = factory_actions::Unchanged;
		$data = array();

		if($this->deleted()){
			$action = factory_actions::Delete;
		}

		if($this->exists() == false){
			$action = factory_actions::Create;
			$data = $this->_state[mstack::Update];
		}

		if($this->updated() == true){
			$action = factory_actions::Update;
			$data = $this->_state[mstack::Update];
		}

		return array(
					'action' 	=> $action, 
					'data' 		=> $data
				);
	}

	public function clear_status(){
		$this->_state = array(
				mstack::Delete 	=> null,
				mstack::Load 	=> $this->_state[mstack::Load],
				mstack::Update 	=> null,
				mstack::Current => null,
				mstack::Done 	=> null
			);
	}


	/**
	 *	Does this object have an update pending?
	 *
	 *	@return 	bool 	True/False depending on the existence of data in the mstack::Update
	 *						position of the state array
	 */
	public function updated(){
		return !is_null($this->_state[mstack::Update]);
	}

	/**
	 *	Has this object been changed in any way? (created, deleted or updated)
	 *
	 *	@return 	bool 	True/False depending on the existence of data in the Update/Delete
	 *						positions in the array.
	 */
	public function changed(){
		return $this->updated() || $this->deleted() || !$this->exists();
	}
	
	private function loaded(){ 
		return !is_null($this->_state[mstack::Load]); 
	}

	public function exists(){
		return !is_null($this->_state[mstack::Load]);
	}

	public function deleted(){
		return !is_null($this->_state[mstack::Delete]);	
	}

	protected function push_load($data){
		if(!is_null($this->_state[mstack::Load])){
			log::warn("Attempting to reload an already loaded object. This shouldn't happen.");
			return exit_status::no_change;
		}

		$this->_state[mstack::Load] = $data;
		return exit_status::success;	
	}

	protected function push_update($data){
		MC::log(print_r($data, true));
		if($this->updated() === false){
			$this->_state[mstack::Update] = $data;
			return exit_status::success;		
		}

		if($this->deleted()){
			log::err("Cannot update a model that is marked for deletion.");
			return exit_status::error;
		}

		foreach($data as $key => $value){
			$this->_state[mstack::Update][$key] = $value;
		}

		return exit_status::success;
	}

	protected function push_create($data){
		if($this->exists()){
			log::err("Cannot reinsert an object on the database. If you need to clone a row, use the clone method instead.");
			return exit_status::error;
		}

		$this->_state[mstack::Update] = $data;

		return exit_status::success;
	} 
	
	protected function push_delete(){
		if($this->deleted()){
			log::warn("Object has already been deleted.");
			return exit_status::no_change;
		}

		if($this->changed() || $this->exists() === false){
			log::warn("Deleting an updated with pending changes will stop these changes from being written to the Database.");
		}

		$this->_state[mstack::Delete] = true;

		return exit_status::success;
	}

	private function validate_data($data, $complete_unexisting = false){
		$fields = $this->_fields();

		foreach($fields as $column => $stats){
			$field_name = $stats['Field'];
			$sent_value = false;

			if(isset($data[$field_name]) === true){
				$value = $data[$field_name];
				$sent_value = true;
			}elseif($complete_unexisting && is_null($stats['Default']) === false){
				$value = $stats['Default'];
			}elseif($complete_unexisting && $stats['Null'] === migrations::Yes){
				$value = null;
			}elseif($complete_unexisting){
				log::err("No value found for column $field_name. Column has no default value and cannot be null.");
				return null;
			}

			if(!$this->$field_name($value)){
				log::err("The value " . (empty($value) ? "[NULL]" : "$value") . " was not successfuly set." . (!$sent_value ? " Value fetched from the generated model." : ''));
				return null;
			}
		}

		return $data;
	}

	protected function _valid($value, $type, $size, $nullable = false){
		switch ($type) {
			case 'int': 
				if(is_int($value) === false && ctype_digit($value) === false){
					log::warn("Value '$value' is not an Integer.");
					return null;
				}
				//Just to make sure we also get the correct value if it happened to be a string containing a number
				$value = intval($value);

				if($value > mysql_int_range::Min && $value > mysql_int_range::Max){
					log::warn("Value '$value' is out of range for a MySQL Integer.");
					return null;
				}
				break;
			case 'varchar'; break;
				if(is_string($value) === false){
					log::warn("Sent in value does not translate to a valid string.");
					return null;
				}
				$len = strlen($value);
				if($len > $size){
					log::warn("The string '$value' is too long ($len) for the maximum size allowed ($size). The string will be truncated to $size characters.");
				}
				break;
			case 'float': 
				/* @todo */
				break;
			default:
				break;
		}

		return true;
	}

	protected function MF(){
		return MF::singleton();
	}

}