<?
class identicall
{
	var $customer;
	var $account;
	
   var $arr_output = array();
   var $res_parser;
   var $str_xml_data;
   
   function identicall($customer=null,$account=null)
   {
	   	if(isset($customer))
	   	{
	   		$this->customer = $customer;
	   	}
	   	if(isset($account))
	   	{
	   		$this->account = $account;
	   	}
   }
   
	function query($phone=null,$confirm_id) 
	{
		if($phone==null)
		{
			return array("ERROR"=>-1000);
		}
		if(substr($phone,0,2) != 00)
		{
			return array("ERROR"=>-1000);
		}
		$phone = "00".(int)trim($phone);
		// send request
		$fp = pfsockopen("ssl://service.identicall.de", 443, $errno, $errstr);
		if (!$fp)
		{
			return array("ERROR"=>1000);
		}
		else
		{
			if(!$confirm_id)
			{
				$confirm_id = md5($phone);
			}
			fputs($fp, "GET /services/request.do?customer=".$this->customer."&account=".$this->account."&confirm_address=".$phone."&confirm_id=".$confirm_id."&block=true  HTTP/1.1\r\n"); 
			fputs($fp, "Host: service.identicall.de\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n");

			while (!feof($fp))
			{
				$s = fgets($fp, 128);
				if($collect and strlen($s) != 0)
				{
					$out .= trim($s);
				}
				if(preg_match("/Content-Type/",$s))
				{
					$collect = true;
				}
			}
			fclose($fp);
		}
		$out = $this->parse($out);
		// analyse and return
		foreach($out[0]['children'] as $item)
		{
			$ret[$item['name']] = $item['tag_data'];
		}
		return  $ret;
	}	

   function parse($str_input_xml) 
   {
		$this->res_parser = xml_parser_create ();
		xml_set_object($this->res_parser,$this);
		xml_set_element_handler($this->res_parser, "tag_open", "tag_closed");
		xml_set_character_data_handler($this->res_parser, "tag_data");
		$this->str_xml_data = xml_parse($this->res_parser,$str_input_xml );
		if(!$this->str_xml_data) 
		{
			die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($this->res_parser)),
			xml_get_current_line_number($this->res_parser)));
		}
		xml_parser_free($this->res_parser);
		return $this->arr_output;
	}
	
	function tag_open($parser, $name, $attrs) 
	{
		$tag=array("name"=>$name,"attrs"=>$attrs);
		array_push($this->arr_output,$tag);
	}
	
	function tag_data($parser, $tag_data) 
	{
		if(trim($tag_data)) 
		{
			if(isset($this->arr_output[count($this->arr_output)-1]['tag_data'])) 
			{
				$this->arr_output[count($this->arr_output)-1]['tag_data'] .= $tag_data;
			}
			else 
			{
				$this->arr_output[count($this->arr_output)-1]['tag_data'] = $tag_data;
			}
		}
   }
   
   function tag_closed($parser, $name) 
   {
		$this->arr_output[count($this->arr_output)-2]['children'][] = $this->arr_output[count($this->arr_output)-1];
		array_pop($this->arr_output);
	}
}

?>