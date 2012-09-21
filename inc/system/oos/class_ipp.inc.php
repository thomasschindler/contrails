<?

/*version 2.53*/

class ipp
{         
	var $items_idx = -1;
	var $items = array();
	var $header = array
	(
		'supplier' => 'boschdruck'
	);
	var $currency_codes = array
	(
	    'AFN' => 'Afghanistani Afghani (AFA)',
	    'ALL' => 'Albanian Lek (ALL)',
	    'DZD' => 'Algerian Dinar (DZD)',
	    'ARS' => 'Argentine Peso (ARS)',
	    'AWG' => 'Aruba Florin (AWG)',
	    'AUD' => 'Australian Dollar (AUD)',
	    'AZN' => 'Azerbaijan New Maneat (AZN)',
	    'BSD' => 'Bahamian Dollar (BSD)',
	    'BHD' => 'Bahraini Dinar (BHD)',
	    'BDT' => 'Bangladeshi Taka (BDT)',
	    'BBD' => 'Barbadian Dollar (BBD)',
	    'BYR' => 'Belarus Ruble (BYR)',
	    'BZD' => 'Belize Dollar (BZD)',
	    'BMD' => 'Bermuda Dollar (BMD)',
	    'BTN' => 'Bhutanese Ngultrum (BTN)',
	    'BOB' => 'Bolivian Boliviano (BOB)',
	    'BAM' => 'Bosnia and Herzegovina Convertible Marka (BAM)',
	    'BWP' => 'Botswana Pula (BWP)',
	    'BRL' => 'Brazilian Real (BRL)',
	    'GBP' => 'British Pound (GBP)',
	    'BND' => 'Brunei Dollar (BND)',
	    'BGN' => 'Bulgarian Lev (BGN)',
	    'BIF' => 'Burundi Franc (BIF)',
	    'KHR' => 'Cambodia Riel (KHR)',
	    'CAD' => 'Canadian Dollar (CAD)',
	    'CVE' => 'Cape Verdean Escudo (CVE)',
	    'KYD' => 'Cayman Islands Dollar (KYD)',
	    'XOF' => 'CFA Franc (BCEAO) (XOF)',
	    'XAF' => 'CFA Franc (BEAC) (XAF)',
	    'CLP' => 'Chilean Peso (CLP)',
	    'CNY' => 'Chinese Yuan (CNY)',
	    'COP' => 'Colombian Peso (COP)',
	    'KMF' => 'Comoros Franc (KMF)',
	    'CRC' => 'Costa Rica Colon (CRC)',
	    'HRK' => 'Croatian Kuna (HRK)',
	    'CUP' => 'Cuban Peso (CUP)',
	    'CYP' => 'Cyprus Pound (CYP)',
	    'CZK' => 'Czech Koruna (CZK)',
	    'DKK' => 'Danish Krone (DKK)',
	    'DJF' => 'Dijiboutian Franc (DJF)',
	    'DOP' => 'Dominican Peso (DOP)',
	    'XCD' => 'East Caribbean Dollar (XCD)',
	    'EGP' => 'Egyptian Pound (EGP)',
	    'SVC' => 'El Salvador Colon (SVC)',
	    'ERN' => 'Eritrean Nakfa (ERN)',
	    'EEK' => 'Estonian Kroon (EEK)',
	    'ETB' => 'Ethiopian Birr (ETB)',
	    'EUR' => 'Euro (EUR)',
	    'FKP' => 'Falkland Islands Pound (FKP)',
	    'FJD' => 'Fiji Dollar (FJD)',
	    'GMD' => 'Gambian Dalasi (GMD)',
	    'GHC' => 'Ghanian Cedi (GHC)',
	    'GIP' => 'Gibraltar Pound (GIP)',
	    'XAU' => 'Gold Ounces (XAU)',
	    'GTQ' => 'Guatemala Quetzal (GTQ)',
	    'GGP' => 'Guernsey Pound (GGP)',
	    'GNF' => 'Guinea Franc (GNF)',
	    'GYD' => 'Guyana Dollar (GYD)',
	    'HTG' => 'Haiti Gourde (HTG)',
	    'HNL' => 'Honduras Lempira (HNL)',
	    'HKD' => 'Hong Kong Dollar (HKD)',
	    'HUF' => 'Hungarian Forint (HUF)',
	    'ISK' => 'Iceland Krona (ISK)',
	    'INR' => 'Indian Rupee (INR)',
	    'IDR' => 'Indonesian Rupiah (IDR)',
	    'IRR' => 'Iran Rial (IRR)',
	    'IQD' => 'Iraqi Dinar (IQD)',
	    'ILS' => 'Israeli Shekel (ILS)',
	    'JMD' => 'Jamaican Dollar (JMD)',
	    'JPY' => 'Japanese Yen (JPY)',
	    'JOD' => 'Jordanian Dinar (JOD)',
	    'KZT' => 'Kazakhstan Tenge (KZT)',
	    'KES' => 'Kenyan Shilling (KES)',
	    'KRW' => 'Korean Won (KRW)',
	    'KWD' => 'Kuwaiti Dinar (KWD)',
	    'KGS' => 'Kyrgyzstan Som (KGS)',
	    'LAK' => 'Lao Kip (LAK)',
	    'LVL' => 'Latvian Lat (LVL)',
	    'LBP' => 'Lebanese Pound (LBP)',
	    'LSL' => 'Lesotho Loti (LSL)',
	    'LRD' => 'Liberian Dollar (LRD)',
	    'LYD' => 'Libyan Dinar (LYD)',
	    'LTL' => 'Lithuanian Lita (LTL)',
	    'MOP' => 'Macau Pataca (MOP)',
	    'MKD' => 'Macedonian Denar (MKD)',
	    'MGA' => 'iraimbilanja',
	    'MWK' => 'Malawian Kwacha (MWK)',
	    'MYR' => 'Malaysian Ringgit (MYR)',
	    'MVR' => 'Maldives Rufiyaa (MVR)',
	    'MTL' => 'Maltese Lira (MTL)',
	    'MRO' => 'Mauritania Ougulya (MRO)',
	    'MUR' => 'Mauritius Rupee (MUR)',
	    'MXN' => 'Mexican Peso (MXN)',
	    'MDL' => 'Moldovan Leu (MDL)',
	    'MNT' => 'Mongolian Tugrik (MNT)',
	    'MAD' => 'Moroccan Dirham (MAD)',
	    'MZM' => 'Mozambique Metical (MZM)',
	    'MMK' => 'Myanmar Kyat (MMK)',
	    'NAD' => 'Namibian Dollar (NAD)',
	    'NPR' => 'Nepalese Rupee (NPR)',
	    'ANG' => 'Neth Antilles Guilder (ANG)',
	    'NZD' => 'New Zealand Dollar (NZD)',
	    'NIO' => 'Nicaragua Cordoba (NIO)',
	    'NGN' => 'Nigerian Naira (NGN)',
	    'KPW' => 'North Korean Won (KPW)',
	    'NOK' => 'Norwegian Krone (NOK)',
	    'OMR' => 'Omani Rial (OMR)',
	    'XPF' => 'Pacific Franc (XPF)',
	    'PKR' => 'Pakistani Rupee (PKR)',
	    'XPD' => 'Palladium Ounces (XPD)',
	    'PAB' => 'Panama Balboa (PAB)',
	    'PGK' => 'Papua New Guinea Kina (PGK)',
	    'PYG' => 'Paraguayan Guarani (PYG)',
	    'PEN' => 'Peruvian Nuevo Sol (PEN)',
	    'PHP' => 'Philippine Peso (PHP)',
	    'XPT' => 'Platinum Ounces (XPT)',
	    'PLN' => 'Polish Zloty (PLN)',
	    'QAR' => 'Qatar Rial (QAR)',
	    'RON' => 'Romanian Leu (RON)',
	    'RUB' => 'Russian Rouble (RUB)',
	    'RWF' => 'Rwandese Franc (RWF)',
	    'WST' => 'Samoan Tala (WST)',
	    'STD' => 'Sao Tome Dobra (STD)',
	    'SAR' => 'Saudi Arabian Riyal (SAR)',
	    'SCR' => 'Seychelles Rupee (SCR)',
	    'RSD' => 'Serbian Dinar (RSD)',
	    'SLL' => 'Sierra Leone Leone (SLL)',
	    'XAG' => 'Silver Ounces (XAG)',
	    'SGD' => 'Singapore Dollar (SGD)',
	    'SKK' => 'Slovak Koruna (SKK)',
	    'SBD' => 'Solomon Islands Dollar (SBD)',
	    'SOS' => 'Somali Shilling (SOS)',
	    'ZAR' => 'South African Rand (ZAR)',
	    'LKR' => 'Sri Lanka Rupee (LKR)',
	    'SHP' => 'St Helena Pound (SHP)',
	    'SDD' => 'Sudanese Dinar (SDD)',
	    'SRD' => 'Surinam Dollar (SRD)',
	    'SZL' => 'Swaziland Lilageni (SZL)',
	    'SEK' => 'Swedish Krona (SEK)',
	    'CHF' => 'Swiss Franc (CHF)',
	    'SYP' => 'Syrian Pound (SYP)',
	    'TWD' => 'Taiwan Dollar (TWD)',
	    'TZS' => 'Tanzanian Shilling (TZS)',
	    'THB' => 'Thai Baht (THB)',
	    'TOP' => 'Tonga Pa\'anga (TOP)',
	    'TTD' => 'Trinidad & Tobago Dollar (TTD)',
	    'TND' => 'Tunisian Dinar (TND)',
	    'TRY' => 'New Turkish Lira (TRY)',
	    'USD' => 'U.S. Dollar (USD)',
	    'AED' => 'UAE Dirham (AED)',
	    'UGX' => 'Ugandan Shilling (UGX)',
	    'UAH' => 'Ukraine Hryvnia (UAH)',
	    'UYU' => 'Uruguayan New Peso (UYU)',
	    'UZS' => 'Uzbekistan Sum (UZS)',
	    'VUV' => 'Vanuatu Vatu (VUV)',
	    'VEB' => 'Venezuelan Bolivar (VEB)',
	    'VND' => 'Vietnam Dong (VND)',
	    'YER' => 'Yemen Riyal (YER)',
	    'YUM' => 'Yugoslav Dinar (YUM)',
	    'ZMK' => 'Zambian Kwacha (ZMK)',
	    'ZWD' => 'Zimbabwe Dollar (ZWD)',
	);    
	                     
