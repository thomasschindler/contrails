<?php
/**
*	@package	accessconf
* konfigurationsdatei der zugriffsrechte des moduls NEWS
*/

$conf = array(
	'rights' => array(
		'use'   => 1,
		'config' => 2,
	),
	'labels' => array(
		'use'   => e::o('access_use',null,null,'login'),
		'config' => e::o('access_config',null,null,'login'),
	),
);

?>
