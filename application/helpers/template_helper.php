<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!function_exists('data')) {
	/**
	 * Returns a link to a data-file if the file exists
	 * @param string $file location of the file inside the data-folder
	 * @return mixed FALSE if file does not exist, else URL
	 */
	function data($file)
	{
		if(file_exists(FCPATH . 'data/' . $file))
		{
			return base_url() . 'data/' . $file;
		}
		return false;
	}
}
if(!function_exists('css')) {
	/**
	 * Returns a link to css file if file exists
	 * @param string $file css-file-location
	 * @return mixed BOOL(FALSE) | STRING(link)
	 */
	function css($file) { return data('css/' . $file . '.css'); }
}
if(!function_exists('dcss')) {
	/**
	 * Echo's <link> to the css file
	 * @param string $file css-file
	 * @param mixed $media FALSE if no media-tag; else media-expression
	 * @return bool
	 */
	function dcss($file, $media = false) {
		if($file = css($file)) 
		{
			$echo = '<link rel="stylesheet" type="text/css" ';
			if($media) $echo .= 'media="' . $media . '" ';
			$echo .= 'href="' . $file . '" />' . "\n";
			echo $echo;
			return true;
		}
		return false;
	}
}
if(!function_exists('js')) {
	/**
	 * Returns a link to js file if file exists
	 * @param string $file js-file-location
	 * @return mixed BOOL(FALSE) | STRING(link)
	 */
	function js($file) { return data('js/' . $file . '.js'); }
}
if(!function_exists('image')) {
	/**
	 * Returns a link to an image if it exists
	 * @param string $file image-location
	 * @return mixed BOOL(FALSE) | STRING(link)
	 */
	function image($file) {	return data('images/' . $file); }
}
if(!function_exists('iimage')) {
	/**
	 * Returns a link to an image user-image (avatar, background or normal image)
	 * @param string $file image-name
	 * @param mixed $userimage type of the image
	 * @param array $types allowed extensions of the image
	 * @return mixed BOOL(FALSE) | STRING(link)
	 */
	function iimage($file, $userimages = false, $types = array('.jpg', '.jpeg', '.png', '.gif')) {
		switch($userimages) {
			case 1:
				$dir = 'users/avatar/';
				break;
			case 'avatar':
				$dir = 'users/avatar/';
				break;
			case 2:
				$dir = 'users/background/';
				break;
			case 'background':
				$dir = 'users/background/';
				break;
			default:
				$dir = 'images/';

		}
		foreach($types as $type)
		{
			$return = data($dir . $file . $type);
			if($return !== false) return $return;
		}
		if($userimages == 1 || $userimages == 'avatar') return call_user_func(__FUNCTION__, 'no-avatar', 1);
		return false;
	}
}
if(!function_exists('avatar_image')) {
	/**
	 * Returns image-link of an avatar with fallback to no-avatar
	 * @param int $id userid
	 * @return mixed BOOL(FALSE) | STRING(link)
	 */
	function avatar_image($id) {
		if($i = iimage($id, 1)) { return $i; }
		return data('users/avatar/no-avatar.png');
	}
}
if(!function_exists('showname')) {
	/**
	 * Displays correctly a username
	 * @param mixed $user OBJECT | ARRAY / of the user
	 * @param string $class Classes the user <span>-tag should have 
	 * @return string HTML-Markup
	 */
	function showname($user, $class = "user") {
		if(!is_array($user)) $user = (array) $user;
		$return = '<span class="' . $class . '">';
		if(strlen($user['ingame_name']) < 1)
			$return .= d($user['username']);
		else
			$return .= d($user['ingame_name']);
		$return .= '</span>';
		return $return;
	}
}
if(!function_exists('h')) {
	/**
	 * Shortcut for htmlentities($string)
	 * @param string $string The string to escape
	 * @return string The escaped string
	 */
	function h($string) { return htmlentities($string); }
}
if(!function_exists('he')) {
	/**
	 * echo's escaped strign
	 * @param string $string The string to escape and echo
	 */
	function he($string) {
		echo h($string);
		return;
	}
}
if(!function_exists('prevent_replace')) {
	/**
	 * Prevents strings containing curly brackets ("{" and "}") to be replaced by accident with variables or removed if the "variable" does not exists.
	 * @param string $string The string containing the curly brackets
	 * @return string The string which prevents the curly brackets from being replaced
	 */
	function prevent_replace($string) { return str_replace(array("{", "}"), array("{<", ">}"), $string); }
}
if(!function_exists('p_r'))
{
	/**
	 * Shortcut for prevent_replace($string)
	 * @param string $string The string containing the curly brackets
	 * @return string The string which prevents the curly brackets from being replaced
	 */
	function p_r($string) { return prevent_replace($string); }
}
if(!function_exists('ph')) {
	/**
	 * Shortcut for prevent_replace(htmlentities($string));
	 * @param string $string The string containing the curly brackets and HTML-Markup
	 * @return string String without HTML-markup and preventing curly brackets from being replaced
	 */
	function ph($string) 
	{
		return p_r(h($string));
	}
}
if(!function_exists('d'))
{
	/**
	 * Returns a fully formated string without any HTML-Markup and replaced brackets
	 * @param string $string The string to format
	 * @param boold $nl2br use nl2br or not
	 * @return string The formated string
	 */
	function d($string, $nl2br = false) {
		$return = p_r(h($string));
		if($nl2br) return nl2br($return);
		return $return;
	}
}
if(!function_exists('dt'))
{
	/**
	 * Returns a correctly formated date
	 * @param mixed $date STRING(datestring) | INT(unix-time-format)
	 * @return string
	 */
	function dt($date) {
		return 'on ' . date('j\<\s\u\p\>S\<\/\s\u\p\> \o\f F Y', strtotime($date)); // return somethint like 1st of January 2014
	}
}
if(!function_exists('tm'))
{
	/**
	 * Returns correctly formated time
	 * @param mixed $date STRING(datestring) | INT(unix-time-format)
	 * @return string
	 */
	function tm($time){
		return 'at ' . date('H:i:s', strtotime($time));
	}
}
if(!function_exists('dt_tm')) {
	/**
	 * Returns correctly formated date-time
	 * @param mixed $date STRING(datestring) | INT(unix-time-format)
	 * @return string
	 */
	function dt_tm($time) {
		return dt($time) . ' ' . tm($time);
	}
}