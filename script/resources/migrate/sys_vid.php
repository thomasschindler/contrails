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
		'pid' => array
		(
			'Field' => 'pid',
			'Type' => 'int(11)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => '',
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
	),
	'relations' => array
	(
		'mod_page' => array
		(
			'field' => 'vid'
		),
		'mod_usradmin_usr' => array
		(
			'table' => 'mm_some_relational_table',
			'field' => 'm_vid'
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
				'1' => 'pid'
			),
		),
		'1' => array
		(
			'type' => 'KEY',
			'fields' => array
			(
				'0' => 'test',
			),
		),
	),

);
?>