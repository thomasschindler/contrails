<?php


/**
*	@package	formconf
* konfigurationsdatei fuer db-tabellen
*/

$conf = array(
	'table' => array(
		'name'  => 'mod_page_tpl',
		'label' => e::o('tbl_tpl_templates',null,null,'page'),
		'sys_fields' => array(
			'deleted' => 'sys_trashcan',
		),
		'title' => 'label',					// welches feld/felder fuer 'kurzansichten' verwenden
												// mehrere mit komma getrennt
//		'icon' => 'icon_mod_news.gif',
	),
	'fields' => array(
		// id und pid sind absolet/default
		
		'tpl_name' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => e::o('tbl_tpl_filename',null,null,'page'),	// beschriftung
			'cnf' => array(
				'type'   => 'input',
				'empty'  => false,
				'min'    => 1,
				'max'    => 255,
			),
		),
		'label' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => e::o('tbl_tpl_label',null,null,'page'),	// beschriftung
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
