<?
/**
*	@package	instanceconf
*	instance configuration for 
*/

$conf = array(
	'instance'	=>	'myName',			# vid will default to this value if it is set
	'method' 	=> 	'methodName',		# if method is set, it will be set as a standard method
	'methods' 			=>	array(		# creates a dropdown with possible startmethods for the current instance
		'admin_panel'	=> e::o('instance_pap'),
	),
	'allow_copies'	=>	true			# if set to true, a menue for choosing a copy of the module from a different page will be offered
);

?>
