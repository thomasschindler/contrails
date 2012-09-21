<?
/**
*	CONFIGURATION-FILE
*	copy this file to your webroot and modify for your needs
*/
class HOST_CONF 
{   	
	/*country index*/
	function country_index()
	{
		return '01';
	}
	/* div things about the invoice */
	function invoice($k)
	{
		return false;
		switch($k)
		{
			case 'email': // send invoice by mail
				return true;
			break;
			default:
				return false;
		}
	}
	function usr($s)
	{
		switch($s)
		{
			case 'groups':
				return array(74);
			break;	
			case 'lang':
				return 71;
			break;
			case 'country':
				return 'BR';
			break;
		}
		return;
	}
	function command($w)
	{
		switch($w)
		{
			case 'pdftk':
				return '/usr/bin/pdftk';
			break;
			case 'convert':
				return '/usr/bin/convert';
			break;
		}
	}
	/* country code */
	function country()
	{
		return 'BR';
	}
	/*payment method used*/
	function payment_method()
	{
		return array
				(
					'ff_pagseguro' => 'pagseguro',
					//'ff_paypal' => 'paypal',
				);
	}
	function paypal($n = 'live')
	{
		/*
		switch($n)
		{
			case 'dev':
					$conf = array
					(
						'username' => 
'marlon_1330970398_biz_api1.fotofeliz.com.br',
						'password' => 
'1330970423',
						'signature' => 
'A6j3PI17kv-bn5O0TbdIOoF-kH0AA8y.I2r9P0nEWjQYA7N8B0PKvNeB',
						'type' => 'dev',
					); 
			break;
			case 'live':
			default:
					$conf = array
					(
						'username' => '',
						'password' => '',
						'signature' => '',
						'type' => '',
					);
			break;
		}
		*/
		return $conf;
	}
		
	function smtp($c=null,$n = 'amazon')
	{
		/*
		switch($n)
		{
			case 'taopix':
					$conf = array
					(
						'host' => 
'75.101.134.221',
						'user' => 'mailer',
						'pass' => '123test',
						'port' => 25,
					); 
			break;
			case 'amazon':
			default:
					$conf = array
					(
						'host' => 
'ssl://email-smtp.us-east-1.amazonaws.com',
						'user' => 
'AKIAJHAATEFCZBVKKCBA',
						'pass' => 
'Agbh227aTj2LQHAKdv+WzlVUJR2s5JvMok/8goODvbbT',
						'port' => 465,
					);
			break;
		}
		switch($c)
		{
			case 'host':
			case 'user':
			case 'pass':
			case 'port':
				return $conf[$c];
			break;
			default:
				return $conf;	
		}
		*/
	}
	/*currency used*/
	function currency()
	{
		return 'BRL';
	}
	/*currency symbol*/
	function currency_symbol()
	{
		return 'R$';
	}
	/*format currency*/
	function currency_format($i,$type)
	{
		switch($type)
		{
			case 'symbol':
				//	untestable, recusive class definition, strix 2012-05-11
				//return CONF::currency_symbol()." ".number_format($i,2,".","");
			break;
			default:
				return number_format($i,2,".","");
		}
	}
	/*currency symbol in front of price*/
	function currency_pre()
	{
		return true;
	}
	/*format sizes*/
	function size_format($s,$type=null)
	{
		return number_format($s,2,".","");
	}
	/*format date*/
	function date_format($i,$type=null)
	{
		switch($type)
		{
			default:
				return date("d.m.Y",$i);
		}
	}

	/* timezone to be used in the system */
	function timezone()
	{
		return 'Brazil/East';
	}	
	/* locale to be used in the system */
	function locale()
	{
		return 'pt_BR';
	}    
	function dir($k)
	{
		$dir = array
		(
			'session' => '/var/www/fotofeliz/tmp',
			'storage' => 
'/var/www/fotofeliz/development/assets',
			'svn' => '/var/www/fotofeliz',
			'deploy' => 
'/var/www/fotofeliz/deploy/assets/psp',
		);
		if($dir[$k])
		{
			return $dir[$k];
		}
	}
	function ftp_send()
	{
		return false;
	} 
	function trackingcode()
	{
		return false;
	}
	function cache()
	{
		return 0;
	}
	/**
	* add all pids that shoud explicitly be NOT cached
	*/
	function cache_check($p)
	{
		$pid = array
		(
			// creation pages
			418 => false,
			458 => false,
			551 => false,
			585 => false,
			612 => false,
			614 => false,
			630 => false,
			// checkout pages
			456 => false,
			457 => false,
			552 => false,
			562 => false,
			563 => false,
			564 => false,
			613 => false,
			628 => false,
			640 => false,
			// errors
			453 => false,
			468 => false,
			1 => false,
			// user images
			645 => false,
			// download email
			381 => false,
			// contact and similar
			399 => false,
			404 => false,
			490 => false,
		);
		return isset($pid[$p]) ? $pid[$p] : true;
	}
	function live()
	{
		return false;
	}
	function log()   
	{
		$d = "/var/www/fotofeliz/log";
		if(!is_dir($d))
		{
			mkdir($d);
		}
		return $d;
	}
   	function notification($t=null)
	{   
		return 'strix@ekhayaict.com';
	}
	function dropbox()
	{
		$d = "/var/www/fotofeliz/dropbox/";
		if(!is_dir($d))
		{
			mkdir($d);
		}
		return $d;
	}
	function project_name()
	{
		return 'fotofeliz';
	}
	function l10n()
	{
		return false;
	}
	function baseurl()
	{
		return 'http://fotofeliz.org.za';
	}
	function default_layout()
	{
		return 'ff_amazonia_com_br';
	}
	function default_pages($k=null)
	{    
		//	untestable, circular requirements, strix 2012-05-11
		//return FF_UTIL::default_links($k);
	}
	function default_lang()
	{
		return 'pt-br';
	}
	function lang()
	{
		return 'pt-br';
	}
	function uid()
	{
		return 200;
	}
	function pid($lang = null)
	{
		return 379;
	}
	function __root()
	{
		return 122;
	}
	function db_options() 
	{
		return array
		(
			'master' => array
			(
				'db_type' => 'mysql',
				'db_host' => 'localhost',
				'db_user' => 'strix',
				'db_pass' => '', 
				'db_name' => 
'fotofeliz',
			)
		);
	}
}

?>
