<?
	$OPC->lang_page_start('usradmin');
?>

		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=e::o('t_rf_headline')?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">
<?php 

	if (!$CLIENT->usr['is_default']) { 
		echo e::o('t_rf_logged_already');
	}
	else {
		$err = $OPC->get_var('usradmin', 'error');
		if ($err) {
			echo '<div class="errbox">'.
				e::o('t_rf_err').
				'</div>';
		}
	
		$form = $OPC->get_var('usradmin', 'register_form');
		$form->use_layout('blue');
		$form->add_button('event_register_sent', e::o('register'));
		
		echo $form->start();
		echo $form->fields();
		echo $form->end();
	}

?>

				</div>
			</div>
		</div>

<?
	$OPC->lang_page_end();
?>
