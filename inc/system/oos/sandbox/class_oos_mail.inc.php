<?
class oos_mail
{
	function oos_mail($to=null,$from=null,$subject=null,$body=null)
	{
//		$to = "kbnet@hotoshi.com";
  		if(func_num_args()==4)
  		{
  			if($from == null)
  			{
  				$from = "no-reply@".substr(CONF::baseurl(),7);
  			}
  			return $this->deliver($to,$from,$subject,$body);
  		}		
	}
	
	function attach($message,$name,$ctype = '')
	{
		if(is_file($message))
		{
			$message = UTIL::file_get_contents($message);
		}
    	if(empty($ctype))
    	{
    		$ctype = UTIL::mime_type($name);
    		if(!$ctype)
    		{
    			$ctype = "application/octet-stream";
    		}
    	}
		$this->parts[] = array 
		(
			"ctype" => $ctype,
			"message" => $message,
			"name" => $name
		);
  	}
  	
	function part_add($part)
	{
		return  "Content-Type: ".$part[ "ctype"].($part[ "name"]? "; name = \"".$part[ "name"]. "\"" :  "")."\nContent-Transfer-Encoding: base64\n\n".chunk_split(base64_encode($part["message"]))."\n";
	}
	
	function build($boundary)
	{
		$this->parts = array_reverse($this->parts);
		
		$m =  "\n\n--".$boundary;		
		
		foreach($this->parts as $p)
		{
			$m .= "\n".$this->part_add($p). "--".$boundary;
		}
    	return $m.=  "--\n";
	}
	
  	function deliver($to,$from,$subject,$body)
  	{
  		if(func_num_args()!=4)
  		{
  			return false;
  		}
		$this->attach($body,"","text/plain");
		
		$boundary =  "oos".md5(uniqid(time()));
		$headers[] = "From: ".$from;
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-Type: multipart/mixed; charset=utf-8; boundary = ".$boundary;
//		$headers[] = "Content-type: text/plain; charset=utf-8";
		
//		$m =  "MIME-Version: 1.0\nContent-Type: multipart/mixed; boundary = ".$boundary."\n\nThis is a MIME encoded message.\n\n--".$boundary;		
		/*
		MC::debug($from,"FROM");
		MC::debug($to,"TO");
		MC::debug($subject,"SUBJECT");
		MC::debug($body,"BODY");
		*/
		mail($to,$subject,$this->build($boundary),implode("\n",$headers));
  	}
}

?>