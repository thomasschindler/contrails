<?
/**
* Amazon API Class
* only itemsearch working for now
*/

class amazon
{    
	
	function amazon($public_key='',$private_key='',$region='de')
	{      
		$this->region = $region;
		$this->public_key = $public_key;
		$this->private_key = $private_key;
	}
	 
	
	function signedRequest($region, $params, $public_key, $private_key)
	{
	    /*
	    Copyright (c) 2009 Ulrich Mierendorff

	    Permission is hereby granted, free of charge, to any person obtaining a
	    copy of this software and associated documentation files (the "Software"),
	    to deal in the Software without restriction, including without limitation
	    the rights to use, copy, modify, merge, publish, distribute, sublicense,
	    and/or sell copies of the Software, and to permit persons to whom the
	    Software is furnished to do so, subject to the following conditions:

	    The above copyright notice and this permission notice shall be included in
	    all copies or substantial portions of the Software.

	    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
	    THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
	    DEALINGS IN THE SOFTWARE.
	    */

	    /*
	    Parameters:
	        $region - the Amazon(r) region (ca,com,co.uk,de,fr,jp)
	        $params - an array of parameters, eg. array("Operation"=>"ItemLookup",
	                        "ItemId"=>"B000X9FLKM", "ResponseGroup"=>"Small")
	        $public_key - your "Access Key ID"
	        $private_key - your "Secret Access Key"
	    */

	    // some paramters
	    $method = "GET";
	    $host = "ecs.amazonaws.".$region;
	    $uri = "/onca/xml";
	    // additional parameters
	    $params["Service"] = "AWSECommerceService";
	    $params["AWSAccessKeyId"] = $public_key;
	    // GMT timestamp
	    $params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
	    // API version
	    $params["Version"] = "2009-03-31";
	    // sort the parameters
	    ksort($params);
	    // create the canonicalized query
	    $canonicalized_query = array();
	    foreach ($params as $param=>$value)
	    {
	        $param = str_replace("%7E", "~", rawurlencode($param));
	        $value = str_replace("%7E", "~", rawurlencode($value));
	        $canonicalized_query[] = $param."=".$value;
	    }
	    $canonicalized_query = implode("&", $canonicalized_query);
	    // create the string to sign
	    $string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;
	    // calculate HMAC with SHA256 and base64-encoding
	    $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $private_key, True));
	    // encode the signature for the request
	    $signature = str_replace("%7E", "~", rawurlencode($signature));
	    // create request
	    $request = "http://".$host.$uri."?".$canonicalized_query."&Signature=".$signature;
	    // do request
	    $response = @file_get_contents($request);

	    if ($response === False)
	    {
	        return False;
	    }
	    else
	    {
	        // parse XML
	        $pxml = simplexml_load_string($response);
	        if ($pxml === False)
	        {
	            return False; // no xml
	        }
	        else
	        {
	            return $pxml;
	        }
	    }
	}
	          
	function  bookSearch($keywords,$page=1)
	{                                              
		$params = array
		(	
			'Keywords' => $keywords,
			'SearchIndex' => 'Books',
			'ItemPage' => 1,
			'Operation' => 'ItemSearch',
			'ResponseGroup' => 'Medium,Offers',
			'ItemPage'=>$page
		);
		$r = $this->signedRequest($this->region, $params, $this->public_key, $this->private_key);
		return $r;
	}
	
	function _ItemSearch($SearchIndex, $Keywords, $ItemPage)
	{
		$request="http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&AWSAccessKeyId=".$this->KEYID."&AssociateTag=".$this->AssocTag."&Version=2006-09-11&Operation=ItemSearch&ResponseGroup=Medium,Offers";
		$request.="&SearchIndex=$SearchIndex&Keywords=$Keywords&ItemPage=$ItemPage";
		//The use of `file_get_contents` may not work on all servers because it relies on the ability to open remote URLs using the file manipulation functions. 
		//PHP gives you the ability to disable this functionality in your php.ini file and many administrators do so for security reasons.
		//If your administrator has not done so, you can comment out the following 5 lines of code and uncomment the 6th.  
		$request = $this->signRequest($request);
		$session = curl_init($request);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session); 
		//$response = file_get_contents($request);
		$parsed_xml = simplexml_load_string($response);
