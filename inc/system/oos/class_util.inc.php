<?php

/*.
	require_module 'standard';
	require_module 'pcre';
	require_module 'curl';
	require_module 'xml';
	require_module 'mime_magic';
	require_module 'hash';
	require_module 'regex';
.*/

//	require_once(__DIR__ . '/../oos.lint.sys');

/**
 *	General helper class.
 *
 *	[en] note that this is not instanitated, but accessed as static methods -> util::func()
 *	[en] TODO: propose moving datasets to extrnal files such that they are easier to edit and only
 *	[en] loaded when needed.
 *
 *	[de] allgemeine hilfsklasse. wird nicht instanziert -> util::func()
 *	dated: 12 05 2004
 *	
 *	@author 		hundertelf, Thomas Schindler, Joachim Klinkhammer, Oli Blum <development@hundertelf.com>
 *	@version		1.0 
 *	@since			1.0
 *	@package		util
 */

class util 
{

	/**
	 *	Capitalize first letters of words in a string
	 *
	 *	@param	string	$s	any string
	 *	@return string
	 */

	function capitalize_first($s)
	{
		return preg_replace('/' . '\\' . 'b(\\' . 'w)/e', 'strtoupper("$1")', strtolower($s));
	}

	/**
	 *	Execute and external program (DEPRECATED)
	 *
	 *	Note that this method performs no sanitization on the executed command, which may be a
	 *	security hazard in some contexts.  Please use template_exec to santize calls to external
	 *	programs.
	 *
	 *	Further information:
	 *
	 *	@param	string	$cmd	Shell command
	 *	@return void
	 */
		
	function execute($cmd)
	{
		//	(TODO) propose adding warning here to developers and reminder to use template exec.
		exec($cmd);
	}

	/**
	 *	Delete a file using rm
	 *
	 *	@param	string	$file_name	Location of file to be deleted
	 *	@return	bool				True if deleted, false if not
	 */

	function unlink($file_name) {
		if (false == file_exists($file_name)) { return false; }
		if (false == is_file($file_name)) { return false; }

		$output = array();		//	lines of output from external program
		$return_var	= 0;		//	exit status of called program

		exec("rm \"" . $file_name . "\"", $output, $return_var);
	
		if (0 != $return_var) { return false; }
		if (true == file_exists($file_name)) { return false; }
		return true;
	}

	/**
	 *	Merge two arrays together, safety wrapper for standard array_merge
	 *
	 *	@param	array	$a1	first array
	 *	@param	array	$a2	second array
	 *	@return array
	 */

	function oos_array_merge($a1, $a2)
	{
		if(!is_array($a1)) { return $a2; }
		if(!is_array($a2)) { return $a1; }
		return array_merge($a1,$a2);
	}

	/**
	 *	Create a dictionary of country codes and names
	 *
	 *	@return array
	 */

	function country_codes(){
		$codes = array
		(
			"AR_54" => "Argentina +54",
			"AU_61" => "Australia +61",
			"AT_43" => "Austria +43",
			"BE_32" => "Belgium +32",
			"BG_359" => "Bulgaria +359",
			"BR_55" => "Brazil +55",
			"CA_1" => "Canada +1",
			"CH_41" => "Switzerland +41",
			"CL_56" => "Chile +56",
			"CN_86" => "China +86",
			"CY_357" => "Cyprus +357",
			"CZ_420" => "Czech Republic +420",
			"DE_49" => "Germany +49",
			"DK_45" => "Denmark +45",
			"EE_372" => "Estonia +372",
			"ES_34" => "Spain +34",
			"FI_358" => "Finland +358",
			"FR_33" => "France +33",
			"GB_44" => "United Kingdom +44",
			"HU_36" => "Hungary +36",
			"IE_353" => "Ireland +353",
			"IL_972" => "Israel +972",
			"IT_39" => "Italy +39",
			"JP_81" => "Japan +81",
			"LT_370" => "Lithuania +370",
			"LU_352" => "Luxembourg +352",
			"LV_371" => "Latvia +371",
			"MX_52" => "Mexico +52",
			"NL_31" => "Netherlands +31",
			"NO_47" => "Norway +47",
			"NZ_64" => "New Zealand +64",
			"PE_51" => "Peru +51",
			"PL_48" => "Poland +48",
			"PT_351" => "Portugal +351",
			"RO_40" => "Romania +40",
			"SE_46" => "Sweden +46",
			"SG_65" => "Singapore +65",
			"TR_90" => "Turkey +90",
			"US_1" => "United States +1",
			"BY_375" => "Belarus +375",
		);
		return $codes;
	}

	/**
	 *	Wrapper to implement mime_content_type if not present in local PHP installation
	 *
	 *	@param	string	$file	file location
	 *	@return string
	 */

	function mime_content_type($file){
		if ( ! function_exists ( 'mime_content_type ' ) )
		{
			$s = shell_exec("file -bi " . $file . " | grep -o '[^ ]" . "\\" . "+" . "\\" . "/[^ ,;,:]" . "\\" . "+' ");
			return trim($s);
		}
		else
		{
			return mime_content_type($file);
		}
	}

	/**
	 *	Generates an array of named colos
	 *
	 *	@return array
	 */
		
