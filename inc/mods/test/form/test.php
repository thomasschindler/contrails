<?
$conf = array
(
	'fields' => array
	(
		'test' => array
		(
			'label' => e::o('label_test'),
			'cnf' => array
			(
				'type' => 'input',
				'empty' => false,
				'err_empty' => e::o('err_empty_test')
			)
		)
	),
	'buttons' => array
	(
		'test' => e::o('button_test')
	)
)
?>