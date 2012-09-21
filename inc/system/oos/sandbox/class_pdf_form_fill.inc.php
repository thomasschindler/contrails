<?

/*
$val = array
(
    'invoice************' => $invoicenumber,
	'order****' => substr($o->f('ordernumber'),0,9),
    'date******' => $date,
    'name******************************************************************' => $o->f('billingcontactfirstname')." ".$o->f('billingcontactlastname'),
	'street******************************************************************' => $o->f('billingcustomeraddress1')." ".$o->f('billingcustomeraddress2'),
	'city******************************************************************' => $o->f('billingcustomercity')." ".$o->f('billingcustomerpostcode'),
	'country******************************************************************' => $o->f('billingcustomercountryname'),
	'shippingname*********************************' => $o->f('shippingcontactfirstname')." ".$o->f('shippingcontactlastname'),
	'shippingstreet*************************************' => $o->f('shippingcustomeraddress1')." ".$o->f('shippingcustomeraddress2'),
	'shippingcity*************************************' => $o->f('shippingcustomercity')." ".$o->f('shippingcustomerpostcode'),
	'shippingcountry*************************' => $o->f('shippingcustomercountryname'),
	'price*****' => 'R$ '.$o->f('itemtotalsell'),
	'quantity***' => $o->f('qty'),
	'description******************************************************************' => $productname,
	'article***' => substr($o->f('productcode'),0,10)
);    


$p = new pdf_form_fill('test.pdf',$val);
$p->save('testout.pdf');


*/

class pdf_form_fill
{
 

	// konvertiert einen string in hexadezimale form 
	function string2hex($string)
	{
		$array = str_split($string);		
		foreach ($array as $value) 
		{
				$final .= "00" .bin2hex($value);			 
		}
		$final = strtoupper($final);
		return $final;
	}


	function pdf_form_fill($pdf_template, $pdf_form_values) 
	{
		if (!is_array($pdf_form_values)) 
		{
			throw new Exception("Array \$pdf_form_values enthält keine Werte.");
		}
		if(!file_exists($pdf_template))
		{
			throw new Exception("kein pdf-template gefunden.");
		}	
		if ($fp = fopen($pdf_template, 'rb')) 
		{
	    	 $template = fread ($fp, filesize ($pdf_template));
		     fclose ($fp);        
		} 
		else 
		{
			throw new Exception("pdf-template konnte nicht geöffnet werden => ist evtl beschädigt.");
		}	   
		foreach($pdf_form_values as $k => $v)
		{
			$pdf_form_values[$k] = mb_convert_encoding($v,'ASCII','UTF-8');
		}
		 
		reset($pdf_form_values);
		while (list($src, $dest) = each ($pdf_form_values)) 
		{	
			if (strlen($dest) <= strlen($src)) 
			{
				$pdf_form_values[$src] = str_pad($dest, strlen($src), " ", STR_PAD_RIGHT );
			} 
			else 
			{ 
				throw new Exception("Ziel-String strlen($dest) länger als Quellstring strlen($src)"); 
			}		 
		}		
	
		// Ersetzungen im Dokument vornehmen
		reset($pdf_form_values);
		while (list($key, $value) = each ($pdf_form_values)) 
		{
			$treffer["hex"] = substr_count($template, $this->string2hex($key));		 
			if ($treffer["hex"] == 0) 
			{ 
				$treffer["asc"] = substr_count($template, $key);			 
			}
			if ($treffer["hex"] == 0 && $treffer["asc"] == 0) 
			{
				throw new Exception("in PDF-Vorlage ist Vorbelegungswert $key nicht enthalten.");
			}
			if ($treffer["hex"] > 0) 
			{
				$template = str_replace($this->string2hex($key), $this->string2hex($value), $template);	
			}
			if ($treffer["asc"] > 0) 
			{
				$template = str_replace($key, $value, $template);	 
			}		 
		}
		$pdf = $template;
		$this->pdf = $pdf;
		return true;
	}	
		
	function download($filename) 
	{		
		if (!isset($this->pdf)) 
		{
			throw new Exception("kein pdf-inhalt angegeben");
		}
		if (!isset($filename)) 
		{
			throw new Exception("kein dateiname angegeben");
		}
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Pragma:  no-cache");	
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-type: application/pdf");
		echo $this->pdf;
		die;
	}  

	function save($filename) 
	{			
		if(!isset($this->pdf))
		{
			throw new Exception("kein pdf-inhalt angegeben.");
		}	
		if(!isset($filename))
		{
			throw new Exception("kein dateiname angegeben.");
		}	
	    if (!$handle = fopen($filename, "a")) 
		{
	        	 throw new Exception("kann die datei $filename nicht öffnen");	         
	    }
		if (!fwrite($handle, $this->pdf)) 
		{
	        	throw new Exception("kann in die datei $filename nicht schreiben");	        
	    }
	    fclose($handle);		
	}  


	function email($filename, $to, $fromname, $fromemail, $subject, $message) 
	{	
		if (!isset($this->pdf)) 
		{
			throw new Exception("kein pdf-inhalt für attachment angegeben");
		}
		if (!isset($filename)) 
		{
			throw new Exception("kein dateiname angegeben");
		}
		if (!isset($to)) 
		{
			throw new Exception("keine empfängeradresse angegeben");
		}
		if (!isset($to)) 
		{
			throw new Exception("keine empfängeradresse angegeben");
		}
		if (!preg_match ("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,6}$/", $to)) 
		{
			throw new Exception("empfängeradresse ist ungültig");
		}
		if (!isset($fromname)) 
		{
			throw new Exception("kein absendername angegeben");
		}
		if (!isset($fromemail)) 
		{
			throw new Exception("keine absendermailadresse angegeben");
		}
		if (!preg_match ("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,6}$/", $fromemail)) 
		{
			throw new Exception("absendermailadresse ist ungültig");
		}
		if (!isset($subject)) {
			throw new Exception("kein betreff angegeben");
		}
		if (!isset($message)) {
			throw new Exception("keine nachricht angegeben");
		}
		$eol="\n";		
		$headers  = "From: ".$fromname."<".$fromemail.">".$eol;
	  	$headers .= "Reply-To: ".$fromname."<".$fromemail.">".$eol;
	  	$headers .= "Return-Path: ".$fromname."<".$fromemail.">".$eol;    
	  	$headers .= "Message-ID: <".time()."-".$fromemail.">".$eol;
	  	$headers .= "X-Mailer: PHP v".phpversion().$eol;          	  	
	  	$headers .= "MIME-Version: 1.0".$eol;  	
		// Attachment
		$att = "begin 666 $filename".$eol;
		$att .= convert_uuencode($this->pdf);
		$att .= "end".$eol;
		// Message
		$att .= $message;
		// Mail versenden
		ini_set(sendmail_from, $fromemail);  
		$mail_sent = mail($to, $subject, $att, $headers);			
	  	ini_restore(sendmail_from);
		return $mail_sent;
	} 

}

?>