
<?php
/**
@abstract 
@access
@author thomas.schindler@hundertelf.com
@category
@copyright
@example
@global  
@ignore 
@internal
@license CC-GNU-GPL
@name lang_file_editor
@package module
@see
@subpackage
@todo
@tutorial
@uses
@version 
*/

class lang_file_editor_view extends modView 
{
	var $mod_name = "lang_file_editor";
	var $vid;
		
	function lang_file_editor_view() {}

	function main($vid, $method_name) 
	{		

		$this->vid = $vid;
		$this->OPC->lnk_add("mod",$this->mod_name);
		$this->OPC->lnk_add("vid",$vid);

		switch(strtolower($method_name)) 
		{	
			case 'search':			$this->search();			break;
			case 'edit':			
			default:				$this->view_default();		break;
		}
	}

	function search()
	{
		$f = MC::create('form');
		$f->create('search');
		$f->set_values($this->data);
		$f->button('search','Search');
		$content = $f->show();
		
		if($r = $this->get_var('result'))
		{
			foreach($r as $mod => $list)
			{
				$content .= '<fieldset><legend>'.$mod.'</legend>';
				
				foreach($list as $k => $v)
				{
					$content .= '<a href="'.$this->OPC->lnk(array(
						'event' => 'edit',
						'data[page]' => $mod,
						'data[lang]' => $this->data['language'].".php",
						'data[ref]' => 'en.php',
						'data[key]' => $k,
					)).'">'.$k.'[<span style="font-size:8px;">'.$v.']</a><br/>';
				}
				
				$content .= '<br/><br/></fieldset>';
			}
		}
		
		$this->set_var('headline','Search');
		$this->set_var("content",&$content);
		$this->generate_view("main.tpl",true);
		return;
	}

