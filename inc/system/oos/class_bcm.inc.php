<?
/**
* BLACK CONVERSION MACHINE
* another extension for oOS. 21.01.2007 
* tom.schindler@hundertelf.com
* joe.klinkhammer@hundertelf.com
* 
* FFMPEG installation:
* FFmpeg version SVN-rUNKNOWN, Copyright (c) 2000-2006 Fabrice Bellard, et al.
*  configuration:  --shlibdir=/usr/lib64 --prefix=/usr --mandir=/usr/share/man --libdir=/usr/lib64 --enable-shared --enable-mp3lame --enable-libogg --enable-vorbis --enable-faad --enable-faac --enable-xvid --enable-a52 --enable-dts --enable-pp --enable-gpl --enable-gprof --enable-x264 --enable-amr_nb --enable-amr_wb 
*  libavutil version: 49.2.0
*  libavcodec version: 51.28.0
*  libavformat version: 51.7.0
*
* @TODO:
* find out dimensions of the video for target size automatisation for 3gp
* find out filetype
* add audio
* add image
*
* optional parameter for convert array with parameter->values
*
* all ffmpeg options are valid for audio and video. 
*
* these are helpful:
* 
* Video options:
* -s size             set frame size (WxH or abbreviation)
* -vcodec codec       force video codec ('copy' to copy stream)
* 
* Audio options:
* -ar rate            set audio sampling rate (in Hz)
* -ac channels        set number of audio channels
*-acodec codec       force audio codec ('copy' to copy stream)
*
*/

class bcm
{
	var $sizes = array
	(
		'-s 128x96',
		'-s 176x144', 
		'-s 352x288', 
		'-s 704x576',
		'-s 1408x1152'
	);
	
	var $imagemagick_opt_map = array
	(
		'jpg'  => true,
		'gif'  => true,
		'png'  => true,
		'bmp'  => true,
		'wbmp' => true,
	);
	
	var $ffmpeg_opt_map = array
	(
		'3gp' => array
		(
			'mov' => '-ar 11025',
			'mp4' => '-ar 11025',
			'flv' => '-ar 11025',
		),
		'mov' => array
		(
			'3gp' => '-ar 8000 -ac 1', // change this!!!
			'mp4' => '',
			'wmv' => '-vcodec wmv1',
			'flv' => '',
			'mpg' => '',
		),
		'mp4' => array
		(
			'3gp' => '-ac 1 -ar 8000',
			'mov' => '',
			'wmv' => '-vcodec wmv1',
			'flv' => '',
			'mpg' => '',
		),
		'wmv' => array
		(
			'3gp' => '-ar 8000 -ac 1', // change this!!!
			'mp4' => '-ar 11025',
			'mov' => '-ar 11025',
			'flv' => '-ar 11025',
			'mpg' => '',
		),
		'avi' => array
		(
			'3gp' => '-ar 8000 -ac 1', // change this!!!
			'mp4' => '',
			'mov' => '',
			'flv' => '',
			'mpg' => '-r 30 -ar 44100',
		),
		'flv' => array
		(
			'3gp' => '-ar 8000 -ac 1', // change this!!!
			'mp4' => '',
			'mov' => '',
			'wmv' => '-vcodec wmv1',
			'mpg' => '',
		),
		'mpg' => array
		(
			'3gp' => '-ar 8000 -ac 1', // change this!!!
			'mp4' => '',
			'mov' => '',
			'wmv' => '-vcodec wmv1',
			'flv' => '',
		),
	);
	var $conversion_map = array();
	var $path = array
	(
		'ffmpeg' => 'ffmpeg',
		'imagemagick' => 'convert',
	);
	/**
	*	constructor - creates an array with two dimensions to quickly access conversion possibilities
	*	this uses the different opt_maps (ffmpep imagemagick etc)
	*/
	function bcm()
	{
		// get the image-conversions
		foreach($this->imagemagick_opt_map as $from => $v)
		{
			foreach($this->imagemagick_opt_map as $to => $v)
			{
				$this->conversion_map[$from][$to] = true;
			}
			$this->conversion_map['converter'][$from] = "imagemagick";
		}
		// get video conversions
		foreach($this->ffmpeg_opt_map as $from => $tmp)
		{
			foreach($tmp as $to => $v)
			{
				$this->conversion_map[$from][$to] = true;
			}
			$this->conversion_map['converter'][$from] = "ffmpeg";
		}
	}
	/**
	*	actually convert
	*	from -> to with p as parameters array(parameter_name=>value)
	*	p is optional
	*/
	function path_set($type,$path)
	{
		$this->path[$type] = $path;
	}
	function convert($in,$out,$p=null,$force=false)
	{
		if(is_file($out))
		{
			return false;
		}
		if(!is_file($in))
		{
			return false;
		}
		// which machine should be used?
		$from = explode(".",$in);
		$from = $from[sizeof($from)-1];
		
		$to = explode(".",$out);
		$to = $to[sizeof($to)-1];
		
		if($this->can_convert($from,$to))
		{		
			switch($this->conversion_map['converter'][$from])
			{
				case 'imagemagick':
					return $this->imagemagick_convert($in,$out,$p);				
				break;
				case 'ffmpeg':
					return $this->ffmpeg_convert($in,$out,$p);
				break;
			}
		}
		switch($force)
		{
			case 'imagemagick':
				return $this->imagemagick_convert($in,$out,$p);
			break;
			case 'ffmpeg':
				return $this->ffmpeg_convert($in,$out,$p);
			break;
		}
		return false;
	}
	
