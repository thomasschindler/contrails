<?php 
/**
*	ALLPAGO Integration
*  	wpf-integrator.php version 0.8
*	
*  	cb@ctpe.net
*  
*  	post connection method with ctpe.net.
*
*/

  class _ctpepost {
	var $params = array();

	var $server = "test.ctpe.net";

	var $path = "/frontend/payment.prc";

	var $wpf = "false";

    function _ctpepost ($server,$path,$sender,$channel,$userid,$userpwd,$token,$transaction_mode,$transaction_response) {
	  $this->path=$path;
	  $this->server=$server;
	  $this->user_agent="php ctpepost";
	  $this->params["request.version"]="1.0";
	  $this->params["security.token"]=$token;

      $this->params["server"] = $server;
      $this->params["path"] = $path;
      $this->params["security.sender"] = $sender;
      $this->params["transaction.channel"] = $channel;
      $this->params["user.login"] = $userid;
      $this->params["user.pwd"] = $userpwd;
	  $this->params["transaction.mode"] = $transaction_mode;
	  $this->params["transaction.response"] = $transaction_response;
    }

    /* 
      using HTTP/POST send message to ctpe server
    */
    function sendToCTPE($host,$path,$postdata) {

	 	$cpt = curl_init();	

		curl_setopt($cpt, CURLOPT_URL, "https://$host$path");
		curl_setopt($cpt, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($cpt, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($cpt, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, 1);

		curl_setopt($cpt, CURLOPT_POST, 1);
		curl_setopt($cpt, CURLOPT_POSTFIELDS, $postdata);

		$this->resultURL = curl_exec($cpt);
		$this->error = curl_error($cpt);
		$this->info = curl_getinfo($cpt);

		curl_close($cpt);
	}

	function setPaymentInformation($payment_amount,$payment_usage,$identification_transactionid,$payment_currency)
	{
		$this->params["presentation.amount"]=$payment_amount;
		$this->params["presentation.usage"]=$payment_usage;
		$this->params["identification.transactionID"]=$identification_transactionid;
		$this->params["presentation.currency"]=$payment_currency;
	}

	function setPaymentCode($payment_code)
	{
		$this->params["payment.code"]=$payment_code;
	}

	function setCustomerContact($contact_email,$contact_mobile,$contact_ip,$contact_phone)
	{
		$this->params["contact.email"]=$contact_email;
		$this->params["contact.mobile"]=$contact_mobile;
		$this->params["contact.ip"]=$contact_ip;
		$this->params["contact.phone"]=$contact_phone;
	}
	
	function setCustomerAddress($address_street,$address_zip,$address_city,$address_state,$address_country)
	{
		$this->params["address.street"]=$address_street;
		$this->params["address.zip"]=$address_zip;
		$this->params["address.city"]=$address_city;
		$this->params["address.state"]=$address_state;
		$this->params["address.country"]=$address_country;
	}

	function setCustomerName($name_salutation,$name_title,$name_give,$name_family,$name_company)
	{
		$this->params["name.salutation"]=$name_salutation;
		$this->params["name.title"]=$name_title;
		$this->params["name.given"]=$name_give;
		$this->params["name.family"]=$name_family;
		$this->params["name.company"]=$name_company;
	}

	function setWPFparams($frontend_enabled,$frontend_popup,$frontend_mode,$frontend_lang,$frontend_response_url)
	{
		$this->wpf=$frontend_enabled;
		$this->params["FRONTEND.ENABLED"]=$frontend_enabled;
		$this->params["FRONTEND.POPUP"]=$frontend_popup;
		$this->params["FRONTEND.MODE"]=$frontend_mode;
		$this->params["FRONTEND.LANGUAGE"]=$frontend_lang;
		$this->params["FRONTEND.RESPONSE_URL"]=$frontend_response_url;
	}

    function commitPOSTPayment() {

	foreach (array_keys($this->params) AS $key)
	{
		$$key .= $this->params[$key];
		$$key = urlencode($$key);
		$$key .= "&";
		$var = strtoupper($key);
		$value = $$key;
		$result .= "$var=$value";
	}
	$strPOST = stripslashes($result);

	$this->sendToCTPE($this->server,$this->path,$strPOST);

	if ($this->resultURL)
	{
		return $this->parserResult($this->resultURL);
	} 
	else
	{
		return false;
	}
	}

    /*
      Parse POST message returned by CTPE server.
    */
    function parserResult($resultURL) 
	{
		
		$r_arr=explode("&",$resultURL);

		$this->wpf=strtolower($this->wpf);

		foreach($r_arr AS $buf)
		{
			$temp=urldecode($buf);
			$temp=split("=",$temp,2);

			$postatt=$temp[0];
			$postvar=$temp[1];

			$returnvalue[$postatt]=$postvar;

		}
		return($returnvalue);



		/*
		   uncomment the following line for debug output (whole XML)
		 */
		//print "<br>$resultXML";


    }

  }
?>
