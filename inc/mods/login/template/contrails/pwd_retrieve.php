<?
	$OPC->lang_page_start('login');
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
	<head>
		<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
		<title>
			<?=e::o('t_p_title')?>
		</title>
		<!--get all necessary styles-->
                <?
                        if(is_file(CONF::web_dir()."/template/oos/css/oos_main.".CONF::project_name().".css"))
                        {
                                echo '<link href="/template/oos/css/oos_main.'.CONF::project_name().'.css" type=text/css rel=stylesheet>';
                        }
                        else
                        {
                                echo '<link href="/template/oos/css/oos_main.css" type=text/css rel=stylesheet>';
                        }
                ?>
		<link href="template/oos/css/oos_login.css" type=text/css rel=stylesheet>
		<link href="template/oos/css/oos_navigation.css" type=text/css rel=stylesheet>
		<link href="system/css/global.css" rel="stylesheet" type="text/css">
		<!--get all necessary javascript-->
		<script src="system/js/common.js" type="text/javascript"></script>
		<script src="system/js/drag.js" type="text/javascript"></script>
	</head>
	<body>

			
			
		<div class="oos_mod_online">
			<div class="oos_mod_online_head">
				<span><?=e::o('t_p_headline')?></span>
			</div>
			<div class="oos_mod_online_body">
				<div class="oos_mod_online_box">

		<?
			$msg =  $OPC->get_var('login','msg');
			$form =  $OPC->get_var('login','form');
			
		if($form){
			echo "<table>
				<tr>
					<td colspan='2'>
						$msg
					</td>
				</tr>
				<form action='".$OPC->action()."' method='post' class='loginform_text'>
				<tr>
					<td>
						".e::o('t_p_username')." 
				    </td>
                    <td align='left'>
						<input size='17' type='text' name='data[uname]' class='loginform'>
					</td>
				</tr>
				<tr>
					<td>
						".e::o('t_p_email')." 
                   </td>
                    <td  align='left'>
						<input size='17' type='text' name='data[email]' class='loginform'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' name='event_find_pwd' value='".e::o('t_p_send')." ' class='loginform'>
					</td>
				</tr>
					".$OPC->lnk_hidden(array('mod'=>'login'))."
				</form>
			</table>";
		}
		else{
			echo "<table>
				<tr>
					<td>
						$msg
					</td>
				</tr>
			</table>";
		}
	?>
				</div>
			</div>
		</div>			
			

	</body>
</html>

<?
	$OPC->lang_page_end();
?>