	function can_convert($from,$to=null)
	{
		if(!$from)
		{
			return false;
		}
		if($to)
		{
			return $this->conversion_map[$from][$to];
		}
		if($this->conversion_map[$from])
		{
			return $this->conversion_map[$from];
		}
		return false;
	}
	
	function get_info($from,$to=null)
	{
		if(!$from)
		{
			return false;
		}
		if(!$this->can_convert)
		{
			$r = shell_exec("ffmpeg -formats");
			$r = explode("File formats:",$r);
			$r = explode("Codecs:",$r[1]);
			$r = explode("\n",$r[0]);

			foreach($r as $l)
			{
				if(strlen($l)==0){continue;}
				$p = explode(" ",$l);
				$tmp = array();
				foreach($p as $i)
				{
					if(strlen($i)==0){continue;}
					$tmp[] = $i;
					if(sizeof($tmp) == 2)
					{
						break;
					}
				}
				$tmp[1] = trim($tmp[1]);
				switch(strtoupper(trim($tmp[0])))
				{
					case 'E':
						$this->can_convert[$tmp[1]]['encode'] = true;
					break;
					case 'D':
						$this->can_convert[$tmp[1]]['decode'] = true;					
					break;
					case 'DE':
						$this->can_convert[$tmp[1]]['encode'] = true;
						$this->can_convert[$tmp[1]]['decode'] = true;
					break;
				}
			}
		}
		
		
		if($to)
		{
			$to = explode(".",$to);
			return (bool)($this->can_convert['decode'][trim($from[sizeof($from)-1])]&&$this->can_convert['encode'][trim($to[sizeof($to)-1])]);
		}

		$from = explode(".",$from);
		return (bool)$this->can_convert['decode'][trim($from[sizeof($from)-1])];


	}

	/**
	*	only converts if the relation in / out is found in the map
	*/
	function ffmpeg_convert($in,$out,$p=null)
	{
		if(is_array($p))
		{
			//
			if($p['width'] OR $p['height'])
			{
				$opt[] = " -s ".$p['width']."x".$p['height'];
				unset($p['width']);
				unset($p['height']);
			}
			foreach($p as $p => $v)
			{
				$opt[] = " -".$p." ".$v." ";
			}
			$opt = implode(" ",$opt);
		}
		elseif($opt = $this->ffmpeg_get_opt($in,$out,$p))
		{
			//
		}
		if(exec($this->path['ffmpeg']." -i ".$in." ".$opt." ".$out))
		{
			return true;
		}
		return false;
	}
	/**
	* use imagemagick to convert images
	*/
	function imagemagick_convert($in,$out,$p)
	{
		$type = ($p['width'] AND $p['height']) ? "!" : null;
		$command = CONF::bin_dir()."convert -resize '".$p['width']."x".$p['height']." ".$type."' ".$in." ".$out;
		exec($command, $returnarray, $returnvalue);
		if($returnvalue) 
		{
			return false;
		}
		return true;
	}
	/**
	*	return the options for ffmpeg
	*/
	function ffmpeg_get_opt($in,$out)
	{
		$in = explode(".",$in);
		$out = explode(".",$out);
		// for 3gp as target we need to know the desired size
		// or we know the dimensions of the source and calculate the right size
		//change to something like "force size" later
		$size = null;
		if($out[sizeof($out)-1] == "3gp")
		{
			$size = $this->sizes[0];
		}
		return $this->ffmpeg_opt_map[$in[sizeof($in)-1]][$out[sizeof($out)-1]]." ".$size;
	}
	/**
	*	singleton
	*/
	function &singleton() 
	{
		static $instance;
		if (!is_object($instance)) 
		{
			$instance = new bcm();
		}
		return $instance;
	}
}
?>
