<?
$config = array
(	'fields' => array
	(
		'id' => array
		(
			'Field' => 'id',
			'Type' => 'int(11) unsigned',
			'Null' => 'NO',
			'Key' => 'PRI',
			'Default' => NULL,
			'Extra' => 'auto_increment',
		),
		'description' => array
		(
			'Field' => 'description',
			'Type' => 'text',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'fuck_id' => array
		(
			'Field' => 'fuck_id',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => 'MUL',
			'Default' => NULL,
			'Extra' => '',
		),
		'user_id' => array
		(
			'Field' => 'user_id',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'location_id' => array
		(
			'Field' => 'location_id',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'executed' => array
		(
			'Field' => 'executed',
			'Type' => 'int(1)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'created_at' => array
		(
			'Field' => 'created_at',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'updated_at' => array
		(
			'Field' => 'updated_at',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
	),
	'keys' => array
	(
		'0' => array
		(
			'type' => 'PRIMARY KEY',
			'fields' => array
			(
				'0' => 'id',
			),
		),
		'1' => array
		(
			'type' => 'KEY',
			'fields' => array
			(
				'0' => 'fuck_id',
				'1' => 'location_id',
				'2' => 'user_id',
			),
		),
	),

);
?>