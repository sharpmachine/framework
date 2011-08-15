<?php
/*
JLFunctions URL Class
Copyright (c)2009-2011 John Lamansky
*/

class suurl {
	
	/**
	 * Approximately determines the URL in the visitor's address bar. (Includes query strings, but not #anchors.)
	 * 
	 * @return string The current URL.
	 */
	function current() {
		$url = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $url .= "s";
		$url .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80")
			return $url.$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else
			return $url.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	function build_query($array) {
		return html_entity_decode(http_build_query($array));
	}

}

?>