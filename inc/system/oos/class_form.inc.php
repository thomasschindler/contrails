<?php
/**
*	
*	FORM creates forms with the help of configuration files
*	or arrays formed in the specified way. => please refer to the development wiki for more information
*
*	
*--22.08.2008
*AJAX support added (web/system/jsprototype.js)
*When creating a form, simply use $f->ajax() to activate the support
*On action side it is important to either send error (array from validator) or success  (string)
*
*->VIEW:
*		$f = MC::create('form');
*		$f->init(1,'test');
*		$f->use_layout('blue');
*		$f->button('test','test');
*		$f->button('test2','test2');
*		$f->ajax();
*		$content .= $f->show();
*<-
*
* ->ACTION:
*		$v = MC::create('validator','test');
*		if(!$v->is_valid($this->data))
*		{
*			$this->json_send(array('error'=>$v->get_error_fields()));
*		}
*		$this->json_send(array('success'=>'your data has been saved!'));
*<-
*
*-- 18.08.2008
*info support added 
*the cnf array in form config can now hold ['info']['field'] to display something (like a link)
*
*-- 18.08.2008 
*hide, show onclick support added
*
*checking checkbox with index 1 toggles the visibility of the field gift_amount
*
*		'gift' => array
*		(
*			'label' => e::o('campaign_target_gift_label'),
*			'cnf' => array
*			(
*				'type' => 'checkbox',
*				'items' => array(1=>null),
*				'show' => array(1=>array('gift_amount')),
*			)
*		),
*		'gift_amount' => array
*		(
*			'label' => e::o('campaign_target_gift_amount'),
*			'cnf' => array
*			(
*				'type' => 'input',
*				'hide' => true,
*			)
*		),
*
*/
class FORM {

	var $conf  = array();
	var $mod   = '';
	var $table = '';
	
	var $active_fields = array();
	
	var $form_prefix  = 'data';
	
	var $layout = array(
		'red' => array(
			'wrap_label'   => array('<td>', '</td>'),
			'wrap_field'   => array('<td>', '</td>'),
			'wrap_info'   => array('', ''),
			'wrap_row'     => array('<tr>', '</tr>'),
			'wrap_sep'     => array('<td colspan="2">', '</td>'),
			'wrap_success'     => array('<td colspan="2" style="font-weight:bold;color:green;">', '</td>'),	
			'wrap_all'     => array('<table border="0" class="oos_form_red">', '</table>'),
			'wrap_buttons' => array('<td colspan="2">', '</td>'),
			'wrap_error'   => array('<br><span class="errtxt" style="width:280px">', '</span>'),
			'wrap_descr'   => array('<br><i>', '</i>'),
		),
		'blue' => array(
			'wrap_label'   => array('<td>', '</td>'),
			'wrap_field'   => array('<td>', '</td>'),
			'wrap_info'   => array('', ''),
			'wrap_row'     => array('<tr>', '</tr>'),
			'wrap_sep'     => array('<td colspan="2">', '</td>'),
			'wrap_success'     => array('<td colspan="2" style="font-weight:bold;color:green;">', '</td>'),	
			'wrap_all'     => array('<table border="0" class="oos_form_blue">', '</table>'),
			'wrap_buttons' => array('<td colspan="2">', '</td>'),
			'wrap_error'   => array('<br><span class="errtxt">', '</span>'),
			'wrap_descr'   => array('<br><i>', '</i>'),		
		),
		
		'none' => array
		(
			'wrap_label'   => array('', ''),
			'wrap_field'   => array('', ''),
			'wrap_info'   => array('', ''),
			'wrap_row'     => array('', ''),
			'wrap_sep'     => array('', ''),
			'wrap_success'     => array('', ''),
			'wrap_all'     => array('', ''),
			'wrap_buttons' => array('', ''),
			'wrap_error'   => array('', ''),
			'wrap_descr'   => array('', ''),
		),		
	);
	
