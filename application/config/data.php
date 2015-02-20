<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['location']['external'] = FALSE;				// needs trailing slash; FALSE will changed to base_url() in the post_controller-hook
$config['location']['internal'] = FALSE;				// needs trailing slash; FALSE will changed to FCPATH in the post_controller-hook

$config['dir']['data'] = 'data';						// Default dir for all data files; Relative path from Filecontroller; NO TRAILING SLASH
$config['dir']['user'] = 'data/user_upload';			// Default dir for files uploaded by users (excluding background-images & avatars); Relative path from data_url; NO TRAILING SLASH

$config['default_file_limit'] = 50;					// Default file limit for users without a defined limit in the database
$config['default_folder_limit'] = 30;				// Default folder limit for users without a defined limit in the database
$config['default_file_size'] = "10MB";				// Default file size limit; Currently not in use

$config['dir_regex'] = "/([a-zA-Z0-9_]*)/";					// Regex for created dirs: Default: Only Numbers, Letters (without umlauts) and _; /([a-zA-Z0-9_]*)/
$config['parent_dir_regex'] = "/([\/a-zA-Z0-9_]*)/";
$config['file_regex'] = "/[a-zA-Z0-9\._\-]*.[a-zA-Z0-9]/";	// Regex for files to create; Default: Only Numbers, Letters (without umlauts) - and _ containing a . (dot); /[a-zA-Z0-9\._\-]*.[a-zA-Z0-9]/

$config['dir_regex_fail'] = "Your foldername contains disallowed characters. Only the following characters are allowed: A-Z, a-z and 0-9.";

$config['filetypes'] = array();
$config['filetypes']['image'] = array('png', 'jpg', 'gif');