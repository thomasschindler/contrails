<?php
/**
*	@package	eventconf
* konfigurationsdatei fr system events
		//'entry_create_sent'   => e::o('test',null,null,'weblog'),
		//'entry_create_sent'   => 'Neuer Eintrag',
*/
$opc = &OPC::singleton();
$opc->lang_page_start('sys_events');
$conf = array(
	'weblog' => array(
		//'entry_create_sent'   => e::o('test',null,null,'weblog'),
		'entry_create_sent'   => e::o('weblog_entry_create_sent'),
	),
	'fbb' => array(
		'save'   => e::o('fbb_save'),
	),
);
$opc->lang_page_end();
?>
