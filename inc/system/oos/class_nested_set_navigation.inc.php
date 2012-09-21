<?php
/**
*
*	helpclass for handling navigation issues
*	things we need to know:
*		
*		should be used by some navigation mod(s) to display navigations
*	
*	has some session handling to allow multiple navigations to store their data
*	in the session via their vid.
*	this data is the current open_nodes array
*
*	TODO:
*	register the amount of open paths.
*		-> 0 is default and unlimited
*
*	@see 			nested_sets
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		util
*/
class nested_set_navigation {
	/**
	*	vars
	*/
	var $num_paths = 0; // number of paths. either 0 - unlimited or 1
	var $vid;
	var $tree;
	var $set;
	var $SESS;
	/**
	*	constructor
	*/
	function nested_set_navigation(){
		$this->SESS = &SESS::singleton();
		$this->set = new NestedSet();
	}
	/**
	* initialisation
	*	num_paths -1 means unlimited amouint of open paths
	*/
	function init($vid,$tbl,$num_paths = 1)
	{
		$this->vid = $vid;
		$this->num_paths = $num_paths;
		$this->set->set_table_name($tbl);
		$this->tree = $this->set->getNodes();
	}
	/**
	*	subtree returns the nodes of the tree that are in open_nodes
	*	and under id
	*/
	function get_subtree($node = 1,$output = array()){
		foreach($this->tree as $item){
			if($item['id'] == $node){
				$level = $item['level']+1;
				$children = $item['childs'];
#				if($node == $open_nodes[0]){
				if($node == 1){ // if root node is 1 - maybe should be set manually some time...
					array_push($output,$item);
				}
				continue;
			}
			// start collecting
			if($level){
				if($children <= 0 OR !$children){
					return $output;
				}
				if($level == $item['level']){
					$children -= ( $item['childs'] + 1 );
					array_push($output,$item);
				}

				if(in_array($item['id'],$this->get_open_nodes())){
					if($item['level'] == $level){
						$output = $this->get_subtree($item['id'],$output);
					}
				}

			}
		}
		return $output;
	}
	/**
	*	get start node by level
	*/
	function get_start_node_by_level($level)
	{
		foreach($this->tree as $node)
		{
			if($node['level'] == $level)
			{
				return $node['id'];
			}
		}
	}
	/**
	* 	returns all nodes at the level asked
	*/
	function get_level($level){
		$output = array();
		$tree = $this->set->getNodes($node);
		foreach($tree as $item){
			if($item['level'] == $level){
				array_push($output,$item);
			}
		}
		return $output;
	}
	/**
	*	sets the open_nodes array in the session for the given vid
	*/
	function set_open_nodes($open_nodes){
		$this->SESS->set($this->vid,'open_nodes',$open_nodes);
	}
	/**
	*	gets the open_nodes array from the session for the given vid
	*/
	function get_open_nodes(){
		$open_nodes =  $this->SESS->get($this->vid,'open_nodes');
		if(is_array($open_nodes)){
			return $open_nodes;
		}
		return array();
	}
	/**
	*	adds a node to the array of open nodes for the given vid
	*	and returns the new array
	*/
	function open_node($node){
		$open_nodes = $this->get_open_nodes($this->vid);		
		// keep only one path open at a time:
		if($this->num_paths != -1 AND sizeof($open_nodes) != 0){
			$path = $this->set->getPath($node,array('id'));
			$open_nodes = array();
			foreach($path as $item){
				$open_nodes[] = $item['id'];
			}
		}
		else{
			array_push($open_nodes,$node);
		}
		$this->set_open_nodes($open_nodes);
		return $open_nodes;
	}
	/**
	*	close a node - take a node out of the array and all its children as well
	*/
	function close_node($node){
		// get the open_nodes
		$open_nodes = $this->get_open_nodes();
		// get the set of the nodes for the set table
		$nodes = $this->set->getSubtree($node,"id");

		$closing = array();
		foreach($nodes as $item){
			$closing[] = $item['id'];
		}
			
						
		foreach($open_nodes as $key => $item){
			if(in_array($item,$closing)){
				unset($open_nodes[$key]);
			}
		}
		// set it back to the session#
		$this->set_open_nodes($open_nodes);
		return $open_nodes;
	}

	/**
	*	filter the tree by an array of ids
	*/
	function filter($tree,$filter = array()){
		foreach($tree as $key => $item){
			if(!in_array($item['id'],$filter)){
				unset($tree[$key]);
			}
		}
		return $tree;
	}
	/**
	*	returns true if node si open
	*/
	function is_open($node){
		if(in_array($node,$this->get_open_nodes())){
			return true;
		}
		return false;
	}
	/**
	*	get open nodes from id
	*/
	function catch_jump($id){
		if($id == 1){
			return $this->set_open_nodes(array());
			return array();
		}
		// if id is in open nodes, do nothing
		if(in_array($id,$this->get_open_nodes($this->vid))){
			return;
		}
		// get the path
		$path = $this->set->getPath($id,array('id'));
		// transform the path into open nodes path
		$open_nodes = array();
		foreach($path as $item){
			$open_nodes[] = $item['id'];
		}
		$this->set_open_nodes($open_nodes);
		return $open_nodes;
	}
	/**
	*	link to getPath in nested sets
	*/
	function get_path($id,$select = null){
		if(is_array($select)){
			return $this->set->getPath($id,$select);
		}
		return $this->set->getPath($id);
	}

}
?>
