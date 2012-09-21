<?php
/**
*	@package	accessconf
* konfigurationsdatei der zugriffsrechte des moduls NEWS
*/

$conf = array(
	'rights' => array(
		'new'            => 1,
		'edit'           => 2,
		'delete'         => 4,
		'acl'            => 8,
		'ar_mod'         => 16,
		'move'			=> 32,
	),
	'labels' => array(
		'new'            => e::o('access_new',null,null,'page'),
		'edit'           => e::o('access_edit',null,null,'page'),
		'delete'         => e::o('access_delete',null,null,'page'),
		'acl'            => e::o('access_acl',null,null.'page'),
		'ar_mod'         => e::o('access_ar_mod',null,null,'page'),
		'move'			=> e::o('access_move',null,null,'page'),
	),
);
//,null,null.''),
?>
