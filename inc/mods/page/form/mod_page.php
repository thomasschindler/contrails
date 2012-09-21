<?php
/**
*	@package	formconf
* konfigurationsdatei fuer db-tabellen
*/
$mod_tbl = 'mod_page';
$set = new NestedSet();
$set->set_table_name($mod_tbl);

$items[0] = '---';

foreach($set->getNodes(1) as $node) 
{
	$items[$node['id']] = str_repeat('&nbsp;', ($node['level']-1)*4).$node['name'];
}

				
$conf = array(
	'table' => array(
		'name'  => 'mod_page',
		'label' => e::o('tbl_pages',null,null,'page'),
		'sys_fields' => array(
			'deleted' => 'sys_trashcan',
			'created' => 'sys_date_created',
			'changed' => 'sys_date_changed',
		),
		'title' => 'name',					// welches feld/felder fuer 'kurzansichten' verwenden
												// mehrere mit komma getrennt
		'tree' => true,			// datensaetze sind in baum-struktur 
								// -> es muss immer folgende felder geben: lft, rgt, root_id
		
		'icon' => 'icon_mod_page.gif',
	),
	'fields' => array(
		// id und pid sind absolet/default
		
		'name' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => e::o('tbl_name',null,null,'page'),	// beschriftung
			'cnf' => array(
				'type'   => 'input',
				'empty'  => false,
				'min'    => 1,
				'max'    => 255,
				'err_empty' => 'Please give the page a name',
			),
		),
		  
         
		'url' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => 'URL (eg: some/thing.html)',	// beschriftung
			'cnf' => array
			(
				'type'   => 'input',
				'empty' => false,
				'unique' => true,
				'regex'  => '/^[a-zA-Z0-9]+[a-zA-Z0-9\/]+/i',
				'err_empty' => 'Please give the page a URL',
				'err_unique' => 'This URL is being used already',
				'err_format' => 'Please provide a correct URL'
			),
		),

		'title' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => e::o('tbl_title',null,null,'page'),	// beschriftung
			'cnf' => array(
				'type'   => 'input',
				'empty'  => false,
				'min'    => 1,
				'max'    => 255,
				'err_empty' => 'Please give the page a title',
			),
		),
		
		'description' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => e::o('tbl_descripition',null,null,'page'),	// beschriftung
			'cnf' => array(
				'type'   => 'input',
			),
		),
		
		'keywords' => array(			// db-name
			'access' => true,			// zugriffsbeschraenkbar (true|false)
			'label'  => e::o('tbl_keywords',null,null,'page'),	// beschriftung
			'cnf' => array(
				'type'   => 'input',
			),
		),
		
		'cookie_name' => array(			
			'access' => true,
			'label'  => 'Cookie name',	// beschriftung
			'cnf' => array(
				'type'   => 'input',
			),
		),		
		
		'cookie_value' => array(			
			'access' => true,
			'label'  => 'Cookie value',	// beschriftung
			'cnf' => array(
				'type'   => 'input',
			),
		),
		
		'cookie_lifetime' => array(			
			'access' => true,
			'label'  => 'Cookie lifetime (days)',	// beschriftung
			'cnf' => array(
				'type'   => 'input',
			),
		),
		
		'redirect_to' => array(
			'access' => true,
			'label'  => 'Redirect to',
			'cnf' => array(
				'type'  	=> 'select', 
				'empty' 	=> false,
				'size' 	=> 1,
				'multi'	=> false,
				'min'   => 1,
				'items' => $items,
				),
			),
			
		'template_name' => array(
			'access' => true,
			'label'  => e::o('tbl_template',null,null,'page'),
			'cnf' => array(
				'type'  	=> 'select', 
				'empty' 	=> false,
				'size' 	=> 1,
				'multi'	=> false,
				'min'   => 1,
				'relation' => array(
					'table' => 'mod_page_tpl',
					'key'   => 'tpl_name',
					'value' => 'label',
					'order' => 'ORDER BY label ASC',
					//'mm'    => 'mm_usradmin_usr_grp',
				),
				'err_empty' => e::o('tbl_template_err',null,null,'page'),
			),
		),
		/*
		'img_active' => array
		(
			'label' => 'active image GIF ONLY',
			'cnf' => array
			(
				'type' => 'file'
			)
		),
		'img_inactive' => array
		(
			'label' => 'inactive image',
			'cnf' => array
			(
				'type' => 'file'
			)
		)	
		*/
	),
		
);


?>
