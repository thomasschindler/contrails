<?php
/**
	-class for handling xml files and strings
	has default handling

	an object can be passed

        class handle_chat{
                var $datamine;

                function start($tag,$att){
                        $this->datamine .= $tag."<br>";
                }
                function end($tag){
                }
                function data($data){
                        $this->datamine .= $data."<br>";
                }
                function collect(){
                        echo $this->datamine." in my object";
                }
        }

        example:
                $my_handler = new handle_chat();

                $cr = new xml_reader(&$my_handler);
                $cr->parse($file);
                echo $cr->collect();
                $cr->free();

*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		util
*/
class xml_reader{

	var $depth = array();	
	var $error = array();
	var $parser;
	var $data;
	var $obj; // foreign object
	/**
	*	constructor
	*/
	function xml_reader($obj = null){
		if(isset($obj)){
			$this->obj = $obj;
		}
    	$this->parser = xml_parser_create_ns("",'^');
    	xml_set_object($this->parser,&$this);
		xml_set_element_handler($this->parser, "__start", "__end");
		xml_set_character_data_handler($this->parser,"__data");
	}
	/**
	*	start element handler
	*/
	function __start($parser, $name, $attrs){
		// step into the user function
		if(isset($this->obj)){
			return $this->obj->start($name,$attrs);
		}
		// show the input
		for ($i = 0; $i < $depth[$parser]; $i++) {
			$this->data .= "  ";
		}
		$tab = str_repeat("&nbsp;",sizeof($this->depth)*4);
		$this->data .= $tab."$name<br>";
		foreach($attrs as $key => $item){
			$this->data .= $tab."&nbsp;&nbsp;$key - $item<br>";
		}
		$this->depth[$parser]++;
	}
	/**
	*	end element handler
	*/
	function __end($parser, $name){
		// step into the user function 
		if(isset($this->obj)){
			return $this->obj->end($name);
		}
		$this->depth[$parser]--;
	}
	/**
	*	data handler
	*/
	function __data($parser,$data) {
		// step into the user function 
		if(isset($this->obj)){
			return $this->obj->data($data);
		}
		$this->data .= str_repeat("&nbsp;",sizeof($this->depth)*4).$data."<br>";
	}
	/**
	*	parse switch
	*	file or string?
	*/
	function parse($input){
		if(is_file($input)){
			$this->parse_file($input);
		}
		else{
			$this->parse_string($input);
		}
	}
	/**
	*	parse file
	*/
	function parse_file($file){
		if (!($fp = fopen($file, "r"))) {
			$this->error[] = "could not open XML input";
			return;
		}

		while ($data = fread($fp, 4096)) {
			if (!xml_parse($this->parser, $data, feof($fp))) {
				$this->error[] = sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($this->parser)),xml_get_current_line_number($this->parser));
				return;
			}
		}	
	}
	/**
	*	parse string
	*/
	function parse_string($string){
		if (!xml_parse($this->parser, $string)) {
			$this->error[] = sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($this->parser)),xml_get_current_line_number($this->parser));
			return;
		}
	}
	/**
	*	get the data
	*/
	function collect(){
		if(sizeof($this->error) != 0){
			return array('ERROR'=>$this->error);
		}
		// step into the user function 
		if(isset($this->obj)){
			return $this->obj->collect();
		}
		return $this->data;
	}
	/**
	*	free the parser
	*/
	function free(){
		xml_parser_free($this->parser);
	}
}
?>
