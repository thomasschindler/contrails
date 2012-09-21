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
	'fields' => array(
		// id und pid sind absolet/default
		
		
		'pwd' => array(
			'access' => true,
			'label'  => e::o('f_uo_pwd',null,null,'usradmin'),
			'cnf' => array(
				'type'   => 'password',
				'empty'  => false,
				'min'    => 4,
				'regex'  => '/^[a-z0-9]+$/i',
				'eval'   => 'trim',
			),
		),
		'email' => array(
			'access' => true,
			'label'  => e::o('f_uo_email',null,null,'usradmin'),
			'cnf' => array(
				'empty'  => false,
				'type'   => 'input',
				'format' => 'email',
				'unique' => true,
				'eval'   => 'trim',
			),
		),


		'register_key' => array(
			'access' => true,
			'label'  => e::o('f_uo_register-key',null,null,'usradmin'),
			'cnf' => array(
				'type'   => 'input',
				'empty'  => true,
				'form'   => false,
				'min'    => 1,
				'max'    => 255,
			),
		),

		'lang' => array(
			'access' => true,
			'label'  => '',
			'cnf' => array(
				'type'   => 'hidden',
				'empty'  => true,
				'form'   => false,
				'min'    => 1,
				'max'    => 255,
				'default'=> 1,
			),
		),

		'groups' => array(
			'access' => true,
			'label' => e::o('f_uo_groups',null,null,'usradmin'),
			'cnf' => array(
				'form'    => false,			// kein formular dafuer anzeigen
				'default' => '58,64',			// default gruppe(mehrere durch komma), 9=G�ste
				'type'  => 'select',	
				'empty' => false,
				'size'  => '10',
				'multi' => true,
				'min'   => 1,
				'relation' => array(
					'table' => 'mod_usradmin_grp',
					'key'   => 'id',
					'value' => 'name',
					'order' => 'ORDER BY name ASC',
					'mm'    => 'mm_usradmin_usr_grp',
				),
				'err_empty' => e::o('f_uo_groups_err',null,null,'usradmin'),
			),
		),
	),
);
?>
