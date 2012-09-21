<?$OPC->lang_page_start('page')?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?=$OPC->get_var('page', 'title'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="/system/js/common.js" type="text/javascript"></script>
	<link href="/system/css/global.css" rel="stylesheet" type="text/css">
	<link href="/template/oos/css/oos_page.css" rel="stylesheet" type="text/css">
</head>

<body>

<div id="oos_pa_head">
	<span id="oos_pa_head_left">
		<?=e::o('pa_choose_page')?>
	</span>
	<span id="oos_pa_head_right">
		 <?=$OPC->show_icon("close_red")?><span><a href="#" onClick="return close_popup();"><?=e::o('pa_type_close')?></a></span>
	</span>
</div>
<div id="oos_pa_body">

<?php
	/**
	* template zur auswahl einer seite zum bearbeiten
	*/
	
	$icon = '<img src="'.$OPC->show_icon('page_red',true).'" alt="'.e::o('pa_alt_pages').'">';

	$vid = $OPC->get_var('page', 'vid');
	
	$nodes = $OPC->get_var('page', 'page_nodes');

	if (!is_array($nodes) || count($nodes) == 0) {
		echo '<div class="oos_pa_box">
			 '.$OPC->show_icon("warning_red").'<span>'.e::o('pa_no_pages_err').'</span>
		</div>
		</div>
		</body>
		</html>';
		$OPC->lang_page_end();
		return;
	}

	if ($msg = trim($OPC->get_var('page', 'msg'))) {
		echo '<div class="oos_pa_box">
			 '.$OPC->show_icon("info_red").'<span>'.$msg.'</span>
		</div>';
	}
?>

<div class="oos_pa_box">
	<?=$OPC->show_icon("new_page_red")?><span><a href="<?=$OPC->lnk(array('mod' => 'page', 'event' => 'pa_enter', 'vid' => $vid))?>"><?=e::o('pa_create_new_page')?></a></span>
</div>

<table border="0" class="adm_table_red">
<tr>
	<th width="16"><?=$icon?></th>
	<th width="260"><?=e::o('pa_list_name')?></th>
	<th width="40"><?=e::o('pa_list_id')?></th>
	<th><?=e::o('pa_list_ltrt')?></th>
	<th><?=e::o('pa_list_parent_id')?></th>
	<th width="240"><?=e::o('pa_list_template')?></th>
</tr>

<?php
 
 	$parent_stack = array();
	
	$root = 1;
	array_push($parent_stack, 0);
//	array_push($parent_stack, $root);
	$old_level = 1;
	$old_id = 0;
	foreach($nodes as $node) {
		$pid      = $node['id'];
		$edit_params = array('edit_pid' => $pid, 'mod' => 'page', 'event' => 'pa_type');
		
		if ($node['level'] > $old_level) {
			array_push($parent_stack, $old_id);
		}
		elseif($node['level'] < $old_level) {
			for($i=$old_level; $i>$node['level'];$i--) {
				array_pop($parent_stack);
			}
		}
		$old_id = $node['id'];
		$old_level = $node['level'];
		
		$parent = $parent_stack[count($parent_stack)-1];
		
		$sql = 'UPDATE mod_page SET parent_id='.$parent.' WHERE id='.$node['id'];
		#$DB->query($sql);
		
		$css_class = $counter++%2==0 ? 'even' : 'odd';
		
		echo '<tr class="adm_table_red_'.$css_class.'">';
		echo '<td>'.$OPC->create_icon('edit', 'pa_type', $edit_params).'</td>';
		echo 
			'<td>'.str_repeat('&nbsp;', ($node['level']-1)*4).
					htmlspecialchars($node['name']).
			'</td>'.
			'<td align="right">'.$pid.'</td>'.
			'<td align="right">'.$node['lft'].'-'.$node['rgt'].'</td>'.
			'<td align="right">'.$node['parent_id'].'</td>'.
			'<td>'.htmlspecialchars($node['template_name']).'</td>'.
		'</tr>';
	}
?>


</table>


</div>
</body>
</html>
<?$OPC->lang_page_end()?>
