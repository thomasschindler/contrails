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
		'name' => array
		(
			'Field' => 'name',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'lat' => array
		(
			'Field' => 'lat',
			'Type' => 'float',
			'Null' => 'YES',
			'Key' => 'MUL',
			'Default' => NULL,
			'Extra' => '',
		),
		'lon' => array
		(
			'Field' => 'lon',
			'Type' => 'float',
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
				'0' => 'lat',
				'1' => 'lon',
			),
		),
	),

);
?>