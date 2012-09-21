<?php
/**
*	@package	eventconf
*	diverse sql events
*/
$events = array(
	'mod_usradmin_usr' => array(
		'delete' => array(
			array('trashcan', 'callback_delete', 'post'),
		),
	),
	'mod_usradmin_grp' => array(
		'delete' => array(
			array('trashcan', 'callback_delete', 'post'),
		),
	),
	'mod_news' => array(
		'delete' => array(
			array('trashcan', 'callback_delete', 'post'),
		),
	),
	'mod_article' => array(
		'delete' => array(
			array('trashcan', 'callback_delete', 'post'),
		),
	),
	'mod_fbb' => array(
		'delete' => array(
			array('trashcan', 'callback_delete', 'post'),
		),
	),
	'mod_level_page' => array(
		'delete' => array(
			array('trashcan', 'callback_delete', 'post'),
		),
	),
	'mod_forum' => array(
		'delete' => array(
			array('trashcan', 'callback_delete', 'post'),
		),
	),
);
?>