	public function ipp($buyer_id=null,$buyer_name=null,$marketplace=null)
	{
		if(!$buyer_id || !$buyer_name)
		{
			throw new Exception('IPP INIT FAILED');	
		}
		$this->header['buyer_id'] = $buyer_id;
		$this->header['buyer_name'] = $buyer_name;		
		if($marketplace)
		{
			$this->header['marketplace'] = $marketplace;
		}
		return true;
	}
	
	/* public access methods*/
	public function output()
	{    
		return $this->order_open().$this->header().$this->items().$this->order_summary().$this->order_close();
	}
	/*setter functions*/
	/* private */	
	private function orderid_set($orderid=null)
	{
		if($orderid==null)
		{
			throw new Exception('No Order ID');
		}                                      
		$this->header['orderid'] = $orderid;
		return true;
	}               
	
	private function currency_set($currency=null)
	{
		if($currency==null)
		{
			throw new Exception('No Currency');
		} 
		if(!$this->currency_codes[strtoupper($currency)])                                     
		{
			throw new Exception('Invalid Currency');
		}
		$this->header['currency'] = strtoupper($currency);
		return true;
	}                                          
	
	private function orderdate_set($orderdate=null)
	{
		if($orderdate==null)
		{          
			$this->header['orderdate'] = $this->date_create(time());
			return true;
		}          
		if(!$this->date_valid($orderdate))
		{
			throw new Exception('Invalid Date');
		} 
		$this->header['orderdate'] = $orderdate;
		return true;
	}
	
