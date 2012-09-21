<?$OPC->lang_page_start('page')?>
<fieldset>
	<legend>
		<?=e::o('pa_path_edit_page')?>
		<?php
			$path = $OPC->get_var('page', 'page_path');
			if (is_array($path)) {
				foreach($path as $node) {
					echo '\\' . $node['name'];
				}
			}
		?>
	</legend>
<?$OPC->lang_page_end()?>
