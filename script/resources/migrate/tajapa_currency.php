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
		'uid' => array
		(
			'Field' => 'uid',
			'Type' => 'int(11)',
			'Null' => 'YES',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'label' => array
		(
			'Field' => 'label',
			'Type' => 'varchar(255)',
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