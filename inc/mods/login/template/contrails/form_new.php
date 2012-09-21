<?
	$OPC->lang_page_start('login');
	$action = $OPC->action();

	
	$pid = $OPC->get_var('login','pid');
	$msg = $OPC->get_var('login', 'err_msg');
	$def_pages = CONF::default_pages();
	$register_link = '<a class="loginform_text2" href="'.$OPC->lnk(array('mod' => 'usradmin', 'event' => 'register', 'pid' => $def_pages['usr_register'])).'">'.e::o('t_f_register').'</a>';
	$retrieve_link = $OPC->lnk(
		array('mod'=>'login','event'=>'pwd_retrieve_start')
		).'" onClick="return popup_with_size(\''.$OPC->lnk(
		array('mod'=>'login','event'=>'pwd_retrieve_start')
		).'\',370,214)';
?>

			<form action="<?=$action?>" method="post" name="loginform">
					<nobr>
					<?=e::o('t_f_name')?> <input style="width:100px;" type="text" name="data[usr]" value="<?=$msg?>"> 
					 <?=e::o('t_f_password')?> <input style="width:100px;" type="password"  name="data[pwd]" >
					 <?
					 if(CONF::get("cookie") == true)
					 {
					 ?>
						 <input type="checkbox" name="data[cookie]" value="1" style="width:12px;height:12px;"><?=e::o('t_f_staylogged')?>
						 <input type="hidden" name="data[expire]" value="1209600">
					 <?
					 }
					 ?>
					 </nobr>

					<button><?=e::o('t_f_login')?></button><a href="<?=$retrieve_link?>" class="linkbutton"><?=e::o('t_f_help')?></a>

				<input type="hidden" name="mod" value="login">
				<input type="hidden" name="event" value="login">
				<input type="hidden" name="pid" value="<?=$pid?>">
				<input type="hidden" name="<?=$SESS->name?>" value="<?=$SESS->id?>">
				
			</form>
					
<?
	$OPC->lang_page_end();
?>
