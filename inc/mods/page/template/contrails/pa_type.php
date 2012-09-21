<?$OPC->lang_page_start('page')?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>contrails</title>
    <link href="/template/contrails/bootstrap/css/bootstrap.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/bootstrap/css/bootstrap-responsive.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/contrails/css/contrails.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/template/contrails/colorbox/colorbox.css" media="screen" rel="stylesheet" type="text/css" />
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
    <script src="/template/contrails/colorbox/jquery.colorbox-min.js"></script>
    <script src="/template/contrails/contrails/js/contrails.js"></script>
    
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="span12">


<?php
/**
* template zur auswahl, was an der seite editiert werden soll (name, zugrifssrechte etc,)
*/

	$edit_pid = (int)$OPC->get_var('page', 'edit_pid');
	$vid      = $OPC->get_var('page', 'vid');
	
	$base_lnk = array(
		'mod' => 'page',
		'edit_pid' => $edit_pid ,
		'vid' => $vid,
		'pid' => $edit_pid
	);
	
	$common_lnk = array_merge($base_lnk, array('event' => 'pa_edit'));
	$del_lnk    = array_merge($base_lnk, array('event' => 'pa_delete'));
	
	//$base_lnk   = $OPC->lnk().'&mod=page&edit_pid='.$edit_pid.'&vid='.$vid;
	$mod_list   = $OPC->get_var('page', 'mod_list');
	$known_mods = $OPC->get_var('page', 'known_mods');

	$ar_new    = $MC->acl_access('mod_page', 'new',$edit_pid);
	$ar_edit   = $MC->acl_access('mod_page', 'edit',$edit_pid);
	$ar_delete = $MC->acl_access('mod_page', 'delete',$edit_pid);
	$ar_mod    = $MC->acl_access('mod_page', 'ar_mod',$edit_pid);
	$ar_acl    = $MC->acl_access('mod_page', 'acl',$edit_pid);
	$ar_move   = $MC->acl_access('mod_page', 'move',$edit_pid);
	
	//-- move select
	$move_select = '<form action="'.$OPC->action().'" method="POST">'.
				'<select name="data[move_type]">'.
				'<option value="child">'.e::o('pa_type_one_level_under').'</option>'.
				'<option value="brother">'.e::o('pa_type_same_level_before').'</option>'.
				'</select> '.
				'<select name="data[move_to]">';
/*
	$move_select = '<form action="'.$OPC->action().'" method="POST">'.
				'<input type="radio" name="data[move_type]" value="child">'.e::o('pa_type_one_level_under').' - '.
				'<input type="radio" name="data[move_type]" value="brother">'.e::o('pa_type_same_level_before').'<br>'.
				'<select name="data[move_to]">';
				
	*/
	foreach($OPC->get_var('page', 'page_nodes') as $node) {
		$style = '';
		
		if ($node['id'] == $edit_pid) {
			$no_move_lft = $node['lft'];
			$no_move_rgt = $node['rgt'];
		}
		
		if ($no_move_lft > 0 && $node['lft'] <= $no_move_rgt && $node['lft'] >= $no_move_lft) {
			continue;
		}
		
		$move_select .= '<option value="'.$node['id'].'"'.$style.'>'.
						str_repeat('&nbsp;', ($node['level']-1)*4).$node['name'].
						'</option>';
	}
	$move_select .= '</select> <input type="submit" name="ok" value="'.e::o('pa_type_ok').'">'.$OPC->lnk_hidden().
					'<input type="hidden" name="mod" value="page">'.
					'<input type="hidden" name="event" value="pa_move">'.
					'<input type="hidden" name="edit_pid" value="'.$edit_pid.'">'.
					'</form>';

?>






	<? if ($ar_edit) {?>
		<div class="oos_pa_box">
			 <?=$OPC->show_icon("arrow_red")?><span><a href="#" onClick="return popup('<?=$OPC->lnk($common_lnk);?>');"><?=e::o('pa_type_global_settings')?></a></span>
		</div>
	<? } ?>
	<? if ($ar_delete AND $edit_pid != CONF::pid()) {?>
		<div class="oos_pa_box">
			<?=$OPC->show_icon("delete_page")?><span><a href="<?=$OPC->lnk($del_lnk);?>" onClick="return confirm('<?=e::o('pa_type_delete_confirm_page')?>');"><?=e::o('pa_type_delete')?></a></span>
		</div>
	<? } ?>

	<? if ($ar_move) {?>
		<div class="oos_pa_box_stretch" style="padding-left:12px;">
			<?=$OPC->show_icon("move_gray")?>
			<span>
				<?=e::o('pa_type_move_page')?>
				<?=$move_select?>
			</span>
		</div>
	<? } ?>
	<? if ($ar_acl) {?>
		<div class="oos_pa_box">
			<?=$OPC->show_icon("rights_red")?><span><a href="#" onClick="return popup('<?=$OPC->lnk(array('edit_id' => $edit_pid, 'mod' => 'acladmin', 'data[tbl]' => $OPC->get_var('page', 'table_name')));?>')"><?=e::o('pa_type_edit_page_access')?></a></span>
		</div>
	<? } ?>	

	<?php if ($ar_mod) {?>
		<div class="oos_pa_box_stretch">
		<ul class="tasklist"><li><?=e::o('pa_type_edit_module_rights')?></li>
		<ul>
		<?php
			foreach($mod_list as $mod) 
			{
				$config_exists = (is_error($MC->access_config($mod['modul_name']))) ? false : true;
				if ($config_exists) {
					$OPC->lnk_add('event','pa_ar');
					$OPC->lnk_add('data[mid]',(int)$mod['id']);
					$OPC->lnk_add('data[pid]',$edit_pid);
					$OPC->lnk($base_lnk);
					echo '<li><a href="#" onClick="popup(\''.$OPC->lnk($base_lnk).'\');">'.
							htmlspecialchars($mod['label']).'</a>';
					if (!isset($known_mods[$mod['id']])) echo ' <i>('.e::o('pa_type_non_set').')</i>';
					echo '</li>';
				}
				/*
				else {
					echo htmlspecialchars($mod_list->f('label')).' (keine Konfiguration)';
				}
				*/
			}
		?>
	</ul>
	</ul>
	</div>
	<?php } ?>
	
	<? if ($ar_new) { ?>
		<div class="oos_pa_box">
			<?=$OPC->show_icon("arrow_red")?><span><a href="<?=$OPC->lnk(array('mod' => 'page', 'event' => 'pa_enter', 'edit_pid' => $edit_pid, 'vid' => $vid, 'data[parent_pid]' => $edit_pid))?>"><?=e::o('pa_type_new_page')?></a></span>
		</div>
	<? } ?>

	<div class="oos_pa_box">
		<?=$OPC->show_icon("arrow_red")?><span><a href="<?=$OPC->lnk(array('pid'=>1,'mod' => 'page', 'event' => 'pa', 'vid' => $vid))?>"><?=e::o('pa_type_overview')?></a></span>
	</div>

        </div>
      </div>
    </div>
  </body>
</html>

<?$OPC->lang_page_end()?>

