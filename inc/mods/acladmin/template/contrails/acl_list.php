<?
	$OPC->lang_page_start('acladmin');
	$headline = $OPC->get_var('page', 'title');
	$headline = strlen($headline) == 0 ? e::o('tpl_access_rights') : "";
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
        <div class="span12">
        	<h1><?=$headline?></h1>
        	<p>
				<?php
					if ($msg = trim($OPC->get_var('acladmin', 'msg'))) 
					{
						echo '<span class="label label-info">'.$msg.'</span><br/>';
					}

					echo $OPC->get_var('acladmin', 'page_select');
					/*
					* zugriffsrechte fuer benutzer fuer eine seiten
					*/
					$edit_id    = $OPC->get_var('acladmin', 'edit_id');
					$vid        = $OPC->get_var('acladmin', 'vid');
					$acl_conf   = $OPC->get_var('acladmin', 'acl_conf');
					$table_conf = $OPC->get_var('acladmin', 'table_conf');
					$usr_ar = $OPC->get_var('acladmin', 'usr_ar');
					$grp_ar = $OPC->get_var('acladmin', 'grp_ar');


					//- forumlar
					$form = $MC->create('form');
					$form->init('page');
					$form->add_hidden('edit_id',  $edit_id);
					$form->add_hidden('mod', 'acladmin');
					$form->add_hidden('vid',       $vid);
					$form->add_hidden('event',     '');
					$form->add_hidden('data[action]', '');		// infos fuer hinzufuegen/loeschen etc.
					$form->add_hidden('data[tbl]', $OPC->get_var('acladmin', 'tbl'));
					echo $form->start('arform');




?>



<table class="table table-striped">
<?php

	$data = $OPC->get_var('acladmin', 'data');


	$max = count($acl_conf['rights']);
	
	//-- horizontal -> zugriffsrechte
	echo '<tr>';
	echo '<th>&nbsp;</th>';	// fuer grp/user
	echo '<th>'.e::o('tpl_delete').'</th>';	// for editthis checkbox 
	echo '<th style="background-color:#66c266;">'.e::o('tpl_change').'</th>';	// for editthis checkbox 
	
	foreach($acl_conf['labels'] as $ar => $label) 
	{
		echo '<th>'.htmlspecialchars($label).'</th>';
	}
	echo '</tr>';

	//-- vertikal -> gruppen/benutzer
	$no_rights = true;
	
	
	
	//-- gruppen und benutzer (und deren rechte)
	$cnt = 0;
	foreach(array('grp', 'usr') as $key) 
	{



		if (!is_array($data['info'][$key])) continue;
		
		foreach($data['info'][$key] as $id => $info) 
		{

			$no_rights = false;
			
			// vorhandene rechte als hidden-fields
			echo '<input type="hidden" name="data[info]['.$key.']['.$id.'][name]" value="'.$info['name'].'">';
			echo '<input type="hidden" name="data[info]['.$key.']['.$id.'][ar]" value="'.$info['ar'].'">';
			
			echo '<tr>';

			echo '<th style="text-align:left"><i class="icon-user"></i>&nbsp;';
			if ( ($key == 'usr' && $id == $CLIENT->usr['id']) || ($key == 'grp' && isset($CLIENT->usr['groups'][$id])) ) 
			{
				echo '<u>'. $info['name'] .'</u>';
			}
			else 
			{
				echo $info['name'];
			}
			echo '</th>';

			echo '<th>
				<input type="checkbox" name="data[info]['.$key.']['.$id.'][del]" value="1">
			</th>';
			
			echo '<th  style="background-color:#66c266;">
				<input type="checkbox" name="data[info]['.$key.']['.$id.'][chg]" value="1">
			</th>';
			
			foreach($acl_conf['rights'] as $name => $number) 
			{
				echo '<td align="center">'.
						'<input type="checkbox" name="data[ar]['.$key.']['.$id.']['.$name.']" value="'.$number.'"'.
						(($info['ar'] & $number) ? ' checked' : '').
						'>'.
						'</td>';
			}
			echo '</tr>';
			
			$cnt++;		
		}
	}
	
	if ($no_rights) 
	{
		echo '<tr><th colspan="'.($max+2).'" align="center">'.e::o('tpl_none_chosen').'</th></tr>';
	}
	
	
	$params = array(
		'event' => 'admin_list',
		'pid' => '1', #161
		'mod' => 'objbrowser',
		'data[tbl][0]' => 'mod_usradmin_usr',
		'data[tbl][1]' => 'mod_usradmin_grp',
		'data[callback]' => 'page_objbrowser_callback',
		'data[return_type]' => 'ser',
	);
	
	$lnk_add = $OPC->lnk($params);
	
?>

</table>

	<a href="<?=$lnk_add?>" id="add_user" class="btn popup">Add a user or a group</a>


<script language="JavaScript">
	var objbrowser_test = "booo";
<!--
	function page_objbrowser_callback(result) {
		var fo = document.forms['arform'];
		fo.elements['data[action]'].value = result;
		fo.elements['event'].value     = 'acl_add';
		fo.submit();
	}
	function page_del_ar(result) {
		var fo = document.forms['arform'];
		fo.elements['data[action]'].value = result;
		fo.elements['event'].value     = 'acl_del';
		fo.submit();		
	}
//-->
</script>
<?
/*
#	<td colspan="2"><input type="button" name="_save" value="<?=e::o('tpl_save')?>" onClick="this.form.event.value='acl_save';this.form.submit();"></td>
*/
?>

<button class="btn btn-primary" onClick="this.form.event.value='acl_save';this.form.submit();"><?=e::o('tpl_save')?></button>

	<?php
		// anwenden auf 'unter-datensaetze' nur bei trees
		if ($OPC->get_var('acladmin', 'is_tree')) {
	?>
	<select name="data[inherit]" size="1">
		<?php
			foreach($OPC->get_var('acladmin', 'select_array') as $key => $val) 
			{
				echo '<option value="'.$key.'">'.htmlspecialchars($val).'</option>';
			}
		?>
		</select>
	
	<?php
		}
	?>

<?php
	echo $form->end();
?>
			</p>
        </div>
      </div>
    </div>
  </body>
</html>

<?$OPC->lang_page_end()?>