//		printSearchResults($parsed_xml, $SearchIndex); 
		return $parsed_xml;
	}
	//------------------------------------------------------------------------------------------------------
	function printSearchResults($parsed_xml, $SearchIndex){
		$numOfItems = $parsed_xml->Items->TotalResults;
		$totalPages = $parsed_xml->Items->TotalPages;
		$CartId = $_GET['CartId'];
		$HMAC = urlencode($_GET['HMAC']);
		print("<table>");
		if($numOfItems>0){
			foreach($parsed_xml->Items->Item as $current){
				if(isset($current->Offers->Offer->OfferListing->OfferListingId)){ //only show items for which there is an offer
					print("<tr><td><img src='".$current->MediumImage->URL."'></td>");
					print("<td><font size='-1'><b>".$current->ItemAttributes->Title."</b>");
					if(isset($current->ItemAttributes->Director)){
						print("<br>Director: ".$current->ItemAttributes->Director);
					} elseif(isset($current->ItemAttributes->Author)) {
						print("<br>Author: ".$current->ItemAttributes->Author);
					} elseif(isset($current->ItemAttributes->Artist)) {
						print("<br>Artist: ".$current->ItemAttributes->Artist);
					}
					print("<br>Price: ".$current->Offers->Offer->OfferListing->Price->FormattedPrice);
					$asin = $current->ASIN;
					$details = "SimpleStore.php?Action=SeeDetails&ASIN=$asin&SearchIndex=$SearchIndex&CartId=$CartId&HMAC=$HMAC";
					print("<br><a href=$details>See Details</a>");
					$offerListingId = urlencode($current->Offers->Offer->OfferListing->OfferListingId);
					$CartAdd = "SimpleStore.php?Action=CartAdd&OfferListingId=$offerListingId&CartId=$CartId&HMAC=$HMAC";
					print("&nbsp;&nbsp;&nbsp; <a href=$CartAdd>Add to Cart</a>");
					print("<tr><td colspan=2>&nbsp;</td> </tr> ");
				}
			}
		}else{
			print("<center>No matches found.</center>");
		}
		print("<tr><td align='left'>");
		//allow for paging through results
		if($_GET['ItemPage'] > 1 && $totalPages > 1){ //check to see if there are previous pages
			$Keywords = urlencode($_GET['Keywords']);
			$ItemPage = $_GET['ItemPage']-1;
			$prevPage = "SimpleStore.php?Action=Search&SearchIndex=$SearchIndex&Keywords=$Keywords&ItemPage=$ItemPage&CartId=$CartId&HMAC=$HMAC";
			print("<a href=$prevPage>Previous Page</a></td><td align='right'>");
		}
		if($_GET['ItemPage'] < $totalPages){ //check to see if there are more pages
			$Keywords = urlencode($_GET['Keywords']);
			$ItemPage = $_GET['ItemPage']+1;
			$nextPage = "SimpleStore.php?Action=Search&SearchIndex=$SearchIndex&Keywords=$Keywords&ItemPage=$ItemPage&CartId=$CartId&HMAC=$HMAC";
			print("<a href=$nextPage>Next Page</a></td></tr>");
		}
	}
	//-------------------------------------------------------------------------------------------------------
	function ItemLookup($asin, $SearchIndex){
		$request = "http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&AWSAccessKeyId=".KEYID."&AssociateTag=".AssocTag."&Version=2006-09-11&Operation=ItemLookup&ItemId=$asin&ResponseGroup=Medium,Offers";
		//The use of `file_get_contents` may not work on all servers because it relies on the ability to open remote URLs using the file manipulation functions. 
		//PHP gives you the ability to disable this functionality in your php.ini file and many administrators do so for security reasons.
		//If your administrator has not done so, you can comment out the following 5 lines of code and uncomment the 6th.  
		$session = curl_init($request);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session);
		//$response = file_get_contents($request);
		$parsed_xml = simplexml_load_string($response);
		printDetails($parsed_xml, $SearchIndex);
	}
	//-------------------------------------------------------------------------------------------------------
	function printDetails($parsed_xml, $SearchIndex){
		print("<table>");
			if($SearchIndex == "Books"){
				print("<tr><td><img src='".$parsed_xml->Items->Item->LargeImage->URL."'></td>");
				print("<td>".$parsed_xml->Items->Item->ItemAttributes->Title);
				print("<br>".$parsed_xml->Items->Item->ItemAttributes->Author);
				print("<br>".$parsed_xml->Items->Item->ItemAttributes->Binding);
				print("<br>".$parsed_xml->Items->Item->ItemAttributes->NumberOfPages." pages");
				print("<br><b>".$parsed_xml->Items->Item->Offers->Offer->OfferListing->Price->FormattedPrice."</b>");
			}
			if($SearchIndex == "Music"){
				print("<tr><td><img src='".$parsed_xml->Items->Item->LargeImage->URL."'></td>");
				print("<td>".$parsed_xml->Items->Item->ItemAttributes->Title);
				print("<br>".$parsed_xml->Items->Item->ItemAttributes->Artist);
				print("<br>Label: ".$parsed_xml->Items->Item->ItemAttributes->Label);
				print("<br>Release Date: ".$parsed_xml->Items->Item->ItemAttributes->ReleaseDate);
				print("<br><b>".$parsed_xml->Items->Item->Offers->Offer->OfferListing->Price->FormattedPrice."</b>");
			}
			if($SearchIndex == "DVD"){
				print("<tr><td><img src='".$parsed_xml->Items->Item->LargeImage->URL."'></td>");
				print("<td>".$parsed_xml->Items->Item->ItemAttributes->Title);
				print("<br>Director: ".$parsed_xml->Items->Item->ItemAttributes->Director);
				print("<br>Rated ".$parsed_xml->Items->Item->ItemAttributes->AudienceRating);
				print("<br>Label: ".$parsed_xml->Items->Item->ItemAttributes->Label);
				print("<br>Release Date: ".$parsed_xml->Items->Item->ItemAttributes->ReleaseDate);
				print("<br><b>".$parsed_xml->Items->Item->Offers->Offer->OfferListing->Price->FormattedPrice."</b>");
			}
			$offerListingId = urlencode($parsed_xml->Items->Item->Offers->Offer->OfferListing->OfferListingId);
			$CartId = $_GET['CartId'];
			$HMAC = urlencode($_GET['HMAC']);
			$CartAdd = "SimpleStore.php?Action=CartAdd&OfferListingId=$offerListingId&CartId=$CartId&HMAC=$HMAC";
			print("&nbsp;&nbsp;&nbsp; <a href=$CartAdd>Add to Cart</a>");
			$search = "SimpleStore.php?Action=Search&CartId=$CartId&HMAC=$HMAC";
			print("<br>&nbsp;&nbsp;&nbsp; <a href=$search>Continue Searching</a>");
			print("</table>");		
	}
	//-------------------------------------------------------------------------------------------------------
	function cartCreate($offerListingId){
		$request="http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&AWSAccessKeyId=".KEYID."&AssociateTag=".AssocTag."&Version=2006-09-11&Operation=CartCreate&Item.1.OfferListingId=$offerListingId&Item.1.Quantity=1";
		//The use of `file_get_contents` may not work on all servers because it relies on the ability to open remote URLs using the file manipulation functions. 
		//PHP gives you the ability to disable this functionality in your php.ini file and many administrators do so for security reasons.
		//If your administrator has not done so, you can comment out the following 5 lines of code and uncomment the 6th.  
		$session = curl_init($request);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session);
		//$response = file_get_contents($request);
		$parsed_xml = simplexml_load_string($response);
		showCartContents($parsed_xml);
	}
	//-------------------------------------------------------------------------------------------------------
	function cartAdd($offerListingId){
		$CartId = $_GET['CartId'];
		$HMAC = $_GET['HMAC'];
		$request="http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&AWSAccessKeyId=".KEYID."&AssociateTag=".AssocTag."&Version=2006-09-11&Operation=CartAdd&CartId=$CartId&HMAC=$HMAC&Item.1.OfferListingId=$offerListingId&Item.1.Quantity=1";
		//The use of `file_get_contents` may not work on all servers because it relies on the ability to open remote URLs using the file manipulation functions. 
		//PHP gives you the ability to disable this functionality in your php.ini file and many administrators do so for security reasons.
		//If your administrator has not done so, you can comment out the following 5 lines of code and uncomment the 6th.  
		$session = curl_init($request);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session);
		//$response = file_get_contents($request);
		$parsed_xml = simplexml_load_string($response);
		showCartContents($parsed_xml);	
	}
	//-------------------------------------------------------------------------------------------------------
	function showCartContents($parsed_xml){
	     print("<table>");
	     $CartId = $parsed_xml->Cart->CartId;
	     $HMAC = $parsed_xml->Cart->URLEncodedHMAC;
	     if (isset($parsed_xml->Cart->CartItems)){
	           foreach($parsed_xml->Cart->CartItems->CartItem as $current){
	                 $CartItemId = $current->CartItemId;
	                 $remove="SimpleStore.php?Action=Remove&CartId=$CartId&HMAC=$HMAC&CartItemId=$CartItemId";
	                 print("<tr><td>".$current->Title.": ".$current->Price->FormattedPrice."&nbsp;&nbsp;&nbsp;<a href=$remove>(Remove from Cart)</a></td></tr>");
	           }
	           print("<tr><td>Subtotal: ".$parsed_xml->Cart->CartItems->SubTotal->FormattedPrice."</td></tr>");
	     } else {
	   print("<tr><td>Your Cart is empty</td></tr>");
	     }
	     print("<tr><td>");
	     $continue = "SimpleStore.php?Action=Search&CartId=$CartId&HMAC=$HMAC";
	     print("<a href=$continue>Continue Shopping</a>");
	     $checkout=$parsed_xml->Cart->PurchaseURL;
	     print("&nbsp;&nbsp;&nbsp;<a href=$checkout>Checkout</a>");
	     print("</table>");
	}
	//------------------------------------------------------------------------------------------------------
	function removeFromCart($CartItemId){
		$CartId = $_GET['CartId'];
		$HMAC = urlencode($_GET['HMAC']);
		$CartItemId = $_GET['CartItemId'];
		$request = "http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&AWSAccessKeyId=".KEYID."&AssociateTag=".AssocTag."&Version=2006-09-11&Operation=CartModify&CartId=$CartId&HMAC=$HMAC&Item.1.CartItemId=$CartItemId&Item.1.Quantity=0";
		//The use of `file_get_contents` may not work on all servers because it relies on the ability to open remote URLs using the file manipulation functions. 
		//PHP gives you the ability to disable this functionality in your php.ini file and many administrators do so for security reasons.
		//If your administrator has not done so, you can comment out the following 5 lines of code and uncomment the 6th.  
		$session = curl_init($request);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session);
		//$response = file_get_contents($request);
		$parsed_xml = simplexml_load_string($response);
		showCartContents($parsed_xml);
	}
	//-----------------------------------------------------------------------------------------------------
	function getCartContents($CartId, $HMAC){
		$request = "http://ecs.amazonaws.com/onca/xml?Service=AWSECommerceService&AWSAccessKeyId=".KEYID."&AssociateTag=".AssocTag."&Version=2006-09-11&Operation=CartGet&CartId=$CartId&HMAC=$HMAC";
		//The use of `file_get_contents` may not work on all servers because it relies on the ability to open remote URLs using the file manipulation functions. 
		//PHP gives you the ability to disable this functionality in your php.ini file and many administrators do so for security reasons.
		//If your administrator has not done so, you can comment out the following 5 lines of code and uncomment the 6th.  
		$session = curl_init($request);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session); 
		//$response = file_get_contents($request);
		$parsed_xml = simplexml_load_string($response);
		showCartContents($parsed_xml);
	}
}

?>