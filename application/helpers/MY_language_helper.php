<?php
/**
 * Returns a language tag when given a ISO-2 code
 * 
 * @access public
 * @param string $iso
 * @return string
 */

function get_language_name($iso) {
	$CI =& get_instance();
	$CI->load->driver('cache');
		
	if (! $lang = $CI->cache->get('lang'))
	{
		$lang = array();
		$row = 1;
		if (($handle = fopen('http://loc.gov/standards/iso639-2/ISO-639-2_utf-8.txt', 'r')) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, '|')) !== FALSE) {
				$row++;
				if (!empty($data[2])) {
					/* to get only the first language (anything before ;) */
					$lang[$data[2]]  = strtok($data[3], ';');
				}
			}
			fclose($handle);
		}

     	// Save into the cache for 1 hour
     	$CI->cache->save('lang', $lang, 3600);
	}
	
	return $lang[$iso];
}