	var $wrap_label   = array('<td>', '</td>');
	var $wrap_field   = array('<td>', '</td>');
	var $wrap_row     = array('<tr>', '</tr>');
	var $wrap_sep     = array('<td colspan="2">', '</td>');
	var $wrap_all     = array('<table border="0" class="oos_form">', '</table>');
	var $wrap_buttons = array('<td colspan="2">', '</td>');
	var $wrap_error   = array('<br><span class="errtxt" style="width:280px">', '</span>');
	var $wrap_descr   = array('<br><i>', '</i>');

	
	var $use_layout = 'red'; // default layout
	/*
		standard:
		.oos_form
		{
			background: #e0b2b2;
			border: thin dotted #990000;
			margin-bottom:5px;
			width: 100%;
		}
		.oos_form_
		{
			background: #e0b2b2;
			border: thin dotted #990000;
			margin-bottom:5px;
			width: 100%;
		}		
	*/
	
	var $values = array();
	
	var $field_button = array();
	var $field_image = array();
	
	var $field_added  = array();
	
	var $error_fields = array();
	
	var $form_name;
	
	function FORM() 
	{
			$this->OPC = &OPC::singleton();
	}
	
	function error_set($name,$error_fields,$data)
	{
		$OPC = &OPC::singleton();
		$OPC->_form_error[$name]['error'] = $error_fields;
		$OPC->_form_error[$name]['data'] = $data;
	}
	
	function error($name)
	{
		if($this->OPC->_form_error[$name])
		{
			$this->set_error_fields($this->OPC->_form_error[$name]['error']);
			$this->set_values($this->OPC->_form_error[$name]['data']);
			return true;
		}
		return false;
	}
	
	/**
	*	
	*/
	function use_layout($layout=null)
	{
		if(!$layout)
		{
			return $this->use_layout;
		}
		if(is_array($layout))
		{
			$this->layout[111] = $layout;
			$layout = 111;
		}
		$this->use_layout = $layout;
	}
	/**
	*	replacement for init with more practical interface
	*	if mod is set, the module is used by MC to grab the form config
	*	usually init should be sufficient for cases where the form config is owned by the current module
	*	however, in scenarios that use  shared config files, "create" is of help.
	*/
	
	function create($form,$mod=null)
	{
		$this->mod = $mod;
		
		if ($form == '') return;
		
		if(is_array($form))
		{
			$this->conf  = $form;
			$this->active_fields = array_keys($this->conf['fields']);
			return;
		}
		
		$this->conf  = MC::table_config($form,$mod);
		$this->table = $form;
		$this->active_fields = array_keys($this->conf['fields']);
	}
	
/**
*	
*	initialize the form
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			module, definition - can be an array or the filename ( files reside in conf folder )
*	@return			void
*	@access			public
*/
	function init($mod, $table = '') 
	{
		$this->mod   = $mod;
		
		if ($table == '') return;
		
		if(is_array($table))
		{
			$this->conf  = $table;
			$this->active_fields = array_keys($this->conf['fields']);
			return;
		}
		
		$this->conf  = MC::table_config($table);
		$this->table = $table;
		$this->active_fields = array_keys($this->conf['fields']);
		
	}
	
