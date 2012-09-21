<?php
/**
*	language file editor
*	attention: currently excludes all native oos modules
*	
*	how to use
*	consolidate langugage files to contain all the same keys
*	./call.sh.php -m lang_file_editor -e consolidate_lang
*
*	consolidate duplicate strings into one module (ff_common)
*	./call.sh.php -m lang_file_editor -e consolidate_duplicates
*
*	remove all unused keys from the language files
*	./call.sh.php -m lang_file_editor -e remove_unused
*
*	add a new language to the system (from pt-br to en)
*	./call.sh.php -m lang_file_editor -e add_lang -a=source:pt-br/target:en
*
*/

class lang_file_editor_action extends modAction
{
	var $mod_name  = "lang_file_editor";
		
	function lang_file_editor_action() {}
		
	function main($a,$p = null) 
	{
		switch(strtolower($a["event"])) 
		{
			case 'edit':								$this->set_view('edit');									break;
			case 'search':								$this->search();											break;
			case 'save':								$this->action_save();										break;
			case 'remove_unused':						$this->remove_unused();										break;
			case 'consolidate_duplicates':				$this->consolidate_duplicates();							break;
			case 'add_lang':							$this->add_lang($p);										break;
			case 'consolidate_lang':					$this->consolidate_lang();									break;
			case 'export_as_csv':						$this->export_as_csv();										break;
		}
	}
	
	/**
	*	allow a user to search for any string in any language file
	*	creates a list of files 
	*/
	
	function search()
	{
		// iterate through all modules 
		// grep for the search string in the language folder
		// list the results with enough information to edit
		
		$l = scandir(CONF::inc_dir().'/mods');
		
		foreach($l as $mod)
		{
			if(!preg_match("/\./",$mod))
			{
				if(is_file(CONF::inc_dir()."/mods/".$mod."/language/".$this->data['language'].".php"))
				{
					$i = file(CONF::inc_dir()."/mods/".$mod."/language/".$this->data['language'].".php");
					if($d = preg_grep("/".$this->data['search']."/",$i))
					{
						// extract the keys
						// list them for the mod 
						// show a link for each (lang/page/ref/key)
						foreach($d as $l)
						{
							$l = explode("=>",$l);
							$l = preg_replace("/'|'\"/","",$l);
							$ret[$mod][trim($l[0])] = preg_replace("/,$/","",trim($l[1]));
						}
					}
				}
			}
		}
		$this->set_var('result',$ret);
		$this->set_view('search');
	}
	
	/**
	*	export all strings as a csv file (per module)
	*/
	
	function export_as_csv()
	{
		// path to the mods folder
		$path = CONF::inc_dir().'mods/';
		// modules to exclude because of incompatibility (only variable in e::o param)
		$exclude = array('acladmin','api_test','article','container','grid','groupswitch','lang_file_editor','lang_file_editor_','login','menubar','messaging','mod_admin','modmanager','mothermod','multiproject_mod_admin','navigation_1','objbrowser','oos_layouter','oos_statistics','page','remote_connection','search','syscheck','template','trashcan','usradmin','wysiwyg');
		
		if ($mods = opendir($path)) 
		{
			echo "READING MODS \n";
			$keys = array();
			clearstatcache();
			// read through each of the modules
			while (false !== ($mod_file = readdir($mods))) 
			{
		   		if ($mod_file != "." && $mod_file != ".." && $mod_file != ".svn" && !in_array($mod_file, $exclude)) 
				{
					echo $mod_file."\n";
					$tmp_path = $path.$mod_file."/language/";
					$tmp_dir = opendir($tmp_path);
					// collect all languages
					while (false !== ($langfile = readdir($tmp_dir))) 
					{
				   		if ($langfile != "." && $langfile != ".." && $mod_file != ".svn") 
						{
							$tmp_lang = substr($langfile,0,-4);
							if(strlen($tmp_lang)==0)
							{
								continue;
							}
							$langs[$tmp_lang] = $tmp_lang;
							$lang = array();
							include($tmp_path.$langfile);
							foreach($lang as $k => $v)
							{
								$keys[$mod_file][$k][$tmp_lang] = $v;
							}
						}
					}		
				}
			}
		}
		
		$out = 'MOD,KEY,'.implode(',',$langs).",TRANSLATED\n";
		
		foreach($keys as $mod => $list)
		{
			foreach($list as $key => $translations)
			{
				$translated = 0;
				$out .= $mod.','.$key;
				foreach($langs as $k)
				{
					if(isset($translations[$k]))
					{
						if($translations[$k] != $key)
						{
							$translated++;
						}
						$out .= ',"'.preg_replace("/\n/","",$translations[$k]).'"';	
					}
					else
					{
						$out .= ',"-"';	
					}
				}
				$out .= ",".$translated."\n";
			}
		}
		
		UTIL::file_put_contents(CONF::inc_dir()."//tmp/lang_file_editor.".date('dmY',time()).".csv",$out);
	}
	
