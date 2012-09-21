<?
	$OPC->lang_page_start('usradmin');
	$vid = $OPC->var_get("vid");
	
?>
		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=e::o('t_search_headline')?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">


					<?
						echo $OPC->get_var('usradmin','content');
						
						$res = $OPC->get_var('usradmin','search_result');

						if($res['err']){
							echo "<br>".$res['err'];
						}
						elseif($res['res']){
							if($res['res']->nr() == 0)
							{
								echo "<br>".e::o('t_search_no_results');
							}
							else
							{
								echo e::o('t_search_num',array('%num%'=>$res['res']->nr()))."<br>";
								while($res['res']->next())
								{
									//echo "<a href='".$MC->call_action(array('event'=>'callback_get_link','mod'=>'usradmin'),array('type'=>'usr_home','uid'=>$res['res']->f('id'),'uname'=>$res['res']->f('usr')))."'>".$res['res']->f('usr')."</a><br>";
									echo '<a href="'.$OPC->lnk(array(
										'mod' => 'usradmin',
										'event' => 'usr_edit',
										'ids' => array($res['res']->f('id')),
										'vid' => $vid
									)).'">'.$res['res']->f('usr').'</a>';
									
									$lnk = $OPC->lnk(array(
										'data[usr]' => $res['res']->f('usr'),
										'data[pid]' => CONF::pid(),
										'mod' => 'page',
										'event' => 'pa_ar'
									));
									
									echo '<a style="padding-top:5px;" href="'.$lnk.'" target="_blank" onClick="return popup(\''.$lnk.'\',900);">'.$OPC->show_icon('rights_red').'</a>';
									
									echo '<br/>';
								}
							}
						}
					?>
				</div>
			</div>
		</div>
<?
	$OPC->lang_page_end();
?>
