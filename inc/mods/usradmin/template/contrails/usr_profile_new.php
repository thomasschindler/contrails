<?
	$OPC->lang_page_start('usradmin');

	$home = $OPC->lnk(array('mod' => 'usradmin', 'event' => 'usr_home'));
	$uid = $CLIENT->usr['id'];
	$usr_file_url  = $this->get_var('usradmin', 'usr_file_url').$uid.'/';
	$usr_file_path = $this->get_var('usradmin', 'usr_file_path').$uid.'/';
?>


		<div class="oos_mod_edit">
			<div class="oos_mod_edit_head">
				<span>
<?=e::o('t_pn_headline',array('%username%'=>htmlspecialchars($CLIENT->usr['usr'])))?>

				</span>
			</div>
			<div class="oos_mod_edit_body">
			
				<!--div class="oos_mod_edit_box">
					<div class="create_edit_button"><a href="<?=$home?>"><?=$OPC->show_icon('arrow_red')?><span>back</a></span></div>
				</div-->
				
				<div class="oos_mod_edit_box">
					<div class="create_edit_button"><span><?=$OPC->show_icon('info_red')?><span><?=e::o('t_pn_your_data')?></span></span></div>
				</div>


				<div class="oos_mod_edit_box">

<?php 
	$form = $OPC->get_var('usradmin', 'profile_form');

	$err = $OPC->get_var('usradmin', 'error');
	$err_msg = $OPC->get_var('usradmin', 'err_msg');
	if ($err) {
		echo '<div class="errbox">'.
			e::o('t_pn_err').
			$err_msg
			.'</div>';
	}
	else {
		$form->set_values($CLIENT->usr);
	}

	#$form->add_image("save_red",'event_profile_save', e::o('save'),'onClick="return confirm(\''.e::o('t_pn_save_confirm').'\');"');
	$form->add_image("save_red",'event_profile_save', e::o('save'));

	
	echo $form->start();
	echo $form->fields();
	echo $form->end();
	
	echo $OPC->var_get("content");
	
?>

				</div>
			</div>
		</div>

<?
	$OPC->lang_page_end();
?>