	function get_color_set(){
    	$colors = array(
			'aliceblue'=>'#F0F8FF',
			'antiquewhite'=>'#FAEBD7',
			'antiquewhite1'=>'#FFEFDB',
			'antiquewhite2'=>'#EEDFCC',
			'antiquewhite3'=>'#CDC0B0',
			'antiquewhite4'=>'#8B8378',
			'aquamarine'=>'#7FFFD4',
			'aquamarine1'=>'#7FFFD4',
			'aquamarine2'=>'#76EEC6',
			'aquamarine3'=>'#66CDAA',
			'aquamarine4'=>'#458B74',
			'azure'=>'#F0FFFF',
			'azure1'=>'#F0FFFF',
			'azure2'=>'#E0EEEE',
			'azure3'=>'#C1CDCD',
			'azure4'=>'#838B8B',
			'beige'=>'#F5F5DC',
			'bisque'=>'#FFE4C4',
			'bisque1'=>'#FFE4C4',
			'bisque2'=>'#EED5B7',
			'bisque2'=>'#EED5B7',
			'bisque3'=>'#CDB79E',
			'bisque4'=>'#8B7D6B',
			'black'=>'#000000',
			'blanchedalmond'=>'#FFEBCD',
			'blue'=>'#0000FF',
			'blue1'=>'#0000FF',
			'blue2'=>'#0000EE',
			'blue3'=>'#0000CD',
			'blue4'=>'#00008B',
			'blueviolet'=>'#8A2BE2',
			'brown'=>'#A52A2A',
			'brown1'=>'#FF4040',
			'brown2'=>'#EE3B3B',
			'brown3'=>'#CD3333',
			'brown4'=>'#8B2323',
			'burlywood'=>'#DEB887',
			'burlywood1'=>'#FFD39B',
			'burlywood2'=>'#EEC591',
			'burlywood3'=>'#CDAA7D',
			'burlywood4'=>'#8B7355',
			'cadetblue'=>'#5F9EA0',
			'cadetblue1'=>'#98F5FF',
			'cadetblue2'=>'#8EE5EE',
			'cadetblue3'=>'#7AC5CD',
			'cadetblue4'=>'#53868B',
			'chartreuse'=>'#7FFF00',
			'chartreuse1'=>'#7FFF00',
			'chartreuse2'=>'#76EE00',
			'chartreuse3'=>'#66CD00',
			'chartreuse4'=>'#458B00',
			'chocolate'=>'#D2691E',
			'chocolate1'=>'#FF7F24',
			'chocolate2'=>'#EE7621',
			'chocolate3'=>'#CD661D',
			'chocolate4'=>'#8B4513',
			'coral'=>'#FF7F50',
			'coral1'=>'#FF7256',
			'coral2'=>'#EE6A50',
			'coral3'=>'#CD5B45',
			'coral4'=>'#8B3E2F',
			'cornflowerblue'=>'#6495ED',
			'cornsilk'=>'#FFF8DC',
			'cornsilk1'=>'#FFF8DC',
			'cornsilk2'=>'#EEE8CD',
			'cornsilk3'=>'#CDC8B1',
			'cornsilk4'=>'#8B8878',
			'crimson'=>'#DC143C',
			'cyan'=>'#00FFFF',
			'cyan1'=>'#00FFFF',
			'cyan2'=>'#00EEEE',
			'cyan3'=>'#00CDCD',
			'cyan4'=>'#008B8B',
			'darkblue'=>'#00008B',
			'darkcyan'=>'#008B8B',
			'darkgoldenrod'=>'#B8860B',
			'darkgoldenrod1'=>'#FFB90F',
			'darkgoldenrod2'=>'#EEAD0E',
			'darkgoldenrod3'=>'#CD950C',
			'darkgoldenrod4'=>'#8B6508',
			'darkgray'=>'#A9A9A9',
			'darkgreen'=>'#006400',
			'darkgrey'=>'#A9A9A9',
			'darkkhaki'=>'#BDB76B',
			'darkmagenta'=>'#8B008B',
			'darkolivegreen'=>'#556B2F',
			'darkolivegreen1'=>'#CAFF70',
			'darkolivegreen2'=>'#BCEE68',
			'darkolivegreen3'=>'#A2CD5A',
			'darkolivegreen4'=>'#6E8B3D',
			'darkorange'=>'#FF8C00',
			'darkorange1'=>'#FF7F00',
			'darkorange2'=>'#EE7600',
			'darkorange3'=>'#CD6600',
			'darkorange4'=>'#8B4500',
			'darkorchid'=>'#9932CC',
			'darkorchid1'=>'#BF3EFF',
			'darkorchid2'=>'#B23AEE',
			'darkorchid3'=>'#9A32CD',
			'darkorchid4'=>'#68228B',
			'darkred'=>'#8B0000',
			'darksalmon'=>'#E9967A',
			'darkseagreen'=>'#8FBC8F',
			'darkseagreen1'=>'#C1FFC1',
			'darkseagreen2'=>'#B4EEB4',
			'darkseagreen3'=>'#9BCD9B',
			'darkseagreen4'=>'#698B69',
			'darkslateblue'=>'#483D8B',
			'darkslategray'=>'#2F4F4F',
			'darkslategray1'=>'#97FFFF',
			'darkslategray2'=>'#8DEEEE',
			'darkslategray3'=>'#79CDCD',
			'darkslategray4'=>'#528B8B',
			'darkslategrey'=>'#2F4F4F',
			'darkturquoise'=>'#00CED1',
			'darkviolet'=>'#9400D3',
			'deeppink'=>'#FF1493',
			'deeppink1'=>'#FF1493',
			'deeppink2'=>'#EE1289',
			'deeppink3'=>'#CD1076',
			'deeppink4'=>'#8B0A50',
			'deepskyblue'=>'#00BFFF',
			'deepskyblue1'=>'#00BFFF',
			'deepskyblue2'=>'#00B2EE',
			'deepskyblue3'=>'#009ACD',
			'deepskyblue4'=>'#00688B',
			'dimgray'=>'#696969',
			'dimgrey'=>'#696969',
			'dodgerblue'=>'#1E90FF',
			'dodgerblue1'=>'#1E90FF',
			'dodgerblue2'=>'#1C86EE',
			'dodgerblue3'=>'#1874CD',
			'dodgerblue4'=>'#104E8B',
			'firebrick'=>'#B22222',
			'firebrick1'=>'#FF3030',
			'firebrick2'=>'#EE2C2C',
			'firebrick3'=>'#CD2626',
			'firebrick4'=>'#8B1A1A',
			'floralwhite'=>'#FFFAF0',
			'forestgreen'=>'#228B22',
			'gainsboro'=>'#DCDCDC',
			'ghostwhite'=>'#F8F8FF',
			'gold'=>'#FFD700',
			'gold1'=>'#FFD700',
			'gold2'=>'#EEC900',
			'gold3'=>'#CDAD00',
			'gold4'=>'#8B7500',
			'goldenrod'=>'#DAA520',
			'goldenrod1'=>'#FFC125',
			'goldenrod2'=>'#EEB422',
			'goldenrod3'=>'#CD9B1D',
			'goldenrod4'=>'#8B6914',
			'gray'=>'#BEBEBE',
			'gray0'=>'#000000',
			'gray1'=>'#030303',
			'gray2'=>'#050505',
			'gray3'=>'#080808',
			'gray4'=>'#0A0A0A',
			'gray5'=>'#0D0D0D',
			'gray6'=>'#0F0F0F',
			'gray7'=>'#121212',
			'gray8'=>'#141414',
			'gray9'=>'#171717',
			'gray10'=>'#1A1A1A',
			'gray11'=>'#1C1C1C',
			'gray12'=>'#1F1F1F',
			'gray13'=>'#212121',
			'gray14'=>'#242424',
			'gray15'=>'#262626',
			'gray16'=>'#292929',
			'gray17'=>'#2B2B2B',
			'gray18'=>'#2E2E2E',
			'gray19'=>'#303030',
			'gray20'=>'#333333',
			'gray21'=>'#363636',
			'gray22'=>'#383838',
			'gray23'=>'#3B3B3B',
			'gray24'=>'#3D3D3D',
			'gray25'=>'#404040',
			'gray26'=>'#424242',
			'gray27'=>'#454545',
			'gray28'=>'#474747',
			'gray29'=>'#4A4A4A',
			'gray30'=>'#4D4D4D',
			'gray31'=>'#4F4F4F',
			'gray32'=>'#525252',
			'gray33'=>'#545454',
			'gray34'=>'#575757',
			'gray35'=>'#595959',
			'gray36'=>'#5C5C5C',
			'gray37'=>'#5E5E5E',
			'gray38'=>'#616161',
			'gray39'=>'#636363',
			'gray40'=>'#666666',
			'gray41'=>'#696969',
			'gray42'=>'#6B6B6B',
			'gray43'=>'#6E6E6E',
			'gray44'=>'#707070',
			'gray45'=>'#737373',
			'gray46'=>'#757575',
			'gray47'=>'#787878',
			'gray48'=>'#7A7A7A',
			'gray49'=>'#7D7D7D',
			'gray50'=>'#7F7F7F',
			'gray51'=>'#828282',
			'gray52'=>'#858585',
			'gray53'=>'#878787',
			'gray54'=>'#8A8A8A',
			'gray55'=>'#8C8C8C',
			'gray56'=>'#8F8F8F',
			'gray57'=>'#919191',
			'gray58'=>'#949494',
			'gray59'=>'#969696',
			'gray60'=>'#999999',
			'gray61'=>'#9C9C9C',
			'gray62'=>'#9E9E9E',
			'gray63'=>'#A1A1A1',
			'gray64'=>'#A3A3A3',
			'gray65'=>'#A6A6A6',
			'gray66'=>'#A8A8A8',
			'gray67'=>'#ABABAB',
			'gray68'=>'#ADADAD',
			'gray69'=>'#B0B0B0',
			'gray70'=>'#B3B3B3',
			'gray71'=>'#B5B5B5',
			'gray72'=>'#B8B8B8',
			'gray73'=>'#BABABA',
			'gray74'=>'#BDBDBD',
			'gray75'=>'#BFBFBF',
			'gray76'=>'#C2C2C2',
			'gray77'=>'#C4C4C4',
			'gray78'=>'#C7C7C7',
			'gray79'=>'#C9C9C9',
			'gray80'=>'#CCCCCC',
			'gray81'=>'#CFCFCF',
			'gray82'=>'#D1D1D1',
			'gray83'=>'#D4D4D4',
			'gray84'=>'#D6D6D6',
			'gray85'=>'#D9D9D9',
			'gray86'=>'#DBDBDB',
			'gray87'=>'#DEDEDE',
			'gray88'=>'#E0E0E0',
			'gray89'=>'#E3E3E3',
			'gray90'=>'#E5E5E5',
			'gray91'=>'#E8E8E8',
			'gray92'=>'#EBEBEB',
			'gray93'=>'#EDEDED',
			'gray94'=>'#F0F0F0',
			'gray95'=>'#F2F2F2',
			'gray96'=>'#F5F5F5',
			'gray97'=>'#F7F7F7',
			'gray98'=>'#FAFAFA',
			'gray99'=>'#FCFCFC',
			'gray100'=>'#FFFFFF',
			'green'=>'#00FF00',
			'green1'=>'#00FF00',
			'green2'=>'#00EE00',
			'green3'=>'#00CD00',
			'green4'=>'#008B00',
			'greenyellow'=>'#ADFF2F',
			'grey'=>'#BEBEBE',
			'grey0'=>'#000000',
			'grey1'=>'#030303',
			'grey2'=>'#050505',
			'grey3'=>'#080808',
			'grey4'=>'#0A0A0A',
			'grey5'=>'#0D0D0D',
			'grey6'=>'#0F0F0F',
			'grey7'=>'#121212',
			'grey8'=>'#141414',
			'grey9'=>'#171717',
			'grey10'=>'#1A1A1A',
			'grey11'=>'#1C1C1C',
			'grey12'=>'#1F1F1F',
			'grey13'=>'#212121',
			'grey14'=>'#242424',
			'grey15'=>'#262626',
			'grey16'=>'#292929',
			'grey17'=>'#2B2B2B',
			'grey18'=>'#2E2E2E',
			'grey19'=>'#303030',
			'grey20'=>'#333333',
			'grey21'=>'#363636',
			'grey22'=>'#383838',
			'grey23'=>'#3B3B3B',
			'grey24'=>'#3D3D3D',
			'grey25'=>'#404040',
			'grey26'=>'#424242',
			'grey27'=>'#454545',
			'grey28'=>'#474747',
			'grey29'=>'#4A4A4A',
			'grey30'=>'#4D4D4D',
			'grey31'=>'#4F4F4F',
			'grey32'=>'#525252',
			'grey33'=>'#545454',
			'grey34'=>'#575757',
			'grey35'=>'#595959',
			'grey36'=>'#5C5C5C',
			'grey37'=>'#5E5E5E',
			'grey38'=>'#616161',
			'grey39'=>'#636363',
			'grey40'=>'#666666',
			'grey41'=>'#696969',
			'grey42'=>'#6B6B6B',
			'grey43'=>'#6E6E6E',
			'grey44'=>'#707070',
			'grey45'=>'#737373',
			'grey46'=>'#757575',
			'grey47'=>'#787878',
			'grey48'=>'#7A7A7A',
			'grey49'=>'#7D7D7D',
			'grey50'=>'#7F7F7F',
			'grey51'=>'#828282',
			'grey52'=>'#858585',
			'grey53'=>'#878787',
			'grey54'=>'#8A8A8A',
			'grey55'=>'#8C8C8C',
			'grey56'=>'#8F8F8F',
			'grey57'=>'#919191',
			'grey58'=>'#949494',
			'grey59'=>'#969696',
			'grey60'=>'#999999',
			'grey61'=>'#9C9C9C',
			'grey62'=>'#9E9E9E',
			'grey63'=>'#A1A1A1',
			'grey64'=>'#A3A3A3',
			'grey65'=>'#A6A6A6',
			'grey66'=>'#A8A8A8',
			'grey67'=>'#ABABAB',
			'grey68'=>'#ADADAD',
			'grey69'=>'#B0B0B0',
			'grey70'=>'#B3B3B3',
			'grey71'=>'#B5B5B5',
			'grey72'=>'#B8B8B8',
			'grey73'=>'#BABABA',
			'grey74'=>'#BDBDBD',
			'grey75'=>'#BFBFBF',
			'grey76'=>'#C2C2C2',
			'grey77'=>'#C4C4C4',
			'grey78'=>'#C7C7C7',
			'grey79'=>'#C9C9C9',
			'grey80'=>'#CCCCCC',
			'grey81'=>'#CFCFCF',
			'grey82'=>'#D1D1D1',
			'grey83'=>'#D4D4D4',
			'grey84'=>'#D6D6D6',
			'grey85'=>'#D9D9D9',
			'grey86'=>'#DBDBDB',
			'grey87'=>'#DEDEDE',
			'grey88'=>'#E0E0E0',
			'grey89'=>'#E3E3E3',
			'grey90'=>'#E5E5E5',
			'grey91'=>'#E8E8E8',
			'grey92'=>'#EBEBEB',
			'grey93'=>'#EDEDED',
			'grey94'=>'#F0F0F0',
			'grey95'=>'#F2F2F2',
			'grey96'=>'#F5F5F5',
			'grey97'=>'#F7F7F7',
			'grey98'=>'#FAFAFA',
			'grey99'=>'#FCFCFC',
			'grey100'=>'#FFFFFF',
			'honeydew'=>'#F0FFF0',
			'honeydew1'=>'#F0FFF0',
			'honeydew2'=>'#E0EEE0',
			'honeydew3'=>'#C1CDC1',
			'honeydew4'=>'#838B83',
			'hotpink'=>'#FF69B4',
			'hotpink1'=>'#FF6EB4',
			'hotpink2'=>'#EE6AA7',
			'hotpink3'=>'#CD6090',
			'hotpink4'=>'#8B3A62',
			'indianred'=>'#CD5C5C',
			'indianred1'=>'#FF6A6A',
			'indianred2'=>'#EE6363',
			'indianred3'=>'#CD5555',
			'indianred4'=>'#8B3A3A',
			'indigo'=>'#4B0082',
			'indigo2'=>'#218868',
			'ivory'=>'#FFFFF0',
			'ivory1'=>'#FFFFF0',
			'ivory2'=>'#EEEEE0',
			'ivory3'=>'#CDCDC1',
			'ivory4'=>'#8B8B83',
			'khaki'=>'#F0E68C',
			'khaki1'=>'#FFF68F',
			'khaki2'=>'#EEE685',
			'khaki3'=>'#CDC673',
			'khaki4'=>'#8B864E',
			'lavender'=>'#E6E6FA',
			'lavenderblush'=>'#FFF0F5',
			'lavenderblush1'=>'#FFF0F5',
			'lavenderblush2'=>'#EEE0E5',
			'lavenderblush3'=>'#CDC1C5',
			'lavenderblush4'=>'#8B8386',
			'lawngreen'=>'#7CFC00',
			'lemonchiffon'=>'#FFFACD',
			'lemonchiffon1'=>'#FFFACD',
			'lemonchiffon2'=>'#EEE9BF',
			'lemonchiffon3'=>'#CDC9A5',
			'lemonchiffon4'=>'#8B8970',
			'lightblue'=>'#ADD8E6',
			'lightblue1'=>'#BFEFFF',
			'lightblue2'=>'#B2DFEE',
			'lightblue3'=>'#9AC0CD',
			'lightblue4'=>'#68838B',
			'lightcoral'=>'#F08080',
			'lightcyan'=>'#E0FFFF',
			'lightcyan1'=>'#E0FFFF',
			'lightcyan2'=>'#D1EEEE',
			'lightcyan3'=>'#B4CDCD',
			'lightcyan4'=>'#7A8B8B',
			'lightgoldenrod'=>'#EEDD82',
			'lightgoldenrod1'=>'#FFEC8B',
			'lightgoldenrod2'=>'#EEDC82',
			'lightgoldenrod3'=>'#CDBE70',
			'lightgoldenrod4'=>'#8B814C',
			'lightgoldenrodyellow'=>'#FAFAD2',
			'lightgray'=>'#D3D3D3',
			'lightgreen'=>'#90EE90',
			'lightgrey'=>'#D3D3D3',
			'lightpink'=>'#FFB6C1',
			'lightpink1'=>'#FFAEB9',
			'lightpink2'=>'#EEA2AD',
			'lightpink3'=>'#CD8C95',
			'lightpink4'=>'#8B5F65',
			'lightsalmon'=>'#FFA07A',
			'lightsalmon1'=>'#FFA07A',
			'lightsalmon2'=>'#EE9572',
			'lightsalmon3'=>'#CD8162',
			'lightsalmon4'=>'#8B5742',
			'lightseagreen'=>'#20B2AA',
			'lightskyblue'=>'#87CEFA',
			'lightskyblue1'=>'#B0E2FF',
			'lightskyblue2'=>'#A4D3EE',
			'lightskyblue3'=>'#8DB6CD',
			'lightskyblue4'=>'#607B8B',
			'lightslateblue'=>'#8470FF',
			'lightslategray'=>'#778899',
			'lightslategrey'=>'#778899',
			'lightsteelblue'=>'#B0C4DE',
			'lightsteelblue1'=>'#CAE1FF',
			'lightsteelblue2'=>'#BCD2EE',
			'lightsteelblue3'=>'#A2B5CD',
			'lightsteelblue4'=>'#6E7B8B',
			'lightyellow'=>'#FFFFE0',
			'lightyellow1'=>'#FFFFE0',
			'lightyellow2'=>'#EEEED1',
			'lightyellow3'=>'#CDCDB4',
			'lightyellow4'=>'#8B8B7A',
			'limegreen'=>'#32CD32',
			'linen'=>'#FAF0E6',
			'magenta'=>'#FF00FF',
			'magenta1'=>'#FF00FF',
			'magenta2'=>'#EE00EE',
			'magenta3'=>'#CD00CD',
			'magenta4'=>'#8B008B',
			'maroon'=>'#B03060',
			'maroon1'=>'#FF34B3',
			'maroon2'=>'#EE30A7',
			'maroon3'=>'#CD2990',
			'maroon4'=>'#8B1C62',
			'mediumaquamarine'=>'#66CDAA',
			'mediumblue'=>'#0000CD',
			'mediumorchid'=>'#BA55D3',
			'mediumorchid1'=>'#E066FF',
			'mediumorchid2'=>'#D15FEE',
			'mediumorchid3'=>'#B452CD',
			'mediumorchid4'=>'#7A378B',
			'mediumpurple'=>'#9370DB',
			'mediumpurple1'=>'#AB82FF',
			'mediumpurple2'=>'#9F79EE',
			'mediumpurple3'=>'#8968CD',
			'mediumpurple4'=>'#5D478B',
			'mediumseagreen'=>'#3CB371',
			'mediumslateblue'=>'#7B68EE',
			'mediumspringgreen'=>'#00FA9A',
			'mediumturquoise'=>'#48D1CC',
			'mediumvioletred'=>'#C71585',
			'midnightblue'=>'#191970',
			'mintcream'=>'#F5FFFA',
			'mistyrose'=>'#FFE4E1',
			'mistyrose1'=>'#FFE4E1',
			'mistyrose2'=>'#EED5D2',
			'mistyrose3'=>'#CDB7B5',
			'mistyrose4'=>'#8B7D7B',
			'moccasin'=>'#FFE4B5',
			'navajowhite'=>'#FFDEAD',
			'navajowhite1'=>'#FFDEAD',
			'navajowhite2'=>'#EECFA1',
			'navajowhite3'=>'#CDB38B',
			'navajowhite4'=>'#8B795E',
			'navy'=>'#000080',
			'navyblue'=>'#000080',
			'oldlace'=>'#FDF5E6',
			'olivedrab'=>'#6B8E23',
			'olivedrab1'=>'#C0FF3E',
			'olivedrab2'=>'#B3EE3A',
			'olivedrab3'=>'#9ACD32',
			'olivedrab4'=>'#698B22',
			'orange'=>'#FFA500',
			'orange1'=>'#FFA500',
			'orange2'=>'#EE9A00',
			'orange3'=>'#CD8500',
			'orange4'=>'#8B5A00',
			'orangered'=>'#FF4500',
			'orangered1'=>'#FF4500',
			'orangered2'=>'#EE4000',
			'orangered3'=>'#CD3700',
			'orangered4'=>'#8B2500',
			'orchid'=>'#DA70D6',
			'orchid1'=>'#FF83FA',
			'orchid2'=>'#EE7AE9',
			'orchid3'=>'#CD69C9',
			'orchid4'=>'#8B4789',
			'palegoldenrod'=>'#EEE8AA',
			'palegreen'=>'#98FB98',
			'palegreen1'=>'#9AFF9A',
			'palegreen2'=>'#90EE90',
			'palegreen3'=>'#7CCD7C',
			'palegreen4'=>'#548B54',
			'paleturquoise'=>'#AFEEEE',
			'paleturquoise1'=>'#BBFFFF',
			'paleturquoise2'=>'#AEEEEE',
			'paleturquoise3'=>'#96CDCD',
			'paleturquoise4'=>'#668B8B',
			'palevioletred'=>'#DB7093',
			'palevioletred1'=>'#FF82AB',
			'palevioletred2'=>'#EE799F',
			'palevioletred3'=>'#CD6889',
			'palevioletred4'=>'#8B475D',
			'papayawhip'=>'#FFEFD5',
			'peachpuff'=>'#FFDAB9',
			'peachpuff1'=>'#FFDAB9',
			'peachpuff2'=>'#EECBAD',
			'peachpuff3'=>'#CDAF95',
			'peachpuff4'=>'#8B7765',
			'peru'=>'#CD853F',
			'pink'=>'#FFC0CB',
			'pink1'=>'#FFB5C5',
			'pink2'=>'#EEA9B8',
			'pink3'=>'#CD919E',
			'pink4'=>'#8B636C',
			'plum'=>'#DDA0DD',
			'plum1'=>'#FFBBFF',
			'plum2'=>'#EEAEEE',
			'plum3'=>'#CD96CD',
			'plum4'=>'#8B668B',
			'powderblue'=>'#B0E0E6',
			'purple'=>'#A020F0',
			'purple1'=>'#9B30FF',
			'purple2'=>'#912CEE',
			'purple3'=>'#7D26CD',
			'purple4'=>'#551A8B',
			'red'=>'#FF0000',
			'red1'=>'#FF0000',
			'red2'=>'#EE0000',
			'red3'=>'#CD0000',
			'red4'=>'#8B0000',
			'rosybrown'=>'#BC8F8F',
			'rosybrown1'=>'#FFC1C1',
			'rosybrown2'=>'#EEB4B4',
			'rosybrown3'=>'#CD9B9B',
			'rosybrown4'=>'#8B6969',
			'royalblue'=>'#4169E1',
			'royalblue1'=>'#4876FF',
			'royalblue2'=>'#436EEE',
			'royalblue3'=>'#3A5FCD',
			'royalblue4'=>'#27408B',
			'saddlebrown'=>'#8B4513',
			'salmon'=>'#FA8072',
			'salmon1'=>'#FF8C69',
			'salmon2'=>'#EE8262',
			'salmon3'=>'#CD7054',
			'salmon4'=>'#8B4C39',
			'sandybrown'=>'#F4A460',
			'seagreen'=>'#2E8B57',
			'seagreen1'=>'#54FF9F',
			'seagreen2'=>'#4EEE94',
			'seagreen3'=>'#43CD80',
			'seagreen4'=>'#2E8B57',
			'seashell'=>'#FFF5EE',
			'seashell1'=>'#FFF5EE',
			'seashell2'=>'#EEE5DE',
			'seashell3'=>'#CDC5BF',
			'seashell4'=>'#8B8682',
			'sgibeet'=>'#8E388E',
			'sgibrightgray'=>'#C5C1AA',
			'sgibrightgrey'=>'#C5C1AA',
			'sgichartreuse'=>'#71C671',
			'sgidarkgray'=>'#555555',
			'sgidarkgrey'=>'#555555',
			'sgigray0'=>'#000000',
			'sgigray4'=>'#0A0A0A',
			'sgigray8'=>'#141414',
			'sgigray12'=>'#1E1E1E',
			'sgigray16'=>'#282828',
			'sgigray20'=>'#333333',
			'sgigray24'=>'#3D3D3D',
			'sgigray28'=>'#474747',
			'sgigray32'=>'#515151',
			'sgigray36'=>'#5B5B5B',
			'sgigray40'=>'#666666',
			'sgigray44'=>'#707070',
			'sgigray48'=>'#7A7A7A',
			'sgigray52'=>'#848484',
			'sgigray56'=>'#8E8E8E',
			'sgigray60'=>'#999999',
			'sgigray64'=>'#A3A3A3',
			'sgigray68'=>'#ADADAD',
			'sgigray72'=>'#B7B7B7',
			'sgigray76'=>'#C1C1C1',
			'sgigray80'=>'#CCCCCC',
			'sgigray84'=>'#D6D6D6',
			'sgigray88'=>'#E0E0E0',
			'sgigray92'=>'#EAEAEA',
			'sgigray96'=>'#F4F4F4',
			'sgigray100'=>'#FFFFFF',
			'sgigrey0'=>'#000000',
			'sgigrey4'=>'#0A0A0A',
			'sgigrey8'=>'#141414',
			'sgigrey12'=>'#1E1E1E',
			'sgigrey16'=>'#282828',
			'sgigrey20'=>'#333333',
			'sgigrey24'=>'#3D3D3D',
			'sgigrey28'=>'#474747',
			'sgigrey32'=>'#515151',
			'sgigrey36'=>'#5B5B5B',
			'sgigrey40'=>'#666666',
			'sgigrey44'=>'#707070',
			'sgigrey48'=>'#7A7A7A',
			'sgigrey52'=>'#848484',
			'sgigrey56'=>'#8E8E8E',
			'sgigrey60'=>'#999999',
			'sgigrey64'=>'#A3A3A3',
			'sgigrey68'=>'#ADADAD',
			'sgigrey72'=>'#B7B7B7',
			'sgigrey76'=>'#C1C1C1',
			'sgigrey80'=>'#CCCCCC',
			'sgigrey84'=>'#D6D6D6',
			'sgigrey88'=>'#E0E0E0',
			'sgigrey92'=>'#EAEAEA',
			'sgigrey96'=>'#F4F4F4',
			'sgigrey100'=>'#FFFFFF',
			'sgilightblue'=>'#7D9EC0',
			'sgilightgray'=>'#AAAAAA',
			'sgilightgrey'=>'#AAAAAA',
			'sgimediumgray'=>'#848484',
			'sgimediumgrey'=>'#848484',
			'sgiolivedrab'=>'#8E8E38',
			'sgisalmon'=>'#C67171',
			'sgislateblue'=>'#7171C6',
			'sgiteal'=>'#388E8E',
			'sgiverydarkgray'=>'#282828',
			'sgiverydarkgrey'=>'#282828',
			'sgiverylightgray'=>'#D6D6D6',
			'sgiverylightgrey'=>'#D6D6D6',
			'sienna'=>'#A0522D',
			'sienna1'=>'#FF8247',
			'sienna2'=>'#EE7942',
			'sienna3'=>'#CD6839',
			'sienna4'=>'#8B4726',
			'silver' => '#C0C0C0',
			'skyblue'=>'#87CEEB',
			'skyblue1'=>'#87CEFF',
			'skyblue2'=>'#7EC0EE',
			'skyblue3'=>'#6CA6CD',
			'skyblue4'=>'#4A708B',
			'slateblue'=>'#6A5ACD',
			'slateblue1'=>'#836FFF',
			'slateblue2'=>'#7A67EE',
			'slateblue3'=>'#6959CD',
			'slateblue4'=>'#473C8B',
			'slategray'=>'#708090',
			'slategray1'=>'#C6E2FF',
			'slategray2'=>'#B9D3EE',
			'slategray3'=>'#9FB6CD',
			'slategray4'=>'#6C7B8B',
			'slategrey'=>'#708090',
			'snow'=>'#FFFAFA',
			'snow1'=>'#FFFAFA',
			'snow2'=>'#EEE9E9',
			'snow3'=>'#CDC9C9',
			'snow4'=>'#8B8989',
			'springgreen'=>'#00FF7F',
			'springgreen1'=>'#00FF7F',
			'springgreen2'=>'#00EE76',
			'springgreen3'=>'#00CD66',
			'springgreen4'=>'#008B45',
			'steelblue'=>'#4682B4',
			'steelblue1'=>'#63B8FF',
			'steelblue2'=>'#5CACEE',
			'steelblue3'=>'#4F94CD',
			'steelblue4'=>'#36648B',
			'tan'=>'#D2B48C',
			'tan1'=>'#FFA54F',
			'tan2'=>'#EE9A49',
			'tan3'=>'#CD853F',
			'tan4'=>'#8B5A2B',
			'thistle'=>'#D8BFD8',
			'thistle1'=>'#FFE1FF',
			'thistle2'=>'#EED2EE',
			'thistle3'=>'#CDB5CD',
			'thistle4'=>'#8B7B8B',
			'tomato'=>'#FF6347',
			'tomato1'=>'#FF6347',
			'tomato2'=>'#EE5C42',
			'tomato3'=>'#CD4F39',
			'tomato4'=>'#8B3626',
			'turquoise'=>'#40E0D0',
			'turquoise1'=>'#00F5FF',
			'turquoise2'=>'#00E5EE',
			'turquoise3'=>'#00C5CD',
			'turquoise4'=>'#00868B',
			'violet'=>'#EE82EE',
			'violetred'=>'#D02090',
			'violetred1'=>'#FF3E96',
			'violetred2'=>'#EE3A8C',
			'violetred3'=>'#CD3278',
			'violetred4'=>'#8B2252',
			'wheat'=>'#F5DEB3',
			'wheat1'=>'#FFE7BA',
			'wheat2'=>'#EED8AE',
			'wheat3'=>'#CDBA96',
			'wheat4'=>'#8B7E66',
			'white'=>'#FFFFFF',
			'whitesmoke'=>'#F5F5F5',
			'yellow'=>'#FFFF00',
			'yellow1'=>'#FFFF00',
			'yellow2'=>'#EEEE00',
			'yellow3'=>'#CDCD00',
			'yellow4'=>'#8B8B00',
			'yellowgreen'=>'#9ACD32',
		);
		return $colors;
	}