	/**
	*	check all language files and consolidate on a module base
	*/
	
	function consolidate_lang()
	{
		// path to the mods folder
		$path = CONF::inc_dir().'mods/';
		// modules to exclude because of incompatibility (only variable in e::o param)
		$exclude = array('acladmin','api_test','article','container','grid','groupswitch','lang_file_editor','lang_file_editor_','login','menubar','messaging','mod_admin','modmanager','mothermod','multiproject_mod_admin','navigation_1','objbrowser','oos_layouter','oos_statistics','page','remote_connection','search','syscheck','template','trashcan','usradmin','wysiwyg');
		
		if ($mods = opendir($path)) 
		{
			echo "READING MODS \n";
			clearstatcache();
			// read through each of the modules
			while (false !== ($mod_file = readdir($mods))) 
			{
		   		if ($mod_file != "." && $mod_file != ".." && $mod_file != ".svn" && !in_array($mod_file, $exclude)) 
				{
					echo $mod_file."\n";
					$languages = array();
					$tmp_path = $path.$mod_file."/language/";
					$tmp_dir = opendir($tmp_path);
					// collect all languages
					while (false !== ($langfile = readdir($tmp_dir))) 
					{
						$tmp_lang = substr($langfile,0,-4);
				   		if ($langfile != "." && $langfile != ".." && $langfile != ".svn") 
						{
							include($tmp_path.$langfile);
							$languages[$tmp_lang] = $lang;
						}
					}
					// get all keys
					$tmp_keys = array();
					foreach($languages as $tmp_lang => $lang)
					{
						foreach($lang as $k => $v)
						{
							$tmp_keys[$k] = $k;
						}
					}
					// now fill them
					foreach($languages as $tmp_lang => $lang)
					{
						$write = false;
						foreach($tmp_keys as $key)
						{
							if(!isset($lang[$key]))
							{
								$lang[$key] = $key;
								$write = true;
							}
						}
						if($write)
						{
							$this->action_save($path.$mod_file."/language/".$tmp_lang.".php",false,$lang);
						}
					}
				}		
			}
		}
	}
	
	/**
	*	add language files based on a reference languages
	*/
	
