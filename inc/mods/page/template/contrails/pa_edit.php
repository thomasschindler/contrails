<?$OPC->lang_page_start('page')?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?=e::o('pa_edit_title')?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="/system/js/common.js" type="text/javascript"></script>
	<link href="/system/css/global.css" rel="stylesheet" type="text/css">
	<link href="/template/oos/css/oos_page.css" rel="stylesheet" type="text/css">
	<link href="/template/oos/css/oos_main.css" rel="stylesheet" type="text/css">
	
</head>
<body>

<div id="oos_pa_head">
	<span id="oos_pa_head_left">
		<?=e::o('pa_edit_headline')?> <?=$OPC->get_var('page', 'path') ?>
	</span>
	<span id="oos_pa_head_right">
		 <?=$OPC->show_icon("close_red")?><span><a href="#" onClick="return close_popup();"><?=e::o('pa_type_close')?></a></span>
	</span>
</div>
<div id="oos_pa_body">

	<?  if ($msg = trim($OPC->get_var('page', 'msg'))) { ?>
		<div class="oos_pa_box">
			 <?=$OPC->show_icon("info_red")?><span><?=$msg?></span>
		</div>
	<? } 
	
	$vid  = $OPC->get_var('page', 'vid');
	$form = $OPC->get_var('page', 'form');
	$err  = $OPC->get_var('news', 'error');
	// kommen wir von 'seite-bearbeiten'-seite
	$edit_pid = (int)UTIL::get_post('edit_pid');
	if ($edit_pid) $form->add_hidden('edit_pid', $edit_pid);
	
	if ($err) {
		echo '<div class="oos_pa_box">
			 '.$OPC->show_icon("warning_red").'<span>'.e::o('pa_edit_err_fields').'</span>
		</div>';		
	}
#	$form->add_button('event_pa_save', e::o('save'));
	
	$form->add_image('save_red','event_pa_save', e::o('save'),null,"#990000");
	
	$form->add_hidden('mod',  'page');
	//$form->add_hidden('vid',  $vid);
	
	echo $form->start();
	echo $form->fields();
	echo $form->end();
?>

</div>
</body>
</html>
<?$OPC->lang_page_end()?>