	/**
	 *	Look up the name of a HTML color
	 *
	 *	@param	string	$c	an HTML color
	 *	@return string
	 */
		
	function colorname($c)
	{
		$colors_inv = array();
		$tmp = util::get_color_set();								//TODO: cache this on object
		foreach($tmp as $k => $v) { $colors_inv[md5((string)$v)] = $k; }	//  why MD5?
		return (string)$colors_inv[md5($c)];
	}

	/**
	 *	Look up a named HTML color, return empty string if not found
	 *
	 *	@param	string	$name	name of an HTML color
	 *	@return string
	 */

	function color_by_name($name = '') {
		$colors = util::get_color_set();
		if(true == in_array($name, $colors)) { return (string)$colors[$name]; }
		return '';
	}

	/**
	 *	Look up a country name given its two letter code, return empty string if not found
	 *	TODO: load this from an external file and cache on this object
	 *
	 *	@param	string	$code	two letter country code
	 *	@return string
	 */

	function get_countries($code = ''){
		$countries = array
		(
			"AF" => "Afghanistan",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AS" => "American Samoa",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"AQ" => "Antarctica",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BT" => "Bhutan",
			"BO" => "Bolivia",
			"BA" => "Bosnia and Herzegowina",
			"BW" => "Botswana",
			"BV" => "Bouvet Island",
			"BR" => "Brazil",
			"IO" => "British Indian Ocean Territory",
			"BN" => "Brunei Darussalam",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"BI" => "Burundi",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CV" => "Cape Verde",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CC" => "Cocos (Keeling) Islands",
			"CO" => "Colombia",
			"KM" => "Comoros",
			"CG" => "Congo",
			"CD" => "Congo, the Democratic Republic of the",
			"CK" => "Cook Islands",
			"CR" => "Costa Rica",
			"CI" => "Cote d'Ivoire",
			"HR" => "Croatia (Hrvatska)",
			"CU" => "Cuba",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"TP" => "East Timor",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"ER" => "Eritrea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"FK" => "Falkland Islands (Malvinas)",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"FX" => "France, Metropolitan",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"TF" => "French Southern Territories",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GN" => "Guinea",
			"GW" => "Guinea-Bissau",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HM" => "Heard and Mc Donald Islands",
			"VA" => "Holy See (Vatican City State)",
			"HN" => "Honduras",
			"HK" => "Hong Kong",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran (Islamic Republic of)",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"KI" => "Kiribati",
			"KP" => "Korea, Democratic People's Republic of",
			"KR" => "Korea, Republic of",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Lao People's Democratic Republic",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libyan Arab Jamahiriya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macau",
			"MK" => "Macedonia, The Former Yugoslav Republic of",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"MX" => "Mexico",
			"FM" => "Micronesia, Federated States of",
			"MD" => "Moldova, Republic of",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"MS" => "Montserrat",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar",
			"NA" => "Namibia",
			"NR" => "Nauru",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"AN" => "Netherlands Antilles",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NU" => "Niue",
			"NF" => "Norfolk Island",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"PK" => "Pakistan",
			"PW" => "Palau",
			"PA" => "Panama",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PN" => "Pitcairn",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RE" => "Reunion",
			"RO" => "Romania",
			"RU" => "Russian Federation",
			"RW" => "Rwanda",
			"KN" => "Saint Kitts and Nevis",
			"LC" => "Saint LUCIA",
			"VC" => "Saint Vincent and the Grenadines",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"ST" => "Sao Tome and Principe",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SK" => "Slovakia (Slovak Republic)",
			"SI" => "Slovenia",
			"SB" => "Solomon Islands",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"GS" => "South Georgia and the South Sandwich Islands",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"SH" => "St. Helena",
			"PM" => "St. Pierre and Miquelon",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen Islands",
			"SZ" => "Swaziland",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syrian Arab Republic",
			"TW" => "Taiwan, Province of China",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania, United Republic of",
			"TH" => "Thailand",
			"TG" => "Togo",
			"TK" => "Tokelau",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks and Caicos Islands",
			"TV" => "Tuvalu",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"UM" => "United States Minor Outlying Islands",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VE" => "Venezuela",
			"VN" => "Viet Nam",
			"VG" => "Virgin Islands (British)",
			"VI" => "Virgin Islands (U.S.)",
			"WF" => "Wallis and Futuna Islands",
			"EH" => "Western Sahara",
			"YE" => "Yemen",
			"YU" => "Yugoslavia",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe",			
			);
			
		if ('' !== $code)
		{
			$code = strtoupper($code);
			if (isset($countries[$code])) { return $countries[$code]; }
		}

		return '';
	}
	
