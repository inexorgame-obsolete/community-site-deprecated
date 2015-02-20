<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['template'] = 'default';
$config['title']['prefix'] = '';		      			// String will be added before every title
$config['title']['suffix'] = ' • Inexor';	    	// String will be added after every title
$config['title']['default'] = 'Inexor';					// Default title if no title is set; The only title not containing the prefix and suffix
$config['variables'] = array();
$config['variables']['sitetitle'] = 'Inexor';
$config['variables']['base'] = FALSE; 				// Will be overwritten and replaced by base_url();
$config['variables']['data'] = FALSE;				// Will be overwritten and replaced by base_url() . $data;
$config['variables']['userdata'] = FALSE;			// Will be overwritten and replaced by base_url() . $userdata;

$config['disable_templating'] = array('this' => FALSE); // TRUE will fully disable templating!

// Disable templating on routes
// Templates can also be disabled by controllers ($this->template->disable()), but has a bad performance. So if possible disable templating here.
$config['disable_templating'][] = '*/api/*'; 		// Possible are also (:num), (:any) and *
$config['disable_templating'][] = '*/api';
$config['disable_templating'][] = 'cronjobs';
$config['disable_templating'][] = 'cronjobs/*';
// $config['disable_templating']['index'] = TRUE; // Disable Template at main

