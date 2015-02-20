<?php
if(!function_exists('intis'))
{
	/**
	 * Checks if a string contains an int or already is an int.
	 * @param mixed $val value to be checked
	 * @return bool 
	 */
	function intis($val) {
		return (ctype_digit($val) || is_int($val));
	}
}
if(!function_exists('isint')) {
	/**
	 * Checks if a string contains an int or already is an int and converts to an int if it is a astring in an int.
	 * @param type &$val 
	 * @param type $fallback 
	 * @return type
	 */
	function isint(&$val, $fallback = false) {
		if(intis($val)) {
			$val = (int) $val;
			return true;
		}
		if(is_int($fallback))
		{
			$val = $fallback;
		}
		return false;
	}
}
if(!function_exists('ip')) {
	/**
	 * Returns ip-address
	 * @return string ip
	 */
	function ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
		return $_SERVER['REMOTE_ADDR'];
	}
}
if(!function_exists('hash_available')) {
	/**
	 * Checks if a hash-algorithm is available on the server
	 * @param string $hash hash-name
	 * @return bool
	 */
	function hash_available($hash) {
		$algos = hash_algos();
		if(in_array(strtolower($hash), $algos)) return true;
		return false;
	}
}
if(!function_exists('mail_host')) {
	/**
	 * Returns the host for e-mail-adresses (including subdomains, domain and TLD)
	 * @return string
	 */
	function mail_host() {
		if(strpos(':', $_SERVER['HTTP_HOST']) === false && strpos('/', $_SERVER['HTTP_HOST']) === false)
		{
			return $_SERVER['HTTP_HOST'];
		}
		$url = parse_url($_SERVER['HTTP_HOST']);
		return $url['host'];
	}
}
if(!function_exists('get_youtube_links')) {
	/**
	 * Gets all youtube links in a string
	 * @param string $message The text message to get the youtube-links from
	 * @return array An array containing all youtube-links
	 */
	function get_youtube_links($message) {
		// youtube regex /(?:(?:http?s:)?(?:\/\/)?(?:www\.)?youtu(?:be\.com\/watch(?:.\W*v\=)|\.be\/)([^&\n\t\f\s\r]*))/i
		$youtube_regex = '/(?:(?:http?s:)?(?:\/\/)?(?:www\.)?youtu(?:be\.com\/watch(?:.\W*v\=)|\.be\/)([^&\n\t\f\s\r]*))/i';
		preg_match_all($youtube_regex, $message, $matches);
		return $matches[1];
	}
}
if(!function_exists('link_links')) {
	/**
	 * Wraps around all URL's a <a>-tag containing the link
	 * @param string $string the text containing the urls
	 * @return string
	 */
	function link_links($string) {
		return preg_replace("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", '<a target="_blank" href="$0">$0</a>', $string);
	}
}