<?
/**
*	@package	instanceconf
*	instance configuration for 
*/

$conf = array(
	'instance'	=>	'myName',			# vid will default to this value if it is set
	'method' 	=> 	'methodName',		# if method is set, it will be set as a standard method
	'methods' 			=>	array(		# creates a dropdown with possible startmethods for the current instance
		'grp_list'	=> 'Gruppen-Verwaltung',
		'usr_list'	=> 'Benutzer-Verwaltung',
		'register_form'  => 'Anmelden Formular',
		'usr_home'       => 'Benutzer Home',
		'usr_search'       => 'Benutzer Suche',
		'register_ok'       => 'Register Confirmation',
		'usr_profile' => 'User Data',
		'usr_headline' => 'User Home Headline'
	),
	'allow_copies'	=>	true			# if set to true, a menue for choosing a copy of the module from a different page will be offered
);

?>