	/**
	 *	Recursively create a directory and its parent directories
	 *
	 *	@param	string	$folder	path to be created
	 *	@param	string	$mod	unix permissions string
	 *	@return bool
	 */

	function mkdir_recursive($folder, $mod = "0775")
	{
		$path = '';
		$parts = explode("/", $folder);
		foreach($parts as $piece)
		{
			$piece = trim($piece);
			if(strlen($piece) != 0)
			{
				$path .= "/" . $piece;
				if(!is_dir($path))
				{
			    	exec('mkdir '.$path);
					//exec("chmod -R ".substr($mod,1)." ".$path);
				}
			}
		}
		return true;
	}

	/**
	 *	Implements in_array, if not present in PHP
	 *	@param	mixed	$needle		value to search for
	 *	@param	array	$haystack	array to be searched for needle
	 *	@return	bool
	 */

	function in_array($needle, $haystack)
	{
		if(!is_array($haystack)) { return false; }
		if (function_exists("in_array")) { return in_array($needle, $haystack); }

		foreach($haystack as $val)
		{
			if ($val === $needle) { return true; }
		}
		return false;
	}

	/**
	 *	Recursively convert a nested array into XML fragments
	 *	@param	array	$ary	Array to serialize
	 *	@param	string	$xml	XML produced thus far
	 *	@return	string
	 */
		
