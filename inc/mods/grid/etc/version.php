<?
$def = array(
	"name" => "grid",
	"type" => "standard",
	"description" => "replacement for the container-template combination",
	"files" => array(
		"grid.access" => "inc/etc/access/grid.access",
		"class_gridAction.inc.php" => "inc/mods/grid/class_gridAction.inc.php",
		"class_gridView.inc.php" => "inc/mods/grid/class_gridView.inc.php",
		"grid.def" => "inc/rsc/def/grid.def",
		"edit.tpl" => "web/tpl/hundertelf/grid/edit.tpl",
		"grid.lang.de" => "inc/etc/lang/de/grid.lang",
		"grid.lang.en" => "inc/etc/lang/en/grid.lang",
		"grid.mod_grid.sql" => "inc/rsc/sql/tbl/grid.mod_grid.sql",
		"resize.gif" => "web/tpl/hundertelf/grid/resize.gif",
		"move.gif" => "web/tpl/hundertelf/grid/move.gif",
		"close.gif" => "web/tpl/hundertelf/grid/close.gif",
	),
	"folders" => array(
		"0" => "web/tpl/hundertelf/grid",
		"1" => "inc/mods/grid",
	),
	"events" => array(
		"edit" => "",
		"new_box" => "",
		"save_state" => "",
	),
	"views" => array(
		"edit" => "",
		"default" => "",
	),
	"author" => "Thomas Schindler",
	"email" => "thomas.schindler@hundertelf.com",
	"date" => "20050127",
	"version" => "9.4",
);
?>
