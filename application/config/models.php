<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Configuration file for the models
 * Contains which model should use which database config-group (See /application/config/database.php)
 * And which table.
 *
 * If for a model no config is given the default-group will be set and a default table.
 */
$config = array(
	'users_model' => array('group' => 'user'),
	'permissions_model' => array('group' => 'user'),
	'users_pgroups_model' => array('group' => 'user'),
	'users_permissions_model' => array('group' => 'user'),
	'pgroups_permissions_model' => array('group' => 'user'),
	'users_stay_logged_in_model' => array('group' => 'user'),
);