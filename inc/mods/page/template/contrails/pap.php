
<div class="navbar-inner">
  <ul class="nav">
	<?
	
	if(!@$CLIENT->usr['du'])
	{
		if($MC->access('mod_page', 'ar_mod'))
		{
			$lnk_rights_mod = $OPC->lnk(array(
				'mod' => 'page',
				'event' => 'pa_ar',
				'data[pid]' => $OPC->pid(),
			));
			echo '<li><a href="'.$lnk_rights_mod.'" class="popup">'.e::o('alt_rights_mod',null,null,'page').'</a></li>';
		}
	
		if($MC->access('mod_page', 'edit'))
		{
			$lnk_page_admin = $OPC->lnk(array(
				'mod' => 'page',
				'event' => 'pa_type',
				'edit_pid' => $OPC->pid()
			));
			echo '<li><a href="'.$lnk_page_admin.'" class="popup">'.e::o('alt_page_admin',null,null,'page').'</a></li>';
		}
	
		if($MC->access('mod_page', 'acl'))
		{
			$lnk_rights_page = $OPC->lnk(array(
				'mod'       => 'acladmin', 
				'event' => 'acl_list',
				'edit_id'   => (int)$OPC->get_pid(), 
				'data[tbl]' => 'mod_page',
			));
			echo '<li><a href="'.$lnk_rights_page.'" class="popup">'.e::o('alt_rights_page',null,null,'page').'</a></li>';
		}
	
		if($MC->access('mod_page', 'new'))
		{
			$lnk_page_new = $OPC->lnk(array(
				'mod' => 'page', 
				'event' => 'pa_enter', 
				'edit_pid' => (int)$OPC->get_pid(), 
				'data[parent_pid]' => (int)$OPC->get_pid()
			));
			echo '<li><a href="'.$lnk_page_new.'" class="popup">'.e::o('alt_page_new',null,null,'page').'</a></li>';
		}
	}
	//$OPC->call("usradmin","du","du")
?>

  </ul>
</div>