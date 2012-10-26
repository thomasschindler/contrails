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
		'title' => array
		(
			'Field' => 'title',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
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
		'image' => array
		(
			'Field' => 'image',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'user_id' => array
		(
			'Field' => 'user_id',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => 'MUL',
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
				'0' => 'user_id',
				'1' => 'location_id',
			),
		),
	),

);
?>