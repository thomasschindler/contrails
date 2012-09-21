<?php
/**
*	@package	accessconf
* konfigurationsdatei der zugriffsrechte des moduls grid
*/

$conf = array(
	'rights' => array(
		'edit'   => 1,
		'set_vid' => 2,
	),
	'labels' => array(
		'edit'   => e::o('edit',null,null,'grid'),
		'set_vid' => e::o("set_vid",null,null,'grid'),
	),
);


?>
