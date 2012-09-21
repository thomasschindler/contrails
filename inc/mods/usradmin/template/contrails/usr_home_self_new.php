<?
	$uid = $OPC->get_var('usradmin', 'uid');
	if ($uid != $CLIENT->usr['id']) return;
	$OPC->lang_page_start('usradmin');	
	$vid = $OPC->get_var('usradmin','vid');
	$profile = $OPC->lnk(array('mod' => 'usradmin', 'event' => 'usr_profile',"vid"=>$vid));
?>

		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span ><?=e::o('t_uhs_home_of',array('%username%'=>htmlspecialchars($CLIENT->usr['usr'])))?> </span>
			</div>
			
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">			
					<div class="create_online_button"><a href="<?=$profile?>"><?=$OPC->show_icon('online_red')?><span><?=e::o('t_uhs_your_data')?></a></span></div>
				</div>
				
				<div class="oos_mod_online_box">
					<?
						$OPC->call_view('messaging', 'messaging', '', true);
					?>
				</div>
			</div>
		</div>
<?
	$OPC->lang_page_end();
?>
