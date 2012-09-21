<?
	$OPC->lang_page_start('usradmin');
	$usr_data = $OPC->get_var('usradmin', 'usr_data');	
?>







		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=e::o('t_uho_home_of',array('%username%'=>htmlspecialchars($usr_data['usr'])))?> -- <?=e::o('t_uho_send_me_msg')?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">

<?php
	// den empfaenger setzen wir in variable
	$OPC->set_var('messaging', 'reciever', $usr_data['usr']);
	
	// damit wir wieder auf dieser user-seite landen, haengen wir id mit dran
	$OPC->lnk_add('data[uid]', $usr_data['id']);
	
	$OPC->call_view('form', 'messaging', 'form_fixed', true);
	
	$OPC->lnk_remove('data[uid]');
?>
				</div>
			</div>
		</div>

<?
	$OPC->lang_page_end();
?>
