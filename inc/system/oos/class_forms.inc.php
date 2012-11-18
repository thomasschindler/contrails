<?php

/*
<form class="form-horizontal">

  <div class="control-group">
    <label class="control-label" for="inputEmail">Email</label>
    <div class="controls">
      <input type="text" id="inputEmail" placeholder="Email">
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="inputPassword">Password</label>
    <div class="controls">
      <input type="password" id="inputPassword" placeholder="Password">
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
      <label class="checkbox">
        <input type="checkbox"> Remember me
      </label>
      <button type="submit" class="btn">Sign in</button>
    </div>
  </div>
</form>
*/

  

class FORMS
{

	var $conf  = array();
	var $mod   = '';
	var $table = '';
	var $active_fields = array();
	var $form_prefix  = 'data';
	var $values = array();
	var $field_button = array();
	var $field_image = array();
	var $field_added  = array();
	var $error_fields = array();
	var $form_name;
	
	function FORMS($form=null)
	{
		$this->OPC = &OPC::singleton();
		if($form)
		{
			$this->create($form);
		}
	}
	
	/**
	* validate the form
	*/
	function valid($data=array(),$mode='enter')
	{
		if(count($data) == 0)
		{
			$data = UTIL::get_post('data');
		}
		$v = new validator($this->conf);
		if(!$v->is_valid($data,$mode))
		{
			$this->set_error_fields($v->get_error_fields());
			return false;
		}
		return true;
	}

	/**
	*	replacement for init with more practical interface
	*	if mod is set, the module is used by MC to grab the form config
	*	usually init should be sufficient for cases where the form config is owned by the current module
	*	however, in scenarios that use  shared config files, "create" is of help.
	*/
	function create($form,$mod=null)
	{
		
		if ($form == '') return;
	
		if($mod == null)
		{
			if($this->OPC->current_mod())
			{
				$mod = $this->OPC->current_mod();
			}
			else
			{
				$mod = UTIL::get_post('mod');
			}
		}
		$this->mod = $mod;

		if(is_array($form))
		{
			$this->conf  = $form;
		}
		else
		{
			$this->conf  = MC::table_config($form,$mod);
		}
		
		$this->table = $form;
		$this->active_fields = array_keys($this->conf['fields']);
		if(isset($this->OPC->set_error_fields))
		{
			$this->error_fields = $this->OPC->set_error_fields;
		}
		// init the forms
		if($this->conf['buttons'])
		{
			foreach($this->conf['buttons'] as $event => $label)
			{
				$this->button($event,$label);
			}
		}
		// set the hidden fields
		foreach($this->conf['fields'] as $field => $data)
		{
			if($data['cnf']['type'] == 'hidden')
			{
				$this->hidden('data['.$field.']',$data['cnf']['value']);
				unset($this->conf['fields'][$field]);
			}
		}
		// add at least the current mod as a hidden field
		$this->hidden('mod',$this->OPC->current_mod());
	}
	
	/**
	*	return the full form at once
	*/
	function show($name='form',$hook=null,$anchor=null)
	{
		//@TODO: only do this if we don't have values present!!!
		$this->set_values($_POST['data']);
		return $this->start($name,'POST',false,null,$hook,$anchor).$this->fields().$this->end();
	}
	
	function form_name()
	{
		return $this->form_name;
	}
	
	/**
	*	
	*	start the form creation
	*	
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			name of the form, method post / get, multipart true / false
	*	@return			returns the header line of the form
	*	@access			public
	*/
	function start($form_name = "form", $method = 'POST', $multipart = false,$pid=-1,$hook,$anchor) 
	{
		if($form_name == 'form')
		{
			if(!$this->OPC->__class_form_start_idx)
			{
				$this->OPC->__class_form_start_idx = 0;
			}
			$form_name .= '_'.(string)$this->OPC->__class_form_start_idx++;
		}
		$this->form_name = $form_name;
		
		// mal schauen, ob wir datei-forms haben
		$enc = '';
		if ($multipart) 
		{
			$enc = ' enctype="multipart/form-data"';
		}
		else 
		{
			if (is_array($this->conf['fields'])) 
			{
				foreach($this->conf['fields'] as $field => $f) 
				{
					if ($f['cnf']['type'] == 'file') 
					{
						$enc = ' enctype="multipart/form-data"';
						break;
					}
				}
			}
		}
		return '<form action="'.$this->OPC->action($pid,$hook,$anchor).'" method="'.$method.'" id="'.$form_name.'" name="'.$form_name.'"'.$enc.'  class="form-horizontal"><legend>'.$this->conf['legend'].'</legend>';
	}