	function do_array2xml($ary, $xml = '')
	{
		if (!is_array($ary)) { return ''; }
		foreach ($ary as $key_mixed => $val)
		{
			$key = (string)$key_mixed;
			if (is_int($key)) { $key = 'ID_' . $key; }
			$key = strtoupper($key);
			if (is_array($val)	)
			{
					$xml .= '<' . $key . '>';
					$xml = util::do_array2xml($val, $xml);
					$xml .= '</' . $key . '>';
			}
			else
			{
				$xml .= '<' . $key . '>' . (string)$val . '</' . $key . '>';
			}
		}
		return $xml;
	}

	/**
	 *	Convert a nested array into an XML document, does not add DTD
	 *	@param	array	$ary	Array to serialize
	 *	@param	string	$name	Name of root XML entity
	 *	@return	string
	 */

	function array2xml($ary, $name = "ARRAY") {
		return '<'.$name.'>'.util::do_array2xml($ary).'</'.$name.'>';
	}
	
	/**
	 *	Recusively serialize levels of a nested array into a plist XML document
	 *	Returns empty string on failure
	 *	@param	array	$ary	Nested array to be serialized
	 *	@return	string
	 */

	function do_array2plist($ary)
	{
		$xml = '';
		if (!is_array($ary)) { return $xml; }
		foreach ($ary as $key_mixed => $val)
		{
			$key = (string)$key_mixed;
			if (is_int($key)) { $key = "ID" . $key; }
			$xml .= '<key>' . $key . '</key>';

			if (is_array($val)) { $xml .= '<dict>' . util::do_array2plist($val) . '</dict>'; }
			elseif (is_int($val)) { $xml .= '<integer>' . (string)$val . '</integer>'; }
			else { $xml .= '<string>' . (string)$val . '</string>'; }
			}
		return $xml;
	}

