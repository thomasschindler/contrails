<?
$conf = array
(
	'table' => array
	(
		'name' => 'mod_usradmin_usr',
	),
	'fields' => array
	(
		'usr' => array
		(
			'label' => e::o('username'),
			'cnf' => array
			(
				'type' => 'input',
				'empty' => 'false',
				'unique' => true,
			)
		),
		'email' => array
		(
			'label' => e::o('email'),
			'cnf' => array
			(
				'type' => 'input',
				'format' => 'email',
				'empty' => 'false',
				'unique' => true,
			)
		),
		'pwd' => array
		(
			'label' => e::o('password'),
			'cnf' => array
			(
				'type' => 'password',
				'empty' => 'false',
			)
		),
	)
);
?>