	private function header_mime_set($type,$source,$description,$alt,$purpose)
	{
		
	}
			
	/* public */ 
	public function order_set($orderid=null,$currency=null,$orderdate=null)
	{   
		try
		{
			$this->orderid_set($orderid);
			$this->currency_set($currency);
			$this->orderdate_set($orderdate);
		}
		catch (Exception $e)
		{           
			throw new Exception('Could not create order. [Last error: '.$e.']');	
		}
		return true;
	}
	   	
	public function delivery_set($start=null,$end=null)
   	{
		if($start==null || $end == null)
		{
			throw new Exception('Please provide a start AND an end date');
		}
		if(!$this->date_valid($start)|| !$this->date_valid($end))
		{
			throw new Exception('Please provide valid dates');
		}
		$this->header['delivery_start_date'] = $start;
		$this->header['delivery_end_date'] = $end;		
		return true;
	}
	
	function delivery_get()
	{
		if($this->header['delivery_start_date'])
		{
			return '<DELIVERY_DATE><DELIVERY_START_DATE>'.$this->header['delivery_start_date'].'</DELIVERY_START_DATE><DELIVERY_END_DATE>'.$this->header['delivery_end_date'].'</DELIVERY_END_DATE></DELIVERY_DATE>';
		}
		return null;
	}
	