	/**
	 *	Serialize an array into a plist XML document (includes DTD)
	 *
	 *	@param	array	$ary	Nested array to be serialized
	 *	@return	string
	 */

	function array2plist($ary)
	{
		return ''
		 . '<?xml version="1.0" encoding="UTF-8"?>'
		 . '<!DOCTYPE plist'
		 . ' PUBLIC "-//Apple Computer//DTD PLIST 1.0//EN"'
		 . ' "http://www.apple.com/DTDs/PropertyList-1.0.dtd"'
		 . '>'
		 . '<plist version="1.0"><dict>' . util::do_array2plist($ary) . '</dict></plist>';
	}

	/**
	 *	Discover if a string is UTF8
	 *
	 *	@param	mixed	$str	String ot array of strings to test
	 *	@return	bool
	 */
	
	function is_utf8($str)
	{
		if (is_array($str))
		{
			$enc = implode('', $str);
			return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
		}
		else
		{
			return (utf8_encode(utf8_decode((string)$str)) === (string)$str);
		}
	}

	/**
	 *	Convert HTML break tags to newlines
	 *
	 *	@param	string	$input	String which may contain line break entities
	 *	@return	string
	 */

	function br2nl($input)
	{
		$input = preg_replace("/<br>/","\n", $input);
		$input = preg_replace("/<br\/>/","\n", $input);
		$input = preg_replace("/<br \/>/","\n", $input);
		$input = preg_replace("/<br >/","\n", $input);
		return $input;
	}

	/**
	 *	Write a string to a file, reimplementation of standard function
	 *
	 *	Differs from standard method in not returning number of bytes on success.
	 *
	 *	@param	string	$filename	Path to a file
	 *	@param	string	$content	New contents of file
	 *	@return	bool
	 */

	function file_put_contents($filename, $content)
	{
		if (function_exists("file_put_contents")) { 
			$bytes = @file_put_contents($filename, $content);
			if (false === $bytes) { return false; }
			return true;
		}

		$handle = @fopen($filename, "w+");
		if (false === $handle) { return false; }
		if (false === fwrite($handle, $content)) { return false; }
		fclose($handle);
		return true;
	}

	/**
	 *	Load contents of a file and return as a string, reimplementation of standard function
	 *
	 *	@param	string	$filename	Path to a file
	 *	@return	mixed
	 */

	function file_get_contents($filename)
	{
		if (function_exists('file_get_contents')) { return @file_get_contents($filename); }

		$file = @fopen($filename,'rb');
		if (false === $file) { return false; }
		$fsize = @filesize($filename);
		$data = '';

		if (false === $fsize)
		{
			// we don't know how big this is, read until it ends
			while (!feof($file)) { $data .= fread($file, 1024); }
		}
		else
		{
			$data = fread($file, $fsize);
		} 

		fclose($file); 
		return $data;

	}

	/**
	 *	Look up a GET or POST field named using RESERVED constant
	 *	TODO: this could use some tidying and clarification to make more maintainable
	 *
	 *	@param	string	$field	Name of a GET or POST variable
	 *	@return	mixed
	 */

