<?
$config = array
(	'fields' => array
	(
		'local_id' => array
		(
			'Field' => 'local_id',
			'Type' => 'int(10) unsigned',
			'Null' => 'NO',
			'Key' => 'MUL',
			'Default' => NULL,
			'Extra' => '',
		),
		'foreign_id' => array
		(
			'Field' => 'foreign_id',
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
				'0' => 'local_id',
				'1' => 'foreign_id',
			),
		),
	),
	'data' => array
	(
		'0' => array
		(
			'local_id' => '200',
			'foreign_id' => '68',
		),
	),

);
?>