	public function customer_set($id=null,$first=null,$last=null,$title=null,$academic_title=null,$street=null,$zip=null,$city=null,$state=null,$country=null,$country_coded=null,$phone=null,$fax=null,$email=null,$url=null)
	{
		if(!$id)
		{
			throw new Exception('Please provide an id for the customer.');
		}
		$attributes = array('id','first','last','title','academic_title','street','zip','city','state','country','country_coded','phone','fax','email','url');
		foreach($attributes as $a)
		{
			if(${$a})
			{
				$this->header['customer'][$a] = ${$a};
			}
		}   
		// add name                          
		$attributes_name = array('title','academic_title','first','last');
		if($this->header['customer']['first'] || $this->header['customer']['last'] || $this->header['customer']['title'] || $this->header['customer']['academic_title'])
		{                                          
			foreach($attributes_name as $a)
			{
				if($this->header['customer'][$a])
				{
					$this->header['customer']['contact_details'][$a] = $this->header['customer'][$a];
					unset($this->header['customer'][$a]);
				}
				$this->header['customer']['name'] = trim(implode(" ",$this->header['customer']['contact_details']));
			}
		}
		// add title, academic_title, and two more name tags
		// 
		$this->header['customer']['name2'] = '';
		$this->header['customer']['name3'] = '';
		$this->header['customer']['title'] = '';
		$this->header['customer']['academic_title'] = '';
		return true;
	}
	
	public function shipping_set($name=null,$name2=null,$name3=null,$street=null,$zip=null,$city=null,$state=null,$country=null,$country_coded=null)
	{
		$attributes = array('name','name2','name3','street','zip','city','state','country','country_coded');
		foreach($attributes as $a)
		{
			switch($a)
			{
				case 'name':
					$this->header['shipping']['name'] = trim($name." ".$name2." ".$name3);
				break;
				default:
					if(${$a})
					{
						$this->header['shipping'][$a] = ${$a};
					}
			}
		}
		$this->header['shipping']['name2'] = '';
		$this->header['shipping']['name3'] = '';
	}
	  
	function invoice_set()
	{
		$this->header['mime']['invoice'] = true;
	}
	
	function shippingnote_set()
	{
		$this->header['mime']['shipping'] = true;
	}
	
	public function item($id=null)
	{                   
		if(!is_int($id))
		{
			$this->items_idx++;
			$id = $this->items_idx;
		}
		if(!$this->items[$id])
		{
			$this->items[$id] = new ipp_item($this->header['supplier']);
		}
		return $this->items[$id];
	}
	 
	public function party_set($p,$r)
	{
		$this->party_set[] = array('party'=>$p,'role'=>$r);
	}
	
	/* getter methods */
	private function control_info_get()
	{
		return '<CONTROL_INFO><GENERATOR_INFO>oos intoprint order generator</GENERATOR_INFO><GENERATION_DATE>'.$this->date_create().'</GENERATION_DATE></CONTROL_INFO>';
	}   
	private function order_info_get()
	{                                
		return '<ORDER_INFO><ORDER_ID>'.$this->header['orderid'].'</ORDER_ID><bmecat:CURRENCY>'.$this->header['currency'].'</bmecat:CURRENCY><ORDER_DATE>'.$this->header['orderdate'].'</ORDER_DATE>'.$this->delivery_get().$this->parties_get().$this->parties_reference_get().$this->header_mime_get().'</ORDER_INFO>';
	} 
	private function parties_get()
	{
		return '<PARTIES>'.$this->party_get().$this->party_marketplace_get().$this->party_buyer_get().$this->party_supplier_get().$this->customer_get().$this->shipping_get().'</PARTIES>';
	} 
	private function party_get()                                        
	{
		$ret = '';
		foreach($this->party_set as $party)
		{
			$ret .= '<PARTY>';
			foreach($party['party'] as $k => $v)
			{
				$ret .= '<bmecat:PARTY_ID type="'.$k.'">'.$v.'</bmecat:PARTY_ID>';
			}
			foreach($party['role'] as $k => $v)
			{
				$ret .= '<PARTY_ROLE>'.$v.'</PARTY_ROLE>';
			}
			$ret .= '</PARTY>';
		}
		return $ret;
	}
	private function party_marketplace_get()
	{
		if($this->header['marketplace'])
		{
			return '<PARTY><bmecat:PARTY_ID type="systemspecific">'.$this->header['marketplace'].'</bmecat:PARTY_ID><PARTY_ROLE>marketplace</PARTY_ROLE><PARTY_ROLE>document_creator</PARTY_ROLE></PARTY>';
		} 
		return null;
	}
	private function party_buyer_get()
	{
		return '<PARTY><bmecat:PARTY_ID type="supplier_specific">'.$this->header['buyer_id'].'</bmecat:PARTY_ID><bmecat:PARTY_ID type="systemspecific">'.$this->header['buyer_name'].'</bmecat:PARTY_ID><PARTY_ROLE>buyer</PARTY_ROLE></PARTY>';
	}   
	/* fixed to boschdruck for now */
	private function party_supplier_get()
	{
		return '<PARTY><bmecat:PARTY_ID type="systemspecific">'.$this->header['supplier'].'</bmecat:PARTY_ID><PARTY_ROLE>supplier</PARTY_ROLE></PARTY>';
	}
     