	function get_post($field) {
		$rstring = (string)RESERVED;		// defined in /inc/etc/system/constants.cnf
		$rfield = $rstring . $field;
		
		if ((isset($_GET[$rfield])) && ((string)$_GET[$rfield] != $rstring)) { return $_GET[$rfield]; }
		if ((isset($_POST[$rfield])) && ((string)$_POST[$rfield] != $rstring)) { return $_POST[$rfield]; }

		if (isset($_GET[$field])) { return $_GET[$field]; }
		if (isset($_POST[$field])) { return $_POST[$field]; }

		$vlen = strlen($field);
		foreach ($_GET as $key => $val) {
			if (substr($key, 0, $vlen + 1) === $field . '_') {
				$tmp = explode('_', $key, 2);
				return $tmp[1];
			}
		}

		foreach ($_POST as $key => $val) {
			if (substr($key, 0, $vlen + 1) === $field . '_') {
				$tmp = explode('_', $key, 2);
				// react on the possibility that submit type was an image
				if (substr($tmp[1],-2) === '_x')
				{
					// check for the matching opposite
					if (array_key_exists(substr($key,0,-2) . '_y', $_POST))
					{
						return substr($tmp[1],0,-2);
					}
				}
				return $tmp[1];
			}
		}

		return NULL;
			
		//_TODO
		// check magic_quotes / stripslashes ...
			
		//return $val;
	}

	/**
	 *	Return the contents of a named GET or POST field, alias of get_post
	 *	TODO: remove this
	 *
	 *	@param	string	$field	Name or index of a GET or POST field
	 *	@return	mixed
	 */

	function getPost($field) {
		return util::get_post($field);
	}

	/**
	 *	List files or subdirectories in a directory
	 *
	 *	@param	string	$path	location to list from
	 *	@param	string	$type	Set to 'file' (default) or 'dir' to list child files or subdirs
	 *	@return	array
	 */

	function get_dir($path, $type = 'file') {
		$ret = array();
			
		$handle = @opendir($path); 							//TODO: check failure
		while ($file = readdir($handle)) { 					//TODO: strict comparison
			if ($file === "." || $file === "..") continue;
				
			if (($type === 'file') && (is_file($path . '' . $file))) { $ret[] = $file; }
			if (($type === 'dir') && (is_dir($path . '' . $file))) { $ret[] = $file; }
		}

		closedir($handle);
		asort($ret);
		return $ret;
	}
		
	/**
	 *	Wrap javascript string in HTML script tags and comment
	 *
	 *	@param	string	$js	Unwrapped javascript
	 *	@return	string
	 */
	
	function get_js($js) {
		return ''
		 . "<script language=\"JavaScript\">\n"
		 . "<!--\n" . $js . "\n" . "//-->\n"
		 . "</script>";
	}

	/**
	 *	Create a friendly (string) version of filesize in bytes
	 *	@param	float	$s	File size in bytes
	 *	@return	string
	 */

	function format_filesize($s) {
		if ($s <= 1024.00) { return number_format($s, 0, ',', '.').' Byte'; }
		$s = $s / 1024;
		//return number_format($s, 0, ',', '.').' KB';
			
		if ($s <= 1024.00) return number_format($s, 0, ',', '.').' KB';
		$s = $s / 1024;
		return number_format($s, 0, ',', '.').' MB';
	}

	/**
	 *	Guess a MIME type given a file extension
	 *
	 *	TODO: decide on a default unknown/unknown MIME type if file not recognized, currently
	 *	returns empty string on failure.
	 *
	 *	@param	string	$type	File extension
	 *	@return	string
	 */