	/**
	*	
	*	end the form
	*	
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			void
	*	@return			end of the form including the hidden fields
	*	@access			public
	*/
	function end() 
	{
		$ret = '';
		
		// hidden felder und lnk_add felder aus opc
		// gleiche form-felder ueberschreiben lnk_add-felder
		$opc = &OPC::singleton();
		$hidden_fields = UTIL::oos_array_merge($opc->get_lnk_add(), $this->field_hidden);

		foreach($hidden_fields as $key => $val) 
		{

			if($this->values[$key])
			{
				$val = $this->values[$key];
			}

			$ret .= '<input type="hidden" id="'.htmlspecialchars($key).'" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($val).'">';
		}
		
		$ret .= '</form>';
		return $ret;
	}

	/**
	*	
	*	add the desired fields
	*	
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			void
	*	@return			return the fields
	*	@access			public
	*/
	function fields() 
	{
		
		$active_fields = (isset($this->conf['table']['form_order'])) ? explode(',', $this->conf['table']['form_order']) : $this->active_fields;
		$this->active_fields = &$active_fields;
		$rowcount = 0;
		
		foreach($active_fields as $field) 
		{
			$data = $this->conf['fields'][$field];
			
			if (isset($data['cnf']['form']) && $data['cnf']['form'] === false)
			{
				continue;
			} 

			$ret .= '<div class="control-group '.(isset($this->error_fields[$field])?' error':'').'"><label class="control-label" for="'.$field.'">'.$data['label'].'</label><div class="controls">';

			switch($data['cnf']['type']) 
			{
				case 'input':		$ret .= $this->field_input($field, $data['cnf']);		break;
				case 'mdb':			$ret .= $this->field_mdb($field, $data['cnf']);			break;
				case 'link':		$ret .= $this->field_link($field, $data['cnf']);		break;
				case 'password':	$ret .= $this->field_password($field, $data['cnf']);	break;
				case 'textarea':	$ret .= $this->field_textarea($field, $data['cnf']); 	break;
				case 'select':		$ret .= $this->field_select($field, $data['cnf']);		break;
				case 'radio':		$ret .= $this->field_radio($field, $data['cnf']);		break;
				case 'checkbox':	$ret .= $this->field_checkbox($field, $data['cnf']);	break;
				case 'date':		$ret .= $this->field_date($field, $data['cnf']);		break;
				case 'file':		$ret .= $this->field_file($field, $data['cnf']);		break;
				case 'year':		$ret .= $this->field_year($field, $data['cnf']);		break;
				case 'separator':	$ret .= $this->field_separator($data['label']); 		break;
				case 'captcha':		$ret .= $this->field_captcha($field,$data['cnf']); 		break;
			}

			// add the error text if applicable
			if($this->error_fields[$field])
			{
				$ret .= '<span class="help-inline">'.$this->error_fields[$field]['text'].'</span>';
			}

			$ret .= '</div></div>';
		}

		$ret .= '<div class="form-actions">';
		foreach($this->field_button as $b) 
		{
			$ret .= '<button name="event" value="'.$b[0].'" '.$b[2].' type="submit" class="btn'.(!isset($primary)?' btn-primary':'').'">'.$b[1].'</button>';
			$primary = true;
		}
		$ret .= '</div>';
		
		return $ret;
	}
	//
	/**
	*	captcha creates a captcha, adds a field and puts the captcha value in the session
	*/
	function field_captcha($name,$cnf)
	{
		$field_name  = $this->form_prefix.'['.$name.']';
		$c = new ASCII_Captcha();
		$data = $c->create($text);
		$ret .=  '<pre style="font-size:4px;line-height:4px;">'.$data.'</pre>' ;
		$this->OPC->SESS->set('form','captcha',$text);
		$style = 'width:50px';
		$ret .= '<input type="text" name="'.$field_name.'"  style="'.$style.'">';
		
		
		/*
		if (isset($this->error_fields[$name])) 
		{
			$ret .= $this->layout[$this->use_layout()]['wrap_error'][0] . $this->error_fields[$name]['text'] . $this->layout[$this->use_layout()]['wrap_error'][1];
		}
		*/
		return $ret;		
	}
	/**
	*	add something between fields
	*/
	function field_separator($label)
	{
		return $this->layout[$this->use_layout()]['wrap_row'][0] . $this->layout[$this->use_layout()]['wrap_sep'][0] . $label . $this->layout[$this->use_layout()]['wrap_sep'][1] . $this->layout[$this->use_layout()]['wrap_row'][1];
	}
/**
*	
*	file - get from mdb
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name, definition
*	@return			field 
*	@access			public
*/
	function field_mdb($name, $cnf) {
		$mc = &MC::singleton();
		return  $mc->call_action(array(
				'mod'=>'wysiwyg',
				'event' => 'mdb_get'
			),
			array(
				'prefix' => $cnf['prefix'],
				'form' => $this->form_name,
				'id' => $this->values[$name]
			)
		);
	}
/**
*	
*	file - get from mdb
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name, definition
*	@return			field 
*	@access			public
*/
	function field_link($name, $cnf) {
		$mc = &MC::singleton();
		return  $mc->call_action(array(
				'mod'=>'wysiwyg',
				'event' => 'link_get'
			),
			array(
				'prefix' => $cnf['prefix'],
				'form' => $this->form_name
			)
		);
	}
/**
*	
*	file - download element
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name, definition
*	@return			field 
*	@access			public
*/
	function field_file($name, $cnf) {
		$width  = (isset($cnf['width'])) ? $cnf['width'] : '280';
		
		//$field_name  = $this->form_prefix.'['.$name.']';
		// kommt eh in eigenes $_FILES array
		$field_name = $name;

		$ret = '<input type="file" name="'.$field_name.'" style="width:'.$width.'px" '.$cnf['js'].' >';
		
		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		
		
		
		/*
		if (isset($this->error_fields[$name])) 
		{
			$ret .= $this->layout[$this->use_layout()]['wrap_error'][0] . $this->error_fields[$name]['text'] . $this->layout[$this->use_layout()]['wrap_error'][1];
		}
		*/
		
		return $ret;
	}
/**
*	year element
*/
	function field_year($name, $cnf) {

		$field_name  = $this->form_prefix.'['.$name.']';
		
		// value ist array von tag,monat,jahr
		$field_value = (isset($this->values[$name])) ? $this->values[$name] : array();
		
		$value_year  = ($tmp = (int)$field_value['year']) ? $tmp : '';
		
		$ret = '';
		
		$ret .= '<input type="text" name="'.$field_name.'[year]" value="'.$value_year.'" size="4"> Jahr';
		
		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		/*
		if (isset($this->error_fields[$name])) 
		{
			$ret .= $this->layout[$this->use_layout()]['wrap_error'][0] . $this->error_fields[$name]['text'] . $this->layout[$this->use_layout()]['wrap_error'][1];
		}
		*/

		return $ret;
	}
/**
*	
*	date element
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name. definition
*	@return			fields
*	@access			public
*/
	function field_date($name, $cnf,$idx=-1) 
	{
		//_TODO, z.b. format (d Tag m Monat y Jahr), default (today), +uhrzeit (sek), mit auswahl heute,morgen, zeitraum ...
		$field_name  = $this->form_prefix.'['.$name.']';
		
		// value ist array von tag,monat,jahr
		$field_value = (isset($this->values[$name])) ? $this->values[$name] : array();
		
		$value_day   = ($tmp = $field_value['day']) ? $tmp : '';
		$value_month = ($tmp = $field_value['month']) ? $tmp : '';
		$value_year  = ($tmp = $field_value['year']) ? $tmp : '';
		
		$ret = '';

		if($idx >= 0)
		{
			$field_name .= '['.$idx.']';
		}
		
		$ret .= '<input type="text" name="'.$field_name.'[day]" value="'.$value_day.'" size="2">&nbsp;'.e::o("day",null,null,'asdfljllkjlkj').'&nbsp;';
		$ret .= '<input type="text" name="'.$field_name.'[month]" value="'.$value_month.'" size="2">&nbsp;'.e::o("month",null,null,"asdfljllkjlkj").'&nbsp;';
		$ret .= '<input type="text" name="'.$field_name.'[year]" value="'.$value_year.'" size="4">&nbsp;'.e::o("year",null,null,"asdfljllkjlkj").'';
		
		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		/*
		if (isset($this->error_fields[$name])) 
		{
			$ret .= $this->layout[$this->use_layout()]['wrap_error'][0] . $this->error_fields[$name]['text'] . $this->layout[$this->use_layout()]['wrap_error'][1];
		}
		*/

		return $ret;
	}
	function field_textarea($name, $cnf) 
	{
		$width  = (isset($cnf['width']))  ? $cnf['width'] : '400px';
		$height = (isset($cnf['height'])) ? $cnf['height'] : '300px';
		$field_name  = $this->form_prefix.'['.$name.']';
		$field_value = "\n".((isset($this->values[$name])) ? $this->values[$name] : (isset($cnf['value']) ? $cnf['value'] : ''));
		return '<textarea name="'.$field_name.'" style="width:'.$width.'; height:'.$height.'" '.($cnf['readonly'] ? 'readonly' : '').'>'.stripslashes($field_value).'</textarea>';
	}
/**
*	
*	input element
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name. definition
*	@return			fields
*	@access			public
*/
	function field_input($name, $cnf,$idx=null) 
	{
		$width  = (isset($cnf['width'])) ? $cnf['width'] : '280';
		
		if(isset($idx))
		{
			$field_name  = $this->form_prefix.'['.$name.']['.$idx.']';		
			if($cnf['cloneable'])
			{
				$field_value = (isset($this->values[$name][$idx][0])) ? $this->values[$name][$idx][0] : (isset($cnf['value']) ? $cnf['value'] : '');
			}
			else
			{
				$field_value = (isset($this->values[$name][$idx])) ? $this->values[$name][$idx] : (isset($cnf['value']) ? $cnf['value'] : '');
			}
		}
		else
		{
			$field_name  = $this->form_prefix.'['.$name.']';
			$field_value = (isset($this->values[$name])) ? $this->values[$name] : (isset($cnf['value']) ? $cnf['value'] : '');
			if($cnf['cloneable'])
			{
				$field_value = (isset($this->values[$name][0])) ? $this->values[$name][0] : (isset($cnf['value']) ? $cnf['value'] : '');
			}
			else
			{
				$field_value = (isset($this->values[$name])) ? $this->values[$name] : (isset($cnf['value']) ? $cnf['value'] : '');
			}
		}

		
			
		$add_ret = '';
		
		//-- haben wir objbrowser-plugin/auswahl
		if (($obj = $cnf['objbrowser']) && is_array($obj)) {
			$width -= 100;
			
			$function_name = 'objbrowser_callback_'.$name.'_'.$this->form_name;
			$js = "function $function_name(result) {
					var fo = document.forms['".$this->form_name."'];
					if (fo.elements['data[".$name."]'].value == '') {
						fo.elements['data[".$name."]'].value = result;
					}
					else {
						fo.elements['data[".$name."]'].value = fo.elements['data[".$name."]'].value + ',' + result;
					}
				}";
			$js = UTIL::get_js($js);
			
			$params = array(
				'mod'               => 'objbrowser',
				'data[callback]'    => $function_name,
				'data[return_type]' => 'csv',
			);
			$cnt = 0;
			foreach($obj as $table => $info) {
				$params['data[tbl]['.$cnt.']'] = $table;
				$params['data[key]['.$cnt.']'] = $info['key'];
				$params['data[value]['.$cnt.']'] = $info['value'];
				$cnt++;
			}
			$lnk_add = $this->OPC->lnk($params);
			//-- hinzufuegen link
			$add_ret .= '&nbsp;<a href="#" onClick="return popup(\''.$lnk_add.'\');" class="btnlnk" style="color:#000000">'.
						e::o('add',null,null,'form').'</a>'.$js;
		}
		
