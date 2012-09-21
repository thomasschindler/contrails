<?php

	require_once('class_template_exec.inc.php');

/**
 *	new imagemagick interface for djinni
 *
 *	this should provide a proper interface to imagemagick
 *	basic commandlines of imagemagick should be added as we go
 *	these should end up in a complete interface for imagemanipulation
 *                                                                   
 *	reason for implementation:
 *	need for text - subline in images
 *	DEVELOPMENT STATUS!
 *	might change at any time
 */

/*.
	require_module 'standard';
	require_module 'gd';
.*/

class magick {

	/**
	 *	Absolute location of imagemagick binaries
	 *	@var string
	 */

	private $path = '';

	/**
	 *	constructor 
	 *
	 *	takes an optional argument path as path to imagemagick binaries
	 *	@param	string	$path	Path to imagemagick libraries
	 *	@return	void
	 */

	function __construct($path = '')
	{
		#$path = !$path ? CONF::bin_dir() : $path."/";	
		$this->path = $path ? $path . "/" : "/usr/bin/";
	}
     
	/**
	 *	singleton
	 *	@param	string	$path	Path to imagemagick libraries
	 *	@return	magick
	 */

	public static function &singleton($path = null) {
		static /*. magick .*/ $instance;	
		if (is_object($instance)) { return $instance; }
		return new magick($path);
	}
	     
	/**
	 *  rotate an image by deg degrees
	 *	@param	string	$from	Source file? (CHECKME)
	 *	@param	int		$deg	Number of degrees to rotate by
	 *	@param	string	$how	'lesswidth'|'morewidth'
	 *	@param	string	$to		Destination file? (CHECKME)
	 *	@return bool
	 */ 
	 	 
