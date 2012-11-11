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
			'Default' => '',
			'Extra' => '',
		),
		'interest_rate' => array
		(
			'Field' => 'interest_rate',
			'Type' => 'int(3)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => '0',
			'Extra' => '',
		),
		'interest_term' => array
		(
			'Field' => 'interest_term',
			'Type' => 'enum(\'DAY\',\'WEEK\',\'MONTH\',\'YEAR\')',
			'Null' => 'NO',
			'Key' => '',
			'Default' => 'YEAR',
			'Extra' => '',
		),
		'source' => array
		(
			'Field' => 'source',
			'Type' => 'enum(\'COMMUNITY\',\'BANK\')',
			'Null' => 'NO',
			'Key' => '',
			'Default' => 'COMMUNITY',
			'Extra' => '',
		),
		'created' => array
		(
			'Field' => 'created',
			'Type' => 'int(11)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'template' => array
		(
			'Field' => 'template',
			'Type' => 'tinyint(1)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => '0',
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