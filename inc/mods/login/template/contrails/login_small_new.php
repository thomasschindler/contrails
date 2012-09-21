<?

	echo '<div style="bottom:4px;">'.e::o('t_sn_logged',array('%username%'=>$CLIENT->usr['usr']),null,'login')." ".'<a class="linkbutton" href="'.$OPC->lnk(array('mod' => 'login',  'event' => 'logout')).'">'.e::o('Logout').'</a></div>'.$OPC->var_get("config");

?>
