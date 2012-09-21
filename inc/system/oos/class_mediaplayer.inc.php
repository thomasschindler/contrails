<?
/**
helper class for jeroen wijierings mediaplayer
*/
class MEDIAPLAYER
{
	function get
	(
		$file=null,
		$type="single",
		$width=300,
		$height=312,
		$display_height=200,
		$fullscreen=true,
		$image = '',
		$playerfile='/system/mediaplayer/mediaplayer.swf'
	)
	{
		if(!$file)
		{
			return;
		}
		if(substr($playerfile,0,4) != 'http')
		{
			$playerfile = CONF::baseurl().$playerfile;
		}
		static $count;
		$count++;
		
		foreach($this->var_add as $var => $val)
		{
			$var_add .= 's'.$count.'.addVariable("'.$var.'","'.$val.'");'."\n";
		}
		
		$p = '
			<p id="player'.$count.'"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</p>
			<script type="text/javascript">
				var s'.$count.' = new SWFObject("'.$playerfile.'","'.$type.'","'.$width.'","'.$height.'","7");
				s'.$count.'.addParam("allowfullscreen","'.$fullscreen.'");
				s'.$count.'.addVariable("file","'.$file.'");
				s'.$count.'.addVariable("displayheight","'.$display_height.'");
				'.$var_add.'
				s'.$count.'.addVariable("image","'.$image.'");
				s'.$count.'.write("player'.$count.'");
			</script>
		';
		return $p;
		
	}
	
	function var_add($var,$val)
	{
		$this->var_add[$var] = $val;
	}
	
	function dir2playlist($dir=null,$filter=array('mp3','flv','swf','jpg'),$title=null)
	{
		if(!$dir)
		{
			return;
		}
		@$d = dir($dir);
		if ($d) 
		{
			while($entry=$d->read()) 
			{
				foreach($filter as $f)
				{
					$ps = false;
					if($ps = strpos(strtolower($entry), $f))
					{
						break;
					}
				}
				if (!($ps === false)) 
				{
					$items[] = $entry; 
				} 
			}
			$d->close();
			sort($items);
		}
		
		$path = explode(CONF::web_dir(),$dir);
		$url = CONF::baseurl()."/".$path[1];
		#header("content-type:text/xml;charset=utf-8");
		$ret = "<playlist version='1' xmlns='http://xspf.org/ns/0/'><title>".(isset($title) ? $title : CONF::baseurl())."</title>	<info>".CONF::baseurl()."</info><trackList>";
		foreach($items as $file) 
		{
	
			$ret  .= "<track><title>".$file."</title><location>".$url.'/'.$file."</location></track>";
		}
		$ret .= "</trackList></playlist>";
		return $ret;
	}
	
	function db2playlist($db,$fields = array("title"=>"label","file"=>"file"),$url=null,$title=null)
	{
		if(!$db->f($fields['title']) OR !$db->f($fields['file']))
		{
			return;
		}
		$ret = "<playlist version='1' xmlns='http://xspf.org/ns/0/'><title>".(isset($title) ? $title : CONF::baseurl())."</title>	<info>".CONF::baseurl()."</info><trackList>";
		while($db->next())
		{
			$ret  .= "<track><title>".$db->f($fields['title'])."</title><location>".$url.'/'.$db->f($fields['file'])."</location></track>";
		}
		$ret .= "</trackList></playlist>";
		return $ret;
	}
	
	function send_playlist($playlist)
	{
		header("content-type:text/xml;charset=utf-8");		
		echo $playlist;
		die;
	}
	
}
?>
