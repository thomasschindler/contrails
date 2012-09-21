<?

$languages = e::lang_name();
$languages['es'] = 'Spanish';

$conf = array
(
	'fields' => array
	(
		'language' => array
		(
			'label' => 'Language',
			'cnf' => array
			(
				'type' => 'select',
				'items' => $languages
			)
		),
		'search' => array
		(
			'label' => 'Needle',
			'cnf' => array
			(
				'type' => 'input'
			)
		)
	)
);

?>