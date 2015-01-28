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

/**
 * Generates a list of <a href=""> </a> links
 * 
 * @param stdClass $items
 * @param string active
 * @return array
 */
function menu_links($items, $active = null)
{
	$menu_links = array();
	
	/*
	 * A URL can either be
	 * 
	 * key->value
	 * array-> "name", "source"
	 * Or a simple menu heading (h3)
	 * 
	 * This function is iterative using itself to iterate :)
	 * Check http://stackoverflow.com/questions/145337/checking-if-array-is-multidimensional-or-not for better understanding
	 */
	
	// This is a bit tricky and intended to be used for subitems :)
	if (is_object($items))
	{
		$items = (array)$items;
	}
	
	if (key_exists("active", $items)) {
		// We want to remove the active item because it is NOT  a menu item (just a propperty)
		$active = $items['active'];
		array_splice($items, array_search("active", $items) -1, 1); // Shift the active tag :)
	}
	
	foreach((array)$items as $key => $item)
	{	
		if (is_string($key)) {
			if (count($item) == count($item, COUNT_RECURSIVE)) {
				$subitems = array_walk($item, function(&$value){
					// Pass along the value and the currently active class
					return menu_links($value, $active);
				});
				
				$link = '<h3>'.$key.'</h3>'.ul($subitems);
				array_push($array, $$link);
				
			} else if (is_array($item)) {
				$link = ($key == $active) ? '<a href="'.$item['name'].'">'.$item['source'].'</a>' : '<a href="'.$item['name'].'" class="active">'.$item['source'].'</a>';
				array_push($menu_links, $link);
				
			} else if (is_string($item)) {
				$link = ($key == $active) ? '<a href="'.$key.'">'.$item.'</a>' : '<a href="'.$key.'" class="active">'.$item.'</a>';
				array_push($menu_links, $link);
				
			} else {
				array_push($menu_links, '<h3>'.$key.'</h3>');
			}
		}
	}
	
	return $menu_links;
}