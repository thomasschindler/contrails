<?
$config = array
(	'fields' => array
	(
		'vid' => array
		(
			'Field' => 'vid',
			'Type' => 'varchar(32)',
			'Null' => 'NO',
			'Key' => 'PRI',
			'Default' => NULL,
			'Extra' => '',
		),
		'mod_name' => array
		(
			'Field' => 'mod_name',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'pid' => array
		(
			'Field' => 'pid',
			'Type' => 'int(11)',
			'Null' => 'NO',
			'Key' => 'MUL',
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
				'0' => 'vid',
			),
		),
		'1' => array
		(
			'type' => 'KEY',
			'fields' => array
			(
				'0' => 'pid',
			),
		),
	),

);
?>