	private function parties_reference_get()
	{
		if(!$this->header['customer'])
		{
			throw new Exception('Customer missing');
		}
		return '<ORDER_PARTIES_REFERENCE><bmecat:BUYER_IDREF type="supplier_specific">'.$this->header['buyer_id'].'</bmecat:BUYER_IDREF><bmecat:SUPPLIER_IDREF type="systemspecific">'.$this->header['supplier'].'</bmecat:SUPPLIER_IDREF><INVOICE_RECIPIENT_IDREF type="buyer_specific">'.$this->header['customer']['id'].'</INVOICE_RECIPIENT_IDREF></ORDER_PARTIES_REFERENCE>';
	}
	
	private function customer_get()
	{     
        $id = $this->header['customer']['id'];
		if(!$this->header['customer']['id'])
		{
			throw new Exception('No customer');
		}
		unset($this->header['customer']['id']); 
		$x = '<PARTY><bmecat:PARTY_ID type="buyer_specific">'.$id.'</bmecat:PARTY_ID><PARTY_ROLE>customer</PARTY_ROLE><ADDRESS>'.$this->array_to_xml($this->header['customer']).'</ADDRESS></PARTY>';
		$this->header['customer']['id'] = $id;
		return $x;
	}   
	
	private function shipping_get()
	{             
		if($this->header['shipping'])
		{
			return '<PARTY><bmecat:PARTY_ID type="systemspecific">delivery</bmecat:PARTY_ID><PARTY_ROLE>delivery</PARTY_ROLE><ADDRESS>'.$this->array_to_xml($this->header['shipping']).'</ADDRESS></PARTY>';
		}         
		return null;
	}
	
	function header_mime_get()
	{            
		if($this->header['mime'])
		{
		   	$x = '<MIME_INFO>';
			if($this->header['mime']['invoice'])
			{
				$x .= '<MIME><bmecat:MIME_TYPE>application/pdf</bmecat:MIME_TYPE><bmecat:MIME_SOURCE>input_invoice/'.$this->header['orderid'].'_RE.pdf</bmecat:MIME_SOURCE><bmecat:MIME_DESCR>Rechnung</bmecat:MIME_DESCR><bmecat:MIME_ALT>Rechnung '.$this->header['orderid'].'</bmecat:MIME_ALT><MIME_PURPOSE>others</MIME_PURPOSE></MIME>';
			}
			if($this->header['mime']['shipping'])
			{
				$x .= '<MIME><bmecat:MIME_TYPE>application/pdf</bmecat:MIME_TYPE><bmecat:MIME_SOURCE>input_invoice/'.$this->header['orderid'].'_LS.pdf</bmecat:MIME_SOURCE><bmecat:MIME_DESCR>Lieferschein</bmecat:MIME_DESCR><bmecat:MIME_ALT>Lieferschein '.$this->header['orderid'].'</bmecat:MIME_ALT><MIME_PURPOSE>others</MIME_PURPOSE></MIME>';
			}
			$x .= '</MIME_INFO>';
			return $x;
		}             
		return null;
	}
	
	/* helper methods */
	public function date_create($t=null)
	{                           
		return date(DATE_ATOM,($t==null?time():$t));
	}
  
