<?php
/**
 * script_tag
 * Generates a javascript tag
 * 
 * @access public
 * @param string $script
 * @return string
 */
function script_tag($script)
{
	$CI =& get_instance();	
	return '<script type="text/javascript" src="' . $CI->config->slash_item('base_url') . $script . '"></script>';
}