	function rotate($from, $deg = 90, $how = 'morewidth', $to = '') 
	{
		if ('' === $to) { $to = $from; }

		$tmp = ('lesswidth' === $how) ? "<" : (('morewidth' === $how) ? ">" : '');

		//	previous direct construction, remove after testing template exec, strix, 2012-03-08
		#$command = $this->path . '/convert -rotate ' . $deg . $tmp . ' ' . $from . ' ' . $to;
		#$i = shell_exec($command);	//TODO: check result, return false if this failed

		$template = ''
		  . '%%magick_path%%/convert %%from_file%% -rotate "%%deg%%%%tmp%%" %%to_file%%';

		$values = array(
			'magick_path' => $this->path,
			'deg' => $deg,
			'tmp' => $tmp,
			'from_file' => $from,
			'to_file' => $to
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'deg' => 'float',
			'tmp' => 'oneof:<||>',
			'from_file' => 'extant_file',
			'to_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//TODO: handle / report on error case
		return false;
	}
	
	/**
	 *	Add a border to an image (CHECKME)
	 *
	 *	See imagemagick documentation for $leftright and $topbottom geometry arguments:
	 *	http://www.imagemagick.org/script/command-line-processing.php#geometry
	 *
	 *	@param	string	$file_in	Source file
	 *	@param	string	$leftright	(DOCUMENTME)
	 *	@param	string	$topbottom	(DOCUMENTME)
	 *	@param	string	$color		Color name or HTML color.
	 *	@param	string	$file_out	Destination file. Input file overwritten if not specified.
	 *	@return	bool
	 */
	
	function border($file_in, $leftright, $topbottom = '', $color = 'white', $file_out = '')
	{
		if (!$topbottom) { $topbottom = $leftright; }                           

		//	previous direct construction, remove after testing template_exec, strix, 2012-03-08
		#$command = ''
		# . $this->path . 'convert ' . $file_in
		# . ' -bordercolor ' . $color
		# . ' -border ' . $leftright . 'x' . $topbottom . ' '
		# . (('' === $file_out) ? $file_in : $file_out);
		#$i = shell_exec($command);	//TODO: check result, return false if this failed

		$template = ''
		 . '%%magick_path%%convert %%from_file%%'  
		 . ' -bordercolor %%color%%' 
		 . ' -border %%leftright%%x%%topbottom%% '
		 . '%%to_file%%';

		$values = array(
			'magick_path' => $this->path,
			'from_file' => $file_in,
			'color' => $color,
			'leftright' => $leftright,
			'topbottom' => $topbottom,
			'to_file' => (('' === $file_out) ? $file_in : $file_out)
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'from_file' => 'extant_file',
			'color' => 'string',
			'leftright' => 'string',
			'topbottom' => 'string',
			'to_file' => 'none',
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//TODO: handle / report on error case
		return false;
	}
	
	/**
	 *	create a thumbnail
	 *
	 *	Default size will be 100x100 if not specified
	 *	TODO: consider making default thumbnail size a setting
	 *
	 *	@param	string	$from		Source file (CHECKME)
	 *	@param	string	$to			Destination file (CHECKME)
	 *	@param	int		$width		Width of thumbnail? (CHECKME)
	 *	@param	int		$height		Height of thumbnail? (CHECKME)
	 *	@return	bool
	 */
	
	function thumbnail($from, $to, $width = -1, $height = -1)
	{
		if (-1 == $width) { $width = 100; }
		if ((0 == $height) || (-1 == $height)) { $height = $width; }

		//TODO: remove this once template_exec is tested, strix 2012-03-08
		#$command = ''
		# . $this->path . 'convert '
		# . $from
		# . ' -thumbnail ' . $width . 'x' . $height
		# . ' -gravity center'
		# . ' -extent ' . $width . 'x' . $height
		# . ' -auto-orient '
		# . $to;   
		#$i = shell_exec($command);

		$template = ''
		 . '%%magick_path%%convert %%from_file%%'
		 . ' -thumbnail %%width%%x%%height%%'
		 . ' -gravity center'
		 . ' -extent %%width%%x%%height%%'
		 . ' -auto-orient '
		 . ' %%to_file%%';   
			
		$values = array(
			'magick_path' => $this->path,
			'width' => (string)$width,
			'height' => (string)$height,
			'from_file' => $from,
			'to_file' => $to
		);	

		$constraints = array(
			'magick_path' => 'extant_dir',
			'width' => 'int',
			'height' => 'int',
			'from_file' => 'extant_file',
			'to_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//TODO: handle / report on error case
		return false;
	}
	
	
	/**
	 *	set the resolution of an image / pdf / etc
	 *	@param	string	$file_in	File to find resolution of
	 *	@return	int
	 */                  
	
	
	function dpi_units($file_in)
	{
		if($file_out == null)
		{
			$file_out = $file_in;
		}
		
		$template = '%%magick_path%%convert %%file_in%% -units PixelsPerInch %%file_in%%';

		$values = array(
			'magick_path' => $this->path,
			'file_in' => $file_in,
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'file_in' => 'extant_file',
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();
		
		if (0 == $check) { return true; }
		//	(TODO) handle / report error cases here
		return false;
	}	
	
	/**
	 *	set the resolution of an image / pdf / etc
	 *	@param	string	$file_in	File to find resolution of
	 *	@return	int
	 */                  
	
	
	function dpi_set($file_in,$dpi,$file_out=null)
	{
		if($file_out == null)
		{
			$file_out = $file_in;
		}
		$template = '%%magick_path%%convert %%file_in%% -density %%dpi%% %%file_out%%';

		$values = array(
			'magick_path' => $this->path,
			'file_in' => $file_in,
			'file_out' => $file_out,
			'dpi' => (int)$dpi
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'file_in' => 'extant_file',
			'file_out' => 'none',
			'dpi' => 'int'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();
		
		if (0 == $check) { return true; }
		//	(TODO) handle / report error cases here
		return false;
	}
	
	/**
	 *	get the resolution of an image / pdf / etc
	 *	alias for dpi_get
	 *	@param	string	$file_in	File to find resolution of
	 *	@return	int
	 */
	
	function resolution($file_in, $cache=true)
	{   
		return $this->dpi_get($file_in,$cache);
	}
	
	
	/**
	 *	get the resolution of an image / pdf / etc
	 *	@param	string	$file_in	File to find resolution of
	 *	@return	int
	 */
	
	function dpi_get($file_in,$cache=true)
	{
		$opc = OPC::singleton();	

		if($cache)
		{
			if($opc->magick_cache['resolution'][md5($file_in)])
			{
				return $opc->magick_cache['resolution'][md5($file_in)];
			}
		}

		//TODO: remove once template exec is tested, strix, 2012-03-08
		#$command = $this->path . 'identify -format "%x" ' . $file_in;   
		#$i = shell_exec($command);                              

		$template = '%%magick_path%%identify -format "%x" %%from_file%%';

		$values = array(
			'magick_path' => $this->path,
			'from_file' => $file_in
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'from_file' => 'extant_file'
		);
		
		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		$i = implode("\n", $cmd->output);

		$f = 1;
		if (false !== strpos($i, 'PixelsPerCentimeter')) { $f = 2.54; }	//	(DOCUMENTME) $f?
		$i = explode(" ",$i);

		//	if the resolution is lower than 1
		//	(DOCUMENTME) when does this occur?
		if ($i[0] < 1) { $i[0] = (string)str_replace(".","",$i[0]); }

		$file_hash = md5($file_in);
		
		$opc->magick_cache['resolution'][$file_hash] = round(trim($i[0])*$f);
//		$opc->magick_cache['resolution'][$file_hash] = trim($i[0])*$f;
		return $opc->magick_cache['resolution'][$file_hash];
	}	
	
	/**
	 *	get the width of an image / pdf / etc
	 *	@param	string	$file_in	File to find width of
	 *	@return	int
	 */
	
	function width_get($file_in)
	{
		$cache = true;
		$opc = OPC::singleton();	

		if($cache)
		{
			if($opc->magick_cache['width_get'][md5($file_in)])
			{
				return $opc->magick_cache['width_get'][md5($file_in)];
			}
		}

		//TODO: remove once template exec is tested, strix, 2012-03-08
		#$command = $this->path . 'identify -format "%x" ' . $file_in;   
		#$i = shell_exec($command);                              

		$template = '%%magick_path%%identify -format "%w" %%from_file%%';

		$values = array(
			'magick_path' => $this->path,
			'from_file' => $file_in
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'from_file' => 'extant_file'
		);
		
		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();
		
		$i = $cmd->output;
		
		$file_hash = md5($file_in);

		$opc->magick_cache['width_get'][$file_hash] = trim($i[0]);
		return $opc->magick_cache['width_get'][$file_hash];
	}
	
	/**
	 *	get the height of an image / pdf / etc
	 *	@param	string	$file_in	File to find height of
	 *	@return	int
	 */
	
	function height_get($file_in)
	{
		$cache = true;
		$opc = OPC::singleton();	

		if($cache)
		{
			if($opc->magick_cache['height_get'][md5($file_in)])
			{
				return $opc->magick_cache['height_get'][md5($file_in)];
			}
		}

		//TODO: remove once template exec is tested, strix, 2012-03-08
		#$command = $this->path . 'identify -format "%x" ' . $file_in;   
		#$i = shell_exec($command);                              

		$template = '%%magick_path%%identify -format "%h" %%from_file%%';

		$values = array(
			'magick_path' => $this->path,
			'from_file' => $file_in
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'from_file' => 'extant_file'
		);
		
		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();
		
		$i = $cmd->output;
		
		$file_hash = md5($file_in);

		$opc->magick_cache['height_get'][$file_hash] = trim($i[0]);
		return $opc->magick_cache['height_get'][$file_hash];
	}
	
	/**
	 *  crop 
	 *
	 *	@param	string	$file_in	Source file.
	 *	@param	string	$file_out	Destination file
	 *	@param	string	$w			Width (pixels?) (CHECKME)
	 *	@param	string	$h			Height (pixels?) (CHECKME)
	 *	@param	int		$x			Left offset of crop (CHECKME)
	 *	@param	int		$y			Top offset of crop (CHECKME)
	 *	@return	bool
	 */      
	
	function crop($file_in, $file_out, $w, $h, $x = 0, $y = 0)
	{
		//TODO: check and sanitize arguments

		//	previous version, using direct exec, remove once templated version is tested
		#// necessary hack to make sure the filesize is acceptable
		#$command = ''
		# . $this->path . "convert"
		# . " -crop '" . $w . "x" . $h . "+" . $x . "+" . $y . "'"
		# . " " . $file_in . " " . $file_out . ".jpg";
		#UTIL::execute($command, $returnarray, $returnvalue);

		$template = ''
		 . "%%magick_path%%convert"
		 . " -crop '%%width%%x%%height%%+%%x%%+%%y%%'"
		 . " %%from_file%% %%to_file%%.jpg";


		$values = array(
			'magick_path' => $this->path,
			'width' => (string)$w,
			'height' => (string)$h,
			'x' => $x,
			'y' => $y,
			'from_file' => $file_in,
			'to_file' => $file_out
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'width' => 'float',
			'height' => 'float',
			'x' => 'float',
			'y' => 'float',
			'from_file' => 'extant_file',
			'to_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();
		if (0 != $check) { return false; }					//	indicates completed correctly

		//	try convert to target format and delete the intermediate .jpg
		$converted = $this->convert($file_out . ".jpg", $file_out);
		UTIL::delete_file($file_out . ".jpg");
		return $converted;
	}

	/**
	 *	create a canvas with a color gradient
	 *	@param	string	$outfile		Location to save image to
	 *	@param	int		$width			Width of image (pixels?) (CHECKME)
	 *	@param	int		$height			Height of image (pixels?) (CHECKME)
	 *	@param	string	$topcolor		Color name, or HTML color? (CHECKME)
	 *	@param	string	$bottomcolor	Color name, or HTML color? (CHECKME)
	 *	@param	int		$angle			(DOCUMENTME) why not an int, degrees (CHECKME)
	 *	@return	bool
	 */
	
	function gradient(
		$outfile = '', $width = 100, $height = 100,
		$topcolor = 'black', $bottomcolor = 'white', $angle = 0
	) {
		if('' === $outfile) { return; }
		$tmp = CONF::tmp_dir() . '/' . uniqid() . '.png';
		$tmp = str_replace('//', '/', $tmp);

		//	previous direct exec, remove when templated exec has been tested, strix, 2012-03-23
		#$cmd = ''
		# . $this->path . 'convert'
		# . ' -size ' . $width . 'x' . $height
		# . ' gradient:"' . $topcolor . '-' . $bottomcolor . ' " '
		# . $tmp;
		#UTIL::execute($cmd);

		$template = ''
		 . '%%magick_path%%convert'
		 . ' -size %%width%%x%%height%%'
		 . ' gradient:"%%topcolor%%-%%bottomcolor%% "'
		 . ' %%temp_file%%';

		$values = array(
			'magick_path' => $this->path,
			'width' => (string)$width,
			'height' => (string)$height,
			'topcolor' => $topcolor,
			'bottomcolor' => $bottomcolor,
			'angle' => (string)$angle,
			'temp_file' => $tmp,
			'out_file' => $outfile
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'width' => 'int',
			'height' => 'int',
			'topcolor' => 'string',
			'bottomcolor' => 'string',
			'angle' => 'float',
			'temp_file' => 'file',
			'out_file' => 'file'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 !== $check) {
			//	(TODO) handle failure here
			return false;
		}

		if($angle != 0)
		{
			//	previous version, remove once templated exec is tested, strix 2012-03-23
			#$cmd = $this->path . 'convert ' . $tmp . ' -distort SRT "' . $angle . '" '. $tmp;
			#UTIL::execute($cmd);

			$template = ''
			 . '%%magick_path%%convert'
			 . ' %%temp_file%%'
			 . ' -distort SRT "%%angle%%"'
			 . ' %%temp_file%%';

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();		//	(TODO) handle failure here
		}

		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$cmd = $this->path . 'convert ' . $tmp .' ' . $outfile;
		#UTIL::execute($cmd);

		$template = '%%magick_path%%convert %%temp_file%% %%out_file%%';
		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();			//	(TODO) handle failure here

		if (0 !== $check) { return false; }

		UTIL::delete_file($tmp);
		return true;
	}


	/**
	 *	(DOCUMENTME) Appears to superimpose one image on another to create a new image
	 *
	 *	@param	string	$backimage	Background image (CHECKME)
	 *	@param	string	$topimage	Image to be added on top of background (CHECKME)
	 *	@param	int		$left		x position of superimposed image (CHECKME)
	 *	@param	int		$top		y position of superimposed image (CHECKME)
	 *	@param	string	$outimage	Destination file? (CHECKME)
	 *	@return bool
	 */

	function stack($backimage, $topimage, $left = 0, $top = 0, $outimage = '')
	{
		if('' === $outimage) { $outimage = $backimage; }

		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$cmd = ''
		# . $this->path . 'composite'
		# . ' -compose src-over'
		# . ' -geometry +' . $left . '+' . $top
		# . ' ' . $topimage . ' ' . $backimage . ' ' . $outimage;
		#UTIL::execute($cmd);

		$template = ''
		 . '%%magick_path%%composite'
		 . ' -compose src-over'
		 . ' -geometry +%%left%%+%%top%%'
		 . ' %%topimage%% %%backimage%% %%outimage%%';

		$values = array(
			'magick_path' => $this->path,
			'left' => (string)$left,
			'top' => (string)$top,
			'topimage' => $topimage,
			'backimage' => $backimage,
			'outimage' => $outimage
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'left' => 'int',
			'top' => 'int',
			'topimage' => 'file',
			'backimage' => 'file',
			'outimage' => 'file'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();			//	(TODO) handle failure here

		if (0 == $check) { return true; }
		//	(TODO) handle / report on error cases here
		return false;
		// composite -compose atop -geometry -13-17 white-highlight.png red-circle.png red-ball.png		
	}	

	/**
	 *	Write text into an image      
	 *
	 *	if $file2 is empty text will be written to file1
	 *
 	 *	@param	string	$text		Test to superimpose in an image
	 *	@param	int		$x			Left offset, pixels? (CHECKME)
	 *	@param	int		$y			Top offset, pixels? (CHECKME)
	 *	@param	string	$font		(DOCUMENTME)
	 *	@param	int		$size		(DOCUMENTME) should this be float?
	 *	@param	string	$outline	(DOCUMENTME) Unused? 
	 *	@param	string	$fill		Fill color name, or HTML color? (CHECKME)
	 *	@param	string	$file1		Source file? (CHECKME)
	 *	@param	string	$file2		Destination file (CHCECKME)
	 *	@param	string	$gravity	(DOCUMENTME) see imagemagick documentation
	 *	@return	bool
	 */

	function write_text(
		$text, $x = 0, $y = 0, $family = 'Courier', $size = 12, $outline = '', $fill = 'white', 
		$file1 = '', $file2 = '', $gravity = "Center",$rotation=0
	) {
		if ('' === $file1) { return; }
		$file2 = ('' === $file2) ? $file1 : $file2;

		//	prevent text file inclusion
		//	see: http://www.imagemagick.org/script/command-line-options.php#annotate
		//	for text arguments of the form @myfile.txt or @/etc/passwd
		if ('@' == substr($text, 0, 1)) { $text = "\\" . $text; }

		/*
		//	commented out when found, strix 2012-03-23
		if ('' !== $font) { $font = " = font ".$font; }

		$cmd = ''
		 . $this->path . 'convert'
		 . ' ' . $font
		 . ' -fill ' . $fill
		 . ' -gravity ' . $gravity
		 . ' -draw "text ' . $x . ',' . $y . ' \'' . $text . '\'" '
		 . $file1 . ' ' . $file2;
		*/

		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$cmd = ''
		# . $this->path . "convert"
		# . " -pointsize " . $size
		# . " -annotate +" . $x . "+" . $y . " '" . $text . "'"
		# . " " . $file1
		# . " " . $file2;
		#UTIL::execute($cmd);

		$template = ''
		 . "%%magick_path%%convert"
		 . " -family %%family%%"
		 . " -pointsize %%pointsize%%"
		 . " -fill %%fill%%"
		 . " -annotate %%rotation_a%%x%%rotation_b%%+%%pos_x%%+%%pos_y%% '%%text%%'"
		 . " %%source_file%%"
		 . " %%dest_file%%";

		$values = array(
			'magick_path' => $this->path,
			'family' => (string)$family,
			'pointsize' => (string)$size,
			'pos_x' => (string)$x,
			'pos_y' => (string)$y,
			'rotation_a' => (string)$rotation,
			'rotation_b' => (string)$rotation,
			'text' => $text,
			'fill' => $fill,
			'source_file' => $file1,
			'dest_file' => $file2
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'family' => 'string',
			'pointsize' => 'float',
			'pos_x' => 'int',
			'pos_y' => 'int',
			'rotation_a' => 'int',
			'rotation_b' => 'int',
			'text' => 'string',
			'fill' => 'string',
			'source_file' => 'extant_file',
			'dest_file' => 'none',
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		
		MC::debug($cmd->err_msg);
		
		//	(TODO) handle / report on erro cases here
		return false;
	}

	/**
	 *	Create a blank image canvas
	 *
	 *	@param	int		$x		Width of image in pixels
	 *	@param	int		$y		Height of image in pixels
	 *	@param	string	$color	Name of a color, or HTML value
	 *	@param	string	$file	File to save new image to.
	 *	@return	bool
	 */

	function create_canvas($x, $y, $color, $file)
	{
		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$cmd = ''
		# . $this->path . 'convert'
		# . ' -size ' . $x . 'x' . $y
		# . ' xc:' . $color
		# . ' ' . $file;
		#UTIL::execute($cmd);

		$template = ''
		 . '%%magick_path%%convert'
		 . ' -size %%x%%x%%y%%'
		 . ' xc:%%color%%'
		 . ' %%dest_file%%';

		$values = array(
			'magick_path' => $this->path,
			'x' => (string)$x,
			'y' => (string)$y,
			'color' => $color,
			'dest_file' => $file
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'x' => 'int',
			'y' => 'int',
			'color' => 'string',
			'dest_file' => 'none',
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//	(TODO) handle / report imagemagick error cases here
		return false;
	}

	/**
	 *	Append a set of images into a single image
	 *
	 *	@param	string[int]	$files		Flat array of file names? (CHECKME)
	 *	@param	string		$final		Location to save single joined up image	
	 *	@param	string		$how		'vertical'|'horizontal'
	 *	@return	bool
	 */

	function append_image_list($files, $final, $how = 'vertical')
	{
		$how = ('vertical' === $how) ? '-' : '+';		//	see imagemagick documentation

		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$cmd = ''
		# . $this->path . 'convert'
		# . ' ' . implode(' ',$files)
		# . ' ' . $how . 'append'
		# . ' ' . $final;
		#UTIL::execute($cmd);
	
		//	note that template_exec cannot check a list of files, manually checking them here
		foreach($files as $file) {
			if (false == file_exists($file)) {
				// (TODO) note, warn about error
				return;
			}
			//	(TODO) further checks here on file type, location, etc
		}

		$template = ''
		 . '%%magick_path%%convert'
		 . ' %%file_list%%'
		 . ' %%how%%append'
		 . ' %%dest_file%%';

		$values = array(
			'magick_path' => $this->path,
			'file_list' => implode(' ', $files),
			'how' => $how,
			'dest_file' => $final
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'file_list' => 'string',
			'how' => 'string',
			'dest_file' => 'none',
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//	(TODO) handle / report error cases here
		return false;
	}

	/**
	 *	Append / Join two images together
	 *
	 *	Note that if $file3 is not given the result will overwrite $file1.
	 *
	 *	@param	string	$file1		Location of a source image? (CHECKME)
	 *	@param	string	$file2		Location of a source image? (CHECKME)
	 *	@param	string	$gravity	(DOCUMENTME)
	 *	@param	string	$file3		Destination image file? (CHECKME)
	 *	@return	bool
	 */

	function append_image($file1, $file2, $gravity = 'south' , $file3 = '')
	{
	    $file3 = ('' === $file3) ? $file1 : $file3;
		$files = /*. (string[int]) .*/ array();
		$how = '';

		switch ($gravity)
		{
			case 'north':
				$files = array($file2, $file1);
				$how = '-';
				break;		//......................................................................

			case 'east':
				$files = array($file1,$file2);
				$how = '+';
				break;		//......................................................................

			case 'south':
				$files = array($file1,$file2);
				$how = '-';
				break;		//......................................................................

			case 'west':
				$files = array($file2,$file1);
				$how = '+';
				break;		//......................................................................


			default:
				//	(TODO) handle this case
				break;		//......................................................................

		}

		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$cmd = ''
		# . $this->path . 'convert'
		# . ' ' . implode(' ',$files)
		# . ' ' . $how . 'append'
		# . ' ' . $file3;
		#UTIL::execute($cmd);

		$template = ''
		 . '%%magick_path%%convert'
		 . ' %%first_file%% %%second_file%%'
		 . ' %%how%%append'
		 . ' %%dest_file%%';

		$values = array(
			'magick_path' => $this->path,
			'first_file' => $files[0],
			'second_file' => $files[1],
			'how' => $how,
			'dest_file' => $file3
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'first_file' => 'extant_file',
			'second_file' => 'extant_file',
			'how' => 'string',
			'dest_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//	(TODO) handle / report error cases here
		return false; 
	}

	/**
	 *	(DOCUMENTME) Appears to superimpose text on an image
	 *
	 *	Note that if $file2 is not given then the result will overwrite $file1
	 *
	 *	@param	string	$label	Caption? (CHECKME)
	 *	@patam	string	$font	(DOCUMENTME) assumed to be a .ttf file
	 *	@param	string	$file1	Source file? (CHECKME)
	 *	@param	string	$file2	Destination file? (CHEKCME)
	 *	@return	bool
	 */

	function label($label, $font, $size, $file1, $file2 = '')
	{
		$file2 = ('' === $file2) ? $file1 : $file2;

		if ('@' === substr($label, 0, 1)) { $label = '\\' . $label; }

		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$cmd = ''
		# . $this->path . 'montage'
		# . ' -font ' . $font
		# . ' -pointsize ' . $size
		# . ' -geometry +0+0'
		# . ' -gravity west'
		# . ' -background white'
		# . ' -label "' . $label . '"'
		# . ' ' . $file1
		# . ' ' . $file2;
		#UTIL::execute($cmd);

		$template = ''
		 . '%%magick_path%%montage'
		 . ' -font %%font%%'
		 . ' -pointsize %%pointsize%%'
		 . ' -geometry +0+0'
		 . ' -gravity west'
		 . ' -background white'
		 . ' -label "%%label%%"'
		 . ' %%source_file%%'
		 . ' %%dest_file%%';

		$values = array(
			'magick_path' => $this->path,
			'font' => $font,
			'pointsize' => $size,
			'label' => $label,
			'source_file' => $file1,
			'dest_file' => $file2
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'font' => 'string',
			'pointsize' => 'float',
			'label' => 'string',
			'source_file' => 'extant_file',
			'dest_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//	(TODO) handle /  report error cases here
		return false;
	}

	/**
	 *	(DOCUMENTME)
	 *	@param	string	$label			Caption? (CHECKME)
	 *	@param	string	$font			(DOCUMENTME)
	 *	@param	string	$size			(DOCUMENTME) points or pixels?
	 *	@param	string	$color			Name of a color, or HTML format? (CHECKME)
	 *	@param	string	$background		Name of a color, or HTML format? (CHECKME)
	 *	@param	int		$width			Pixels? (CHECKME)
	 *	@param	string	$output_file	Image to save to
	 *	@return	void
	 */

	function create_label($label, $font, $size, $color, $background, $width, $output_file)
	{
		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$cmd = ''
		# . $this->path . 'convert'
		# . ' -background ' . $background
		# . ' -font ' . $font
		# . ' -fill ' . $color
		# . ' -stroke ' . $color
		# . ' -pointsize ' . $size
		# . '  -size ' . $width . 'x  '
		# . ' caption:\'' . $label . '\''
		# . ' ' . $output_file;
		#UTIL::execute($cmd);

		$template = ''
		 . '%%magick_path%%convert'
		 . ' -background %%background%%'		//	(TODO) discover if this is a file or a color
		 . ' -font %%font%%'
		 . ' -fill %%color%%'
		 . ' -stroke %%color%%'
		 . ' -pointsize %%pointsize%%'
		 . '  -size %%width%%x  '
		 . ' caption:\'%%label%%\''
		 . ' %%dest_file%%';

		$values = array(
			'magick_path' => $this->path,
			'background' => $background,
			'font' => $font,
			'color' => $color,
			'pointsize' => (string)$size,
			'width' => (string)$width,
			'label' => $label,
			'dest_file' => $output_file
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'background' => 'string',
			'font' => 'string',
			'color' => 'string',
			'pointsize' => 'float',
			'width' => 'int',
			'label' => 'string',
			'dest_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//	(TODO) handle / report error case here
		return false;
	}	

	/* "normal" editing functions --------------------------------------------------------------- */

	/**
	 *	(DOCUMENTME) Appears to convert between image formats
	 *
	 *	(DOCUMENTME) this was originally commented 'resize and image', and could be used for that
	 *	(TODO) replace the single letter variable names with something clearer.
	 *	(TODO) find out if this is used anywhere, what it is used for and if it can be improved or
	 *	taken out.
	 *
	 *	@param	string	$source_file	Source image file? (CHECKME)
	 *	@param	string	$dest_file		Destination image file? (CHECKME)
	 *	@return	void
	 */
	
	function convert($source_file, $dest_file)
	{
		//	previous version, remove once templated exec is tested, strix 2012-03-23

		$template = $this->path . "convert " . $source_file . " " . $dest_file;

		$values = array(
			'magick_path' => $this->path,
			'source_file' => $source_file,
			'dest_file' => $dest_file
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'source_file' => 'extant_file',
			'dest_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();

		if (0 == $check) { return true; }
		//	(TODO) handle / report error cases here
		return false;
	}	 
	 

	/**
	 *	Create a PDF file from something/an image? (CHECKME)
	 *
	 *	TODO: replace the single letter variable names with something clearer.
	 *
	 *	@param	string	$i		Source (image) file? (CHECKME)
	 *	@param	string	$o		Output (PDF) file? (CHECKME)
	 *	@param	itn		$w		Width of PDF? (CHECKME) Unit? (DOCUMENTME)
	 *	@param	int		$h		Height of PDF? (CHECKME) Unit? (DOCUMENTME)
	 *	@param	int		$dpi	Resolution of output document, in dots per inch (CHECKME)
	 *	@return void
	 */

	function createpdf($i, $o, $w = 0, $h = 0, $dpi = 0)
	{
		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$option = '';
		#if (($w > 0) && ($h > 0)) { $option = "-page " . (string)$w . "x" . (string)$h; }
		#if ($dpi > 0) { $option .= " -density " . (string)$dpi; }
		#$command = $this->path . "convert " . $i . " " . $option . " " . $o;      
		#UTIL::execute($command, $returnarray, $returnvalue);

		$template = "%%magick_path%%convert %%source_file%% %%dest_file%%";

		if (($w > 0) && ($h > 0)) {
			$option = "-page " . (string)((string)$w) . "x" . (string)((string)$h);
			$template = ''
			 . "%%magick_path%%convert"
			 . " %%source_file%%"
			 . " -page %%width%%x%%height%%"
			 . " %%dest_file%%";
		}

		if ($dpi > 0) {
			$option .= " -density " . (string)((string)$dpi);
			$template = "%%magick_path%%convert %%source_file%% -density %%density%% %%dest_file%%";
		}

		$values = array(
			'magick_path' => $this->path,
			'density' => (string)$dpi,
			'width' => (string)$w,
			'height' => (string)$h,
			'source_file' => $i,
			'dest_file' => $o
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'density' => 'float',
			'width' => 'float',
			'height' => 'float',
			'source_file' => 'extant_file',
			'dest_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();
		
		if (0 == $check) { return true; }
		//	(TODO) handle / report error cases here
		return false;
	}

	/**
	 *	(DOCUMENTME) Resize an image or PDF?
	 *
	 *	@param	string	$file_in	Source file? (CHECKME)
	 *	@param	string	$file_out	Destination file? (CHECKME)
	 *	@param	int		$x_size		New width
	 *	@param	int		$y_size		New height
	 *	@return	bool
	 */

	function resize($file_in, $file_out, $x_size = 0, $y_size = 0)
	{
		$type = '';										//	(DOCUMENTME)

		if ( (0 == $x_size) && (0 == $y_size) )
		{
			$i = /*. (int[int]) .*/ array();		//	[0 => width, 1 => height]
			$i = getimagesize($file_in);			//	(TODO) check for and handle failure here
			$x_size = $i[0];
			$type = '';
		}
		else
		{
//			$type = ((0 != $x_size) && (0 != $y_size)) ? "!" : '';
			$type = ($x_size AND $y_size) ? "!" : null;
		}

		//	previous version, remove once templated exec is tested, strix 2012-03-23
		#$command = ''
		# . $this->path . "convert"
		# . " -resize '" . $x_size . "x" . $y_size . " " . $type . "'"
		# . " " . $file_in
		# . " " . $file_out;
		#$returnarray = /*. (string[int]) .*/ array();		//	for static analyzer
		#$returnvalue = false;								//	for static analyzer
		#UTIL::execute($command, $returnarray, $returnvalue);
		#if ($returnvalue != 0) { return false; }

		$template = ''
		 . "%%magick_path%%convert"
		 . " -resize '%%width%%x%%height%% " . $type . "'"
		 . " %%source_file%%"
		 . " %%dest_file%%";

		$values = array(
			'magick_path' => $this->path,
			'width' => (string)($x_size === 0 ? null : $x_size),
			'height' => (string)($y_size === 0 ? null : $y_size),
			'source_file' => str_replace('//', '/', $file_in),
			'dest_file' => str_replace('//', '/', $file_out)
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'width' => 'none',
			'height' => 'none',
			'source_file' => 'extant_file',
			'dest_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();
		

		if (0 == $check) { return true; }
		//	(TODO) handle / report error cases here
		return false;
	}

	/**
	 *	(DOCUMENTME) Appears to round the corners of an image?
	 *
	 *	@param	string	$source			Source file? (CHECKME)
	 *	@param	string	$target			Destination file? (CHECKME)
	 *	@param	int		$round			(DOCUMENTME)
	 *	@param	int		$borderwidth	Pixels? (CHECKME)
	 *	@param	string	$bordercolor	Name of color, or HTML value? (CHECKME)
	 *	@return	bool					True on success, false on failure
	 */

	function corners(
		$source = '', $target = '', $round = 0,
		$borderwidth = 0, $bordercolor = 'black'
	) {	

		// create square
		$token = md5((string)uniqid(rand(), true));

		$i = /*. (int[int]) .*/ array();			//	[0 => width, 1 => height]
		$i = getimagesize($source);					//	(TODO) check for and handle failure here
		
		$width = $i[0];
		$height = $i[1];
		
		$resized = $this->resize($source, $target, $width, $height);
		if (false == $resized) { return false; }

		#$bordercolor = strtolower($bordercolor);	
		#MC::debug(UTIL::colorname($bordercolor));
		#$bordercolor = UTIL::colorname($bordercolor);

		//	Set up common variables and constraints for shell commands
		$tmp_border = CONF::tmp_dir() . "/tmp_border" . $token . ".png";
		$tmp_mask = CONF::tmp_dir() . "/tmp_mask" . $token . ".png";

		$values = array(
			'magick_path' => $this->path,
			'target' => $target,
			'bordercolor' => $bordercolor,
			'borderwidth' => (string)$borderwidth,
			'width' => (string)$width,
			'height' => (string)$height,
			'round' => $round,
			'tmp_border' => str_replace('//', '/', $tmp_border),
			'tmp_mask' => str_replace('//', '/', $tmp_mask)
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'target' => 'file',
			'bordercolor' => 'string',
			'borderwidth' => 'float',
			'width' => 'int',
			'height' => 'int',
			'round' => 'int',
			'tmp_border' => 'file',
			'tmp_mask' => 'file',
		);
		
		// now add the rounded corner
		if ((0 != $round) && (0 != $borderwidth))
		{
			//	if borderwidth and rounding are both specified			

			//	(DOCUMENTME) why reduce width by borderwidth?
			$width -= $borderwidth;
			$values['width'] = $width;

			//	Make $tmp_border image

			//	previous, unsanitized version of this, remove when templated exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert"
			# . " " . $target
			# . " -border 2"
			# . " -matte"
			# . " -channel RGBA"
			# . " -threshold -1"
			# . " -background none"
			# . " -fill none"
			# . " -stroke '" . $bordercolor . "'"
			# . " -strokewidth " . (string)$borderwidth
			# . " -draw 'roundRectangle "
			#	 . (string)($borderwidth) . "," . (string)($borderwidth) . " "
			#	 . (string)($width-$borderwidth) . "," . (string)($height-$borderwidth) . " "
			#	 . (string)$round . "," . (string)$round . "'"
			# . " " . $tmp_border . "";
	  		#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -border 2"
			 . " -matte"
			 . " -channel RGBA"
			 . " -threshold -1"
			 . " -background none"
			 . " -fill none"
			 . " -stroke '%%bordercolor%%'"
			 . " -strokewidth %%borderwidth%%"
			 . " -draw "
				 . "'roundRectangle"
				 . " %%borderwidth%%,%%borderwidth%%"
				 . " " . (string)((string)$width-(string)$borderwidth)
				 . "," . (string)((string)$height-(string)$borderwidth)
				 . " %%round%%,%%round%%" . "'"
			 . " %%tmp_border%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			if (0 != $check) { return false; }

			//	Make tmp_mask image

			//	previous, unsanitized version, remove when template exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert"
			# . " " . $target
			# . " -border 2"
			# . " -matte"
			# . " -channel RGBA"
			# . " -threshold -1"
			# . " -background none"
			# . " -fill white"
			# . " -stroke black"
			# . " -strokewidth 1"
			# . " -draw "
			#	 . "'roundRectangle "
			#	 . (string)($borderwidth) . "," . (string)($borderwidth) . " "
			#	 . (string)($width-$borderwidth) . "," . (string)($height-$borderwidth) . " "
			#	 . (string)$round . "," . (string)$round . "'"
			# . " " . $tmp_mask . "";
    		#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -border 2"
			 . " -matte"
			 . " -channel RGBA"
			 . " -threshold -1"
			 . " -background none"
			 . " -fill white"
			 . " -stroke black"
			 . " -strokewidth 1"
			 . " -draw "
				 . "'roundRectangle"
				 . " %%borderwidth%%,%%borderwidth%%"
				 . " " . (string)((string)$width - (string)$borderwidth)
				 . "," . (string)((string)$height - (string)$borderwidth) . " "
				 . "%%round%%,%%round%%'"
			 . " %%tmp_mask%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			if (0 != $check) { return false; }

			//	Combine rounded corner mask with original image? (CHECKME)

			//	previous unsanitized version, remove when template exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert"
			# . " " . $target
			# . " -matte"
			# . " -bordercolor none"
			# . " -border 0"
			# . " " . $tmp_mask
			# . " -compose DstIn"
			# . " -composite " . $tmp_border
			# . " -compose Over"
			# . " -composite"
			# . " -depth 8"
			# . " -quality 95"
			# . " " . $target;
			#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -matte"
			 . " -bordercolor none"
			 . " -border 0"
			 . " %%tmp_mask%%"
			 . " -compose DstIn"
			 . " -composite %%tmp_border%%"
			 . " -compose Over"
			 . " -composite"
			 . " -depth 8"
			 . " -quality 95"
			 . " %%target%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();

			//	clean up temporary files
			UTIL::delete_file($tmp_border);
			UTIL::delete_file($tmp_mask);
			if (0 != $check) { return false; }
    	}
    	elseif (0 != $round)
    	{
			//	if rounding is specified and border width is not

			//	previous, unsanitized version of this, remove once template exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert"
			# . " " . $target
			# . " -border " . $borderwidth
			# . " -matte"
			# . " -channel RGBA"
			# . " -threshold -1"
			# . " -background none"
			# . " -fill white"
			# . " -stroke black"
			# . " -strokewidth 1"
			# . " -draw "
			#	 . "'roundRectangle 0,0 "
			#	 . (string)$width . "," . (string)$height . " "
			#	 . $round . "," . $round . "'"
			# . " " . $tmp_mask."";
    		#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -border %%borderwidth%%"
			 . " -matte"
			 . " -channel RGBA"
			 . " -threshold -1"
			 . " -background none"
			 . " -fill white"
			 . " -stroke black"
			 . " -strokewidth 1"
			 . " -draw "
				 . "'roundRectangle 0,0"
				 . " %%width%%,%%height%%"
				 . " %%round%%,%%round%%'"
			 . " %%tmp_mask%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			if (0 != $check) { return false; }

			//	previous, unsanitized version of this, remove when possible
			#$shell_cmd = ''
			# . $this->path . "convert"
			# . " " . $target
			# . " -matte"
			# . " -bordercolor none"
			# . " -border 0"
			# . " " . $tmp_mask
			# . " -compose DstIn"
			# . " -composite"
			# . " " . $target;
			#UTIL::execute($shell_cmd);    		

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -matte"
			 . " -bordercolor none"
			 . " -border 0"
			 . " %%tmp_mask%%"
			 . " -compose DstIn"
			 . " -composite"
			 . " %%target%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			UTIL::delete_file($tmp_mask);
			if (0 != $check) { return false; }
    	}
    	elseif (0 != $borderwidth)
    	{
			//	if border widht is specified and rounding is not

			//	previous version of this, remove once template exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert " . $target
			# . " -border " . $borderwidth
			# . " -matte"
			# . " -channel RGBA"
			# . " -threshold -1"
			# . " -background none"
			# . " -fill none"
			# . " -stroke '" . $bordercolor . "'"
			# . " -strokewidth " . $borderwidth
			# . " -draw "
			#	 . "'roundRectangle 0,0 "
			#	 . (string)$width . "," . (string)$height . " " . (string)$round . "," . (string)$round . "'"
			# . " " . $tmp_border . "";
	  		#UTIL::execute($shell_cmd);		

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -border %%borderwidth%%"
			 . " -matte"
			 . " -channel RGBA"
			 . " -threshold -1"
			 . " -background none"
			 . " -fill none"
			 . " -stroke '%%bordercolor%%'"
			 . " -strokewidth %%borderwidth%%"
			 . " -draw "
			 	. "'roundRectangle 0,0 "
				. "%%width%%,%%height%% %%round%%,%%round%%'"
			 . " %%tmp_border%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			if (0 != $check) { return false; }

			//	previous version of this, remove once template_exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert " . $target
			# . " " . $tmp_border
			# . " -compose Over"
			# . " -composite"
			# . " -depth 8"
			# . " -quality 95"
			# . " " . $target;
			#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " %%tmp_border%%"
			 . " -compose Over"
			 . " -composite"
			 . " -depth 8"
			 . " -quality 95"
			 . " %%target%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			UTIL::delete_file($tmp_border);
			if (0 != $check) { return false; }
    	}

		return true;

	} // end corners(...)

	/**
	 *	make a roudned thummbnail or icon (checkme)
	 *
	 *	(DOCUMENTME) describe why this does not use corners(...) above
	 *
	 *	@param	string	$source			Source image? (CHECKME)
	 *	@param	string	$target			Destination image? (CHECKME)
	 *	@param	int		$width			(DOCUMENTME)
	 *	@param	int		$round			(DOCUMENTME)
	 *	@param	string	$color			Name of color, or HTML color? (CHECKME)
	 *	@param	int		$borderwidth	Width of border, pixels (CHECKME)
	 *	@param	string	$bordercolor	Name of color, or HTML color? (CHECKME)
	 *	@return	bool
	 */

	function rounded_icon_create(
		$source = '', $target = '', $width = 100, $round = 0, $color = 'white',
		$borderwidth = 0, $bordercolor = 'black'
	) {	
		//TODO: sanitize everything used at the shell

		// create square
		$token = md5((string)uniqid(rand(), true));

		$i = /*. (int[int]) .*/ array();	//	[0 => width, 1 => height]
		$i = getimagesize($source); 		//	(TODO) check for / handle error case

		$tmp = CONF::tmp_dir() . "/tmp_" . $token . ".gif";
		$canvas = CONF::tmp_dir() . "/canvas_" . $token . ".gif";

		$tmp = str_replace('//', '/', $tmp);
		$canvas = str_replace('//', '/', $canvas);

		if($i[0] > $i[1])
		{
			//	width is greater than height
			$height = (string)(( $width - ( ( $i[1] * $width ) / $i[0] ) ) / 2);
			$this->resize($source, $tmp, $width);	
			$this->create_canvas($width, $height, $color, $canvas);
			$this->append_image_list(array($canvas, $tmp, $canvas), $target);
		}
		elseif($i[0] < $i[1])
		{
			//	width is less than height
			$height = (string)(($width - ( ( $i[0] * $width ) / $i[1] ) ) / 2);
			$this->resize($source, $tmp, (string)(($width * $i[0])  / $i[1]), $width);
			$this->create_canvas($height, $width, $color, $canvas);
			$this->append_image_list(array($canvas, $tmp, $canvas), $target, "horizontal");
		}
		else
		{
			//	image is square
			$this->resize($source, $target, $width, $width);
		}					
		
		UTIL::delete_file($canvas);
		UTIL::delete_file($tmp);

		// now add the rounded corner

		$tmp_border = CONF::tmp_dir() . "/tmp_border" . $token . ".png";
		$tmp_mask = CONF::tmp_dir() . "/tmp_mask" . $token . ".png";

		$values = array(
			'magick_path' => $this->path,
			'target' => $target,
			'bordercolor' => $bordercolor,
			'borderwidth' => $borderwidth,
			'round' => $round,
			'height' => (string)$height,
			'width' => (string)$width,
			'tmp_mask' => str_replace('//', '/', $tmp_mask),
			'tmp_border' => str_replace('//', '/', $tmp_border),
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'target' => 'file',
			'bordercolor' => 'string',
			'borderwidth' => 'int',
			'round' => 'int',
			'height' => 'int',
			'width' => 'int',
			'tmp_mask' => 'file',
			'tmp_border' => 'file'
		);

		if ((0 != $round) && (0 != $borderwidth))
		{
			$width -= $borderwidth;
			$values['width'] = $width;

			//	previous version, remov eone template exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert " . $target
			# . " -border 2"
			# . " -matte"
			# . " -channel RGBA"
			# . " -threshold -1"
			# . " -background none"
			# . " -fill none"
			# . " -stroke '" . $bordercolor . "'"
			# . " -strokewidth " . $borderwidth
			# . " -draw "
			#	 . "'roundRectangle " . ($borderwidth) . "," . ($borderwidth) . " "
			#	 . ($width - $borderwidth) . "," . ($width - $borderwidth)
			#	 . " " . $round . "," . $round . "'"
			# . " " . $tmp_border."";
	  		#UTIL::execute($shell_cmd);		

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -border 2"
			 . " -matte"
			 . " -channel RGBA"
			 . " -threshold -1"
			 . " -background none"
			 . " -fill none"
			 . " -stroke '%%bordercolor%%'"
			 . " -strokewidth %%borderwidth%%"
			 . " -draw "
				 . "'roundRectangle %%borderwidth%%,%%borderwidth%%"
				 . " " . (string)((string)$width - (string)$borderwidth)
				 . "," . (string)((string)$width - (string)$borderwidth)
				 . " %%round%%,%%round%%'"
			 . " %%tmp_border%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			if (0 != $check) { return false; }
	
			//	previous, unsanitized version, remove once template_exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert " . $target
			# . " -border 2"
			# . " -matte"
			# . " -channel RGBA"
			# . " -threshold -1"
			# . " -background none"
			# . " -fill white"
			# . " -stroke black"
			# . " -strokewidth 1"
			# . " -draw "
			#	 . "'roundRectangle " . ($borderwidth) . "," . ($borderwidth) . " "
			#	 . ($width - $borderwidth) . "," . ($width - $borderwidth) . " "
			#	 . $round . "," . $round . "'"
			# . " " . $tmp_mask."";
    		#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -border 2"
			 . " -matte"
			 . " -channel RGBA"
			 . " -threshold -1"
			 . " -background none"
			 . " -fill white"
			 . " -stroke black"
			 . " -strokewidth 1"
			 . " -draw "
				 . "'roundRectangle %%borderwidth%%,%%borderwidth%%"
				 . " " . (string)((string)$width - (string)$borderwidth)
				 . "," . (string)((string)$width - (string)$borderwidth)
				 . " %%round%%,%%round%%'"
			 . " %%tmp_mask%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			if (0 != $check) { return false; }

			//	previous, unsanitized version, remove once template_exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert " . $target
			# . " -matte"
			# . " -bordercolor none"
			# . " -border 0"
			# . " " . $tmp_mask
			# . " -compose DstIn"
			# . " -composite " . $tmp_border
			# . " -compose Over"
			# . " -composite"
			# . " -depth 8"
			# . " -quality 95"
			# . " " . $target;
			#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -matte"
			 . " -bordercolor none"
			 . " -border 0"
			 . " %%tmp_mask%%"
			 . " -compose DstIn"
			 . " -composite %%tmp_border%%"
			 . " -compose Over"
			 . " -composite"
			 . " -depth 8"
			 . " -quality 95"
			 . " %%target%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();

			UTIL::delete_file($tmp_border);
			UTIL::delete_file($tmp_mask);
			if (0 != $check) { return false; }
    	}
    	elseif (0 != $round)
    	{
			//	if round is set, but not borderwidth

			//	previous, unsanitized version, remove once template_exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert " . $target
			# . " -border " . $borderwidth
			# . " -matte"
			# . " -channel RGBA"
			# . " -threshold -1"
			# . " -background none"
			# . " -fill white"
			# . " -stroke '" . $bordercolor . "'"
			# . " -strokewidth 1"
			# . " -draw "
			#	 . "'roundRectangle 0,0 " . $width . "," . $width . " "
			#	 . $round . "," . $round . "'"
			# . " " . $tmp_mask . "";
    		#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -border %%borderwidth%%"
			 . " -matte"
			 . " -channel RGBA"
			 . " -threshold -1"
			 . " -background none"
			 . " -fill white"
			 . " -stroke '%%bordercolor%%'"
			 . " -strokewidth 1"
			 . " -draw "
				 . "'roundRectangle 0,0 %%width%%,%%width%%"
				 . " %%round%%,%%round%%'"
			 . " %%tmp_mask%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			if (0 != $check) { return false; }

			//	previous, unsanitized version, remove once template_exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert " . $target
			# . " -matte"
			# . " -bordercolor none"
			# . " -border 0"
			# . " " . $tmp_mask
			# . " -compose DstIn"
			# . " -composite"
			# . " " . $target;
			#UTIL::execute($shell_cmd);    		

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -matte"
			 . " -bordercolor none"
			 . " -border 0"
			 . " %%tmp_mask%%"
			 . " -compose DstIn"
			 . " -composite"
			 . " %%target%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			UTIL::delete_file($tmp_mask);
			if (0 != $check) { return false; }
    	}
    	elseif (0 != $borderwidth)
    	{
			//	if borderwidth is set, but not round

			//	previous, unsanitized version, remove once template exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert " . $target
			# . " -border " . $borderwidth
			# . " -matte"
			# . " -channel RGBA"
			# . " -threshold -1"
			# . " -background none"
			# . " -fill none"
			# . " -stroke '" . $bordercolor . "'"
			# . " -strokewidth " . $borderwidth
			# . " -draw "
			#	 . "'roundRectangle 0,0 " . $width . "," . $width . " "
			#	 . $round . "," . $round . "'"
			# . " " . $tmp_border . "";
	  		#UTIL::execute($shell_cmd);		

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " -border %%borderwidth%%"
			 . " -matte"
			 . " -channel RGBA"
			 . " -threshold -1"
			 . " -background none"
			 . " -fill none"
			 . " -stroke '%%bordercolor%%'"
			 . " -strokewidth %%borderwidth%%"
			 . " -draw "
				 . "'roundRectangle 0,0 %%width%%,%%width%%"
				 . " %%round%%,%%round%%'"
			 . " %%tmp_border%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			if (0 != $check) { return false; }

			//	previous, unsanitized version, remove once template_exec is tested
			#$shell_cmd = ''
			# . $this->path . "convert"
			# . " " . $target
			# . " " . $tmp_border
			# . " -compose Over"
			# . " -composite"
			# . " -depth 8"
			# . " -quality 95"
			# . " " . $target;
			#UTIL::execute($shell_cmd);

			$template = ''
			 . "%%magick_path%%convert"
			 . " %%target%%"
			 . " %%tmp_border%%"
			 . " -compose Over"
			 . " -composite"
			 . " -depth 8"
			 . " -quality 95"
			 . " %%target%%";

			$cmd = new template_exec($template, $values, $constraints);
			$check = $cmd->run();
			UTIL::delete_file($tmp_border);
			if (0 != $check) { return false; }
    	}

		return true;

	} // end rounded_icon_create()
	
	/**
	 *	(DOCUMENTME)
	 *
	 *	@param	string	$from			Source file (CHECKME)
	 *	@param	string	$watermark		Watermark image file? (CHECKME)
	 *	@param	string	$to				Destination file, source is used if not set
	 *	@param	string	$percentage		'0' to '100', aplha
	 *	@param	string	$gravity		(DOCUMENTME) see imagemagick documentation
	 *	@param	string	$geometry		(DOCUMENTME) see imagemagick documentation
	 *	@return	bool					True on success, false on failure
	 */
	
	function watermark(
		$from, $watermark, $to = '', $percentage = '100',
		$gravity = 'NorthEast', $geometry = "+50+50"
	) {
		if ('' === $to) { $to = $from; }

		//	previous, unsanitized version, remove once template_exec is tested
		#$command = ''
		# . $this->path . "composite"
		# . " -watermark " . $percentage . "%"
		# . " -geometry " . $geometry
		# . " -gravity " . $gravity
		# . " " . $watermark 
		# . " " . $from
		# . " " . $to;
		#UTIL::execute($this->path.$command);

		$template = ''
		 . "%%magick_path%%composite"
		 . " -watermark %%percentage%%" . "%"
		 . " -geometry '%%geometry%%'"
		 . " -gravity '%%gravity%%'"
		 . " %%watermark%%" 
		 . " %%source_file%%"
		 . " %%dest_file%%";

		$values = array(
			'magick_path' => $this->path,
			'percentage' => $percentage,
			'geometry' => $geometry,
			'gravity' => $gravity,
			'watermark' => $watermark,
			'source_file' => $from,
			'dest_file' => $to
		);

		$constraints = array(
			'magick_path' => 'extant_dir',
			'percentage' => 'float',
			'geometry' => 'string',
			'gravity' => 'string',
			'watermark' => 'extant_file',
			'source_file' => 'extant_file',
			'dest_file' => 'none'
		);

		$cmd = new template_exec($template, $values, $constraints);
		$check = $cmd->run();
		if (0 != $check) { return false; }
		return true;
	}
	
}

/*

USAGE EXAMPLES

$input_file = "/Users/thomasschindler/Pictures/test/figaro.jpg";

$output_file = "/Users/thomasschindler/Pictures/test/tmp_".time().'.jpg';

$final_file = "/Users/thomasschindler/Pictures/test/final_".time().".jpg";

$font = '/Users/thomasschindler/Library/Fonts/Frahvit.ttf';

$label = "dlkkj asflkj asffkj asddffkjj dlkkj asflkj asffkjasddffkjj dlkkj asflkj asffkjasddffkjj dlkkj asflkj asffkjasddffkjj dlkkj asflkj asffkj asddffkjj dlkkj asflkj asffkj asddffkjj dlkkj asflkj asffkj asddffkjj dlkkj asflkj asffkj asddffkjj dlkkj asflkj asffkj asddffkjj ";

$magick = new magick('/ImageMagick-6.0.3/bin');

#$magick->create_canvas(200,100,'white',$output_file);

#$magick->write_text("Das ist die Bildunterschrift für dieses Bild. JAJA.",'10','10',$font,'20','gray24','black',$output_file);

#$magick->append_image(array($input_file,$output_file),'/Users/thomasschindler/Pictures/test/final'.time().'.jpg');

#$magick->label($label,$font,24,$input_file,$output_file);

$magick->create_label($label,'arial',11,'gray24','white',290,$output_file);

$magick->append_image($input_file,$output_file,'east',$final_file);

*/

?>
