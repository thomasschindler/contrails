<?
	$OPC->lang_page_start('login');
	echo e::o('t_c_show',array(
		'%num_online%' => $OPC->get_var('login','num_online'),
		'%num_logged%' => $OPC->get_var('login','num_logged'),
		'%num_registered%' => $OPC->get_var('login','num_registered'),
	));
	$OPC->lang_page_end();
?>
