<?
$config = array
(	'fields' => array
	(
		'id' => array
		(
			'Field' => 'id',
			'Type' => 'int(11) unsigned',
			'Null' => 'NO',
			'Key' => 'MUL',
			'Default' => NULL,
			'Extra' => '',
		),
		'mod_shout' => array
		(
			'Field' => 'mod_shout',
			'Type' => 'varchar(200)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'event_shout' => array
		(
			'Field' => 'event_shout',
			'Type' => 'varchar(200)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'mod_listen' => array
		(
			'Field' => 'mod_listen',
			'Type' => 'varchar(200)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'event_listen' => array
		(
			'Field' => 'event_listen',
			'Type' => 'varchar(200)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'att_listen' => array
		(
			'Field' => 'att_listen',
			'Type' => 'text',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'start' => array
		(
			'Field' => 'start',
			'Type' => 'int(11)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'stop' => array
		(
			'Field' => 'stop',
			'Type' => 'int(11)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => '2147483647',
			'Extra' => '',
		),
		'pre' => array
		(
			'Field' => 'pre',
			'Type' => 'tinyint(1) unsigned',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
	),
	'keys' => array
	(
		'0' => array
		(
			'type' => 'KEY',
			'fields' => array
			(
				'0' => 'id',
			),
		),
	),
	'data' => array
	(
		'0' => array
		(
			'id' => '1',
			'mod_shout' => 'page',
			'event_shout' => 'pa_save',
			'mod_listen' => 'navigation_1',
			'event_listen' => 'inherit',
			'att_listen' => 'a:1:{s:3:"vid";s:15:"main_navigation";}',
			'start' => '0',
			'stop' => '2147483647',
			'pre' => '1',
		),
	),

);
?>