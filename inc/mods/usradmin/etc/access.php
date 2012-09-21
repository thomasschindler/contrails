<?php
/**
*	@package	accessconf
* konfigurationsdatei der zugriffsrechte des moduls NEWS
*/

$conf = array(
	'rights' => array(
		'usr_enter'  => 1,
		'usr_edit'   => 2,
		'usr_delete' => 4,
		'grp_enter'  => 8,
		'grp_edit'   => 16,
		'grp_delete' => 32,
		'edit_registerform' => 64,
	),
	'labels' => array(
		'usr_enter'  => e::o('access_usr_enter',null,null,'usradmin'),
		'usr_edit'   => e::o('access_usr_edit',null,null,'usradmin'),
		'usr_delete' => e::o('access_usr_delete',null,null,'usradmin'),
		'grp_enter'  => e::o('access_grp_enter',null,null,'usradmin'),
		'grp_edit'   => e::o('access_grp_edit',null,null,'usradmin'),
		'grp_delete' => e::o('access_grp_delete',null,null,'usradmin'),
		'edit_registerform' => e::o('access_edit_registerform',null,null,'usradmin'),
	),
);

?>
