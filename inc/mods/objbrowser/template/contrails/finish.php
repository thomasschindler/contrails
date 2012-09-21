<?
	$OPC->lang_page_start('objbrowser');

	/**
	* template zum generieren des JavaScriptes, das die ergebnisse an den opener zurueckliefert
	* das ergebnis ist ein string, das serialisierte array
	*/
	
	$data = UTIL::get_post('data');
	$tables = $data['tbl'];

	switch($SESS->get('objbrowser', 'return_type')) 
	{
		case 'js':
		
			$_key     = $this->SESS->get('objbrowser', 'key');
			$_value   = $this->SESS->get('objbrowser', 'value');
			
			$cnt = 0;
			
			$js = 'var ret = new Array();';
			
			foreach($OPC->get_var('objbrowser', 'choosen') as $table => $keys) 
			{
				$sql = 'SELECT '.$_key.' AS k , '.$_value.' AS v FROM '.$table.' WHERE '.$_key.' IN ('.implode(',', $keys).') ORDER BY v';
				$res = $DB->query($sql);
				$js .= 'var '.$table.' = new Array();';
				$len = 0;
				while($res->next()) 
				{
					$js .= $table.'['.$res->f('k').'] = \''.$res->f('v').'\';';
					$len++;
				}
				$js .= 'ret['.($cnt++).'] = '.$table.';';
			}

			$js_function = $SESS->get('objbrowser', 'callback');

			$js .= '
				


				if (parent.opener && parent.opener.'.$js_function.')
				{
					parent.opener.'.$js_function.'(ret);
				}
				self.close();

			';
			echo UTIL::get_js($js);
			
		
/*
			$js_function = $SESS->get('objbrowser', 'callback');
			$js = 'var ret = new Array();';
			$cnt = 0;
			foreach($OPC->get_var('objbrowser', 'choosen') as $table => $keys) 
			{
#				$js .= 'function obj_'.$table.'(){}';
#				$js .= 'var '.$table.' = new obj_'.$table.'();';
				
				$js .= 'var '.$table.' = new Array();';
				
				$len = 0;
				foreach($keys as $k => $v)
				{
#					$js .= $table.'.key_'.$k.' = "'.$v.'";';
					$js .= $table.'['.$len.'] = "'.$v.'";';
					$len++;
				}
#				$js .= $table.'.length = "'.$len.'";';
#				$js .= $table.'.table = "'.$table.'";';
				$js .= 'ret['.($cnt++).'] = '.$table.';';
			}
*/
		break;
		case 'csv':
			$_key     = $this->SESS->get('objbrowser', 'key');
			$_value   = $this->SESS->get('objbrowser', 'value');
			$_choosen = array();
			
			foreach($OPC->get_var('objbrowser', 'choosen') as $table => $keys) 
			{
				$sql = 'SELECT '.$_value[0].' AS v FROM '.$table.
						' WHERE '.$_key[0].' IN ('.implode(',', $keys).') ORDER BY v';
				$res = $DB->query($sql);
				while($res->next()) {
					$_choosen[] = stripslashes($res->f('v'));
				}
			}
			
			$result = implode(',', $_choosen);
			break;
		case 'ser':
		default:
			$result = serialize($OPC->get_var('objbrowser', 'choosen'));
	}
	
	$this->SESS->remove('objbrowser', 'return_type');
	
	if(!$js)
	{
	$js_function = $SESS->get('objbrowser', 'callback');
	if ($js_function == '') $js_function = 'alert';
?>

<script language="JavaScript">
	var result = '<?=addslashes($result)?>';
	parent.<?=$js_function?>(result);
	parent.$.fn.colorbox.close();
</script>
 
<?
	}
	$OPC->lang_page_end();
?>
