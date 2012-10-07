<?
$config = array
(	'fields' => array
	(
		'pid' => array
		(
			'Field' => 'pid',
			'Type' => 'int(10) unsigned',
			'Null' => 'NO',
			'Key' => 'MUL',
			'Default' => NULL,
			'Extra' => '',
		),
		'uid' => array
		(
			'Field' => 'uid',
			'Type' => 'int(10) unsigned',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'mid' => array
		(
			'Field' => 'mid',
			'Type' => 'int(10) unsigned',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'ar' => array
		(
			'Field' => 'ar',
			'Type' => 'int(32) unsigned',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'inherit_pid' => array
		(
			'Field' => 'inherit_pid',
			'Type' => 'int(10) unsigned',
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
			'type' => 'KEY',
			'fields' => array
			(
				'0' => 'pid',
				'1' => 'uid',
				'2' => 'mid',
			),
		),
	),

);
?>