<?$OPC->lang_page_start('page')?>
<h4><?=e::o('pa_common_headline')?></h4>

<?php
	$form = MC::create('form');
	$form->init('page');
	
	$form->add_button('event_pa_common_save', e::o('save'));
	$form->add_button('event_pa',  e::o('cancel'));

	$form->add_hidden('edit_pid', $OPC->get_var('page', 'edit_pid'));

	echo $form->start();
	echo $form->fields();
	echo $form->end();
?>
<?$OPC->lang_page_end()?>
