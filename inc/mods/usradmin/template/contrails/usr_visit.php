<?
	$OPC->lang_page_start('usradmin');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
	<head>
		<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
		<title>
			zu Besuch
		</title>
		<?
			if(is_file(CONF::web_dir()."/template/oos/css/oos_main.".CONF::project_name().".css"))
			{
				echo '<link href="template/oos/css/oos_main.'.CONF::project_name().'.css" type=text/css rel=stylesheet>';
			}
			else
			{
				echo '<link href="template/oos/css/oos_main.css" type=text/css rel=stylesheet>';
			}
		?>
		<link href="template/oos/css/oos_page.css" type=text/css rel=stylesheet>
		<link href="system/css/global.css" rel="stylesheet" type="text/css">
		<script src="system/js/common.js" type="text/javascript"></script>
		<script src="system/js/drag.js" type="text/javascript"></script>
	</head>
	<body onunload="cleanup()">
		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">
					<?
						$u = $OPC->var_get("content");
						echo $u->f('usr');
						echo '<img src="'.$MC->call('usradmin','get_file',array('uid'=>$u->f('id'),'name'=>'icon')).'">';
						echo '<img src="'.$MC->call('usradmin','get_file',array('uid'=>$u->f('id'),'name'=>'image')).'">';
					?>
				</div>
			</div>
		</div>
	</body>
</html>
<?
	$OPC->lang_page_end();
?>