		if($cnf['style'])
		{
			$style = $cnf['style'];
		}
		else
		{
			$style = 'width:'.$width.'px';
		}
		
		//MC::Debug($this->values);
		
		if($cnf['cloneable'] == true)
		{
			$field_name .= '[]';
		}
		$ret = '<input type="text" id="'.$field_name.'" name="'.$field_name.'" value="'.htmlspecialchars($field_value).'" style="'.$style.'" '.($cnf['readonly'] ? 'readonly' : '').' '.$cnf['js'].' >'.$add_ret;


		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		
		
		return $ret;
	}
/**
*	
*	password element
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name. definition
*	@return			fields
*	@access			public
*/
	function field_password($name, $cnf) {
		$width  = (isset($cnf['width'])) ? $cnf['width'] : '280';
		
		$field_name  = $this->form_prefix.'['.$name.']';
		$field_value = (isset($this->values[$name])) ? $this->values[$name] : '';
		
		if($cnf['style'])
		{
			$style = $cnf['style'];
		}
		else
		{
			$style = 'width:'.$width.'px';
		}
		
		$ret = '<input type="password" name="'.$field_name.'" value="'.htmlspecialchars($field_value).'" style="'.$style.'">';
		
		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		
		
		return $ret;
	}
/**
*	
*	radio element
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name. definition
*	@return			fields
*	@access			public
*/
	function field_radio($name, $cnf) {
		$field_name  = $this->form_prefix.'['.$name.']';
		$field_value = (isset($this->values[$name])) ? $this->values[$name] : (isset($cnf['default']) ? $cnf['default'] : '');
		
		if (is_array($cnf['relation'])) 
		{
			$db = &DB::singleton();
			$rel = $cnf['relation'];
			$sql = 'SELECT '.$rel['key'].', '.$rel['value'].' FROM '.$rel['table'].' WHERE 1=1 '.$db->table_restriction($rel['table']);
			if ($rel['order']) $sql .= ' '.$rel['order'];
			$res = $db->query($sql);
			while($res->next()) 
			{
				$cnf['items'][$res->f($rel['key'])] = $res->f($rel['value']);
			}
			
		}
		
		$ret = '';
		foreach($cnf['items'] as $key => $val) {
			if($cnf['align'] == 'vertical')
			{
				$ret .= '<div class="field_radio">';
			}
			$ret .= '<input type="radio" name="'.$field_name.'" value="'.$key.'" id="id'.$name.$key.'"';
			if ($key == $field_value) $ret .= ' checked';
			$ret .= '> <label for="id'.$name.$key.'">'.$val.'</label>';
			
			if($cnf['align'] == 'vertical')
			{
				$ret .= '</div>';
			}
			else{
				$ret .= '&nbsp;';
			}
		}

		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		
		
		/*
		if (isset($this->error_fields[$name])) 
		{
			$ret .= $this->layout[$this->use_layout()]['wrap_error'][0] . $this->error_fields[$name]['text'] . $this->layout[$this->use_layout()]['wrap_error'][1];
		}
		*/
		return $ret;
	}
