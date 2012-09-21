<?
	$OPC->lang_page_start('objbrowser');
?>
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
			<div class="span6">
				<?
				$filter = $OPC->get_var('objbrowser', 'filter');
				?>

				<form action="<?=$OPC->action()?>" method="POST" style="padding-left:10px;padding-top:5px;">
					<input type="text" name="data[filter]" value="<?=$filter?>">
					<input type="submit" name="set_filter" value="Find">
					<?=$OPC->lnk_hidden($OPC->get_var('objbrowser', 'base_lnk'))?>
				</form>

				<?php

					$data = $OPC->get_var('objbrowser', 'data');
					$base_lnk = $OPC->get_var('objbrowser', 'base_lnk');
					$choosen = $SESS->get('objbrowser', 'choosen');

					foreach($data as $table => $info) 
					{
						$open = (is_array($info['header'])) ? true : false;
						
						if ($open) {
							$params = array_merge(array('event' => 'close', 'data[close]' => urlencode($table)), $base_lnk);
							$lnk = '<a href="'.$OPC->lnk($params).'"><i class="icon-minus-sign"></i></a>';
						}
						else {
							$params = array_merge(array('event' => 'open', 'data[open]' => urlencode($table)), $base_lnk);
							$lnk = '<a href="'.$OPC->lnk($params).'"><i class="icon-plus-sign"></i></a>';
						}
						
						#echo '<span style="background-color:#BBBBBB;width:320px;border:1px solid white">'.$lnk.'<b> '.$info['label'].'</b> ('.$table.')</span><br>';
						
						echo '<div class="oos_pa_box">
							'.$lnk.'<span>'.$info['label'].'</span>
						</div>';
						
						if (!$open) continue;
						
						echo '<table class="table table-striped">';
						
						echo '<tr><th>'.$info['icon'].'</th>';
						foreach($info['header'] as $key => $val) {
							echo '<th>'.$val.'</th>';
						}
						echo '</tr>';
						
						$cnt = 0;
						foreach($info['data'] as $row) {
							$css_class = $counter++%2==0 ? 'even' : 'odd';
							echo '<tr>';
							echo '<th>';
							if (isset($choosen[$table][$row['id']])) {
								echo '&nbsp;';
							}
							else {
								$params = array_merge(array('event' => 'add', 'data[add_id]' => $row['id'], 'data[add_table]' => urlencode($table)), $base_lnk);
								echo '<a href="'.$OPC->lnk($params).'"><i class="icon-plus-sign"></i></a>';
							}
							echo '</th>';
							foreach($info['header'] as $key => $val) {
								echo '<td>'.$row[$key].'</td>';
							}
							echo '</tr>';
						}
								
						$up = null;
						$down = null;
						
						if($info['nav']['up'] !== null)
						{
							$arr = array_merge($base_lnk,array('filter'=>$filter,'data[offset]['.$table.']'=>$info['nav']['up']));
							$up = $OPC->create_icon('online_red','',$arr);
						}
						if($info['nav']['down'])
						{
							$arr = array_merge($base_lnk,array('filter'=>$filter,'data[offset]['.$table.']'=>$info['nav']['down']));
							$down = $OPC->create_icon('arrow_red','',$arr);
						}
						
						echo '<tr><td colspan="'.(sizeof($info['header'])+1).'">'.$up.$down.'</td></tr>';
						
						echo '</table>';
						
					}
					
				?>
			</div>

			<div class="span6">
				<span><?=e::o('chosen_objects')?></span>

				<?php

					$choosen = $SESS->get('objbrowser', 'choosen');
					if (!is_array($choosen) || count($choosen) == 0) {
						echo '<div class="oos_pa_box">
							<span>'.e::o('no_object_chosen').'</span>
						</div>';
					}
					else {
							foreach($choosen as $tbl => $obj) {
								$conf = $MC->table_config($tbl);
								$tbl = $conf['table']['name'];
								
								//$base_lnk .= '&data[tbl][]='.$tbl;	// ist schon drin, gell?
								
								$data[$tbl] = array();
								$data[$tbl]['label'] = $conf['table']['label'];
								
								
								$header = explode(',', $conf['table']['title']);
								
								//-- aus db lesen
								$sql = 'SELECT id, '.$conf['table']['title'].' FROM ' . $tbl . ' WHERE id IN ('.implode(',', $obj).') '.
											$this->DB->table_restriction($tbl);
								$res = $DB->query($sql);
								

								
								echo '<table class="table table-striped">';
								//-- header bestimmen
								$data[$tbl]['header'] = array();
								echo '<tr><th>'.$icon.'</th>';
								foreach($header as $field) {
									echo '<th>'.$conf['fields'][$field]['label'].'</th>';
								}
								echo '</tr>';
								
								//-- daten eintragen
								$data[$tbl]['data'] = array();
								$cnt = 0;
								while($res->next()) {
									$css_class = $cnt++%2==0 ? 'even' : 'odd';
									echo '<tr class="adm_table_red_'.$css_class.'">';
									$params = array_merge(array('event' => 'remove', 'data[rem_id]' => $res->f('id'), 'data[rem_table]' => urlencode($tbl)), $base_lnk);
									echo '<th><a href="'.$OPC->lnk($params).'"><i class="icon-minus-sign"></i></a></th>';
									foreach($header as $field) {
										echo '<td>'.htmlspecialchars($res->f($field)).'</td>';
									}

									echo '</tr>';
								}
								echo '</table>';
							}
					}
					
				?>



				<?php
					$form = MC::create('form');
					
					$form->init('objbrowser');
					
					if (count($choosen) > 0) {
						$form->add_button('event_finish', e::o('choose'));
						$form->add_button('event_clear',  e::o('empty'));
					}
					$form->add_button('event_cancel',  e::o('cancel'), 'onClick="window.close();"');
					
					$form->add_hidden('mod', 'objbrowser');
					
					$tables = $OPC->get_var('objbrowser', 'tables');
					foreach($tables as $table) {
						$form->add_hidden('data[tbl]['.$table.']', $table);
					}
							
					echo $form->start();
					echo $form->fields();
					echo $form->end();
					
				?>
			</div>
		</div>
	</div>
</body>
</html>
<?
	$OPC->lang_page_end();
?>
