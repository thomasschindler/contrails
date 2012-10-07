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
		'modul_name' => array
		(
			'Field' => 'modul_name',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'label' => array
		(
			'Field' => 'label',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'sys_trashcan' => array
		(
			'Field' => 'sys_trashcan',
			'Type' => 'smallint(1) unsigned',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'virtual' => array
		(
			'Field' => 'virtual',
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
			'id' => '9',
			'modul_name' => 'page',
			'label' => 'PAGE',
			'sys_trashcan' => '0',
			'virtual' => '1',
		),
		'1' => array
		(
			'id' => '46',
			'modul_name' => 'acladmin',
			'label' => 'ACLADMIN',
			'sys_trashcan' => '0',
			'virtual' => '1',
		),
		'2' => array
		(
			'id' => '48',
			'modul_name' => 'objbrowser',
			'label' => 'OBJBROWSER',
			'sys_trashcan' => '0',
			'virtual' => '1',
		),
	),

);
?>