	function view_default()
	{
		
		$exclude = array('acladmin','api_test','article','container','grid','groupswitch','lang_file_editor','lang_file_editor_','login','menubar','messaging','mod_admin','modmanager','mothermod','multiproject_mod_admin','navigation_1','objbrowser','oos_layouter','oos_statistics','page','remote_connection','search','syscheck','template','trashcan','usradmin','wysiwyg');

		$dir = CONF::inc_dir().'mods/';	
		$d = dir($dir);
		while(false !== ($e =$d->read()))
		{
			if(!preg_match("/\./",$e) && !in_array($e,$exclude))
			{
				if(is_dir($dir.$e))
				{
					$items[$e] = $e;
				}
			}
		}
		$f = MC::create('form');
		$f->use_layout('blue');
		$conf = array
		(
			'fields' => array
			(
				'page' => array
				(
					'label' => '',
					'cnf' => array
					(
						'type' => 'select',
						'items' => $items
					)
				)
			)
		);
		$f->init(1,$conf);
		$f->add_button('event_asdf','Choose module');
		$f->set_values($this->data);
		$content .= $f->show();
		
		if($this->data['page'])
		{
			$items = array();
			$d = dir($dir.$this->data['page'].'/language/');
			while(false !== ($e =$d->read()))
			{
				if(preg_match("/.php/",$e))
				{	
					$items[$e] = substr($e,0,-4);
					$last = $e;
				}
			}
			$f = MC::create('form');
			$f->use_layout('blue');
			$conf = array
			(
				'fields' => array
				(
					'lang' => array
					(
						'label' => '',
						'cnf' => array
						(
							'type' => 'select',
							'items' => $items
						)
					)
				)
			);
			/*
			if(sizeof($items) == 1)
			{
				$this->data['lang'] = $last;
			}
			*/
			if(sizeof($items)==0)
			{
				$content .= 'No lang file available!<br/>';
			}
			else
			{
				$f->init(1,$conf);
				$f->add_button('event_asdf','Set language');
				$f->set_values($this->data);
				$f->add_hidden('data[page]',$this->data['page']);
				$content .= $f->show();			
			}
			
			if($this->data['lang'])
			{
				$f = MC::create('form');
				$f->use_layout('blue');
				$conf = array
				(
					'fields' => array
					(
						'ref' => array
						(
							'label' => '',
							'cnf' => array
							(
								'type' => 'select',
								'items' => $items
							)
						),
						'untranslated_only' => array
						(
							'label' => 'Only show untranslated keys',
							'cnf' => array
							(
								'type' => 'checkbox',
								'items' => array(0=>''),
								'default' => 1,
							)
						),
					)
				);
				/*
				if(sizeof($items) == 1)
				{
					$this->data['lang'] = $last;
				}
				*/
				if(sizeof($items)==0)
				{
					$content .= 'No lang file available!<br/>';
				}
				else
				{
					$f->init(1,$conf);
					$f->add_button('event_asdf','Set reference language');
					$f->set_values($this->data);
					$f->add_hidden('data[page]',$this->data['page']);
					$f->add_hidden('data[lang]',$this->data['lang']);
					$content .= $f->show();			
				}
			}
			
		}
		
		if($this->data['lang'] && $this->data['page'] && $this->data['ref'])
		{	
			include($dir.$this->data['page'].'/language/'.$this->data['lang']);
			$language = $lang;
			if($this->data['ref'] != $this->data['lang'])
			{
				include($dir.$this->data['page'].'/language/'.$this->data['ref']);
				$ref = $lang;
			}
			$keys = array_keys($language);
			foreach($keys as $k => $key)
			{
				$keys[$key] = $key;
				unset($keys[$k]);
				if(isset($this->data['untranslated_only']) && $language[$key] != $key)
				{
					unset($keys[$key]);
					if($this->data['key'] == $key)
					{
						$this->data['key'] = $keys[$k+1];
					}
				}
			}
			$f = MC::create('form');
			$f->use_layout('blue');
			$conf = array
			(
				'fields' => array
				(
					'key' => array
					(
						'label' => '',
						'cnf' => array
						(
							'type' => 'select',
							'items' => $keys
						)
					)
				)
			);
			/*
			if(sizeof($items) == 1)
			{
				$this->data['lang'] = $last;
			}
			*/
			if(sizeof($items)==0)
			{
				$content .= 'No Keys!<br/>';
			}
			else
			{
				$f->init(1,$conf);
				$f->add_button('event_asdf','Select translation key');
				$f->set_values($this->data);
				$f->add_hidden('data[page]',$this->data['page']);
				$f->add_hidden('data[lang]',$this->data['lang']);
				$f->add_hidden('data[ref]',$this->data['ref']);
				if(isset($this->data['untranslated_only']))
				{
					$f->add_hidden('data[untranslated_only]',$this->data['untranslated_only']);
				}
				$content .= $f->show();			
			}			

			$content .= '<form action="'.$this->OPC->action().'" method="POST">';
			
			if($this->data['key'])
			{
				if(is_array($language[$this->data['key']]))
				{
					foreach($language[$this->data['key']] as $key => $version)
					{
						$content .= 'Version:'.$key.'<br/><textarea name="data['.$this->data['key'].']['.$key.']" style="width:500px;height:100px;" wrap="none" disabled="disabled">'.$ref[$this->data['key']][$key].'</textarea><br>';
						$content .= 'Version:'.$key.'<br/><textarea name="data['.$this->data['key'].']['.$key.']" style="width:500px;height:100px;" wrap="none">'.$version.'</textarea><br>';
					}
					$content .= '<br><input type="submit" name="event_save" value="Save">';
				}
				else
				{
					$content .= '<textarea name="data['.$this->data['key'].']" style="width:500px;height:100px;" wrap="none" disabled="disabled">'.$ref[$this->data['key']].'</textarea><br>';
					$content .= '<textarea name="data['.$this->data['key'].']" style="width:500px;height:100px;" wrap="none">'.$language[$this->data['key']].'</textarea><br><input type="submit" name="event_save" value="Save"><br>';
				}
			}
			
			if(isset($this->data['untranslated_only']))
			{
				$content .= $this->OPC->lnk_hidden(array(
					'data[untranslated_only]' => $this->data['untranslated_only'],
					'data[lang]' => $this->data['lang'],
					'data[page]' => $this->data['page'],
					'data[ref]' => $this->data['ref'],
					'data[key]' => $this->data['key'],
					'mod' => $this->mod_name
				));	
			}
			else
			{
				$content .= $this->OPC->lnk_hidden(array(
					'data[lang]' => $this->data['lang'],
					'data[page]' => $this->data['page'],
					'data[ref]' => $this->data['ref'],
					'data[key]' => $this->data['key'],
					'mod' => $this->mod_name
				));
			}
			

		}
		
		$this->set_var('headline','Lang file editor');
		$this->set_var("content",&$content);
		$this->generate_view("main.tpl",true);
		return;
	}
}
?>