	function mime_type($type)
	{
		$mt = '';
		switch (strtolower($type)) {						
			case "3gp":			$mt = "video/3gpp";						break;
			case "3g2":			$mt = "video/3gpp2";					break;
			case "3gpp":		$mt = "audio/3gpp";						break;
			case "3gpp2":		$mt = "audio/3gpp2";					break;
			case "aac":			$mt = "audio/x-aac";					break;
			case "ac3":			$mt = "audio/x-ac3";					break;
			case "ai":			$mt = "application/postscript";			break;
			case "aif":			$mt = "audio/x-aiff";					break;
			case "aifc":		$mt = "audio/x-aiff";					break;
			case "aiff":		$mt = "audio/x-aiff";					break;
			case "asc":			$mt = "text/plain";						break;
			case "asf":			$mt = "video/x-ms-asf";					break;
			case "asf_stream":	$mt = "video/x-ms-asf";					break;
			case "asx":			$mt = "video/x-ms-asf";					break;
			case "au":			$mt = "audio/basic";					break;
			case "avi":			$mt = "video/x-msvideo";				break;
			case "bcpio":		$mt = "application/x-bcpio";			break;
			case "bin":			$mt = "application/octet-stream";		break;
			case "c":			$mt = "text/plain";						break;
			case "cc":			$mt = "text/plain";						break;
			case "ccad":		$mt = "application/clariscad";			break;
			case "cdf":			$mt = "application/x-netcdf";			break;
			case "class":		$mt = "application/octet-stream";		break;
			case "cpio":		$mt = "application/x-cpio";				break;
			case "cpt":			$mt = "application/mac-compactpro";		break;
			case "csh":			$mt = "application/x-csh";				break;
			case "css":			$mt = "text/css";						break;
			case "dcr":			$mt = "application/x-director";			break;
			case "dir":			$mt = "application/x-director";			break;
			case "dms":			$mt = "application/octet-stream";		break;
			case "doc":			$mt = "application/msword";				break;
			case "drw":			$mt = "application/drafting";			break;
			case "dvd":			$mt = "video/mpeg";						break;
			case "dvi":			$mt = "application/x-dvi";				break;
			case "dwg":			$mt = "application/acad";				break;
			case "dxf":			$mt = "application/dxf";				break;
			case "dxr":			$mt = "application/x-director";			break;
			case "eps":			$mt = "application/postscript";			break;
			case "etx":			$mt = "text/x-setext";					break;
			case "exe":			$mt = "application/octet-stream";		break;
			case "ez":			$mt = "application/andrew-inset";		break;
			case "f":			$mt = "text/plain";						break;
			case "f90":			$mt = "text/plain";						break;
			case "fli":			$mt = "video/x-fli";					break;
			case "flv":			$mt = "video/x-flv";					break;
			case "gif":			$mt = "image/gif";						break;
			case "gsd":			$mt = "audio/gsm";						break;
			case "gsm":			$mt = "audio/gsm";						break;
			case "gtar":		$mt = "application/x-gtar";				break;
			case "gz":			$mt = "application/x-gzip";				break;
			case "h":			$mt = "text/plain";						break;
			case "h261":		$mt = "video/x-h261";					break;
			case "h263":		$mt = "video/x-h263";					break;
			case "hdf":			$mt = "application/x-hdf";				break;
			case "hh":			$mt = "text/plain";						break;
			case "hqx":			$mt = "application/mac-binhex40";		break;
			case "htm":			$mt = "text/html";						break;
			case "html":		$mt = "text/html";						break;
			case "ice":			$mt = "x-conference/x-cooltalk";		break;
			case "ief":			$mt = "image/ief";						break;
			case "iges":		$mt = "model/iges";						break;
			case "igs":			$mt = "model/iges";						break;
			case "ips":			$mt = "application/x-ipscript";			break;
			case "ipx":			$mt = "application/x-ipix";				break;
			case "jpe":			$mt = "image/jpeg";						break;
			case "jpeg":		$mt = "image/jpeg";						break;
			case "jpg":			$mt = "image/jpeg";						break;
			case "js":			$mt = "application/x-javascript";		break;
			case "kar":			$mt = "audio/midi";						break;
			case "latex":		$mt = "application/x-latex";			break;
			case "lha":			$mt = "application/octet-stream";		break;
			case "lsp":			$mt = "application/x-lisp";				break;
			case "lzh":			$mt = "application/octet-stream";		break;
			case "m":			$mt = "text/plain";						break;
			case "man":			$mt = "application/x-troff-man";		break;
			case "me":			$mt = "application/x-troff-me";			break;
			case "mesh":		$mt = "model/mesh";						break;
			case "mid":			$mt = "audio/midi";						break;
			case "midi":		$mt = "audio/midi";						break;
			case "mif":			$mt = "application/vnd.mif";			break;
			case "mime":		$mt = "www/mime";						break;
			case "mmf":			$mt = "application/vnd.smaf";			break;
			case "mjpeg":		$mt = "video/x-mjpeg";					break;
			case "mov":			$mt = "video/quicktime";				break;
			case "movie":		$mt = "video/x-sgi-movie";				break;
			case "mp2":			$mt = "audio/mpeg";						break;
			case "mp3":			$mt = "audio/mpeg";						break;
			case "mp4":			$mt = "video/mp4";						break;
			case "m4a":			$mt = "audio/mp4";						break;
			case "m4p":			$mt = "audio/audio/x-m4p";				break;
			case "mpe":			$mt = "video/mpeg";						break;
			case "mpeg":		$mt = "video/mpeg";						break;
			case "mpg":			$mt = "video/mpeg";						break;
			case "mpga":		$mt = "audio/mpeg";						break;
			case "ms":			$mt = "application/x-troff-ms";			break;
			case "msh":			$mt = "model/mesh";						break;
			case "m3u":			$mt = "audio/x-mpegurl";				break;
			case "nc":			$mt = "application/x-netcdf";			break;
			case "nut":			$mt = "video/x-nut";					break;
			case "oda":			$mt = "application/oda";				break;
			case "pbm":			$mt = "image/x-portable-bitmap";		break;
			case "pdb":			$mt = "chemical/x-pdb";					break;
			case "pdf":			$mt = "application/pdf";				break;
			case "pgm":			$mt = "image/x-portable-graymap";		break;
			case "pgn":			$mt = "application/x-chess-pgn";		break;
			case "png":			$mt = "image/png";						break;
			case "pnm":			$mt = "image/x-portable-anymap";		break;
			case "pot":			$mt = "application/mspowerpoint";		break;
			case "ppm":			$mt = "image/x-portable-pixmap";		break;
			case "pps":			$mt = "application/mspowerpoint";		break;
			case "ppt":			$mt = "application/mspowerpoint";		break;
			case "ppz":			$mt = "application/mspowerpoint";		break;
			case "pre":			$mt = "application/x-freelance";		break;
			case "prt":			$mt = "application/pro_eng";			break;
			case "ps":			$mt = "application/postscript";			break;
			case "qt":			$mt = "video/quicktime";				break;
			case "ra":			$mt = "audio/x-realaudio";				break;
			case "ram":			$mt = "audio/x-pn-realaudio";			break;
			case "ras":			$mt = "image/cmu-raster";				break;
			case "rgb":			$mt = "image/x-rgb";					break;
			case "rm":			$mt = "audio/x-pn-realaudio";			break;
			case "rmf":			$mt = "audio/rmf";						break;
			case "roff":		$mt = "application/x-troff";			break;
			case "rpm":			$mt = "audio/x-pn-realaudio-plugin";	break;
			case "rtf":			$mt = "text/rtf";						break;
			case "rtx":			$mt = "text/richtext";					break;
			case "scm":			$mt = "application/x-lotusscreencam";	break;
			case "set":			$mt = "application/set";				break;
			case "sgm":			$mt = "text/sgml";						break;
			case "sgml":		$mt = "text/sgml";						break;
			case "sh":			$mt = "application/x-sh";				break;
			case "shar":		$mt = "application/x-shar";				break;
			case "silo":		$mt = "model/mesh";						break;
			case "sit":			$mt = "application/x-stuffit";			break;
			case "skd":			$mt = "application/x-koan";				break;
			case "skm":			$mt = "application/x-koan";				break;
			case "skp":			$mt = "application/x-koan";				break;
			case "skt":			$mt = "application/x-koan";				break;
			case "smi":			$mt = "application/smil";				break;
			case "smil":		$mt = "application/smil";				break;
			case "snd":			$mt = "audio/basic";					break;
			case "sol":			$mt = "application/solids";				break;
			case "spl":			$mt = "application/x-futuresplash";		break;
			case "src":			$mt = "application/x-wais-source";		break;
			case "step":		$mt = "application/STEP";				break;
			case "stl":			$mt = "application/SLA";				break;
			case "stp":			$mt = "application/STEP";				break;
			case "stream":		$mt = "audio/x-qt-stream";				break;
			case "sv4cpio":		$mt = "application/x-sv4cpio";			break;
			case "sv4crc":		$mt = "application/x-sv4crc";			break;
			case "svcd":		$mt = "video/mpeg";						break;
			case "swf":			$mt = "application/x-shockwave-flash";	break;
			case "t":			$mt = "application/x-troff";			break;
			case "tar":			$mt = "application/x-tar";				break;
			case "tcl":			$mt = "application/x-tcl";				break;
			case "tex":			$mt = "application/x-tex";				break;
			case "textinfo":	$mt = "application/x-texinfo";			break;
			case "tif":			$mt = "image/tiff";						break;
			case "tiff":		$mt = "image/tiff";						break;
			case "tr":			$mt = "application/x-troff";			break;
			case "tsi":			$mt = "audio/TSP-audio";				break;
			case "tsp":			$mt = "application/dsptype";			break;
			case "tsv":			$mt = "text/tab-separated-values";		break;
			case "txt":			$mt = "text/plain";						break;
			case "unv":			$mt = "application/i-deas";				break;
			case "ustar":		$mt = "application/x-ustar";			break;
			case "vcd":			$mt = "application/x-cdlink";			break;
			case "vda":			$mt = "application/vda";				break;
			case "viv":			$mt = "video/vnd.vivo";					break;
			case "vivo":		$mt = "video/vnd.vivo";					break;
			case "vob":			$mt = "video/mpeg";						break;
			case "vrml":		$mt = "model/vrml";						break;
			case "wav":			$mt = "audio/x-wav";					break;
			case "wax":			$mt = "audio/x-ms-wax";					break;
			case "wbmp":		$mt = "image/vnd.wap.wbmp";				break;
			case "wm":			$mt = "video/x-ms-wm";					break;
			case "wma":			$mt = "audio/x-ms-wma";					break;
			case "wml":			$mt = "text/vnd.wap.wml";				break;
			case "wmls":		$mt = "text/vnd.wap.wmlscript";			break;
			case "wmv":			$mt = "video/x-ms-wmv";					break;
			case "wmx":			$mt = "video/x-ms-wmx";					break;
			case "wrl":			$mt = "model/vrml";						break;
			case "wtx":			$mt = "audio/wtx";						break;
			case "wvx":			$mt = "video/x-ms-wvx";					break;
			case "xbm":			$mt = "image/x-xbitmap";				break;
			case "xlc":			$mt = "application/vnd.ms-excel";		break;
			case "xll":			$mt = "application/vnd.ms-excel";		break;
			case "xlm":			$mt = "application/vnd.ms-excel";		break;
			case "xls":			$mt = "application/vnd.ms-excel";		break;
			case "xlw":			$mt = "application/vnd.ms-excel";		break;
			case "xml":			$mt = "text/xml";						break;
			case "xpm":			$mt = "image/x-xpixmap";				break;
			case "xwd":			$mt = "image/x-xwindowdump";			break;
			case "xyz":			$mt = "chemical/x-pdb";					break;
			case "zip":			$mt = "application/zip";				break;
			default:			$mt = "";	//TODO: consider 'application/unknown' here
		}
		return $mt;
	}

	/**
	 *	Delete a single file, directory or directory tree
	 *	@param	string	$file	Path to delete
	 *	@return void
	 */

	function delete_folder($file)
	{ 
		@chmod($file,0777); 
		if (is_dir($file)) { 
			$handle = @opendir($file);					//TODO: failure check
			while ($filename = readdir($handle)) {		//TODO:	strict checks
				if ($filename != "." && $filename != "..") { 
					util::delete_folder($file."/".$filename); 
				}
			}
			closedir($handle); 
			@rmdir($file); 

		} else { @unlink($file); } 
	}

	/**
	 *	Change permissions and unlink
	 *	@param	string	$file	Path to file to be removed
	 *	@return void
	 */
		
	function delete_file($file)
	{ 
		@chmod($file,0777); 
		@unlink($file); 
	}

	/**
	 *	Delete a single file, directory or directory tree, is just an alias of delete_folder
	 *	@param	string	$file	Path to delete
	 *	@return void
	 */

	function delete($file)
	{
		util::delete_folder($file);
	}
	
	
	/**
	*	normalize a string
	*	@param 	string 	$str string to normalize
	*	@return string
	*/
	function norm($str)
	{
		$str = str_replace(array('ö', 'ä', 'ü', 'ß'), array('oe', 'ae', 'ue', 'ss'), strtolower($str));
		$str = preg_replace('/ /', '_', $str);
		$str = preg_replace('/[^0-9a-z_]+/', '', $str);
		return $str;
	}
	
}

?>