	function add_lang($p)
	{
		// path to the mods folder
		$path = CONF::inc_dir().'mods/';
		// modules to exclude because of incompatibility (only variable in e::o param)
		$exclude = array('acladmin','api_test','article','container','grid','groupswitch','lang_file_editor','lang_file_editor_','login','menubar','messaging','mod_admin','modmanager','mothermod','multiproject_mod_admin','navigation_1','objbrowser','oos_layouter','oos_statistics','page','remote_connection','search','syscheck','template','trashcan','usradmin','wysiwyg');
	
		if ($mods = opendir($path)) 
		{
			echo "READING MODS \n";
			clearstatcache();
			// read through each of the modules
			while (false !== ($mod_file = readdir($mods))) 
			{
		   		if ($mod_file != "." && $mod_file != ".." && $mod_file != ".svn" && !in_array($mod_file, $exclude)) 
				{
					// go to this directory 
					// copy the reference file to the target file
					// add to svn
					echo $mod_file."\n";
					chdir($path.$mod_file."/language");
					exec("cp ".$p['source'].".php ".$p['target'].".php");
					exec("svn add ".$p['target'].".php");
					// rewrite the target to contain only keys
					$lang = array();
					$target = array();
					include($p['target'].'.php');
					foreach($lang as $k => $v)
					{
						$target[$k] = $k;
					}
					$this->action_save($path.$mod_file."/language/".$p['target'].".php",false,$target);
				}
			}
		}
	}
	
