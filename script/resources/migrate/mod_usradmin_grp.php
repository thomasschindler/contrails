<?
$config = array
(	'fields' => array
	(
		'id' => array
		(
			'Field' => 'id',
			'Type' => 'int(10) unsigned',
			'Null' => 'NO',
			'Key' => 'PRI',
			'Default' => NULL,
			'Extra' => '',
		),
		'pid' => array
		(
			'Field' => 'pid',
			'Type' => 'int(10) unsigned',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'name' => array
		(
			'Field' => 'name',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'lang' => array
		(
			'Field' => 'lang',
			'Type' => 'int(2)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'sys_trashcan' => array
		(
			'Field' => 'sys_trashcan',
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
			'type' => 'PRIMARY KEY',
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
			'id' => '66',
			'pid' => '367',
			'name' => 'Registered',
			'lang' => '1',
			'sys_trashcan' => '0',
		),
		'1' => array
		(
			'id' => '68',
			'pid' => '367',
			'name' => 'Guest',
			'lang' => '1',
			'sys_trashcan' => '0',
		),
	),

);
?>