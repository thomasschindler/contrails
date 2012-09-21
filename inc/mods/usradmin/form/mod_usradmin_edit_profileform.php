<?php
/**
*	@package	formconf
* diese config-datei wird verwendet fuer online-anmeldungen
*/

$conf = array(
	'mod' => 'usradmin',
	'table' => array(
		'name'  => 'mod_usradmin_usr',
		'label' => e::o('f_uo_label',null,null,'usradmin'),
		'sys_fields' => array(					// automatisch gefuellte felder
			'deleted' => 'sys_trashcan',		// geloescht (ts)
			'created' => 'sys_date_created',	// timestamp angelegt
			'changed' => 'sys_date_changed',	// timestamp zuletzt geaendert
		),
		'title' => 'usr,email',					// welches feld/felder fuer 'kurzansichten' verwenden
												// mehrere mit komma getrennt
		'icon' => 'icon_mod_usradmin_usr.gif',
		#'form_order' => 'usr,pwd,_sep,firstname,lastname,street,zip,city,email,_sep,sex,datebirth,research,accept',		// optionale reihenfolge fuer forms (mit seps)
	),
	'fields' => array
	(	
		'welcome_page' => array
		(
			'access' => true,
			'label' => "Welcome Page",
			'cnf' => array(
				'type'  => 'select',	
				'empty' => false,
				'size'  => '1',
				'multi' => false,
				'min'   => 1,
				'relation' => array(
					'table' => 'mod_page',
					'key'   => 'id',
					'value' => 'name',
					'order' => 'ORDER BY name ASC',
				),
				'err_empty' => e::o('f_uo_groups_err',null,null,'usradmin'),
			),
		),
		
	),
);
?>
