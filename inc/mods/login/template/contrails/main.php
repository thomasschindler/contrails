<?
	$OPC->lang_page_start('login');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
	<head>
		<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
		<title>
			<?=$OPC->get_var("login","headline")?>
		</title>
		<!--get all necessary styles-->
		<link href="template/oos/css/oos_main.css" type=text/css rel=stylesheet>
		<link href="template/oos/css/oos_login.css" type=text/css rel=stylesheet>
		<link href="template/oos/css/oos_navigation.css" type=text/css rel=stylesheet>
		<link href="system/css/global.css" rel="stylesheet" type="text/css">
		<!--get all necessary javascript-->
		<script src="system/js/common.js" type="text/javascript"></script>
		<script src="system/js/drag.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=$OPC->get_var("login","headline")?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">
					<?=$OPC->get_var("login","content")?>
				</div>
			</div>
		</div>
	</body>
</html>
<?
	$OPC->lang_page_end();
?>
