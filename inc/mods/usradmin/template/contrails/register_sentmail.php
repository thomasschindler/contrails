<?
	$OPC->lang_page_start('usradmin');

/**
* template zum senden der anmelde-mail
*/
	$usr = $OPC->get_var('usradmin', 'data');
	
				
	$pid = $OPC->get_var("usradmin","pid");
	
	$register_link = $OPC->lnk(array('pid'=>$pid,'mod' => 'usradmin', 'event' => 'register_ok', 'register_key' => $usr['register_key']),null,'strict',false);
	
	new oos_mail($usr['email'],null, e::o('t_rs_subject'), e::o('t_rs_body',array(
		'%first%' => $usr['usr'],
		'%link%' => CONF::baseurl().'/'.$register_link,
		'%username%' => $usr['usr'],
		'%password%' => $usr['pwd'],
	)));
	
	$OPC->lang_page_end();
?>
