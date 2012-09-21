<?php


/**
*	@package	formconf
* konfigurationsdatei fuer db-tabellen
*/

$conf = array(
	'table' => array(
		'name'  => 'sys_module',
		'label' => 'Module',
		'sys_fields' => array(
			'deleted' => 'sys_trashcan',
		),
		'title' => 'label',					// welches feld/felder fuer 'kurzansichten' verwenden
												// mehrere mit komma getrennt
//		'icon' => 'icon_mod_news.gif',
	),
	'fields' => array(
		// id und pid sind absolet/default
		
		'modul_name' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => 'interner Modulname',	// beschriftung
			'cnf' => array(
				'type'   => 'input',
				'empty'  => false,
				'min'    => 1,
				'max'    => 255,
			),
		),
		'label' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => 'Modulname',	// beschriftung
			'cnf' => array(
				'type'   => 'input',
				'empty'  => false,
				'min'    => 1,
				'max'    => 255,
			),
		),
		
	),
		
);


?>