/**
*	
*	checkbox element
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name. definition
*	@return			fields
*	@access			public
*/
	function field_checkbox($name, $cnf) {
		// werte sind in array !

		$field_name  = $this->form_prefix.'['.$name.']';
		$field_value = (isset($this->values[$name])) ? $this->values[$name] : (isset($cnf['default']) ? $cnf['default'] : '');
		//$field_value = isset($cnf['default']) ? $cnf['default'] : '';
						
		if (!is_array($field_value)) $field_value = array($field_value);
		
		$ret = '';
		
		if (is_array($cnf['relation'])) {
			$db = &DB::singleton();
			$rel = $cnf['relation'];
			$sql = 'SELECT '.$rel['key'].', '.$rel['value'].' FROM '.$rel['table'].' WHERE 1=1 '.$db->table_restriction($rel['table']);

			if ($rel['order']) $sql .= ' '.$rel['order'];

			$res = $db->query($sql);
			while($res->next()) {
				$cnf['items'][$res->f($rel['key'])] = $res->f($rel['value']);
			}
		}
		

			
		foreach($cnf['items'] as $key => $val) 
		{
			if($cnf['onclick'])
			{
				$onclick = $cnf['onclick'];
			}
			if(is_array($cnf['show'][$key]))
			{
				foreach($cnf['show'][$key] as $show_id)
				{
					$onclick .= "$('".$show_id."_label').toggle();$('".$show_id."_field').toggle();";
				}
			}
			$ret .= '<input type="checkbox" name="'.$field_name.'[]" value="'.$key.'" id="id'.$name.$key.'"';
			if (in_array($key, $field_value)) $ret .= ' checked';
			$ret .= ' '.($onclick?' onClick="'.$onclick.'" ':'').'> <label for="id'.$name.$key.'">'.$val.'</label>';
			if($cnf['align'] == 'vertical'){
				$ret .= '<br>';
			}
			else{
				$ret .= '&nbsp;';
			}
		}

		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		
		
		/*
		if (isset($this->error_fields[$name])) 
		{
			$ret .= $this->layout[$this->use_layout()]['wrap_error'][0] . $this->error_fields[$name]['text'] . $this->layout[$this->use_layout()]['wrap_error'][1];
		}
		*/
		
		return $ret;
	}

	/**
	*	
	*	select element
	*	
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			name. definition
	*	@return			fields
	*	@access			public
	*/
	function field_select($name, $cnf,$idx=null) 
	{
		$size   = (isset($cnf['size']))  ? $cnf['size']  : '1';
		
		if(isset($idx))
		{
			$field_name = $this->form_prefix.'['.$name.']['.$idx.']' . ($cnf['multiple'] ? '[]' : '');	
		}
		else
		{
			$field_name = $this->form_prefix.'['.$name.']' . ($cnf['multiple'] ? '[]' : '');
		}

		// bei multiple=true kriegen wir werte als array, sonst nur einen wert
		$field_value = (isset($this->values[$name])) ? $this->values[$name] : (isset($cnf['default']) ? explode(',', $cnf['default']) : array());

		if (!is_array($field_value))
		{
			$field_value = array($field_value=>$field_value);
		} 

		if (is_array($cnf['relation'])) 
		{
			if($cnf['relation']['type'] == 'tree')
			{
				$db = &DB::singleton();
				$set = new NestedSet();
				$set->set_table_name($cnf['relation']['table']);
				//    function getNodes ($root=null, $fields = '*', $where = '')
				$nodes = $set->getNodes(
					($cnf['relation']['root'] ? $cnf['relation']['root'] : null ),
					$cnf['relation']['key'].','.$cnf['relation']['value'],
					$where.$db->table_restriction($cnf['relation']['table'])
				);
				foreach($nodes as $node)
				{
					$cnf['items'][$node[$cnf['relation']['key']]] = str_repeat("&nbsp;",($node['level']-1)*4).$node[$cnf['relation']['value']];
				}
			}
			else
			{
				$db = &DB::singleton();
				$rel = $cnf['relation'];
				$sql = 'SELECT '.$rel['key'].', '.$rel['value'].' FROM '.$rel['table'].' WHERE 1=1 '.(is_array($rel['where'])?" AND ".implode(" AND ",$rel['where']):'').' '.$db->table_restriction($rel['table']);
				if ($rel['order']) $sql .= ' '.$rel['order'];
				$res = $db->query($sql);
				while($res->next()) 
				{
					$cnf['items'][$res->f($rel['key'])] = $res->f($rel['value']);
				}
			}
		}
		
		if (!is_array($cnf['items'])) $cnf['items'] = array('' => '---');
		
		$ret = '<select '.(isset($cnf['disabled'])?'disabled="disabled"':'').' id="'.$name.'" name="'.$field_name.'" size="'.$size.'" '.($cnf['multiple'] ? 'multiple' : '') .'  '.($cnf['js'] ? $cnf['js'] : '') .'>';
		
		if(isset($idx) && isset($field_value[$idx]))
		{
			$field_value = array($field_value[$idx]);
		}
		
		foreach($field_value as $v)
		{
			$fv[$v] = true;
		}
		
		foreach($cnf['items'] as $key => $val) 
		{
			$ret .= '<option value="'.htmlspecialchars($key).'"';
			$ret .= ($fv[$key]==true) ? ' selected' : '';
			$ret .= '>'.($val).'</option>';
		}
		$ret .= '</select>';
		
		return $ret;
	}

	/**
	*	
	*	add a button to the form
	*	
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			event the button should call, the label of the button, params
	*	@return			void
	*	@access			public
	*/
	function button($event,$label,$params='')
	{
		$this->field_button[] = array($event, $label, $params);
	}

	/**
	*	
	*	add a hidden field
	*	
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			name, value
	*	@return			void
	*	@access			public
	*/
	function hidden($key, $val) 
	{
		$this->field_hidden[$key] = $val;
	}

	/**
	*	
	*	set the error fields on error
	*	
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			error text
	*	@return			void
	*	@access			public
	*/
	function set_error_fields($err) 
	{
		if (!is_array($err)) $err = array();
		$OPC = OPC::singleton();
		$OPC->set_error_fields = $err;
		$this->error_fields = $err;
		return;
	}

	/**
	*	
	*	set the values in the fields of the form
	*	
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			array of values
	*	@return			void
	*	@access			public
	*/
	function set_values($values) 
	{
		if(!is_array($values))
		{
			return;
		}
		foreach($values as $key => $val) 
		{
			$this->values[$key] = $val;
		}
	}

		
	/**
	*	
	*	used for perfomance optimization
	*	every creation of an object has to use this 
	*	
	*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
	*	@date			12 05 2004
	*	@version		1.0
	*	@since			1.0
	*	@param			void
	*	@return			an object of form
	*	@access			public
	*/
	function &singleton() 
	{
		static $instance;
		if (!is_object($instance)) 
		{
			$instance = new FORMS();
		}
		return $instance;
	}
}

?>
