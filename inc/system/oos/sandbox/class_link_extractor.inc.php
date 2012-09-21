<?
/**
* Extract links from a string, from a file or from a valid url
* .
* Compatibility: PHP >= 4.0.5
* ---------------------------------------------------------------------
* EXAMPLE:
*
* [ TO PARSE A STRING ]
* $myLinks = &new LinkExtractor(); // create a LinkExtractor Object
* $myLinks->parseString( $myStringWithLinks ); // parse a string
* for( $a = 0, $b = count( $fetchLinks = $myLinks->getLinks() ); $a < $b; $a++ ) {
* 	echo $fetchLinks[$a]."<br />";
* }
*
* [ TO PARSE AN URL ( or if you want, a file ) ]
* $myLinks = &new LinkExtractor(); // create a LinkExtractor Object
* if( $myLinks->parseUrl( "http://www.3site.it/index.php" ) == true ) {
* 	for( $a = 0, $b = count( $fetchLinks = $myLinks->getLinks() ); $a < $b; $a++ ) {
* 		echo $fetchLinks[$a]."<br />";
* 	}
* }
*
* [ TO PARSE A FILE ]
* $myLinks = &new LinkExtractor(); // create a LinkExtractor Object
* if( $myLinks->parseFile( "myTextFile.txt" ) == true ) {
* 	for( $a = 0, $b = count( $fetchLinks = $myLinks->getLinks() ); $a < $b; $a++ ) {
* 		echo $fetchLinks[$a]."<br />";
* 	}
* }
* ---------------------------------------------------------------------
* @Author		Andrea Giammarchi
* @Alias		andr3a
* @Site			http://www.3site.it
* @Mail			andrea@3site.it
* @Version		0.1
* @Begin		18/06/2004
* @lastModify		28/06/2004 13:45
*/
class LinkExtractor {
	/* private Array variable: $linkReg [ contains pregs to parse links ]*/
	var $linkReg = Array(
	"/(?i)<a([^\a]+?)href='([^\a]+?)'/i",
	"/(?i)<a([^\a]+?)href=\"([^\a]+?)\"/i",
	"/(?i)<a([^\a]+?)href=([^\a]+?)[ |>]/i",
	"/(?i)<link([^\a]+?)href='([^\a]+?)'/i",
	"/(?i)<link([^\a]+?)href=\"([^\a]+?)\"/i",
	"/(?i)<link([^\a]+?)href=([^\a]+?)[ |>]/i",
	"/(?i)<img([^\a]+?)src='([^\a]+?)'/i",
	"/(?i)<img([^\a]+?)src=\"([^\a]+?)\"/i",
	"/(?i)<img([^\a]+?)src=([^\a]+?)[ |>]/i",
	"/(?i)<script([^\a]+?)src='([^\a]+?)'/i",
	"/(?i)<script([^\a]+?)src=\"([^\a]+?)\"/i",
	"/(?i)<script([^\a]+?)src=([^\a]+?)[ |>]/i"
	);

	/**
	* Public constructor.
	* Create a global Array with no value, used for parsing
	* and an internal array with valid pregs for links parsing.
	*/
	function LinkExtractor() {
		global $__linkExtractor_linkRecipient;
		$__linkExtractor_linkRecipient = Array();
	}

	/**
	* Private method, popolate internal Array with preg matches
	* .
	* @Param	String		String to push into internal array
	* @Return	nothing
	*/
        function __manageLinkRecipient( $replacement ) {
		global $__linkExtractor_linkRecipient;
		array_push( $__linkExtractor_linkRecipient, htmlspecialchars( $replacement[2] ) );
	}

	/**
	* Private method, call preg_replace_callback function with string.
	* .
	* @Param	String		String to parse
	* @Return	nothing
	*/
	function __callBackCaller( $st ) {
		preg_replace_callback( $this->linkReg, Array( &$this, '__manageLinkRecipient' ), $st );
	}

	/**
	* Public method, read remote page or file and parse them
	* .
	* @Param	String		valid url address to parse
	* @Return	Boolean		true if readed , false in other cases
	*/
	function parseUrl( $url ) {
		if( @$fp = fopen( $url, "r" ) ) {
			$st = '';
			while( $text = fread( $fp, 8192 ) ) {
				$st .= $text;
			}
			fclose( $fp );
			$this->__callBackCaller( $st );
			return true;
		}
		return false;
	}
	
	/**
	* Public method, parse links in a file
	* .
	* @Param	String		string to parse
	* @Return	nothing
	*/
	function parseFile( $st ) {
		return $this->parseUrl( $st );
	}

	/**
	* Public method, parse links in a string
	* .
	* @Param	String		string to parse
	* @Return	nothing
	*/
	function parseString( $st ) {
		$this->__callBackCaller( $st );
	}
	
	/**
	* Public method, return an array with all found links
	* .
	* @Param	no	no params need
	* @Return	Array	Array with all links ( if there're )
	*/
	function getLinks() {
		global $__linkExtractor_linkRecipient;
		return $__linkExtractor_linkRecipient;
	}
}
?>