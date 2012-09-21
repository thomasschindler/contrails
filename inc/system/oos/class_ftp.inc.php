<?
class ftp
{               
	var $conn_id;
	
	function ftp($ftp_server,$ftp_user_name,$ftp_user_pass)
	{
		$this->conn_id = ftp_connect($ftp_server);
		$login_result = ftp_login($this->conn_id, $ftp_user_name, $ftp_user_pass);
		if ((!$this->conn_id) || (!$login_result)) 
		{
			throw new Exception('Could not connect');
		}		
		// set the connection to passive mode
		ftp_pasv( $this->conn_id, true );
	} 
	
	public function file_upload($local,$remote,$delete=false)
	{                                                          
		if(ftp_put($this->conn_id,$remote,$local,FTP_BINARY) AND $delete == true)
		{
			UTIL::delete_file($local);
		}
	}    
	
	public function file_download($remote,$local,$delete=false)
	{                                                          
		ftp_get($this->conn_id,$local,$remote,FTP_BINARY);
		if(is_file($local) AND $delete == true)
		{
			ftp_delete($this->conn_id,$remote);
		}
	}
	/**
	* pull data from ftp servers
	* synchronize a folder
	*/  
	public function folder_download($remote,$local,$delete=false)
	{
		// list the files
		$l = ftp_nlist($this->conn_id,$remote);
		// get the files			         
		foreach($l as $f)
		{   
			if(strlen($remote)>1)      
			{
				$f = preg_replace("/".$remote."/",'',$f);
			}    
			if(!is_file($local.$f) OR $overwrite == true)
			{
				ftp_get($this->conn_id,$local.$f,$remote.$f,FTP_BINARY);
				if(is_file($local.$f) AND $delete == true)
				{
					ftp_delete($this->conn_id,$remote.$f);
				}
			}
		}
		// close
		ftp_close($this->conn_id);
		return true;
	}
	/**
	*	
	*/
	public function folder_upload($local,$remote,$delete=false)
	{    
		$local .= "/";
		$remote .= "/";		
		// get a list of files, locally
		$d = dir($local);
		while($e = $d->read())
		{
			if(substr($e,0,1)==".")
			{
				continue;
			}
			if(is_dir($local.$e))
			{
				continue;
			}
			if(ftp_put($this->conn_id,$remote.$e,$local.$e,FTP_BINARY) AND $delete == true)
			{
				UTIL::delete_file($local.$e);
			}
		}

	}
}
?>