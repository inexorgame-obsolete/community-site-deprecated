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

/*
 * 
 */
function menu_links($items)
{
	/*
	 * Menu item can either be a
	 * key->value pair
	 * or a simple heading (h3)
	 *
	 * This can be nested as well
	 * heading -> item, item, item
	 */

	// Use clojures for recursion!
	// This function can recursively call itself :)
	$item = function($items) use (&$item)
	{
		$menu_entries = array();

		array_walk($items, function(&$value, &$key) use(&$item, &$menu_entries){
			if (is_string($key) && empty($value)) {
				// Heading
				array_push($menu_entries, '<h3>'.$key.'</h3>');

			} else if (is_string($key) && is_string($value)) {
				// Simple link
				array_push($menu_entries, '<a href="'.$value.'">'.$key.'</a>');

			} else if (is_string($key) && is_array($value)) {
				// Nested entry with heading
				$nestedEntry = '<h3>'.$key.'</h3>' . ul($item($value));
				array_push($menu_entries, $nestedEntry);
					
			}
		});

			return $menu_entries;
	};

	return $item($items);
}