	/**
	*	return the full form at once
	*/
	function show($name='formular',$hook=null,$anchor=null)
	{
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
	function start($form_name = "formular", $method = 'POST', $multipart = false,$pid=-1,$hook,$anchor) 
	{
		if($form_name == 'formular')
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
		if ($multipart) {
			$enc = ' enctype="multipart/form-data"';
		}
		else {
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
		return '<form action="'.$this->OPC->action($pid,$hook,$anchor).'" method="'.$method.'" id="'.$form_name.'" name="'.$form_name.'"'.$enc.'>';
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
	function end() {
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
		// set the ajax functions
		$ret = $this->ajax_start();
		
		$ret .= $this->layout[$this->use_layout()]['wrap_all'][0];
		
		// set the ajax success field
		$ret .= $this->ajax_success();
		
		
		$active_fields = (isset($this->conf['table']['form_order'])) ? explode(',', $this->conf['table']['form_order']) : $this->active_fields;
		
		$this->active_fields = &$active_fields;
		
		$row = $this->layout[$this->use_layout()]['wrap_row'][0];
		$rowcount = 0;
		
		foreach($active_fields as $field) 
		{
			// add an id to the opening tag of the row (applies to tr and div)
			if(preg_match("/<tr>/i",$row))
			{
				$this->layout[$this->use_layout()]['wrap_row'][0] = preg_replace("/<tr>/",'<tr id="row_'.$rowcount++.'">',$row);
			}
			elseif(preg_match("/<div>/",$row))
			{
				$this->layout[$this->use_layout()]['wrap_row'][0] = preg_replace("/<div>/",'<div id="row_'.$rowcount++.'">',$row);
			}
			
			if ($field == '_sep') 
			{
				$ret .= $this->layout[$this->use_layout()]['wrap_row'][0] . $this->layout[$this->use_layout()]['wrap_sep'][0] . '<hr>' . $this->layout[$this->use_layout()]['wrap_sep'][1] . $this->layout[$this->use_layout()]['wrap_row'][1];
			}
			else 
			{
				$data = $this->conf['fields'][$field];
				
				if (isset($data['cnf']['form']) && $data['cnf']['form'] === false) continue;
				
				
				
				$ret .= $this->layout[$this->use_layout()]['wrap_row'][0];
				
				$hide = ($data['cnf']['hide']?'style="display:none;"':'');
				

				if ($data['label'] AND $data['cnf']['type'] != 'separator' AND !$data['cnf']['inline']) 
				{     
					$ret .= $this->layout[$this->use_layout()]['wrap_label'][0] .'<span id="'.$field.'_label" '.$hide.'>'. $data['label'] . '</span>' . $this->layout[$this->use_layout()]['wrap_label'][1];
					if($data['cnf']['info']['label'])
					{
						$ret .= $this->layout[$this->use_layout()]['wrap_info'][0].$data['cnf']['info']['label'].$this->layout[$this->use_layout()]['wrap_info'][1];
					}
				}
				     
				if($data['cnf']['inline'])
				{
					$ret .= $this->layout[$this->use_layout()]['wrap_inline'][0].'<span id="'.$field.'_label" '.$hide.'>'. $data['label'] . '</span> ';					
				}
				else
				{
					$ret .= $this->layout[$this->use_layout()]['wrap_field'][0];
			 	}
				if($data['multiple'])
				{
					$o_field = $field;
					foreach($data['multiple'] as $k=>$cnf)
					{
						switch($cnf['type']) 
						{
							case 'input':			$ret .= $this->field_input($field, $cnf,$k); 		break;
							case 'mdb':			$ret .= $this->field_mdb($field, $cnf);			break;
							case 'link':			$ret .= $this->field_link($field, $cnf);			break;
							case 'password':	$ret .= $this->field_password($field, $cnf);	break;
							case 'textarea':	$ret .= $this->field_textarea($field, $cnf); 	break;
							case 'select':		$ret .= $this->field_select($field, $cnf,$k);		break;
							case 'radio':			$ret .= $this->field_radio($field, $cnf);			break;
							case 'checkbox':	$ret .= $this->field_checkbox($field, $cnf);	break;
							case 'date':			$ret .= $this->field_date($field, $cnf,$k);			break;
							case 'file':			$ret .= $this->field_file($field, $cnf);			break;
							case 'year':			$ret .= $this->field_year($field, $cnf);			break;
						}						
					}
				}
				else
				{
					$ret .= '<span id="'.$field.'_field" '.$hide.'>';
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
						case 'separator':		$ret .= $this->field_separator($data['label']); break;
						case 'captcha':		$ret .= $this->field_captcha($field,$data['cnf']); break;
					}
					$ret .= '</span>';
				}
				if($data['cnf']['inline'])
				{
					$ret .= $this->layout[$this->use_layout()]['wrap_inline'][1];					
				}
				else
				{
					$ret .= $this->layout[$this->use_layout()]['wrap_field'][1];
			 	}
				if($data['cnf']['info']['field'])
				{
					$ret .= $this->layout[$this->use_layout()]['wrap_info'][0].$data['cnf']['info']['field'].$this->layout[$this->use_layout()]['wrap_info'][1];
				}

				
				$ret .= $this->layout[$this->use_layout()]['wrap_row'][1];
			}

		}
		
		if(!$this->layout[$this->use_layout()]['allinone'])
		{	
			$ret .= $this->layout[$this->use_layout()]['wrap_all'][1];
			$ret .= $this->layout[$this->use_layout()]['wrap_all'][0];
		}
		$ret .= $this->layout[$this->use_layout()]['wrap_row'][0];
		$ret .= $this->layout[$this->use_layout()]['wrap_buttons'][0];
		
		foreach($this->field_button as $b) 
		{
			$ret .= '<button class="btn btn-primary" type="'.$this->ajax_button_type().'" name="'.$b[0].'" '.$b[2].' '.$this->ajax_button(substr($b[0],6)).'>'.$b[1].'</button>';
		}
		// this is the field we set the event via js for text submit of imagebuttons
		$ret .= '<input type="hidden" name="'.RESERVED.'event" value="'.RESERVED.'">';
		foreach($this->field_image as $b) 
		{
			$ev = explode("event_",$b[1]);
			#$ret .= '<div><input type="image"  src="'.$this->OPC->show_icon($b[0],true).'" '.$b[3].'><a href="#"  onClick="submit();"> '.$b[2].'</a></div>';
			$ret .= '<span>
					<input type="image" name="'.$b[1].'" src="'.$this->OPC->show_icon($b[0],true).'" '.$b[3].'>';
					if(!empty($b[2]))
					{
						$ret .= '<font> '.$b[2].'</font>';
					}
				$ret .'</span>';
		}
		
		if($this->field_reset)
		{
			$ret .= '<input type="reset">';
		}
		
		$ret .= $this->layout[$this->use_layout()]['wrap_buttons'][1];
		
		$ret .= $this->layout[$this->use_layout()]['wrap_row'][1];
		
		$ret .= $this->layout[$this->use_layout()]['wrap_all'][1];

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
		
		$ret .= $this->ajax_error($name);
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
		
		
		$ret .= $this->ajax_error($name);
		
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
		$ret .= $this->ajax_error($name);
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
		$ret .= $this->ajax_error($name);
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
		
		$ret .= $this->ajax_error($name);
		
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
		
		$ret .= $this->ajax_error($name);
		
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
		
		$ret .= $this->ajax_error($name);
		
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
		
		$ret .= $this->ajax_error($name);
		
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
*	textarea element
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
	function field_textarea_tiny_mce($name,$cnf)
	{       
		
		$width  = (isset($cnf['width']))  ? trim($cnf['width']) : '280';
		$height = (isset($cnf['height'])) ? trim($cnf['height']) : '120';
       	$width = preg_match("/%/",$width) ? $width : preg_match("/px/",$width) ? $width : $width."px";
        $height = preg_match("/%/",$height) ? $height : preg_match("/px/",$height) ? $height : $height."px";
       	$field_value = (isset($this->values[$name])) ? $this->values[$name] : (isset($cnf['value']) ? $cnf['value'] : '');
       	$field_name  = $this->form_prefix.'['.$name.']';

		$textarea = '<textarea style="text-align:left;width:'.$width.';height:'.$height.';" '.$ta.' name="'.$field_name.'" '.($cnf['readonly'] ? 'readonly' : '').'>'.stripslashes($field_value).'</textarea>';

       	if (isset($cnf['descr']))
       	{
			$textarea .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
       	}
		
        if(!$cnf['wysiwig'])
        {
			return $textarea.$this->ajax_error($name);
        }
         
		// try and add the content css from the project template folder
		$cs = '';
		if(is_file(CONF::web_dir()."/template/".CONF::project_name().'/css/edit.css'))
		{
			$cs = 'content_css : "/template/'.CONF::project_name().'/css/edit.css",';
		}
		
		$SESS = &SESS::singleton();
		
		$ret = '
		<!-- TinyMCE -->
		<script type="text/javascript" src="/system/js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="/system/js/tiny_mce/plugins/tinyBrowser/tb_tinymce.js.php?SESSION='.$SESS->id.'"></script>		
		
		<script type="text/javascript">

			tinyMCE.init({
				// General options
				mode : "textareas",
				theme : "advanced",
				plugins : "tinyBrowser,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

				file_browser_callback : "tinyBrowser", 
				
				// Theme options          

				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,

                /*
				plugin_simplebrowser_width : \'800\', //default
				plugin_simplebrowser_height : \'600\', //default
				plugin_simplebrowser_browselinkurl : \'/system/js/tiny_mce/plugins/simplebrowser/browser.html?Connector=connectors/php/connector.php\',
				plugin_simplebrowser_browseimageurl : \'/system/js/tiny_mce/plugins/simplebrowser/browser.html?Type=Image&Connector=connectors/php/connector.php\',
				plugin_simplebrowser_browseflashurl : \'/system/js/tiny_mce/plugins/simplebrowser/browser.html?Type=Flash&Connector=connectors/php/connector.php\',
				*/
				// Example content CSS (should be your site CSS)
				// content_css : "/template/oos/oos_app/css/main.css",
				
                '.$cs.'       
                 
				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "lists/template_list.js",
				external_link_list_url : "lists/link_list.js",
//				external_image_list_url : "lists/image_list.js",
				media_external_list_url : "lists/media_list.js",

				// Style formats
				style_formats : [
					{title : \'Bold text\', inline : \'b\'},
					{title : \'Red text\', inline : \'span\', styles : {color : \'#ff0000\'}},
					{title : \'Red header\', block : \'h1\', styles : {color : \'#ff0000\'}},
					{title : \'Example 1\', inline : \'span\', classes : \'example1\'},
					{title : \'Example 2\', inline : \'span\', classes : \'example2\'},
					{title : \'Table styles\'},
					{title : \'Table row 1\', selector : \'tr\', classes : \'tablerow1\'}
				],
                


				// Replace values for the template plugin
				template_replace_values : {
					username : "Some User",
					staffid : "991234"
				}
			});
		</script>
		<!-- /TinyMCE -->';
		      
		$ret .= $textarea;
		$ret .= $this->ajax_error($name);
		return $ret;
	}

	function field_textarea($name, $cnf) 
	{
		return $this->field_textarea_tiny_mce($name,$cnf);
		
		$width  = (isset($cnf['width']))  ? $cnf['width'] : '280';
		$height = (isset($cnf['height'])) ? $cnf['height'] : '120';

		$field_name  = $this->form_prefix.'['.$name.']';
//		$field_value = "\n".( (isset($this->values[$name])) ? $this->values[$name] : '');
		$field_value = (isset($this->values[$name])) ? $this->values[$name] : 
							(isset($cnf['value']) ? $cnf['value'] : '');
		$field_value = "\n".$field_value;
		
		$add_ret = '';
		
#MC::debug($cnf);
		
		// wysiwyg entscheidung
		
		if($cnf['wysiwig'])
		{
			$cnf['wysiwyg'] = $cnf['wysiwig'];
		}
		
		#if($cnf['wysiwig'] == 1 && CLIENT::remote('javascript') == 'true'){
		if($cnf['wysiwyg'] == 1)
		{
				$start = 'false';		

			// browserentscheid		
			$browser = @get_browser();

			if( $browser->browser == 'IE' && $browser->version >5){
				$start = 'ok';
			}
			if( $browser->browser == 'Netscape' && $browser->version >6){
				$start = 'ok';
			}
			if( $browser->browser == 'Mozilla' && $browser->version >1.4){
				$start = 'ok';
			}			
			/*if( CLIENT::remote('browser_name') == 'IE' && CLIENT::remote('browser_version') >5){
				$start = 'ok';
			}
			if( CLIENT::remote('browser_name') == 'Netscape' && CLIENT::remote('browser_version') >6){
				$start = 'ok';
			}
			if( CLIENT::remote('browser_name') == 'Mozilla' && CLIENT::remote('browser_version') >1.4){
				$start = 'ok';
			} */   

			// letzte bastion
			//coder_level

			$coder_level = '1';
			if(!empty($cnf['coder_level']))
			{
				$coder_level = $cnf['wysiwyg'];
			}

			// dont try this at home!!!
			$start = "ok";
			if($start == 'ok')
			{
				$pid = UTIL::get_post('pid');
				// wysiwyg javascript reinziehen				
				
				echo '<script type="text/javascript" src="system/js/wysiwyg/htmlarea.js.php?fontsize='.$cnf['fontsize'].'&fontname='.$cnf['fontname'].'&formatblock='.$cnf['formatblock'].'&textformat='.$cnf['textformat'].'&textformat_='.$cnf['textformat_'].'&paste='.$cnf['copy_pas'].'&redo_undo='.$cnf['redo_undo'].'&align='.$cnf['align'].'&list='.$cnf['list'].'&color='.$cnf['color'].'&hr='.$cnf['hr'].'&link='.$cnf['link'].'&image='.$cnf['image'].'&table='.$cnf['table'].'&html='.$cnf['html'].'&popeditor='.$cnf['popeditor'].'&help='.$cnf['help'].'&about='.$cnf['about'].'&'.$this->OPC->SESS->name.'='.$this->OPC->SESS->id.'&pid='.$pid.'"></script>';
				echo '<script type="text/javascript" src="system/js/wysiwyg/dialog.js"></script>';
				echo '<script type="text/javascript" src="system/js/wysiwyg/lang/'.e::current_lang().'.js"></script>';
				echo '<script type="text/javascript" src="system/js/wysiwyg/popupwin.js"></script>';
			
				echo '<style type="text/css">'.
						'@import url(htmlarea.css);'.
					  '</style>';		
					  
				$ta = 'id="'.md5($field_name).'"';
				
				if($browser->browser != 'IE' AND is_array($cnf['style']))
				{
					$add_ret .= UTIL::get_js("var config = new HTMLArea.Config; config.pageStyle = '".implode("'+'",$cnf['style'])."';");
					#$add_ret .= UTIL::get_js('window.setTimeout("HTMLArea.replaceAll(config)", 200);');				
					#field_name.
					$add_ret .= UTIL::get_js('window.setTimeout("HTMLArea.replace(\''.md5($field_name).'\',config)", 200);');				
				}
				else
				{
					$add_ret .= UTIL::get_js('window.setTimeout("HTMLArea.replaceAll()", 200);');
				}

			}	
		}
		else{
			$ta = '';
			$onload = '';
		}
		
		$width = preg_match("/%/",$width) ? $width : preg_match("/px/",$width) ? $width : $width."px";
		$height = preg_match("/%/",$height) ? $height : preg_match("/px/",$height) ? $height : $height."px";
		
		$ret .= '<textarea '.$ta.' name="'.$field_name.'" style="width:'.$width.'; height:'.$height.'" '.($cnf['readonly'] ? 'readonly' : '').'>'.
					stripslashes($field_value).'</textarea>'.$add_ret;

		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		$ret .= $this->ajax_error($name);
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
	function field_select($name, $cnf,$idx=null) {
		$width  = (isset($cnf['width'])) ? $cnf['width'] : '280';
		$size   = (isset($cnf['size']))  ? $cnf['size']  : '1';
		
		if(isset($idx))
		{
			$field_name = $this->form_prefix.'['.$name.']['.$idx.']' . ($cnf['multi'] ? '[]' : '');	
		}
		else
		{
			$field_name = $this->form_prefix.'['.$name.']' . ($cnf['multi'] ? '[]' : '');
		}

		// bei multi=true kriegen wir werte als array, sonst nur einen wert
		$field_value = (isset($this->values[$name])) ? $this->values[$name] : (isset($cnf['default']) ? explode(',', $cnf['default']) : array());

		if (!is_array($field_value)) $field_value = array($field_value=>$field_value);

//MC::debug($field_value);
		
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
		
		if (is_array($cnf['function_post'])) {
			$mc = &MC::singleton();
			$cnf['items'] = $mc->call_action(array(
					'mod' => $cnf['function_post'][0], 
					'event' => $cnf['function_post'][1]),
				$cnf['items']);
		}
		
//		$ret = '<select id="'.$name.'" name="'.$field_name.'" size="'.$size.'" style="width:'.$width.'px"'.
		$ret = '<select '.(isset($cnf['disabled'])?'disabled="disabled"':'').' id="'.$name.'" name="'.$field_name.'" size="'.$size.'" style="width:'.$width.'px"'.
				($cnf['multi'] ? 'multiple' : '') .'  '.($cnf['js'] ? $cnf['js'] : '') .'>';
		
		
//		$field_value = array($field_value[$this->internal_counter[md5($field_value)]++]);
		
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
//			$ret .= (in_array($key, $field_value)) ? ' selected' : '';
//			$ret .= ($fv[$val]==true) ? ' selected' : '';
			$ret .= ($fv[$key]==true) ? ' selected' : '';
			$ret .= '>'.($val).'</option>';
		}
		$ret .= '</select>';
		
		if (isset($cnf['descr'])) {
			$ret .= $this->layout[$this->use_layout()]['wrap_descr'][0] . $cnf['descr'] . $this->layout[$this->use_layout()]['wrap_descr'][1];
		}
		
		
		$ret .= $this->ajax_error($name);
		
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
	function add_button($event, $label, $params = '') 
	{
		$this->field_button[] = array($event, $label, $params);
	}
	
	function button($event,$label,$params='')
	{
		$this->field_button[] = array("event_".$event, $label, $params);
	}
	
	/**
	*	set ajax to true
	*	this makes the form to send the request via ajax (prototype)
	*	and receive the answer as json
	*/
	
	function ajax($track=false)
	{
		$this->_ajax = true;  
		$this->ajax_track = track;
	}
	/**
	*	adds ajax to the buttons
	*/
	function ajax_button($event)
	{
		if($this->_ajax)
		{
			return 'onClick="ajax_form_'.$this->form_name.'(\''.$event.'\');"';
		}		
	}
	/**
	*	makes sure the button doesn't submit
	*/
	function ajax_button_type()
	{
		if($this->_ajax)
		{
			return 'button';
		}
		return 'submit';
	}
	/**
	*	
	*/
	function ajax_start()
	{
		if($this->_ajax)
		{                        
			
//			MC::Debug($this->active_fields);
			
			foreach($this->active_fields as $f)
			{     
				if($this->conf['fields'][$f]['cnf']['type'] != 'separator')
				{
					$js_hide .=  '$(\''.$this->form_name.'_'.$f.'_error\').hide();';
				}
			}
			if($this->ajax_track)
			{
				$js = 'function ajax_form_'.$this->form_name.'(event){'.$js_hide.'new Ajax.Request(\'/'.$this->OPC->action($pid).'?event=\' + event + \'\',{parameters: $(\''.$this->form_name.'\').serialize(true),onSuccess: function(transport, json){if(json.error){var googletrack = \'/formerror/'.$this->form_name.'\';for (var key in json.error){value = eval("json.error." + key + ".text");eval("$(\''.$this->form_name.'_\' + key + \'_error\').innerHTML = value");eval("$(\''.$this->form_name.'_\' + key + \'_error\').show()");googletrack = googletrack + \'/\' + key;}track(googletrack);}else if(json.success){if(json.location){location.href=json.location;self.focus();return;}$(\''.$this->form_name.'_ajax_success\').innerHTML = json.success;$(\''.$this->form_name.'_ajax_success\').show();}else{alert(\'Error: The server sent an unknown response.\');}}});}';
			 }
			else
			{
				$js = 'function ajax_form_'.$this->form_name.'(event){'.$js_hide.'new Ajax.Request(\'/'.$this->OPC->action($pid).'?event=\' + event + \'\',{parameters: $(\''.$this->form_name.'\').serialize(true),onSuccess: function(transport, json){if(json.error){for (var key in json.error){value = eval("json.error." + key + ".text");eval("$(\''.$this->form_name.'_\' + key + \'_error\').innerHTML = value");eval("$(\''.$this->form_name.'_\' + key + \'_error\').show()");}}else if(json.success){if(json.location){location.href=json.location;self.focus();return;}$(\''.$this->form_name.'_ajax_success\').innerHTML = json.success;$(\''.$this->form_name.'_ajax_success\').show();}else{alert(\'Error: The server sent an unknown response.\');}}});}';
			}
			/*
			  
				var googletrack = '/form/'+billing_address';
				for (var key in json.error)
				{
					value = eval("json.error." + key + ".text");
					eval("$('billing_address_' + key + '_error').innerHTML = value");
					eval("$('billing_address_' + key + '_error').show()");           
					googletrack = googletrack + "/" + key;
				}
				track(googletrack);
			
			*/
			
			echo UTIL::get_js($js);
		}
	}
	function ajax_success()
	{
		if($this->_ajax)
		{
			return $this->layout[$this->use_layout()]['wrap_row'][0].$this->layout[$this->use_layout()]['wrap_success'][0].'<div id="'.$this->form_name.'_ajax_success" style="display:none;">test</div>'.$this->layout[$this->use_layout()]['wrap_sep'][1].$this->layout[$this->use_layout()]['wrap_success'][1];
		}
	}
	function ajax_error($name)
	{                           
		if($this->_ajax)
		{
				return $this->layout[$this->use_layout()]['wrap_error'][0] . '<div id="'.$this->form_name.'_'.$name.'_error" style="display:none;">' . $this->error_fields[$name]['text'] . '</div>' .  $this->layout[$this->use_layout()]['wrap_error'][1];				
		}
		if (isset($this->error_fields[$name])) 
		{
			if(isset($idx))
			{
				// only for the last entry
				if($idx == (sizeof($this->conf['fields'][$name]['multiple'])-1))
				{
					return $this->layout[$this->use_layout()]['wrap_error'][0] . $this->error_fields[$name]['text'] . $this->layout[$this->use_layout()]['wrap_error'][1];
				}
			}
			else
			{
				return $this->layout[$this->use_layout()]['wrap_error'][0] . $this->error_fields[$name]['text']  . $this->layout[$this->use_layout()]['wrap_error'][1];
			}
		}
	}
	/**
	*	add a reset button
	*/
	function add_reset() {
		$this->field_reset = true;
	}

/**
*	
*	add an image button to the form
*	
*	
*	@author 		thomas schindler
*	@date			27 02 2005
*	@version		1.0
*	@since			1.0
*	@param			event the button should call, the label of the button, params
*	@return			void
*	@access			public
*/
	function add_image($type="save_red",$event=null, $label=null, $params = '') {
		$this->field_image[] = array($type, $event, $label, $params);
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
	function add_hidden($key, $val) {
		$this->field_hidden[$key] = $val;
	}
/**
*	
*	add a hidden field in the beginning of the form
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
	function add_hidden_pre($key, $val) {
		$this->field_hidden[$this->form_prefix.'['.$key.']'] = $val;
	}
/**
*	
*	add a  field thats not part of the configuration so far
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name, value, conf
*	@return			void
*	@access			public
*/
	function add_field($key, $label, $conf){
		$this->conf['fields'][$key] = array(
			'label' => $label,
			'cnf'   => $conf,
		);
		$this->active_fields[] = $key;
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
	function set_error_fields($err) {
		if (!is_array($err)) $err = array();
		
		$this->error_fields = $err;
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
	function set_values($values) {
		if(!is_array($values)){
			return;
		}
		foreach($values as $key => $val) 
		{
			$this->values[$key] = $val;
		}
	}
/**
*	
*	remove a field
*	
*	
*	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum  <development@hundertelf.com>
*	@date			12 05 2004
*	@version		1.0
*	@since			1.0
*	@param			name
*	@return			void
*	@access			public
*/
	function remove_field($name) {
		foreach($this->active_fields as $k => $v) {
			if ($v == $name) {
				unset($this->active_fields[$k]);
				break;
			}
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
	function &singleton() {

		static $instance;
		
		if (!is_object($instance)) {
			$instance = new FORM();
		}

		return $instance;
	}
}

?>
