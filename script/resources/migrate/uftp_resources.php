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
		'unfuck_id' => array
		(
			'Field' => 'unfuck_id',
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
		'description' => array
		(
			'Field' => 'description',
			'Type' => 'text',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'unit' => array
		(
			'Field' => 'unit',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'quantity' => array
		(
			'Field' => 'quantity',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'avail_from' => array
		(
			'Field' => 'avail_from',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'avail_to' => array
		(
			'Field' => 'avail_to',
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
				'0' => 'unfuck_id',
				'1' => 'location_id',
				'2' => 'avail_from',
				'3' => 'avail_to',
			),
		),
	),

);
?>