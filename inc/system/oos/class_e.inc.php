<?
/**
* internationalization
* usage: e::o("string")
* looks for a file named after the current language 
* expects the file to be located in the subfolder lang of the current module
* {module}/lang/{lang}.php
*/
class e
{
	var $mod_name = '';

	/**
	*	default lang for fallback
	*/
	function def(){
		#return 'de';
		return CONF::lang();
	}
	/**
	*	returns the languages the system is capable of
	*/
	function lang($id=-1)
	{
		static $__LANGUAGES;
		if(!$__LANGUAGES)
		{
			$__LANGUAGES = array
			(
				'0' => 'pl',
				'1' => 'en',
				'2' => 'de',
				'3' => 'es',
				'4' => 'fr',
				'5' => 'se',
				'6' => 'af',
				'7' => 'sq',
				'8' => 'ar-sa',
				'9' => 'ar-iq',
				'10' => 'ar-eg',
				'11' => 'ar-ly',
				'12' => 'ar-dz',
				'13' => 'ar-ma',
				'14' => 'ar-tn',
				'15' => 'ar-om',
				'16' => 'ar-ye',
				'17' => 'ar-sy',
				'18' => 'ar-jo',
				'19' => 'ar-lb',
				'20' => 'ar-kw',
				'21' => 'ar-ae',
				'22' => 'ar-bh',
				'23' => 'ar-qa',
				'24' => 'eu',
				'25' => 'bg',
				'26' => 'be',
				'27' => 'ca',
				'28' => 'zh-tw',
				'29' => 'zh-cn',
				'30' => 'zh-hk',
				'31' => 'zh-sg',
				'32' => 'hr',
				'33' => 'da',
				'34' => 'Dutch',
				'35' => 'en-us',
				'36' => 'en-gb',
				'37' => 'en-au',
				'38' => 'en-ca',
				'39' => 'en-nz',
				'40' => 'en-ie',
				'41' => 'en-za',
				'42' => 'en-jm',
				'43' => 'en-bz',
				'44' => 'en-tt',
				'45' => 'et',
				'46' => 'fo',
				'47' => 'fi',
				'48' => 'fr-be',
				'49' => 'fr-ca',
				'50' => 'fr-ch',
				'51' => 'fr-lu',
				'52' => 'gd',
				'53' => 'de-ch',
				'54' => 'de-at',
				'55' => 'de-lu',
				'56' => 'de-li',
				'57' => 'he',
				'58' => 'hu',
				'59' => 'is',
				'60' => 'id',
				'61' => 'it',
				'62' => 'it-ch',
				'63' => 'ja',
				'64' => 'ko',
				'65' => 'lv',
				'66' => 'lt',
				'67' => 'mk',
				'68' => 'ms',
				'69' => 'mt',
				'70' => 'no',
				'71' => 'pt-br',
				'72' => 'pt',
				'73' => 'rm',
				'74' => 'ro',
				'75' => 'ro-mo',
				'76' => 'of',
				'77' => 'ru',
				'78' => 'ru-mo',
				'79' => 'sz',
				'80' => 'sr',
				'81' => 'sk',
				'82' => 'sl',
				'83' => 'sb',
				'84' => 'es-mx',
				'85' => 'es-gt',
				'86' => 'es-cr',
				'87' => 'es-pa',
				'88' => 'es-do',
				'89' => 'es-ve',
				'90' => 'es-co',
				'91' => 'es-pe',
				'92' => 'es-ar',
				'93' => 'es-ec',
				'94' => 'es-cl',
				'95' => 'es-uy',
				'96' => 'es-py',
				'97' => 'es-bo',
				'98' => 'es-sv',
				'99' => 'es-hn',
				'100' => 'es-ni',
				'101' => 'es-pr',
				'102' => 'sx',
				'103' => 'sv',
				'104' => 'sv-fi',
				'105' => 'th',
				'106' => 'ts',
				'107' => 'tn',
				'108' => 'tr',
				'109' => 'uk',
				'110' => 'ur',
				'111' => 'vi',
				'112' => 'ji',
			);
		}
		
		
		
		if($id != -1)
		{
			return $__LANGUAGES[$id];
		}
		return $__LANGUAGES;
	}
	function lang_name($p=null)
	{
		$l = array
		(
			'af' => 'Afrikaans',
			'sq' => 'Albanian',
			'ar-sa' => 'Arabic (Saudi Arabia)',
			'ar-iq' => 'Arabic (Iraq)',
			'ar-eg' => 'Arabic (Egypt)',
			'ar-ly' => 'Arabic (Libya)',
			'ar-dz' => 'Arabic (Algeria)',
			'ar-ma' => 'Arabic (Morocco)',
			'ar-tn' => 'Arabic (Tunisia)',
			'ar-om' => 'Arabic (Oman)',
			'ar-ye' => 'Arabic (Yemen)',
			'ar-sy' => 'Arabic (Syria)',
			'ar-jo' => 'Arabic (Jordan)',
			'ar-lb' => 'Arabic (Lebanon)',
			'ar-kw' => 'Arabic (Kuwait)',
			'ar-ae' => 'Arabic (U.A.E.)',
			'ar-bh' => 'Arabic (Bahrain)',
			'ar-qa' => 'Arabic (Qatar)',
			'eu' => 'Basque',
			'bg' => 'Bulgarian',
			'be' => 'Belarusian',
			'ca' => 'Catalan',
			'zh-tw' => 'Chinese (Taiwan)',
			'zh-cn' => 'Chinese (PRC)',
			'zh-hk' => 'Chinese (Hong',
			'zh-sg' => 'Chinese (Singapore)',
			'hr' => 'Croatian',
			'da' => 'Danish',
			'Dutch' => '(Belgium)',
			'en-us' => 'English (United States)',
			'en-gb' => 'English (United Kingdom)',
			'en-au' => 'English (Australia)',
			'en-ca' => 'English (Canada)',
			'en-nz' => 'English (New Zealand)',
			'en-ie' => 'English (Ireland)',
			'en-za' => 'English (South Africa)',
			'en-jm' => 'English (Jamaica)',
			'en-bz' => 'English (Belize)',
			'en-tt' => 'English (Trinidad)',
			'et' => 'Estonian',
			'fo' => 'Faeroese',
			'fi' => 'Finnish',
			'fr-be' => 'French (Belgium)',
			'fr-ca' => 'French (Canada)',
			'fr-ch' => 'French (Switzerland)',
			'fr-lu' => 'French (Luxembourg)',
			'gd' => 'Gaelic (Scotland)',
			'de-ch' => 'German (Switzerland)',
			'de-at' => 'German (Austria)',
			'de-lu' => 'German (Luxembourg)',
			'de-li' => 'German (Liechtenstein)',
			'he' => 'Hebrew',
			'hu' => 'Hungarian',
			'is' => 'Icelandic',
			'id' => 'Indonesian',
			'it' => 'Italian (Standard)',
			'it-ch' => 'Italian (Switzerland)',
			'ja' => 'Japanese',
			'ko' => 'Korean (Johab)',
			'lv' => 'Latvian',
			'lt' => 'Lithuanian',
			'mk' => 'Macedonian (FYROM)',
			'ms' => 'Malaysian',
			'mt' => 'Maltese',
			'no' => 'Norwegian (Nynorsk)',
			'pl' => 'Polish',
			'pt-br' => 'Portuguese (Brazil)',
			'pt' => 'Portuguese (Portugal)',
			'rm' => 'Rhaeto-Romanic',
			'ro' => 'Romanian',
			'ro-mo' => 'Romanian (Republic',
			'of' => 'Moldova)',
			'ru' => 'Russian',
			'ru-mo' => 'Russian (Republic',
			'Sami' => '(Lappish)',
			'sr' => 'Serbian (Latin)',
			'sk' => 'Slovak',
			'sl' => 'Slovenian',
			'sb' => 'Sorbian',
			'es-mx' => 'Spanish (Mexico)',
			'es-gt' => 'Spanish (Guatemala)',
			'es-cr' => 'Spanish (Costa',
			'es-pa' => 'Spanish (Panama)',
			'es-do' => 'Spanish (Dominican Republic)',
			'es-ve' => 'Spanish (Venezuela)',
			'es-co' => 'Spanish (Colombia)',
			'es-pe' => 'Spanish (Peru)',
			'es-ar' => 'Spanish (Argentina)',
			'es-ec' => 'Spanish (Ecuador)',
			'es-cl' => 'Spanish (Chile)',
			'es-uy' => 'Spanish (Uruguay)',
			'es-py' => 'Spanish (Paraguay)',
			'es-bo' => 'Spanish (Bolivia)',
			'es-sv' => 'Spanish (El Salvador)',
			'es-hn' => 'Spanish (Honduras)',
			'es-ni' => 'Spanish (Nicaragua)',
			'es-pr' => 'Spanish (Puerto',
			'sv' => 'Swedish',
			'sv-fi' => 'Swedish (Finland)',
			'ts' => 'Tsonga',
			'tn' => 'Tswana',
			'tr' => 'Turkish',
			'uk' => 'Ukrainian',
			'vi' => 'Vietnamese',
			'ji' => 'Yiddish',
		);
		if($p)
		{
			return $l[$p];
		}                 
		return $l;
	}
	/**
	*	return an array of i18n module information
	*/
	function mod_info($mod)
	{
		$mod_info = array();
		static $mod_info;
		if($mod_info[$mod])
		{
			return $mod_info[$mod];
		}
		if(is_file(CONF::inc_dir()."/mods/".$mod."/etc/version.php"))
		{
			include CONF::inc_dir()."/mods/".$mod."/etc/version.php";
			if($def['i18n'][e::current_lang()])
			{
				$mod_info[$mod]['label'] = $def['i18n'][e::current_lang()]['name'];
				$mod_info[$mod]['description'] = $def['i18n'][e::current_lang()]['description'];
			}
			else
			{
				$mod_info[$mod]['label'] = strtoupper($def['name']);
				$mod_info[$mod]['description'] = $def['description'];
			}
		}
		else
		{
			$mod_info[$mod]['label'] = strtoupper($mod);
			$mod_info[$mod]['description'] =null;
		}
		return $mod_info[$mod];
	}
	/**
	*	returns the id of the language
	*/
	function id($lang){
		foreach(e::lang() as $key => $val){
			if($val == $lang){
				return $key;
			}
		}
		return false;
	}
	/**
	*	return the current lang for the user
	*/
	function current_lang(){
		if(defined('USR_LANG_LOG')){
			return USR_LANG_LOG;
		}
		if(defined('USR_LANG')){
			return USR_LANG;
		}
		return false;
	}
	/**
	*	include the file we need
	*	TODO: backup if the file doesnt exist
	*	file should be an array
	*	we could provide a parser for parsing files of the type: key:val into an array
	*	
	*	with case we switch between singular and plural
	*/
	function o($key = null,$replace = null,$case = false,$page = null){	

		$debug = false;
		if($debug)
		{
			MC::Debug("NEW CALL: ".$key,"-------------------------------------");
		}
		if(!$key)
		{
			return;
		}
		$opc = &OPC::singleton();
		if($page)
		{
			$target_folder = $page;
			if($debug)
			{
				MC::debug($target_folder,'page');
			}
		}
		elseif($opc->lang_page())
		{
			$target_folder = $opc->lang_page();
			if($debug)
			{
				MC::debug($target_folder,'opc');
			}
		}
		
		elseif(@$this->mod_name){

			switch($this->mod_name){
				case 'container':
					$info = get_object_vars($this);
					$mod = explode('_',get_class($this));
					$target_folder = $info['mod'] ? $info['mod'] : $mod[0];

					if($debug)
					{
						MC::debug($target_folder,"1");
					}
					
				break;
				default:
					$target_folder = $this->mod_name;
					if($debug)
					{
						MC::debug($target_folder,"2");
					}
			}
		}
		else{
			switch(strtolower(get_class($this))){
				case 'form':
					$info = get_object_vars($this);
					$target_folder = $opc->current_mod();
if($debug){
	MC::debug($target_folder,"3");
}
				break;
				case 'mc':
					$info = get_object_vars($this);
					$target_folder = UTIL::get_post('mod');
if($debug){
	MC::debug($target_folder,"4");
}
				break;
				case 'opc':
					$info = get_object_vars($this);
					if(@$info['position_info']['mod_name']){
						$target_folder = $info['position_info']['mod_name'];
if($debug){
	MC::debug($target_folder,"5");
}
					}
					elseif(@$info['start_view'][1]){
						$target_folder = $info['start_view'][1];
if($debug){
	MC::debug($target_folder,"6");
}
					}
					else{
						$target_folder = 'system/'.get_class($this);
if($debug){
	MC::debug($target_folder,"7");
}
					}
				break;
				default:
				
					$target_folder = 'system/'.get_class($this);
if($debug){
	MC::debug($target_folder,"8");
}
			}
		}
		
		$lang_folder = defined('USR_LANG_LOG') ? USR_LANG_LOG : USR_LANG;
//		die(USR_LANG);
		if(is_file(CONF::inc_dir().'/mods/'.$target_folder.'/language/'.CONF::project_name().'/'.$lang_folder.'.php'))
		{
			$file = CONF::inc_dir().'/mods/'.$target_folder.'/language/'.CONF::project_name().'/'.$lang_folder.'.php';
		}
		else
		{
			$file = CONF::inc_dir().'/mods/'.$target_folder.'/language/'.$lang_folder.'.php';
		}
		if($debug)
		{
			$OPC = &OPC::singleton();
			$OPC->lang_log[] = $file.' for '.$key;
		}
		
		if(!is_file($file)){
			// try and catch the default language
			if(is_file(CONF::inc_dir().'/mods/'.$target_folder.'/language/'.CONF::project_name().'/'.CONF::lang().'.php'))
			{
				$file = CONF::inc_dir().'/mods/'.$target_folder.'/language/'.CONF::project_name().'/'.CONF::lang().'.php';
			}
			else
			{
				$file = CONF::inc_dir().'/mods/'.$target_folder.'/language/'.CONF::lang().'.php';
			}



			if(!is_file($file))
			{
				$file = CONF::inc_dir().'/etc/lang/'.$lang_folder.'.php';
				if(!is_file($file))
				{
					$file = CONF::inc_dir().'/etc/lang/'.CONF::lang().'.php';
				}
			}
		}


		if(is_file($file))
		{
			include($file);
		}
		else
		{
			return $key;
		}
		

		// now we should be able to return the value for the current key
		// we should provide a possibility for inserting variable values into the string
		$get = is_array($lang[$key]) ? $lang[$key][(int)$case] : $lang[$key];
		
		if(is_array($replace))
		{
			$search = array_keys($replace);
			return str_replace($search,$replace,$get);
		}
		
		$t = $get;
		
		return strlen($t) == 0 ? $key : $t;
		
#		return $lang[$key][(int)$case];
	}
	

	
	// singleton
	function &singleton() {
		static $instance;
		if (is_object($instance)) {
			return $instance;
		}
		return  new e();
	}	
}
?>
