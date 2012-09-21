<?
$OPC->lang_page_start('usradmin');
$boxed = $OPC->get_var('usradmin','boxed');
if($boxed)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
	<HEAD>
		<TITLE>
			<?=$OPC->get_var('usradmin','title')?>
		</TITLE>
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
		<link href="system/css/global.css" rel="stylesheet" type="text/css">
		<link href="template/oos/css/oos_main.css" type=text/css rel=stylesheet>
		<script src="system/js/common.js" type="text/javascript"></script>
	</HEAD>
	<BODY onunload="cleanup()">
<?
}
?>
		<?=$OPC->get_var('usradmin','button')?>
		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=$OPC->get_var('usradmin','headline')?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">
					<?=$OPC->get_var('usradmin','content')?>
				</div>
			</div>
		</div>
<?
if($boxed)
{
?>
	</BODY>
</HTML>	
<?
}
$OPC->lang_page_end();
?>
