<?$OPC->lang_page_start('page')?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?=e::o('p_title_modul_rights')?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="/system/js/common.js" type="text/javascript"></script>
	<link href="/system/css/global.css" rel="stylesheet" type="text/css">
	<link href="/template/oos/css/oos_page.css" rel="stylesheet" type="text/css">
</head>

<body>


<div id="oos_pa_head">
	<span id="oos_pa_head_left">
		<? echo e::o('page').' '.$OPC->get_var('page', 'path'); ?>
	</span>
	<span id="oos_pa_head_right">
		 <?=$OPC->show_icon("close_red")?><span><a href="#" onClick="return close_popup();"><?=e::o('pa_type_close',null,null,'page')?></a></span>
	</span>
</div>
<div id="oos_pa_body">

<?php
		
	/*
	* zugriffsrechte fuer module fuer benutzer fuer seiten
	*/
	$mid      = $OPC->get_var('page', 'mid');
	$edit_pid = $OPC->get_var('page', 'edit_pid');
	$mod      = $OPC->get_var('page', 'mod_info');
	$vid      = $OPC->get_var('page', 'vid');
	
	$usr_ar = $OPC->get_var('page', 'usr_ar');
	$grp_ar = $OPC->get_var('page', 'grp_ar');
	
	$access_conf = $MC->access_config($mod->f('modul_name'));
	
	if (is_error($access_conf)) {
		echo '<div class="oos_pa_box">
			'.$OPC->show_icon("info_red").'<span>'.e::o('no_config_file_found',array('%mod%'=>$mod->f('label'))).'</span>
		</div>
		</body>
		</html>';
		$OPC->lang_page_end();		
		return;
	}
	
	//- forumlar
	$form = $MC->create('form');
	$form->init('page');

	$form->add_hidden('edit_pid',  $edit_pid);
	$form->add_hidden('vid',       $vid);
	$form->add_hidden('mod',       'page');
	$form->add_hidden('event',     '');
	$form->add_hidden('data[mid]', $mid);
	$form->add_hidden('data[action]', '');		// infos fuer hinzufuegen/loeschen etc.
	
	echo 	$form->start('arform');
?>

<div class="oos_pa_box">
	<?=$OPC->show_icon("info_red")?><span><?=e::o('access_rights_for_module',array('%mod%'=>$mod->f('label')))?></span>
</div>


<?php
	// gibts messages ?
	if ($msg = trim($OPC->get_var('page', 'msg'))) {
		echo '<div class="oos_pa_box">
			'.$OPC->show_icon("info_red").'<span>'.$msg.'</span>
		</div>';
	}
	
	// haben wir die rechte von einer anderen seiten geerbt ?
	$inheritet_from = $OPC->get_var('page', 'inheritet_from');
	if ($inheritet_from != '') {
		echo '<div class="oos_pa_box">
			'.$OPC->show_icon("info_red").'<span>'.e::o('inherited_from',array('%page%'=>$inheritet_from)).'</span>
		</div>';
	}
	echo '<div class="oos_pa_box">
		'.$OPC->show_icon("info_red").'<span>'.e::o('u_r_underlined').'</span>
	</div>';
?>

<table border="0" class="adm_table_red">