	/**
	 * search through all modules and consolidate duplicates into the ff_common language file
	 * 
	 * only run if remove_unused() has been run, this formats the lang files with multiple arrays properly
	 */
	function consolidate_duplicates()
	{
		// path to the mods folder
		$path = CONF::inc_dir().'mods/';
		// modules to exclude because of incompatibility (only variable in e::o param)
		$exclude = array('acladmin','api_test','article','container','grid','groupswitch','lang_file_editor','lang_file_editor_','login','menubar','messaging','mod_admin','modmanager','mothermod','multiproject_mod_admin','navigation_1','objbrowser','oos_layouter','oos_statistics','page','remote_connection','search','syscheck','template','trashcan','usradmin','wysiwyg');
	
		if ($mods = opendir($path)) 
		{
			echo "GETTING TRANSLATION KEYS \n";
			clearstatcache();
			// read through each of the modules
			while (false !== ($mod_file = readdir($mods))) 
			{
		   		if ($mod_file != "." && $mod_file != ".." && $mod_file != ".svn" && !in_array($mod_file, $exclude)) 
				{
					echo "	".$mod_file." \n";
					if ($lang_folder = opendir($path.$mod_file."/language")) 
					{
						// go through each language file
						while (false !== ($lang_file = readdir($lang_folder))) 
						{
							if ($lang_file != "." && $lang_file != ".." && $lang_file != ".svn") 
							{
								// get the text from the current language file
								$contents = file_get_contents($path.$mod_file."/language/".$lang_file);
								$contents = explode("\n", $contents);
								// store all the keys and translations of the $lang array
								foreach($contents as $k => $content)
								{									
									if(preg_match('/=>/', $content))
									{
										$temp = explode('=>', $content);
										
										// get key
										$key = $temp[0];
										$key = str_replace("'", "", $key);
										$key = str_replace('"', '', $key);
										$key = trim($key);										
										if($key != '0' && $key != '1') // only check for 2 ints right now
										{
											//get translation
											$str = $temp[1];
											$str = str_replace("'", "", $str);
											$str = str_replace('"', '', $str);
											$str = trim($str);
											
											// handle multiple lines of translation
											// if the field is an array (older language files)
											if($str == 'array(')
											{
												while(trim($contents[++$k]) != '),')
												{
													$str .= "\n".$contents[$k]."\n)";
												}
											}
											// if the field is not an array (newer language files)
											else 
											{
												while(!preg_match('/=>/', $contents[$k+1]) && trim($contents[$k+1]) != ');' && trim($contents[$k+1]) != '?>')
												{
													$add = $contents[$k+1];
													$add = str_replace("'", "", $add);
													$add = str_replace('"', '', $add);
													$str .= "\n".$add;
													$k++;
												}
											}
											$str = trim($str);
											
											if(substr($str, -1) == ',')
											{
												$str = substr($str, 0, -1);
											}												
											if($str != '')
											{
												$translations[$mod_file][$lang_file][$key] = $str;
											}
										}
									}
								}
							}
						}
					  	closedir($lang_folder);
					}	
		   		}		   				   		
			}
		  	closedir($mods);
		}
		
		echo "SCANNING FF_COMMON FOR DUPLICATES \n";
		// make sure ff_common is scanned first to keep it's duplicate keys
		foreach($translations['ff_common'] as $lf => $lang)
		{
			foreach($translations as $mod_compare => $trans_compare)
			{
				if($mod_compare != 'ff_common')
				{						
					$duplicates['ff_common'][$lf][$mod_compare] = array_intersect($trans[$lf], $trans_compare[$lf]);
				}
			}				
		}
		
		echo "SCANNING MODULES FOR DUPLICATES \n";
		// find the duplicates for the rest of the modules
		foreach($translations as $mod => $trans)
		{
			if($mod != 'ff_common')
			{
				echo "	".$mod." \n";
				foreach($trans as $lf => $lang)
				{
					foreach($translations as $mod_compare => $trans_compare)
					{
						if($mod_compare != $mod)
						{						
							$duplicates[$mod][$lf][$mod_compare] = array_intersect($trans[$lf], $trans_compare[$lf]);
						}
					}				
				}
			}
		}
		
		echo "CONSOLIDATING DUPLICATES \n";
		// consolidate duplicates into individual language files and update duplicates array with new keys
		foreach($duplicates as $m => $mod)
		{
			foreach($mod as $lf => $file)
			{
				foreach($file as $cm => $comp_mod)
				{
					foreach($comp_mod as $t => $translation)
					{
						$match_key = array_search($translation, $commons[$lf]);
						if(!$match_key)
						{
							$commons[$lf][$t] = $translation;
						}
						else 
						{
							if($match_key != $t)
							{
								$duplicates[$m][$lf][$cm][$match_key] = $translation;
								unset($duplicates[$m][$lf][$cm][$t]);
							}
						}
					}
				}
			}
		}

		echo "WRITING LANGUAGE FILES IN FF_COMMON \n";
		// write the lang files for ff_common
		foreach($commons as $file => $content)
		{
			$this->action_save($path.'ff_common/language/'.$file,false,$content);
		}
		
		echo "CHANGING MODS TO CALL FF_COMMON FOR COMMON TRANSLATIONS \n";
		// go through all the files and replace occurrences of the duplicate keys
		foreach($translations as $mod => $trans)
		{
			echo "	".$mod." \n";
			foreach($trans as $lf => $lang)
			{
				foreach($lang as $k => $translation)	
				{
					$match = array_search($translation, $commons[$lf]);
					if($match)
					{
						$files = $this->get_file_txt($path."/".$mod,true);	
						$files = explode("\n", $files);
						foreach($files as $f => $file)
						{
							if(trim($file) != '')
							{
								$text = file_get_contents($file);
								if((substr_count($text, 'e::o("'.$match.'")')))
								{
									echo "\t\t".$match."\n";

									$out = str_replace('e::o("'.$match.'")', 'e::o("'.$match.'",null,null,"ff_common")', $text);
									$fp = fopen($file,'w+');
									fwrite($fp,$out);
									fclose($fp);

								}
								if((substr_count($text, "e::o('".$match."')")))
								{
									echo "\t\t".$match."\n";

									$out = str_replace("e::o('".$match."')", "e::o('".$match."',null,null,'ff_common')", $text);
									$fp = fopen($file,'w+');
									fwrite($fp,$out);
									fclose($fp);

								}
							}
						}
					}
				}			
			}
		}
		
		echo "DONE :) \n\n";
	}	
	
