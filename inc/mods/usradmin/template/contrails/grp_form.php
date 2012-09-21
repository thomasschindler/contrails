<?
	$OPC->lang_page_start('usradmin');
?>

		<?=$OPC->get_var('article','button')?>
		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=$OPC->get_var('usradmin', 'headline'); ?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">

<?php
		$err = $OPC->get_var('usradmin', 'grp_error');
		if ($err) {
			echo '<div class="errbox">'.
				e::o('t_gf_create_err').
				'</div>';
		}
		
		$form = $OPC->get_var('usradmin', 'grp_form');
		$form->add_button('event_grp_save', e::o('save'));
		$form->add_button('event_cancel',  e::o('cancel'));
		
		echo $form->start();
		echo $form->fields();
		echo $form->end();
	?>
				</div>
			</div>
		</div>
<?
	$OPC->lang_page_end();
?>
