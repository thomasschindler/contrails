<?
	$OPC->lang_page_start('usradmin');
	$vid = $OPC->var_get('vid');
?>

		
		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=e::o('t_ul_headline')?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">




<?php
	$form = $OPC->get_var('usradmin', 'usr_form');
	
	#$form->add_button('event_usr_new', e::o('new'));
	$form->add_image('new_user_red','event_usr_new', e::o('new'));
	#$form->add_button('event_usr_edit', e::o('edit'));
//	$form->add_image('edit_user_red','event_usr_edit', e::o('edit'));
	#$form->add_button('event_usr_delete', e::o('delete'), 'onClick="return confirm(\''.e::o('t_ul_del_txt').'\')"');
//	$form->add_image('delete_user_red','event_usr_delete', e::o('delete'), 'onClick="return confirm(\''.e::o('t_ul_del_txt').'\')"');
	
	if ($error = $OPC->get_var('usradmin', 'error')) {
		echo '<div class="errbox">'.$error.'</div>';
	}
	
?>

<?=$form->start();?>

<?=$form->fields();?>

<table border="0" width="500" class="adm_table_red">
<tr>
	<th style="width:30px;"><?=e::o('t_ul_id')?></th>
	<th style="width:200px;"><?=e::o('t_ul_uname')?></th>
	<th>&nbsp;</th>
</tr>

<?php 
	$usr = $OPC->get_var('usradmin', 'usr_liste');

	if ($usr->nr() == 0) {
		echo '<tr><td colspan="5">'.e::o('t_ul_no_users').'</td></tr>';
	}
	while($usr->next()) 
	{
		$id = $usr->f('id');
		if($id == CLIENT::__root() AND $CLIENT->usr['id'] != CLIENT::__root())
		{
			continue;
		}
		
		$lnk = $OPC->lnk(array(
			'data[usr]' => $usr->f('usr'),
			'data[pid]' => CONF::pid(),
			'mod' => 'page',
			'event' => 'pa_ar'
		));
		echo '<tr>'.
			'<td align="right">'.$id.'</td>'.
			'<td><a href="'.$OPC->lnk(array(
				'mod' => 'usradmin',
				'event' => 'usr_edit',
				'ids' => array($usr->f('id')),
				'vid' => $vid
			)).'">'.htmlspecialchars($usr->f('usr')).'</a></td>'.
			'<td><a href="'.$lnk.'" target="_blank" onClick="return popup(\''.$lnk.'\',900);">'.$OPC->show_icon('rights_red').'</a></td>'.
			'</tr>';
	}
	
	if($usr->nav)
	{
		if($usr->nav['last'])
		{
			$nav[] = '<a href="'.$usr->nav['last'].'"><img src="/system/img/famfamfam/resultset_previous.png" /></a>';
		}
		if($usr->nav['pages'])
		{
			if(count($usr->nav['pages'])>6)
			{
				foreach($usr->nav['pages'] as $page => $link)
				{
					$cnt++;
					if($cnt == 4)
					{
						$nav[] = '...';
					}
					if($cnt < (count($usr->nav['pages'])-2) AND $cnt > 3)
					{
						continue;
					}
					$nav[] = '<a href="'.$link.'">'.$page.'</a>';
				}
			}
			else
			{
				foreach($usr->nav['pages'] as $page => $link)
				{
					$nav[] = '<a href="'.$link.'">'.$page.'</a>';
				}
			}	
		}
		if($usr->nav['next'])
		{
			$nav[] = '<a href="'.$usr->nav['next'].'"><img src="/system/img/famfamfam/resultset_next.png" /></a>';
		}
		
		echo '<tr><td colspan="3"><nobr>'.implode(" ",$nav).'</nobr></td></tr>';
		
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