<?php
	//$data = UTIL::get_post('data');
	$data = $OPC->get_var('page', 'data');
	
	
	$max = count($access_conf['rights']);
	
	//-- horizontal -> zugriffsrechte
	echo '<tr>';
	echo '<th>&nbsp;</th>';	// fuergruppen/user
	echo '<th>'.e::o('delete').'</th>';	// for editthis checkbox 
	echo '<th>'.e::o('change').'</th>';	// for editthis checkbox 
	foreach($access_conf['labels'] as $ar => $label) {
		echo '<th>'.htmlspecialchars($label).'</th>';
	}
	echo '</tr>';

	//-- vertikal -> gruppen/benutzer
	$no_rights = true;
	
	
	
	//-- gruppen und benutzer (und deren rechte)
	$icon_base   = CONF::img_url().'icons/';
	$icon_delete = '<img src="'.$icon_base.'icon_delete.gif" width="16" height="16" alt="'.e::o('delete').'" border="0">';

	$cnt = 0;
	foreach(array('grp', 'usr') as $key) {
		$my_icon = $icon_base . 'icon_mod_usradmin_'.$key.'.gif';
		
		if (!is_array($data['info'][$key])) continue;
		
		foreach($data['info'][$key] as $id => $info) {
			$no_rights = false;
			
			// vorhandene rechte als hidden-fields
			echo '<input type="hidden" name="data[info]['.$key.']['.$id.'][name]" value="'.$info['name'].'">';
			echo '<input type="hidden" name="data[info]['.$key.']['.$id.'][ar]" value="'.$info['ar'].'">';
			
			$css_class = $counter++%2 == 0 ? 'even' : 'odd';
		
			echo '<tr class="adm_table_red_'.$css_class.'">';
#			echo '<th align="center">'.
#					'<a href="#" onClick="page_del_ar(\''. $key.','.$id .'\');return false;">'.$icon_delete.'</a></th>';
					
			echo '<th style="text-align:left"><img src="'.$my_icon.'">&nbsp;';
			if ( ($key == 'usr' && $id == $CLIENT->usr['id']) || ($key == 'grp' && isset($CLIENT->usr['groups'][$id])) ) {
				echo '<u>'. $info['name'] .'</u>';
			}
			else {
				echo $info['name'];
			}
			echo '</th>';
			
			echo '<th>
				<input type="checkbox" name="data[info]['.$key.']['.$id.'][del]" value="1">
			</th>';
			
			echo '<th>
				<input type="checkbox" name="data[info]['.$key.']['.$id.'][chg]" value="1">
			</th>';
						
			
			foreach($access_conf['rights'] as $name => $number) {
			
					echo '<td align="center">'.
						'<input type="checkbox" name="data[ar]['.$key.']['.$id.']['.$name.']" value="'.$number.'"'.
						(($info['ar'] & $number) ? ' checked' : '').
						'>'.
						'</td>';

			}
			echo '</tr>';
			
			$cnt++;		
		}
	}
	
	if ($no_rights) {
		echo '<tr class="adm_table_red_even">
				<th colspan="3" align="center">
					'.e::o('no_grp_usr_selection').'
				</th>
				<td colspan="'.$max.'">
					&nbsp;
				</td>			
			</tr>';
	}
	
	

	$add_param = array(
					'mod' => 'objbrowser',
					'data[tbl][0]' => 'mod_usradmin_usr',
					'data[tbl][1]' => 'mod_usradmin_grp',
					'data[callback]' => 'page_objbrowser_callback',
					'data[return_type]' => 'ser',
				);
	$lnk_add = $OPC->lnk($add_param);
	
	//-- hinzufuegen link
/*
	echo '<tr class="adm_table_red_even">
			<th colspan="3">
				<a href="#" onClick="return popup(\''.$lnk_add.'\');" class="btnlnk">
					'.$OPC->show_icon('new_user_red').' '.e::o('add').'
				</a>
			</th>
			<td colspan="'.$max.'">
				&nbsp;
			</td>
		</tr>';
*/


?>

</table>

<div class="oos_pa_box">
	<a href="#" onClick="return popup('<?=$lnk_add?>');">
		<?=$OPC->show_icon('new_user_red')?><span><?=e::o('add')?></span>
	</a>
</div>


<script language="JavaScript">
<!--
	function page_objbrowser_callback(result) {
		var fo = document.forms['arform'];
		fo.elements['data[action]'].value = result;
		fo.elements['event'].value     = 'pa_ar_add';
		fo.submit();
	}
	function page_del_ar(result) {
		var fo = document.forms['arform'];
		fo.elements['data[action]'].value = result;
		fo.elements['event'].value     = 'pa_ar_del';
		fo.submit();		
	}
//-->
</script>

<div class="oos_pa_box">

<table border="0">
<tr>
	<td colspan='2'><input type="button" name="_save" value="<?=e::o('save_for')?>" onClick="this.form.event.value='pa_ar_save';this.form.submit();"></td>
	<td><select name="data[inherit]" size="1">
		<?php
			foreach($OPC->get_var('page', 'select_array') as $key => $val) {
				echo '<option value="'.$key.'">'.htmlspecialchars($val).'</option>';
			}
		?>
		</select>
	</td>
</tr>
</table>

</div>
	
<?php
	echo $form->end();
?>
</div>
</body>
</html>
<?$OPC->lang_page_end()?>
