<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* 
	Search sitest, do not add beginning or ending slash.
	$config['search']['/user/search/']
	                   ^           ^
	not these slashes, like this:
	$config['search']['user/search']

 */

$config['search'] = array();
$config['search']['User'] = array('user/search', 'search/api/user');		// First item for form action, second for json api
$config['max_results'] = 250;
$config['min_chars'] = 3;