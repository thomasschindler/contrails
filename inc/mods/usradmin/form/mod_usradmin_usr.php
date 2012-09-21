<?php
/**
*	@package	formconf
*	form configuration
*/
$conf = array(
	'mod' => 'usradmin',
	'table' => array(
		'name'  => 'mod_usradmin_usr',
		'label' => e::o('f_uu_label',null,null,'usradmin'),
		'sys_fields' => array(					// automatisch gefuellte felder
			'deleted' => 'sys_trashcan',		// geloescht (ts)
			'created' => 'sys_date_created',	// timestamp angelegt
			'changed' => 'sys_date_changed',	// timestamp zuletzt geaendert
		),
		'title' => 'usr',					// welches feld/felder fuer 'kurzansichten' verwenden
												// mehrere mit komma getrennt
		'icon' => 'icon_mod_usradmin_usr.gif',
	),
	'fields' => array(
		// id und pid sind absolet/default
		
		'usr' => array(
			'access' => true,
			'label'  => e::o('f_uu_usr',null,null,'usradmin'),
			'cnf' => array(
				'type'   => 'input',
				'empty'  => false,
				'min'    => 3,
				'max'    => 22,
				'regex'  => '/^[a-z0-9]+$/i',
				'unique' => true,
				'eval' => 'trim',
				'err_empty' =>  e::o('f_uu_usr_empty'),
				'err_format' => e::o('f_uu_usr_format'),
				'err_unique' => e::o('f_uu_usr_uniue'),
			),
		),
		
		'pwd' => array(
			'access' => true,
			'label'  => e::o('f_uu_pwd',null,null,'usradmin'),
			'cnf' => array(
				'type'   => 'input',
				'empty'  => false,
				'min'    => 4,
				'regex'  => '/^[a-z0-9]+$/i',
				'eval' => 'trim',
			),
		),
		'email' => array(
			'access' => false,
			'label'  => e::o('f_uu_email',null,null,'usradmin'),
			'cnf' => array(
				'type'   => 'input',
				'format' => 'email',
				'eval'   => 'trim',
			),
		),

		'name' => array(
			'access' => true,
			'label'  => e::o('f_uu_lastname',null,null,'usradmin'),
			'cnf' => array(
				'type'   => 'textarea',
				'min'    => 1,
				'max'    => 255,
				'eval' => 'trim',
				'err_empty' => e::o('f_uu_lastname_err',null,null,'usradmin'),
			),
		),

		'street' => array
		(
			'access' => true,
			'label'  => e::o('f_street',null,null,'usradmin'),
			'cnf' => array
			(
				'type'   => 'input',
				'eval' => 'trim',
				'format' => 'text',
				'err_empty' => e::o('f_empty',null,null,'usradmin'),
				'err_format' => e::o('f_format',null,null,'usradmin'),
			),
		),
		'num' => array
		(
			'access' => true,
			'label'  => e::o('f_num',null,null,'usradmin'),
			'cnf' => array
			(
				'type'   => 'input',
				'eval' => 'trim',
				'format' => 'text',
				'err_empty' => e::o('f_empty',null,null,'usradmin'),
				'err_format' => e::o('f_format',null,null,'usradmin'),
			),
		),
		'city' => array
		(
			'access' => true,
			'label'  => e::o('f_city',null,null,'usradmin'),
			'cnf' => array
			(
				'type'   => 'input',
				'eval' => 'trim',
				'format' => 'text',
				'err_empty' => e::o('f_empty',null,null,'usradmin'),
				'err_format' => e::o('f_format',null,null,'usradmin'),
			),
		),
		'zip' => array
		(
			'access' => true,
			'label'  => e::o('f_zip',null,null,'usradmin'),
			'cnf' => array
			(
				'type'   => 'input',
				'eval' => 'trim',
				'format' => 'text',
				'err_empty' => e::o('f_empty',null,null,'usradmin'),
				'err_format' => e::o('f_format',null,null,'usradmin'),
			),
		),
		'country' => array
		(
			'access' => true,
			'label'  => e::o('f_country',null,null,'usradmin'),
			'cnf' => array
			(
				'type'   => 'select',
				'items' => UTIL::get_countries(),
				'default' => 'DE'
			),
		),
		'tel' => array
		(
			'access' => true,
			'label'  => e::o('f_tel',null,null,'usradmin'),
			'cnf' => array
			(
				'type'   => 'input',
				'min' => '10',
				'eval' => 'trim',
				'format' => 'text',
				'err_empty' => e::o('f_empty',null,null,'usradmin'),
				'err_format' => e::o('f_format',null,null,'usradmin'),
			),
		),
		'fax' => array
		(
			'access' => true,
			'label'  => e::o('f_fax',null,null,'usradmin'),
			'cnf' => array
			(
				'type'   => 'input',
				'eval' => 'trim',
				'format' => 'text',
				'err_empty' => e::o('f_empty',null,null,'usradmin'),
				'err_format' => e::o('f_format',null,null,'usradmin'),
			),
		),

		'groups' => array(
			'access' => true,
			'label' => e::o('f_uu_groups',null,null,'usradmin'),
			'cnf' => array(
#				'type'  => 'select',
				
				'type'  => 'checkbox',
				
				'empty' => true,
#				'size'  => '10',
#				'multi' => true,
#				'min'   => 1,
				
				'align' => 'vertical',
				
				'vertical' => true, // new
				
				'relation' => array(
					'table' => 'mod_usradmin_grp',
					'key'   => 'id',
					'value' => 'name',
					'order' => 'ORDER BY name ASC',
					'mm'    => 'mm_usradmin_usr_grp',
				),
				'err_empty' => e::o('f_uu_groups_err',null,null,'usradmin'),
			),
		),
		'lang' => array(
			'access' => true,
			'label' => e::o('f_uu_lang',null,null,'usradmin'),
			'cnf' => array(
				'type'  => 'select',				
				'empty' => false,
				'min'   => 1,
				'items' => e::lang()
			),
		),
		
		'lang_default' => array(
			'access' => false,
			'label' => e::o('f_uu_lang_default',null,null,'usradmin'),
			'cnf' => array(
				'form' => false,
				'type'  => 'radio',				
				'empty' => false,
				'items' => array(
					0 => e::o('f_uu_lang_default_no',null,null,'usradmin'),
					1 => e::o('f_uu_lang_default_yes',null,null,'usradmin'),
				),
				'default' => 0
			),
		)
		/*
		'lang_default' => array(
			'access' => true,
			'label' => 'Default',
			'cnf' => array(
				'type'  => 'checkbox',
				'empty' => true,
				'items' => array(
					'1'=>'',
				),
				'default' => '0'
			),
		),
		*/
	),
);
?>
