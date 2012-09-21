<?php

/**
* Klasse zum generieren von Formularen 
*
* $maske enthält das array mit den formularfeldern   
* $table_data enthält die formatierungdaten der tabelle 
*
* wird von einem array aufgerufen 
*
* Beispiel Tabelle:   
* # $table_act: width|border | cellpadding | cellspacing | tabellenheadline | th_align | th_color | table_bg_color    
*
*
* Beispiel Formularfelder->maske   
*	# $maske	 : input | type | name | value | class_css | bezeichnung | optional (selected) 
*
* 
* WYSIWYG-EDITOR Beispiel: es kann nur eines in einem formular vorkommen  
*  # input | id (ta immer!) |name  | value | class_css | bezeichnung | optional (wysiwyg)  
*
*@deprecated DO NOT USE!
*@version	0.1.0 14.01.04
*@author	Oli Blum <development@hundertelf.com>
*@package	util
*@access	public
*/

	class form_handle {
	
		var $action;
		
		// erzeugt formulare und gestaltet diese
		function form($maske,$table_data,$form_action) {
		
			$this->action = $form_action;
			

			//tabelle und form wird geschrieben
			$table_act='<form action="'.$this->action.'" method="post">';
			$table_act.='<table width="'.$table_data[width].'" border="'.$table_data[border].'" cellspacing="'.$table_data[cellspacing].'" cellpadding="'.$table_data[cellpadding].'" bgcolor="'.$table_data[table_bg_color].'">';
		
			$table_act.='<th colspan="2" bgcolor="'.$table_data[th_color].'" align="'.$table_data[th_align].'"> '.$table_data[tabellenheadline].' </th>';
			
			//schleifenbereich
			
			for($i=0;$i<sizeof($maske);$i++) {
			
			$table_act.='<tr>';
			$table_act.='<td>';
			
			// ausnahmefall tree_select zur auswahl in der tree administration
			if($maske[$i][0] == 'tree_select'){
			
			$table_act.=$maske[$i][5].'<td><select name="data[node_id]" size="1" class="'.$maske[$i][4].'">';
					$root_id = 1;
					$set = new NestedSet();
					$set->set_table_name('page');
					$nodes = $set->getNodes($root_id);
					if (is_error($nodes)) {
						$tree = 'FEHLER:'.$nodes->txt;
					}
					else {
						$tree = ''; 
						foreach($nodes as $node) {
								$abstand = str_repeat('&nbsp;', ($node['level']-1)*4);
							 $table_act.='<option value="'.$node['id'].'">'.$abstand.''.$node['name'].'</option>';
						}
					}
					$table_act.='</select>';
			}
			
			// fuer selectfelder
			if($maske[$i][0] == 'select' && $maske[$i][0] != 'tree_select'){
			
			$table_act.= $maske[$i][5].'</td>';
			$table_act.='<td><select name="'.$maske[$i][2].'" class="'.$maske[$i][4].'" size="1">';
			//inhalt der select optionen
			
			
			$inhalt= explode("|", $maske[$i][3]);
			
			for($b=0;$b<sizeof($inhalt);$b++){
			$table_act.='<option value="'.$inhalt[$b].'">'.$inhalt[$b].'</option>';
			}
			$table_act.='</select>';
			} // select ende
			
			// alle inputs
			elseif($maske[$i][0] != 'select' && $maske[$i][6] != 'wysiwyg'  && $maske[$i][0] != 'tree_select'){
			$table_act.= $maske[$i][5].'</td>';

			$table_act.='<td> <'.$maske[$i][0].' type="'.$maske[$i][1].'" name="'.$maske[$i][2].'" class="'.$maske[$i][4].'" value="'.$maske[$i][3].'" '.$maske[$i][6].'>';
			} // input ende
			
			// fuer wysiwyg textarea
			elseif($maske[$i][0] == 'textarea' && $maske[$i][6] == 'wysiwyg'){
			$table_act.= $maske[$i][5].'</td>';
			$table_act.='<td> <textarea id="'.$maske[$i][1].'" name="'.$maske[$i][2].'"  rows="20" style="width:400px" class="'.$maske[$i][4].'">';
			}
			
			//fuer textarea
			if($maske[$i][0] == 'textarea'){
			$table_act.=$maske[$i][3].'</textarea>';
			} // textarea ende
			
			$table_act.='</td>';
			$table_act.='</tr>';
			
			}
		
			$table_act.='</table></form>';
			
			return $table_act;
		}
	}


	/*
	*	beispiel wie die klasse aufgerufen wird 
	*	könnte ja hilfreich sein:-) 
	*
	*
	
		// $table_act: width|border|cellpadding|cellspacing
	
	
	// testausgabe klasse form
		//klassen einbinden
		include_once('./system.inc.php');
		
		$action = 'http://jin/index.php';
		#$action = 'http://hundertelf/index.php';
		
		$table_data = array (
			'width' 			=> ('350'),
			'border' 			=> ('0'),
			'cellpadding' 		=> ('7'),
			'cellspacing' 		=> ('0'),
			'tabellenheadline' 	=> ('Registriere dich bei JIN'),
			'th_align' 			=> ('left'),
			'th_color' 			=> ('#cecece'),
			'table_bg_color' 	=> ('#efefef'),
		);
		
		// $maske	 : input | type | name | value | class_css | bezeichnung | optional (selected) 
		// optionen von selectfeldern werden wie folgt aufgeschrieben
		
		//			input |type   |name  |value|class_css |bezeichnung | selected bzw. checked
		
		//bei wysiwyg editoren
		// input |id (ta)   |name  |value|class_css |bezeichnung | wysiwyg (args)
		$maske = array (
			array('input','text','vorname','','eingabefeld','Vorname', ''),
			array('input','text','name','','eingabefeld','Name', ''),
			array('input','text','strasse','','eingabefeld','Straße', ''),
			
			array('input','hidden','mod','login','','', ''),
			array('input','hidden','event','view','','', ''),
			array('select','','gelesen','ja|nein|oder|wie|wat|warum','eingabefeld','AGB', 'selected'),
			array('input','text','ort','','eingabefeld','Dein Wohnort', ''),
			array('textarea','','text','','eingabefeld2','Kurzer Text', ''),
			array('input','radio','radio','yes','fliess','ja oder nein', ''),
			array('input','checkbox','checkbox','yes','fliess','ja oder nein', 'checked'),
			
			array('textarea','ta','text','','','WYSIWYG-EDITOR zur TEXTEINGABE', 'wysiwyg'),
			array('textarea','','text','','eingabefeld2','Kurzer Text', ''),
						
			array('input','submit','event_login','Speichern','eingabefeld','', ''),

		
		);
	
	*/
?>