	/**
	 * search through all modules and remove unused translations
	 */
	function remove_unused()
	{
		// path to the mods folder
		$path = CONF::inc_dir().'mods/';
		// modules to exclude because of incompatibility (only variable in e::o param)
		$exclude = array('ff_common, ff_amazonia, ff_attribute','ff_checkout','ff_coupon','ff_pictureplix','ff_print','oos_layouter','acladmin','api_test','article','container','grid','groupswitch','lang_file_editor','lang_file_editor_','login','menubar','messaging','mod_admin','modmanager','mothermod','multiproject_mod_admin','navigation_1','objbrowser','oos_layouter','oos_statistics','page','remote_connection','search','syscheck','template','trashcan','usradmin','wysiwyg');
	
		if ($mods = opendir($path)) 
		{
			echo "GETTING TRANSLATION KEYS \n";
			clearstatcache();
			// read through each of the modules
			while (false !== ($mod_file = readdir($mods))) 
			{
				if($mod_file != 'ff_deploy')
				{
//					continue;
				}
		   		if ($mod_file != "." && $mod_file != ".." && $mod_file != ".svn" && !in_array($mod_file, $exclude)) 
				{
					echo "	".$mod_file." \n";
					$modules[$mod_file] = $mod_file;
					if ($lang_folder = opendir($path.$mod_file."/language")) 
					{
						// go through each language file
						while (false !== ($lang_file = readdir($lang_folder))) 
						{
							if ($lang_file != "." && $lang_file != ".." && $lang_file != ".svn") 
							{
								// get the text from the current language file
								$contents = file_get_contents($path.$mod_file."/language/".$lang_file);
								$contents = explode("\n", $contents);
								// store all the keys of the $lang array
								foreach($contents as $content)
								{
									if(preg_match('/=>/', $content))
									{
										$temp = explode('=>', $content);
										
										$key = $temp[0];
										$key = str_replace("'", "", $key);
										$key = str_replace('"', '', $key);
										$key = trim($key);
										if($key != '0' && $key != '1') // only check for 2 ints right now
										{
											$keys[$mod_file][$key] = $key;
										}
									}
								}
							}
						}
					  	closedir($lang_folder);
					}	
							
					// get all the text from all the files in mods							
					$text = $this->get_file_txt($path."/".$mod_file);
					// delimit by e::o
					$temp = explode("e::o(", $text);
					foreach($temp as $k => $t)
					{
						if($k > 0)
						{
							$temp_2 = explode(')', $t);
							$temp_str = $temp_2[0];
							$n = 0;
							// handle parentheses in the parameters for e::o
							while((substr_count($temp_str, '(') - substr_count($temp_str, ')')) != 0)
							{						
								$n++;
								$temp_str .= ')'.$temp_2[$n];
							}
							// handle quotes
							$call = str_replace("'", "&q&", $temp_str);
							$call = str_replace('"', '&q&', $call);
							$call = trim($call);

							// handle for if e::o is called with more parameters, sets the mod accordingly
							if(substr_count($call,',') > 1)
							{
								$temp_mod = explode(',', $call);
								if(count($temp_mod) == 4 && substr($temp_mod[3], 0, 3) == '&q&' && substr($temp_mod[3], -3, 3) == '&q&')
								{
									$mod = $temp_mod[3];
									$mod = str_replace('&q&', '', $mod);
									$call = trim($temp_mod[0]);
								}
								else
								{
									$mod = $mod_file;									
								}
							}
							else
							{
								$mod = $mod_file;
							}
							
							// load all the calls to e::o into an array
							// handles e::o if a variable is the first thing as a parameter
							if(substr($call,0,1) == '$')
							{
								$temp_var_s = explode('.', $call);
								$temp_var_k = explode('&q&', $temp_var_s[1]);
								$lang_calls[$mod][$temp_var_k[1]]['key'] = $temp_var_k[1];	
								$lang_calls[$mod][$temp_var_k[1]]['type'] = 'variable';					
							}
							// handles if a variable exists in the parameter
							elseif(substr_count($call,'.$') > 0)
							{
								$temp_var_s = explode('.', $call);
								$temp_var_k = explode('&q&', $temp_var_s[0]);
								$lang_calls[$mod][$temp_var_k[1]]['key'] = $temp_var_k[1];	
								$lang_calls[$mod][$temp_var_k[1]]['type'] = 'variable';								
							}
							// otherwise it is just a string
							else 
							{	
								$lang_calls[$mod][str_replace('&q&', '', $call)]['key'] = str_replace('&q&', '', $call);
								$lang_calls[$mod][str_replace('&q&', '', $call)]['type'] = 'normal';								
							}					
						}
					}	
		   		}		   				   		
			}
		  	closedir($mods);
		}
		
		echo "CHECKING IF TRANSLATION KEY IS BEING USED \n";
		// check to see if the language file key is used at all in the text
		$existing = array();
		foreach($lang_calls as $mod => $calls)
		{
			echo "	".$mod." \n";
			foreach($calls as $call)
			{
				if($call['type'] == 'variable')
				{
					foreach($keys[$mod] as $k => $str)
					{
						if(substr_count($str,$call['key']) > 0)
						{
							$existing[$mod][$k] = $k;
						}
					}
				}
				else 
				{
					$match = array_search($call['key'], $keys[$mod]);
					if($match)
					{
						$existing[$mod][$match] = $match;
					}
				}
			}
			$differences[$mod] = array_diff($keys[$mod], $existing[$mod]);
		}
		
		// check to see if we have keys in our lang file that are not used in the mod
		foreach($modules as $mod)
		{
			if ($lang_folder = opendir($path.$mod."/language")) 
			{
				// go through each language file
				while (false !== ($lang_file = readdir($lang_folder))) 
				{
					if ($lang_file != "." && $lang_file != ".." && $lang_file != ".svn") 
					{
						$lang = array();
						include($path.$mod."/language/".$lang_file);
						foreach($lang as $k => $v)
						{
							if(!isset($lang_calls[$mod][$k]))
							{
								echo "UNUSED: \t".$mod."\t".$k."\n";
							}
						}
					}
				}
			  	closedir($lang_folder);
			}
		}
		
//		return;
		
		echo "REWRITING THE LANGUAGE FILES \n";
		// rewrite all the language files
		if ($mods = opendir($path)) 
		{
			clearstatcache();
			// read through each of the modules
			while (false !== ($mod_file = readdir($mods))) 
			{
		   		if ($mod_file != "." && $mod_file != ".." && $mod_file != ".svn" && !in_array($mod_file, $exclude)) 
				{
					echo "	".$mod_file." \n";
					if ($lang_folder = opendir($path.$mod_file."/language")) 
					{
						// go through each language file
						while (false !== ($lang_file = readdir($lang_folder))) 
						{
							if ($lang_file != "." && $lang_file != ".." && $lang_file != ".svn") 
							{
//								$this->action_save($path.$mod_file."/language/".$lang_file,$existing[$mod_file]);
							}
						}
					  	closedir($lang_folder);
					}	
		   		}		   				   		
			}
		  	closedir($mods);
		}
		echo count($differences,COUNT_RECURSIVE)-count(array_keys($differences)) . " REMOVED \n";
		echo "DONE :) \n\n";
	}
	
