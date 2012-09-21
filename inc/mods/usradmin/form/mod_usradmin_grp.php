<?php
/**
*	@package	formconf
*	form configuration
*/

$conf = array(
	'table' => array(
		'name'  => 'mod_usradmin_grp',
		'label' => e::o('f_g_label',null,null,'usradmin'),
		'sys_fields' => array(
			'deleted' => 'sys_trashcan',
		),
		'title' => 'name',						// welches feld/felder fuer 'kurzansichten' verwenden
												// mehrere mit komma getrennt
		'icon' => 'icon_mod_usradmin_grp.gif',
	),
	'fields' => array(
		// id und pid sind absolet/default
		
		'name' => array(
			'access' => true,
			'label'  => e::o('f_g_name',null,null,'usradmin'),
			'cnf' => array(
				'type'   => 'input',
				'empty'  => false,
				'min'    => 1,
				'max'    => 255,
				'unique' => true,
				'eval' => 'trim',
				'err_empty' => e::o('f_g_name_err',null,null,'usradmin'),
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
	),
);


?>