	private function date_valid($date)
	{
		if(date(DATE_ATOM,strtotime($date)) != $date)
		{
			return false;
		}                
		return true;
	}
	
	private function array_to_xml($a)
	{
		foreach($a as $k => $v)
		{                                                           
			if(is_array($v))
			{               
				$x .= '<'.strtoupper($k).'>'.$this->array_to_xml($v).'</'.strtoupper($k).'>';
			}                                                                                
			else
			{
				$x .= '<bmecat:'.strtoupper($k).'>'.$v.'</bmecat:'.strtoupper($k).'>';
			}
		} 
		return $x;
	}
	
  	/* output methods */
	private function order_open()
	{
		return '<?xml version="1.0" encoding="UTF-8"?><ORDER version="2.1" type="standard" xmlns="http://www.opentrans.org/XMLSchema/2.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentrans.org/XMLSchema/2.1 opentrans_2_1.xsd" xmlns:bmecat="http://www.bmecat.org/bmecat/2005" xmlns:xmime="http://www.w3.org/2005/05/xmlmime">';
	}
	private function order_close()
	{  
		return '</ORDER>';
	}
	private function order_summary()
	{                   
		if(count($this->items)>0)
		{
			foreach($this->items as $item)
			{
				$total_item_num += $item->quantity;
				$total_amount += $item->quantity*$item->price;
			}
			return '<ORDER_SUMMARY><TOTAL_ITEM_NUM>'.$total_item_num.'</TOTAL_ITEM_NUM><TOTAL_AMOUNT>'.$total_amount.'</TOTAL_AMOUNT></ORDER_SUMMARY>';
		}
		return null;
	}
	private function header()
	{   
		return '<ORDER_HEADER>'.$this->control_info_get().$this->order_info_get().'</ORDER_HEADER>';
	}   
	private function items()
	{   
		foreach($this->items as $k => $item)
		{
			$x .= $item->output($k,$this->header['orderid']);
		}
		if($x)
		{
			return '<ORDER_ITEM_LIST>'.$x.'</ORDER_ITEM_LIST>';
		}
	}
}
  
class ipp_item
{                                                                  
	var $item = array();
	var $feature = array();    
	var $mime = array();
	var $quantity = 1;     
	var $price = 0;
	var $vat = 0;     
	var $line = null;
	var $ordernumber = null;
	    
	public function ipp_item($supplier_idref='boschdruck')
	{
		$this->item['supplier_id'] - $supplier_idref;
	}
	
	public function set($supplier_pid=null,$buyer_pid=null,$config=null,$descr_short=null,$descr_long=null,$manufacturer_info=null)
	{                                   
		if(!$supplier_pid)
		{
			throw new Exception('Supplier PID missing');
		}
		$a = array('supplier_pid','buyer_pid','config','descr_short','descr_long','manufacturer_info');
		foreach($a as $n)
		{
			if(${$n})
			{
				$this->item[$n] = ${$n};
			}
		}
		return true;
	}
	
	public function quantity_set($q)
	{
		$this->quantity = (int)$q;
		return true;
	}   
	 
	public function feature_set($name,$value)
	{
		$this->feature[$name] = $value;
		return true;
	}
	
	public function price_set($price=null,$vat=null)
	{                                           
		if(!$price)
		{
			throw new Exception('Price missing');
		}
		if($vat === null)
		{
			throw new Exception('Vat missing');
		}
		$this->price = $price;
		$this->vat = $vat/100;                                  
		return;
	}
	
	public function envelope_set($file=null,$mime='application/pdf')
	{                                                              
		if($file == null)
		{
			throw new Exception("Please provide a filename");
		}
		$this->mime['envelope']['mime'] = $mime;
		$this->mime['envelope']['file'] = $file;		
	}
	
	public function content_set($file=null,$mime='application/pdf')
	{    
		if($file == null)
		{
			throw new Exception("Please provide a filename");
		}
		$this->mime['content']['mime'] = $mime;
		$this->mime['content']['file'] = $file;  
	}                       
	public function thumb_set($file=null,$mime='application/jpg')
	{    
		if($file == null)
		{
			throw new Exception("Please provide a filename");
		}
		$this->mime['thumb']['mime'] = $mime;
		$this->mime['thumb']['file'] = $file;		
	}

	
	private function item_get()
	{
		$a = array('supplier_pid'=>'bmecat:SUPPLIER_PID','buyer_pid'=>'bmecat:BUYER_PID','supplier_id'=>'bmecat:SUPPLIER_IDREF','config'=>'CONFIG_CODE_FIX','descr_short'=>'bmecat:DESCRIPTION_SHORT','descr_long'=>'bmecat:DESCRIPTION_LONG','manufacturer_info'=>'bmecat:MANUFACTURER_INFO');
		foreach($a as $k => $v)
		{
			if($this->item[$k])
			{
				$x .= '<'.$v.'>'.$this->item[$k].'</'.$v.'>';
			}
		}    
		return '<PRODUCT_ID>'.$x.'</PRODUCT_ID>';
	}   
	
