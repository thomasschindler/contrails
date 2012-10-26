<?
$config = array
(	'fields' => array
	(
		'burc' => array
		(
			'Field' => 'burc',
			'Type' => 'varchar(20)',
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
			'Default' => NULL,
			'Extra' => '',
		),
		'data' => array
		(
			'Field' => 'data',
			'Type' => 'text',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'sys_date_created' => array
		(
			'Field' => 'sys_date_created',
			'Type' => 'int(11)',
			'Null' => 'NO',
			'Key' => '',
			'Default' => NULL,
			'Extra' => '',
		),
		'permanent' => array
		(
			'Field' => 'permanent',
			'Type' => 'tinyint(1)',
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
				'0' => 'burc',
			),
		),
	),
	'data' => array
	(
		'0' => array
		(
			'burc' => 'p108071938_348',
			'pid' => '348',
			'data' => 'a:3:{s:3:"pid";i:348;s:5:"event";s:6:"logout";s:3:"mod";s:8:"usradmin";}',
			'sys_date_created' => '1348350503',
			'permanent' => '0',
		),
		'1' => array
		(
			'burc' => 'p1409228737_348',
			'pid' => '348',
			'data' => 'a:4:{s:3:"pid";i:348;s:3:"mod";s:4:"page";s:5:"event";s:5:"pa_ar";s:4:"data";a:1:{s:3:"pid";i:348;}}',
			'sys_date_created' => '1348350503',
			'permanent' => '0',
		),
		'2' => array
		(
			'burc' => 'p1603634944_348',
			'pid' => '348',
			'data' => 'a:3:{s:3:"pid";i:348;s:5:"event";s:4:"test";s:3:"mod";s:4:"test";}',
			'sys_date_created' => '1348350503',
			'permanent' => '0',
		),
		'3' => array
		(
			'burc' => 'p2314348126_348',
			'pid' => '348',
			'data' => 'a:5:{s:3:"pid";i:348;s:3:"mod";s:4:"page";s:5:"event";s:8:"pa_enter";s:8:"edit_pid";i:348;s:4:"data";a:1:{s:10:"parent_pid";i:348;}}',
			'sys_date_created' => '1348350503',
			'permanent' => '0',
		),
		'4' => array
		(
			'burc' => 'p2460342256_348',
			'pid' => '348',
			'data' => 'a:4:{s:3:"pid";i:348;s:3:"mod";s:4:"page";s:5:"event";s:7:"pa_type";s:8:"edit_pid";i:348;}',
			'sys_date_created' => '1348350503',
			'permanent' => '0',
		),
		'5' => array
		(
			'burc' => 'p4269505774_348',
			'pid' => '348',
			'data' => 'a:5:{s:3:"pid";i:348;s:3:"mod";s:8:"acladmin";s:5:"event";s:8:"acl_list";s:7:"edit_id";i:348;s:4:"data";a:1:{s:3:"tbl";s:8:"mod_page";}}',
			'sys_date_created' => '1348350503',
			'permanent' => '0',
		),
	),

);
?>