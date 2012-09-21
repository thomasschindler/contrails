<?
	$OPC->lang_page_start('usradmin');
	$vid = $OPC->var_get('vid');
?>

		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=e::o('a_gl_headline')?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">

<?php
	$form = $OPC->get_var('usradmin', 'grp_form');

	$del_txt = e::o('a_gl_del_txt');
/*
	$form->add_button('event_grp_new', e::o('new'));
	$form->add_button('event_grp_edit', e::o('edit'));
	$form->add_button('event_grp_delete', e::o('delete'), 'onClick="return confirm(\''.$del_txt.'\')"');
*/
	$form->add_image('new_user_red','event_grp_new', e::o('new'));
//	$form->add_image('edit_user_red','event_grp_edit', e::o('edit'));
//	$form->add_image('delete_user_red','event_grp_delete', e::o('delete'), 'onClick="return confirm(\''.$del_txt.'\')"');
	
	if ($error = $OPC->get_var('usradmin', 'grp_error')) {
		echo '<div class="errbox">'.$error.'</div>';
	}
	
?>

<?=$form->start();?>

<?=$form->fields();?>

<table border="0" width="500" class="adm_table_red">
<tr>
	<th style="width:30px;"><?=e::o('t_gl_id')?></th>
	<th><?=e::o('t_gl_grp_name')?></th>
	<th>&nbsp;</th>
</tr>

<?php 
	$grp = $OPC->get_var('usradmin', 'grp_liste');

	if ($grp->nr() == 0) {
		echo '<tr><td colspan="4">'.e::o('t_gl_no_grp').'</td></tr>';
	}
	while($grp->next()) {

		$id = $grp->f('id');
		//-- user dieser gruppe ermitteln
		/*
		$usr = $DB->query('SELECT usr FROM mod_usradmin_usr usr, mm_usradmin_usr_grp mm WHERE mm.local_id=usr.id AND mm.foreign_id='.$id);
		$show_usr = array();
		while($usr->next()) $show_usr[] = $usr->f('usr');
		$show_usr = implode(', ', $show_usr);
		*/
		$lnk = $OPC->lnk(array(
			'data[gid]' => $id,
			'data[pid]' => CONF::pid(),
			'mod' => 'page',
			'event' => 'pa_ar'
		));
		
		echo '<tr>'.
			'<td align="right">'.$id.'</td>'.
			'<td><label for="lbl'.$id.'"><a href="'.$OPC->lnk(array(
				'mod' => 'usradmin',
				'event' => 'grp_edit',
				'ids' => array($grp->f('id')),
				'vid' => $vid
			)).'">'.htmlspecialchars($grp->f('name')).'</a></label></td>'.
			'<td><a href="'.$lnk.'" target="_blank" onClick="return popup(\''.$lnk.'\',900);">'.$OPC->show_icon('rights_red').'</a></td>'.
			'</tr>';
	}
?>


</table>

<?=$form->end();?>



				</div>
			</div>
		</div>


<?
	$OPC->lang_page_end();
?>
