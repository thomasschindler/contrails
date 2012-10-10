<?

$link = $OPC->var_get("link");
if(isset($link))
{
	echo '<a href="'.$link.'">'.e::o('link_uftp_fuck').'</a>';
}

echo $OPC->var_get("form")

?>