	private function features_get()
	{
		if(count($this->feature)==0)
		{
			return null;
		}               
		foreach($this->feature as $name => $value)
		{
			$x .= '<FEATURE><bmecat:FNAME>'.$name.'</bmecat:FNAME><bmecat:FVALUE>'.$value.'</bmecat:FVALUE></FEATURE>';
		}
		return '<PRODUCT_FEATURES>'.$x.'</PRODUCT_FEATURES>';
	}
	                         
	private function price_get()
	{                       
		return '<PRODUCT_PRICE_FIX><bmecat:PRICE_AMOUNT>'.$this->price.'</bmecat:PRICE_AMOUNT><TAX_DETAILS_FIX><TAX>'.$this->vat.'</TAX></TAX_DETAILS_FIX></PRODUCT_PRICE_FIX>';
		
	}
	                      
	private function mime_get()
	{             
		return '<MIME_INFO>'.$this->envelope_get().$this->content_get().$this->thumb_get().'</MIME_INFO>';
	}
	
	function envelope_get()
	{
		if($this->mime['envelope'])
		{
	 		return '<MIME><bmecat:MIME_TYPE>'.$this->mime['envelope']['mime'].'</bmecat:MIME_TYPE><bmecat:MIME_SOURCE>input_printfile/'.$this->mime['envelope']['file'].'</bmecat:MIME_SOURCE><bmecat:MIME_DESCR>Umschlag</bmecat:MIME_DESCR><bmecat:MIME_ALT>Druckdatei '.$this->mime['envelope']['file'].'_01_US.pdf</bmecat:MIME_ALT><MIME_PURPOSE>original_document</MIME_PURPOSE></MIME>';
		}
		return null;
	}
	
	function content_get()
	{
		if($this->mime['content'])
		{
	 		return '<MIME><bmecat:MIME_TYPE>'.$this->mime['content']['mime'].'</bmecat:MIME_TYPE><bmecat:MIME_SOURCE>input_printfile/'.$this->mime['content']['file'].'</bmecat:MIME_SOURCE><bmecat:MIME_DESCR>Inhalt</bmecat:MIME_DESCR><bmecat:MIME_ALT>Druckdatei '.$this->mime['content']['file'].'</bmecat:MIME_ALT><MIME_PURPOSE>original_document</MIME_PURPOSE></MIME>';
		}
//		throw new Exception('Please provide content to print');
	}
	
	function thumb_get()
	{
		if($this->mime['thumb'])
		{
	 		return '<MIME><bmecat:MIME_TYPE>'.$this->mime['thumb']['mime'].'</bmecat:MIME_TYPE><bmecat:MIME_SOURCE>input_thumbnail/'.$this->mime['thumb']['file'].'</bmecat:MIME_SOURCE><bmecat:MIME_DESCR>thumbnail</bmecat:MIME_DESCR><bmecat:MIME_ALT>thumbnail '.$this->mime['thumb']['file'].'</bmecat:MIME_ALT><MIME_PURPOSE>thumbnail</MIME_PURPOSE></MIME>';
		}
		return null;
	} 
	
	public function output($line=null,$ordernumber=null)
	{                                                  
		if(!$ordernumber)
		{
			throw new Exception('ordernumber missing');
		} 
		if($line === null)
		{
			throw new Exception('line missing');
		}
		$this->line = $line;
		$this->ordernumber = $ordernumber;
		return '<ORDER_ITEM><LINE_ITEM_ID>'.($line+1).'</LINE_ITEM_ID>'.$this->item_get().$this->features_get().'<QUANTITY>'.$this->quantity.'</QUANTITY><bmecat:ORDER_UNIT>C62</bmecat:ORDER_UNIT>'.$this->price_get().$this->mime_get().'</ORDER_ITEM>';	
	}
}

?>