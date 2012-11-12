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
		'parent' => array
		(
			'Field' => 'parent',
			'Type' => 'tinyint(1)',
			'Null' => 'YES',
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
	'data' => array
	(
		'0' => array
		(
			'id' => '1',
			'uid' => '1',
			'label' => 'community ',
			'interest_rate' => '0',
			'interest_term' => 'YEAR',
			'source' => 'COMMUNITY',
			'created' => '0',
			'template' => '1',
			'parent' => NULL,
		),
		'1' => array
		(
			'id' => '2',
			'uid' => '1',
			'label' => 'local',
			'interest_rate' => '-2',
			'interest_term' => 'MONTH',
			'source' => 'BANK',
			'created' => '0',
			'template' => '1',
			'parent' => NULL,
		),
		'2' => array
		(
			'id' => '19',
			'uid' => '1',
			'label' => 'community ',
			'interest_rate' => '0',
			'interest_term' => 'YEAR',
			'source' => 'COMMUNITY',
			'created' => '0',
			'template' => '0',
			'parent' => NULL,
		),
		'3' => array
		(
			'id' => '20',
			'uid' => '1',
			'label' => 'community ',
			'interest_rate' => '0',
			'interest_term' => 'YEAR',
			'source' => 'COMMUNITY',
			'created' => '0',
			'template' => '0',
			'parent' => NULL,
		),
		'4' => array
		(
			'id' => '21',
			'uid' => '1',
			'label' => 'community ',
			'interest_rate' => '0',
			'interest_term' => 'YEAR',
			'source' => 'COMMUNITY',
			'created' => '0',
			'template' => '0',
			'parent' => NULL,
		),
		'5' => array
		(
			'id' => '22',
			'uid' => '1',
			'label' => 'community ',
			'interest_rate' => '0',
			'interest_term' => 'YEAR',
			'source' => 'COMMUNITY',
			'created' => '0',
			'template' => '0',
			'parent' => NULL,
		),
		'6' => array
		(
			'id' => '23',
			'uid' => '1',
			'label' => 'community ',
			'interest_rate' => '0',
			'interest_term' => 'YEAR',
			'source' => 'COMMUNITY',
			'created' => '0',
			'template' => '0',
			'parent' => NULL,
		),
		'7' => array
		(
			'id' => '24',
			'uid' => '1',
			'label' => 'community ',
			'interest_rate' => '0',
			'interest_term' => 'YEAR',
			'source' => 'COMMUNITY',
			'created' => '0',
			'template' => '0',
			'parent' => NULL,
		),
		'8' => array
		(
			'id' => '25',
			'uid' => '1',
			'label' => 'community ',
			'interest_rate' => '0',
			'interest_term' => 'YEAR',
			'source' => 'COMMUNITY',
			'created' => '0',
			'template' => '0',
			'parent' => NULL,
		),
	),

);
?>