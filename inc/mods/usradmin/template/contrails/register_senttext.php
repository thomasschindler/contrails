<?
	$OPC->lang_page_start('usradmin');
	/**
	* template zum ausgeben des bestaetigungs-textes
	*/
	$usr = $OPC->get_var('usradmin', 'data');
?>	

		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=e::o('t_rst_headline')?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">
			<?=e::o('t_rst_msg',array(
				'%first%' => $usr['usr'],
			))?>
				</div>
			</div>
		</div>
<?
	$OPC->lang_page_end();
?>
