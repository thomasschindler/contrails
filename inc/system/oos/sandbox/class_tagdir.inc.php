<?php
/**
* bekommt ein array von woertern/tags und versucht passende Kategorien/Pfade zu finden
*/

/**
* bekommt ein array von woertern/tags und versucht passende Kategorien/Pfade zu finden
*/

	class tagdir 
	{
	
		var $db;		// db-klasse
		
		var $tbl_dir = 'dir';	// tabelle von verzeichnisbaum 

		var $field_pid   = 'parent_id';		// feldname parent-id in tbl_dir
		var $field_label = 'label';		// 			label
		var $field_norm  = 'norm';		//			normalisiertes label

		var $debug = false;
		
		var $path_pre   = '/';
		var $path_delim = '/';
		var $path_post  = '';
		
		function tagdir($tbl="dir",$pid="parent_id",$label="label",$norm="norm") 
		{
			
			$this->tbl_dir = $tbl;
			$this->field_pid = $pid;
			$this->field_label = $label;
			$this->field_norm = $norm;
			
			$this->set = new NestedSet();
			$this->set->set_table_name($this->tbl_dir);
			
			$this->db = &DB::singleton();
		}
		/**
		
			add a single tag or a whole path
			accepts an array of the path
			
			adds the nodes without asking
			
		*/
		
		function add_dir($path=null)
		{
			if(!is_array($path))
			{
			 	return;
			}
			foreach($path as $tag)
			{
				if(strlen($tag) == 0)
				{
					continue;
				}
				// which level are we at?
				// do we have a node of that name at this level?
				if(!$parentid)
				{
					$parentid = 1;
					continue;
				}
				/*
				$t = $this->set->getSubtree($parentid);
				
				foreach($t as $node)
				{
					if($node['norm'] == $this->norm($tag))
					{
						$found = $node['id'];
						break;
					}
				}
				*/
				$select = "SELECT * FROM ".$this->tbl_dir." WHERE ".$this->field_pid." = ".$parentid." AND ".$this->field_norm." = '".$this->norm($tag)."'";
				$r = $this->db->query($select);

				if($r->nr() == 0)
				{
					$p = $this->set->NodeNew($parentid);
					$update = "UPDATE ".$this->tbl_dir." SET ".$this->field_pid." = '".$parentid."', ".$this->field_label." = '".$tag."', ".$this->field_norm." = '".$this->norm($tag)."' WHERE id = ".$p;
					$parentid = $p;
					$this->db->query($update);
				}
				else
				{
					$parentid = $r->f('id');
				}
				$found = false;
			}
			return $parentid;
		}
		
		/**
		* sucht passende Kategorien (Pfade) anhand von uebergebenen tags
		*
		*@param	array	$tags 			
		*@param bool $strict  [true->finds exact matches | false finds LIKE matches]
		*@return	array	key=id des letzten knotens, val=pfad in textform
		*@access public
		*/
		function search($param_tags,$strict=true) 
		{
			
			// tags normalisieren, doppelte eleminieren
			$tags = $seen = array();
			foreach($param_tags as $tag) 
			{
				$norm = $this->norm($tag);
				if ($norm == '' || isset($seen[$norm])) continue;
				$tags[] = array('orig' => $tag, 'norm' => $norm);
				$seen[$norm] = true;
			}
			unset($seen);
			if (count($tags) == 0) return array();

			
			// (genau) passende Kategorie zu jedem tag finden
			// gefundene kat. und ihre jeweilige position (d.h. pos. ihres tags) merken
			$known_cats = array();
			$pos = 1;
			foreach($tags as $nr => $tag) 
			{
				$tags[$nr]['cat_ids'] = $this->search_cat($tag['norm'],$strict);
				$tags[$nr]['found']   = (count($tags[$nr]['cat_ids']) > 0);
				foreach($tags[$nr]['cat_ids'] as $cid) 
				{
					$known_cats[$cid] = $pos;
				}
				$pos++;
			}

			
			// alle pfade lesen (und dabei auch bereinigen; d.h. alle unbekannte kat. entfernen);
			// aber pos. der kat. merken (verbessert das scoring, je weiter vorne)
			$paths = array();

			foreach($tags as $tag) 
			{
				if (!$tag['found']) continue;
				foreach($tag['cat_ids'] as $cat_id) 
				{
					$path = $this->get_path($cat_id);
					$path_norm = array();
					$pos = 0;
					foreach($path as $p) {
						$pos++;
						if (isset($known_cats[$p['id']])) 
						{
							$p['pos'] = $pos;
							$path_norm[] = $p;
						}
					}
					$paths[] = array
					(
						'orig' => $path,
						'norm' => $path_norm,
					);

				}
			}


			// pfade scoren
			$max_points = count($known_cats);
			foreach($paths as $nr => $path) 
			{
				// jedes vorkommen einer kategorie = [max-points - (position des tags)] + 1/position der kategorie in pfad
				// d.h. je hoeher ein tag steht, desto mehr punkte
				// je eher ein tag im pfad kommt, desto mehr punkte
				$score = 0;
				foreach($path['norm'] as $cc) 
				{
					$score += $max_points - $known_cats[$cc['id']] + 1;	// punkte durch tag (position)
					$score += 1/$cc['pos'];		// punkte durch position des tags im pfad
				}
				// vorhande kat. paerchenweise vergleichen, ob die position korrekt ist (in bezug auf pos. d. tags)
				for($i=0, $to=count($path['norm'])-1; $i<$to; $i++) 
				{
					$c1 = $path['norm'][$i]['id'];
					$c2 = $path['norm'][$i+1]['id'];

					if ($known_cats[$c1] < $known_cats[$c2]) 
					{
						$score += $max_points+1;
					}
				}
				
				$paths[$nr]['score'] = $score;
			}

			usort($paths, array($this, '_sort_path'));

			
			if ($this->debug) {
				echo '<table border="1">';
				foreach($paths as $p) 
				{
					echo '<tr>';
					echo '<td>'.$this->_pretty_path($p['orig']).'</td>'.
						'<td>'.$this->_id_path($p['orig']).'</td>'.
						'<td>'.$this->_pretty_path($p['norm']).'</td>'.
						'<td>'.$this->_id_path($p['norm']).'</td>'.
						'<td><b>['.$p['score'] .'] </b></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
	
			$ret = array();
			foreach($paths as $p) 
			{
				$last_node = $p['orig'][count($p['orig'])-1]['id'];
				$ret[$last_node] = $this->_pretty_path($p['orig']);
			}

			return $ret;
		}
		
		function _sort_path($a, $b) 
		{
			if ($a['score'] == $b['score']) return 0;
			return ($a['score'] < $b['score']);
		}
		
		/**
		* liefert kompl. Pfad von root bis zu diesem knoten
		*
		*@param	int	id	id des knotens
		*@return	array	array der knoten mit allen infos
		*/
		function get_path($id) 
		{
			
			return $this->set->getPath($id);
			
			$ret = array();
			$sql = 'SELECT id, '.$this->field_pid.', '.$this->field_label.', '.$this->field_norm.' FROM '.$this->tbl_dir.' WHERE id='.(int)$id;
			$node = $this->db->query($sql);
			$pid = $node->f($this->field_pid);
			
			$ret[] = $node->r();
			
			$cnt = 0;
			while($pid != 0) 
			{
				if ($cnt++ > 10000) { echo 'panic';exit; }
				$parent = $this->db->query('SELECT id, '.$this->field_pid.', '.$this->field_label.', '.$this->field_norm.' FROM '.$this->tbl_dir.' WHERE id='.$pid);
				$ret[] = $parent->r();
				$pid = $parent->f($this->field_pid);
			}
	
			return array_reverse($ret);
	
		}
		
		/*
		** alle kategorien zu einem tag liefern
		*
		*@param	int	$field	in welchem feld nach tag suchen. 1=norm, 2=label
		*@param	int	$type	wie wird gesucht. 1=genauer treffer, 2=teil anfang/ende, 3=teil ueberall (_TODO)
		*/

		function search_cat($tag, $strict=true, $field=1) 
		{
			$ret = array();
			
			$sql_field = ($field == 1) ? $this->field_norm : $this->field_label;
			
			if($strict)
			{
				$sql = 'SELECT id FROM '.$this->tbl_dir.' WHERE '.$sql_field.'=\''.mysql_real_escape_string($tag).'\'';
			}
			else
			{
	
				$sql = 'SELECT id FROM '.$this->tbl_dir.' WHERE 
				'.$sql_field.' LIKE \'%'.mysql_real_escape_string($tag).'%\' 
				OR '.$sql_field.'  LIKE \''.mysql_real_escape_string($tag).'%\' 
				OR '.$sql_field.'  LIKE \'%'.mysql_real_escape_string($tag).'\' 
				'.( strlen($tag) > 3 ? ' OR '.$sql_field.'  LIKE \'%'.mysql_real_escape_string(substr($tag,1,-1)).'%\'' : '').'';
	
#				$sql = "SELECT id FROM ".$this->tbl_dir." WHERE MATCH (".$sql_field.") AGAINST ('%".$tag."%' IN BOOLEAN MODE)";
			}

			$cats = $this->db->query($sql);

			while($cats->next()) $ret[] = $cats->f('id');
			
			return $ret;
		}


	
		function norm($str) 
		{
			$str = str_replace(array('ö', 'ä', 'ü', 'ß'), array('oe', 'ae', 'ue', 'ss'), strtolower($str));
			$str = preg_replace('/[^0-9a-z]+/', '', $str);
			return $str;
		}

		function _pretty_path($arr) 
		{
			$ret = array();
			foreach($arr as $node) $ret[] = $node[$this->field_label];
			return $this->path_pre . implode($this->path_delim, $ret) . $this->path_post;
		}
		function _id_path($arr) 
		{
			$ret = array();
			foreach($arr as $node) $ret[] = $node['id'];
			return implode('-', $ret);
		}

	}

///////////////////////////////////////////////////////////////////////


	class tagdir_OLD
	{
	
		var $db;		// db-klasse
		
		var $tbl_dir = 'dir';	// tabelle von verzeichnisbaum 

		var $field_pid   = 'pid';		// feldname parent-id in tbl_dir
		var $field_label = 'label';		// 			label
		var $field_norm  = 'norm';		//			normalisiertes label

		var $debug = false;
		
		var $path_pre   = '/';
		var $path_delim = '/';
		var $path_post  = '';
		
		function tagdir($tbl="dir",$pid="pid",$label="label",$norm="norm") 
		{
			
			$this->tbl_dir = $tbl;
			$this->field_pid = $pid;
			$this->field_label = $label;
			$this->field_norm = $norm;
			
			$this->db = &DB::singleton();
		}
		
		/**
		* sucht passende Kategorien (Pfade) anhand von uebergebenen tags
		*
		*@param	array	$tags
		*@return	array	key=id des letzten knotens, val=pfad in textform
		*@access public
		*/
		function search($param_tags) 
		{
			
			// tags normalisieren, doppelte eleminieren
			$tags = $seen = array();
			foreach($param_tags as $tag) 
			{
				$norm = $this->norm($tag);
				if ($norm == '' || isset($seen[$norm])) continue;
				$tags[] = array('orig' => $tag, 'norm' => $norm);
				$seen[$norm] = true;
			}
			unset($seen);
			if (count($tags) == 0) return array();

			
			// (genau) passende Kategorie zu jedem tag finden
			// gefundene kat. und ihre jeweilige position (d.h. pos. ihres tags) merken
			$known_cats = array();
			$pos = 1;
			foreach($tags as $nr => $tag) 
			{
				$tags[$nr]['cat_ids'] = $this->search_cat($tag['norm'],$strict);
				$tags[$nr]['found']   = (count($tags[$nr]['cat_ids']) > 0);
				foreach($tags[$nr]['cat_ids'] as $cid) 
				{
					$known_cats[$cid] = $pos;
				}
				$pos++;
			}

			
			// alle pfade lesen (und dabei auch bereinigen; d.h. alle unbekannte kat. entfernen);
			// aber pos. der kat. merken (verbessert das scoring, je weiter vorne)
			$paths = array();

			foreach($tags as $tag) 
			{
				if (!$tag['found']) continue;
				foreach($tag['cat_ids'] as $cat_id) 
				{
					$path = $this->get_path($cat_id);
					$path_norm = array();
					$pos = 0;
					foreach($path as $p) {
						$pos++;
						if (isset($known_cats[$p['id']])) 
						{
							$p['pos'] = $pos;
							$path_norm[] = $p;
						}
					}
					$paths[] = array
					(
						'orig' => $path,
						'norm' => $path_norm,
					);

				}
			}


			// pfade scoren
			$max_points = count($known_cats);
			foreach($paths as $nr => $path) 
			{
				// jedes vorkommen einer kategorie = [max-points - (position des tags)] + 1/position der kategorie in pfad
				// d.h. je hoeher ein tag steht, desto mehr punkte
				// je eher ein tag im pfad kommt, desto mehr punkte
				$score = 0;
				foreach($path['norm'] as $cc) 
				{
					$score += $max_points - $known_cats[$cc['id']] + 1;	// punkte durch tag (position)
					$score += 1/$cc['pos'];		// punkte durch position des tags im pfad
				}
				// vorhande kat. paerchenweise vergleichen, ob die position korrekt ist (in bezug auf pos. d. tags)
				for($i=0, $to=count($path['norm'])-1; $i<$to; $i++) 
				{
					$c1 = $path['norm'][$i]['id'];
					$c2 = $path['norm'][$i+1]['id'];

					if ($known_cats[$c1] < $known_cats[$c2]) 
					{
						$score += $max_points+1;
					}
				}
				
				$paths[$nr]['score'] = $score;
			}

			usort($paths, array($this, '_sort_path'));

			
			if ($this->debug) {
				echo '<table border="1">';
				foreach($paths as $p) 
				{
					echo '<tr>';
					echo '<td>'.$this->_pretty_path($p['orig']).'</td>'.
						'<td>'.$this->_id_path($p['orig']).'</td>'.
						'<td>'.$this->_pretty_path($p['norm']).'</td>'.
						'<td>'.$this->_id_path($p['norm']).'</td>'.
						'<td><b>['.$p['score'] .'] </b></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
	
			$ret = array();
			foreach($paths as $p) 
			{
				$last_node = $p['orig'][count($p['orig'])-1]['id'];
				$ret[$last_node] = $this->_pretty_path($p['orig']);
			}

			return $ret;
		}
		
		function _sort_path($a, $b) 
		{
			if ($a['score'] == $b['score']) return 0;
			return ($a['score'] < $b['score']);
		}
		
		/**
		* liefert kompl. Pfad von root bis zu diesem knoten
		*
		*@param	int	id	id des knotens
		*@return	array	array der knoten mit allen infos
		*/
		function get_path($id) 
		{
			$ret = array();
			$sql = 'SELECT id, '.$this->field_pid.', '.$this->field_label.', '.$this->field_norm.' FROM '.$this->tbl_dir.' WHERE id='.(int)$id;
			$node = $this->db->query($sql);
			$pid = $node->f($this->field_pid);
			
			$ret[] = $node->r();
			
			$cnt = 0;
			while($pid != 0) 
			{
				if ($cnt++ > 10000) { echo 'panic';exit; }
				$parent = $this->db->query('SELECT id, '.$this->field_pid.', '.$this->field_label.', '.$this->field_norm.' FROM '.$this->tbl_dir.' WHERE id='.$pid);
				$ret[] = $parent->r();
				$pid = $parent->f($this->field_pid);
			}
	
			return array_reverse($ret);
	
		}

		
		/*
		** alle kategorien zu einem tag liefern
		*
		*@param	int	$field	in welchem feld nach tag suchen. 1=norm, 2=label
		*@param	int	$type	wie wird gesucht. 1=genauer treffer, 2=teil anfang/ende, 3=teil ueberall (_TODO)
		*/

		function search_cat($tag, $field = 1, $type = 1) 
		{
			$ret = array();
			
			$sql_field = ($field == 1) ? $this->field_norm : $this->field_label;
			
			$sql = 'SELECT id FROM '.$this->tbl_dir.' WHERE '.$sql_field.'=\''.mysql_real_escape_string($tag).'\'';
			$cats = $this->db->query($sql);

			while($cats->next()) $ret[] = $cats->f('id');
			
			return $ret;
		}


	
		function norm($str) 
		{
			$str = str_replace(array('ö', 'ä', 'ü', 'ß'), array('oe', 'ae', 'ue', 'ss'), strtolower($str));
			$str = preg_replace('/[^0-9a-z]+/', '', $str);
			return $str;
		}

		function _pretty_path($arr) 
		{
			$ret = array();
			foreach($arr as $node) $ret[] = $node[$this->field_label];
			return $this->path_pre . implode($this->path_delim, $ret) . $this->path_post;
		}
		function _id_path($arr) 
		{
			$ret = array();
			foreach($arr as $node) $ret[] = $node['id'];
			return implode('-', $ret);
		}

	}
	
?>