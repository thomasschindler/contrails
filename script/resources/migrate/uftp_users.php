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
		'firstname' => array
		(
			'Field' => 'firstname',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'lastname' => array
		(
			'Field' => 'lastname',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'gender' => array
		(
			'Field' => 'gender',
			'Type' => 'enum(\'M\',\'F\')',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'email' => array
		(
			'Field' => 'email',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'auth_provider' => array
		(
			'Field' => 'auth_provider',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'auth_token' => array
		(
			'Field' => 'auth_token',
			'Type' => 'varchar(255)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'auth_uid' => array
		(
			'Field' => 'auth_uid',
			'Type' => 'double',
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
	),

);
?>