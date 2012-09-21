<?
class paypal_express_checkout
{
	var $version = '3.0';
	var $paypal_url_dev = 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=';
	var $paypal_url_live = 'https://www.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=';
	var $paypal_url_giro_dev = 'https://www.sandbox.paypal.com/webscr?cmd=_complete-express-checkout&useraction=commit&token=';
	var $paypal_url_giro_live = 'https://www.paypal.com/webscr?cmd=_complete-express-checkout&useraction=commit&token=';	
	var $API_Endpoint_dev = 'https://api-3t.sandbox.paypal.com/nvp';
	var $API_Endpoint_live = 'https://api-3t.paypal.com/nvp';
	var $_log;
	
	function paypal_express_checkout
	(
		$username,
		$password,
		$signature,
		$type = "live"
	)
	{
		if($type == "dev")
		{
			$this->paypal_url = $this->paypal_url_dev;
			$this->paypal_url_giro = $this->paypal_url_giro_dev;
			$this->API_Endpoint = $this->API_Endpoint_dev;
		}
		else
		{
			$this->paypal_url = $this->paypal_url_live;		
			$this->paypal_url_giro = $this->paypal_url_giro_live;		
			$this->API_Endpoint = $this->API_Endpoint_live;
		}		
		$this->API_UserName = $username;
		$this->API_Password = $password;
		$this->API_Signature = $signature;
		
		$this->_log['constructor'] = func_get_args();
		
	}
	
	function finalize
	(
		$amt,
		$itemamt,
		$shippingamt,
		$handlingamt,
		$taxamt,
		$paymentType,
		$currencyCodeType,
		$token,
		$payer_id
	)
	{
		//DoExpressCheckoutPaymentRequest 
		$nvpstr = "&PAYMENTACTION=".$paymentType."&CURRENCYCODE=".$currencyCodeType."&IPADDRESS=".urlencode($_SERVER['SERVER_NAME'])."&PayerID=".urlencode($payer_id)."&TOKEN=".urlencode($token)."&AMT=".$amt."&ITEMAMT=".$itemamt."&SHIPPINGAMT=".$shippingamt."&HANDLINGAMT=".$handlingamt."&TAXAMT=".$taxamt;
		$resArray=$this->hash_call("DoExpressCheckoutPayment",$nvpstr);
		
		//MC::debug($resArray);
		
		$this->_log['finalize'] = $resArray;
		
		if(strtoupper($resArray["ACK"])=="SUCCESS")
		{
			return array
			(
				'STATUS' => strtoupper($resArray['PAYMENTSTATUS']),
				'AMT' => $resArray['AMT'],
				'REASON' => strtoupper($resArray['PENDINGREASON']),
				'TRANSACTIONID' => $resArray['TRANSACTIONID']
			);
			#return true;
		} 
		else  
		{
			return false;
		}
	}
	
	function check()
	{
		$token =urlencode( $_REQUEST['token']);
		
		 /* Build a second API request to PayPal, using the token as the
			ID to get the details on the payment authorization
			*/
		$nvpstr="&TOKEN=".$token;

		 /* Make the API call and store the results in an array.  If the
			call was a success, show the authorization details, and provide
			an action to complete the payment.  If failed, show the error
			*/
		$resArray=$this->hash_call("GetExpressCheckoutDetails",$nvpstr);
		
		$this->_log['check'] = $resArray;
		
		// giropay redirection required
		if($resArray['REDIRECTREQUIRED'])
		{
			header('Location '.$this->paypal_url_giro.$token);
			die;
		}
		
		if(strtoupper($resArray["ACK"])=="SUCCESS")
		{
			return true;
		} 
		else  
		{
			return false;
		}
	}
	
	function init
	(
		$paymentAmount,
		$paymentType,
		$returnURL,
		$cancelURL,
		$currencyCodeType,
		$GIROPAYSUCCESSURL=null,
		$GIROPAYCANCELURL=null,
		$BANKTXNPENDINGURL=null,
		$NOSHIPPING=1
	)
	{

		$nvpstr="&LOCALECODE=GB&Amt=".urlencode($paymentAmount)."&PAYMENTACTION=".urlencode($paymentType)."&ReturnUrl=".urlencode($returnURL)."&CANCELURL=".urlencode($cancelURL)."&CURRENCYCODE=".urlencode($currencyCodeType)."&NOSHIPPING=".$NOSHIPPING."&PAYMENTREQUEST_0_AMT=".urlencode($paymentAmount);
		
		// add giropay if it exists

		if($BANKTXNPENDINGURL && $GIROPAYSUCCESSURL && $GIROPAYCANCELURL)
		{
			$nvpstr .= "&GIROPAYSUCCESSURL=".urlencode($GIROPAYSUCCESSURL)."&GIROPAYCANCELURL=".urlencode($GIROPAYCANCELURL)."&BANKTXNPENDINGURL=".urlencode($BANKTXNPENDINGURL);
		}
	
		$resArray = $this->hash_call("SetExpressCheckout",$nvpstr);
		
		//MC::debug($resArray);
		
		if(strtoupper($resArray["ACK"])=="SUCCESS")
		{
			//header("Location: ".$this->paypal_url.urldecode($resArray["TOKEN"]));
			return $this->paypal_url.urldecode($resArray["TOKEN"]);
		} 
		else  
		{
			$this->_log['init'] = func_get_args();
			return false;
		}
	}
	
	function hash_call($methodName,$nvpStr)
	{
		//declaring of global variables
		// global $API_Endpoint,$version,$API_UserName,$API_Password,$API_Signature,$nvp_Header;

		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
    	//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
	   //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
	   /*
		if(USE_PROXY)
		{
			curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT); 	
		}
		*/

		//NVPRequest for submitting to server
		$nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($this->version)."&PWD=".urlencode($this->API_Password)."&USER=".urlencode($this->API_UserName)."&SIGNATURE=".urlencode($this->API_Signature).$nvpStr;
				
		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

		//getting response from server
		$response = curl_exec($ch);
		
		//convrting NVPResponse to an Associative Array
		$nvpResArray=$this->deformatNVP($response);
		
		$this->_log[$methodName]['nvpstr'] = $nvpStr;
		$this->_log[$methodName]['nvpreq'] = $nvpreq;
		$this->_log[$methodName]['nvpstr'] = $nvpReqArray;
		
		if (curl_errno($ch)) 
		{
			// error handling
			return false;
		 } 
		 else 
		 {
				curl_close($ch);
	  	}
		return $nvpResArray;
	}

	/** This function will take NVPString and convert it to an Associative Array and it will decode the response.
	  * It is usefull to search for a particular key and displaying arrays.
	  * @nvpstr is NVPString.
	  * @nvpArray is Associative Array.
	  */

	function deformatNVP($nvpstr)
	{
		$intial=0;
	 	$nvpArray = array();
		while(strlen($nvpstr))
		{
			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
	     }
		return $nvpArray;
	}
	
	function log_get()
	{
		return $this->_log;
	}
	
}
?>