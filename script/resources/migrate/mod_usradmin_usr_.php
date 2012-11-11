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
			'Extra' => 'auto_increment',
		),
		'show_id' => array
		(
			'Field' => 'show_id',
			'Type' => 'varchar(16)',
			'Null' => 'NO',
			'Key' => '',
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
		'usr' => array
		(
			'Field' => 'usr',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'pwd' => array
		(
			'Field' => 'pwd',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'name' => array
		(
			'Field' => 'name',
			'Type' => 'text',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'email' => array
		(
			'Field' => 'email',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'tel' => array
		(
			'Field' => 'tel',
			'Type' => 'varchar(40)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'fax' => array
		(
			'Field' => 'fax',
			'Type' => 'varchar(40)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'street' => array
		(
			'Field' => 'street',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'num' => array
		(
			'Field' => 'num',
			'Type' => 'varchar(10)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'zip' => array
		(
			'Field' => 'zip',
			'Type' => 'varchar(10)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'city' => array
		(
			'Field' => 'city',
			'Type' => 'varchar(255)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'country' => array
		(
			'Field' => 'country',
			'Type' => 'char(2)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => 'de',
			'Extra' => '',
		),
		'lang' => array
		(
			'Field' => 'lang',
			'Type' => 'tinyint(2)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'lang_default' => array
		(
			'Field' => 'lang_default',
			'Type' => 'tinyint(1)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'type' => array
		(
			'Field' => 'type',
			'Type' => 'tinyint(1)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'register_key' => array
		(
			'Field' => 'register_key',
			'Type' => 'varchar(64)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'accept' => array
		(
			'Field' => 'accept',
			'Type' => 'tinyint(1)',
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
		'sys_date_created' => array
		(
			'Field' => 'sys_date_created',
			'Type' => 'int(10) unsigned',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'sys_date_changed' => array
		(
			'Field' => 'sys_date_changed',
			'Type' => 'int(10) unsigned',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'sys_date_lastlogin' => array
		(
			'Field' => 'sys_date_lastlogin',
			'Type' => 'int(11)',
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
			'id' => '122',
			'show_id' => '122',
			'pid' => '348',
			'usr' => 'root',
			'pwd' => 'a029d0df84eb5549c641e04a9ef389e5',
			'name' => '',
			'email' => '',
			'tel' => '',
			'fax' => '',
			'street' => '',
			'num' => '',
			'zip' => '',
			'city' => '',
			'country' => 'AF',
			'lang' => '1',
			'lang_default' => '0',
			'type' => '0',
			'register_key' => '',
			'accept' => '0',
			'sys_trashcan' => '0',
			'sys_date_created' => '0',
			'sys_date_changed' => '1147172573',
			'sys_date_lastlogin' => '1265383859',
		),
		'1' => array
		(
			'id' => '200',
			'show_id' => '200',
			'pid' => '357',
			'usr' => 'guest',
			'pwd' => 'a6d414ac4f293187dd042025834925f7',
			'name' => '',
			'email' => '',
			'tel' => '',
			'fax' => '',
			'street' => '',
			'num' => '',
			'zip' => '',
			'city' => '',
			'country' => 'DE',
			'lang' => '1',
			'lang_default' => '1',
			'type' => '0',
			'register_key' => '',
			'accept' => '0',
			'sys_trashcan' => '0',
			'sys_date_created' => '1126541294',
			'sys_date_changed' => '1265383381',
			'sys_date_lastlogin' => '0',
		),
	),

);
?>