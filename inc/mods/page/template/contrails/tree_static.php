<?$OPC->lang_page_start('page')?>
<div style="width:180px; background-color:white; padding:4px;margin-left:2px;">

<?php

	$nodes = $OPC->get_var('page', 'tree');
	$pid   = $OPC->get_pid();
	$paths = $OPC->get_var('paths', 'tree');
	
	foreach($nodes as $node) {
		
		$class = ($node['id'] == $pid) ? 'menuact' : 'menu';
		
		$href  = $OPC->lnk(array('pid' => $node['id']),$paths[$node['id']]);
		
		echo str_repeat('&nbsp;', ($node['level']-1)*4);
		echo '<a class="'.$class.'" href="'. $href .'">'. $node['name'] .'</a><br>';
			
	}

?>

</div>
<hr>
<?$OPC->lang_page_end()?>
