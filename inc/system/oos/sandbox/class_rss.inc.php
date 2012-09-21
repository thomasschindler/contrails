<?
/**
* class for rss handling
* example array:
*	$rssArray = array(
*		"channel" => array(
*			"RDF:ABOUT" => "�ber",
*			"DESCRIPTION" => "beschreibung",
*			"TITLE" => "titel",
*			"LINK" => "http://www.google.de"
*		),
*		"items" => array(
*			"0" => array(
*				"RDF:ABOUT" => "�ber null",
*				"DESCRIPTION" => "beschreibung",
*				"TITLE" => "titel",
*				"LINK" => "http://www.google.de",
*				"DC:DATE" => "heute"
*			),
*			"1" => array(
*				"RDF:ABOUT" => "�ber eins",
*				"DESCRIPTION" => "beschreibung",
*				"TITLE" => "titel",
*				"LINK" => "http://www.google.de",
*				"DC:DATE" => "heute"
*			)
*		)
*	);
*
*
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@access			public
*	@package		util
*/
class rss {
	/**
	*constructor
	*/
	function rss()
	{
		if(!is_dir(CONF::asset_dir()."/rss"))
		{
			mkdir(CONF::asset_dir()."/rss");
		}
		$this->rss_dir = CONF::asset_dir()."/rss";
	}
	
	/**
		create a link for a module and vid
	*/
	
	function lnk($mod=null,$vid=null,$style=null)
	{
		if(!$mod)
		{
			$mod = UTIL::get_post("mod");
		}
		if(!$vid)
		{
			$vid = UTIL::get_post("vid");
		}
		
		$sess = &SESS::singleton();
		$opc = &OPC::singleton();
		
		return '<a href="'.$opc->lnk(array(
			'event' => 'get_rss',
			'mod' => $mod,
			'vid' => $vid,
		),null,array($sess->name),false,"rss").'" target="_blank" '.$style.'><img src="/system/img/icons/feed-icon-16x16.jpg" border=0></a>';
	}
	
	/**
	* function for reading rss feeds
	* takes an url as single argument 
	* tries to parse the file and 
	* returns an array representing the file 
	* example: arrayName[ "channel"] [ "TAGNAME" ]
	* example: arrayName[ "ITEMS"] [ ID ][ "TAGNAME" ]
	*/
	function rssRead ($rssFeed)
	{
	
		$simple = implode('',file($rssFeed));

		$p = xml_parser_create();
		xml_parse_into_struct($p,$simple,$vals,$index);
		xml_parser_free($p);
		
		$channel = array();
		$item = array();
		$items = array();
		$type = 0;
		$id = 0;
		
		for($i=0;$i<count($vals);$i++) {
			
			if(($vals[$i]['tag']=="CHANNEL")&&($vals[$i]['type']=="open")){ 
				$id=$vals[$i]['level']+1;
			}
			
			if(($type==0)&&($id==$vals[$i]['level']))
			{
				if(!UTIL::is_utf8($vals[$i]['value']))
				{
					$vals[$i]['value'] = utf8_encode($vals[$i]['value']);
				}
				$channel[$vals[$i]['tag']] = $vals[$i]['value'];
			}
			
			else 
			{
				if(!UTIL::is_utf8($vals[$i]['value']))
				{
					$vals[$i]['value'] = utf8_encode($vals[$i]['value']);
				}
				$item[$vals[$i]['tag']]=$vals[$i]['value']; 
			}
			
			if($vals[$i]['tag']=="ITEM") {
				if(($vals[$i]['type']=="open")&&($type==0)) {
					$type=1;
				}
				
				if($vals[$i]['type']=="close") {
					$items[]=$item;
					$item = array();
				}
			}
		
		}
		
		return array(
			"channel" => $channel,
			"items" => $items
		);
	} 
	
	/**
	*method to write rss feeds.
	*receives an array of the form returned by rssRead
	*returns 
	*/
	function rss_write($r,$version="0.91")
	{
		switch($version)
		{
			case "1.0":
				return $this->rssWrite($r);
			break;
			default:
			case "0.91":
				return $this->rss_write_091($r);
			break;
		}
	}
	function rssWrite($rssArray)
	{
		$rssFeed = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
			<rdf:RDF xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:h=\"http://www.w3.org/1999/xhtml\" xmlns:hr=\"http://www.w3.org/2000/08/w3c-synd/#\" xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\">
			<channel rdf:about=\"".$rssArray["channel"]["RDF:ABOUT"]."\">
				<title>".$rssArray["channel"]["TITLE"]."</title>
				<description>".$rssArray["channel"]["DESCRIPTION"]."</description>
				<link>".$rssArray["channel"]["LINK"]."</link>
			</channel>";
		
		while (list($ID,$ITEM) = each($rssArray["items"])) 
		{
			if(!$ITEM["DC:DATE"])
			{
				$ITEM["DC:DATE"] = time();
			}
			$rssFeed .= "<item rdf:about=\"".$ITEM["RDF:ABOUT"]."\">
				<title>
					".$ITEM["TITLE"]."
				</title>
				<description>
					".$ITEM["DESCRIPTION"]."
				</description>
				<link>
					".$ITEM["LINK"]."
				</link>
				<dc:date>
					".$ITEM["DC:DATE"]."
				</dc:date>
			</item>\n\n";
		}
		$rssFeed .= "</rdf:RDF>";
		return trim($rssFeed);
	}
	
	
	function rss_write_091($r)
	{

$content = '<?xml version="1.0" encoding="UTF-8" ?>';

#$content .= '<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN" "http://my.netscape.com/publish/formats/rss-0.91.dtd">';

$content .='
<rss version="0.91">
	<channel>
		<title>'.$r['channel']['TITLE'].'</title>
		<link>'.$r['channel']['LINK'].'</link>
		<description>'.$r['channel']['DESCRIPTION'].'</description>
		<language>de-de</language>
';
			$p = &$r['items'];
			foreach($p as $idx => $val)
			{		
$content .= 
'		<item>
			<title>'.$val['TITLE'].'</title>
			<link>'.$val['LINK'].'</link>
			<description>'.$val['DESCRIPTION'].'</description>
		</item>
';
			}
$content .= '	</channel>';
$content .=  '
</rss>';
		
#		$content = implode("",explode("\n",$content));
		
		return trim($content);
		
	}
	
	/**
	*sends the rssFeed with the appropiate header 
	*returns nothing
	*/
	function rss_send($r)
	{
		return $this->rssSend($r);
	}
	function rssSend ($rssFeed)
	{
		ob_clean();
		ob_start();
		header ("Content-type: text/xml");
		echo $rssFeed;
		ob_flush();
		die;
	}
	
	function strip($s)
	{
#		return "ja";

		if(!UTIL::is_utf8($s))
		{
			$s = utf8_encode($s);
		}
		$s = preg_replace("/\n/","",$s);
		$s = preg_replace("/\r/","",$s);
		
		
		$s = htmlspecialchars(substr(html_entity_decode(strip_tags($s)),0,200));

		$s = preg_replace("/'/","",$s);
		$s = preg_replace('/""/','',$s);
		$s = preg_replace("/</","",$s);
		$s = preg_replace("/>/","",$s);



		return $s;

	}
}
?>