	/**
	 * Recursively goes through a file tree starting at a certain level ($path)
	 * 
	 * @param $path - string of starting folder of file tree to traverse
	 */
	function get_file_txt($path,$names = false)
	{
		$text = "";
		$files = "";
		if ($curr_folder = opendir($path)) 
		{
			while (false !== ($file = readdir($curr_folder))) 
			{
				if ($file != "." && $file != ".." && $file != ".svn") 
				{
					if(is_dir($path."/".$file))
					{
						if($names)
						{
							$files .= $this->get_file_txt($path."/".$file,$names)."\n";
						}
						else
						{
							$text .= $this->get_file_txt($path."/".$file,$names);
						}
					}
					else 
					{		
						if($names)
						{
							$files .= $path."/".$file."\n";
						}				
						else 
						{
							$text .= file_get_contents($path."/".$file);
						}
					}
				}
			}
		  	closedir($curr_folder);
		}
		if($names)
		{
			return $files;
		}
		else 
		{
			return $text;
		}
	}
		
	/**
	 * Writes changes into the language files
	 * 
	 * @param $file - string of file location
	 * @param $used - array of existing keys (used to remove unused ones)
	 */
	function action_save($file = false, $used = false, $content = false)
	{
		if(!$file)
		{
			$file = CONF::inc_dir().'mods/'.$this->data['page'].'/language/'.$this->data['lang'];
		}
		
		if($used && $content)
		{
			$this->OPC->error('cannot add and remove content at the same time');
			return;
		}
		
		if(file_exists($file))
		{
			include($file);
			
			$out = "<?\n\$lang = array\n(\n";	
			if(!$used && !$content && $this->data)
			{
				foreach($lang as $k => $v)
				{
					if($this->data[$k])
					{
						$v = $this->data[$k];
					}
//					MC::debug($k);
					
					$out .= $this->add_field($k, $v);
//					MC::debug($out);
				}
			}
			elseif($used && !$content)
			{
				foreach($lang as $k => $v)
				{
					if(in_array($k, $used))
					{
						$out .= $this->add_field($k, $v);
					}
				}
			}
			elseif(is_array($content))
			{
				foreach($content as $key => $translation)
				{
					if(substr_count($translation,'array(') > 0)
					{
						$out .= "\t'".$key."' => ".$translation.",\n";
					}
					else
					{
						$val = preg_replace('/"/','\"',$translation);
						$out .= "\t'".$key."' => '".preg_replace("/\r/","",preg_replace("/'/",'"',$translation))."',\n";
					}
				}				
			}
			else 
			{
				foreach($lang as $k => $v)
				{
					$out .= $this->add_field($k, $v);
				}
				// if there is a content array, use it to add to the file...make sure it is correctly formatted!
				if($content)
				{
					foreach($content as $key => $translation)
					{
						if(substr_count($translation,'array(') > 0)
						{
							$out .= "\t'".$key."' => ".$translation.",\n";
						}
						else
						{
							$val = preg_replace('/"/','\"',$translation);
							$out .= "\t'".$key."' => '".preg_replace("/\r/","",preg_replace("/'/",'"',$translation))."',\n";
						}
					}
				}
			}			
			$out .= ");\n?>";
		}
		else 
		{
			$out = "<?\n\$lang = array\n(\n";		
			// if there is a content array, use it to add to the file...make sure it is correctly formatted!
			if($content)
			{
				foreach($content as $key => $translation)
				{
					if(substr_count($translation,'array(') > 0)
					{
						$out .= "\t'".$key."' => ".$translation.",\n";
					}
					else
					{
						$val = preg_replace('/"/','\"',$translation);
						$out .= "\t'".$key."' => '".preg_replace("/\r/","",preg_replace("/'/",'"',$translation))."',\n";
					}
				}
			}
			else 
			{
				$this->OPC->error('language file does not exist');
			}
			$out .= ");\n?>";
		}


		
		$fp = fopen($file,'w+');
		fwrite($fp,$out);
		fclose($fp);		
		$this->OPC->success($this->data['key']." saved");
	}
	
	function add_field($k,$v)
	{
		if(is_array($v))
		{
			$out .= "\t'".$k."' => array(\n";
			foreach($v as $key => $val)
			{
				$out .= "\t\t".$key." => '".preg_replace("/\r/","",preg_replace("/'/",'"',$val))."',\n";
			}
			$out .= "\t),\n";
		}
		else
		{
			$val = preg_replace('/"/','\"',$val);
			$out .= "\t'".$k."' => '".preg_replace("/\r/","",preg_replace("/'/",'"',$v))."',\n";
		}	
//		MC::debug($out);
		return $out;
	}

	
}
?>