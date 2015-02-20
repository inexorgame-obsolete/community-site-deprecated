<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Implements a loadconfig function for loading the database and setting the table
 */
class MY_Model extends CI_Model
{
	protected $Table;

	function __construct()
	{
		parent::__construct();
	}


	protected function load_config($fallbackTable = NULL)
	{
		require FCPATH . 'application/config/database.php';

		$this->load->config('models'  , TRUE);
		$config = $this->config->item('models');

		$class = strtolower(get_class($this));

		if(isset($config[$class]) && is_array($config[$class])) 
		{
			if( !empty($config[$class]['group']) )
				$dbconf = $config[$class]['group'];
			else
				$dbconf = $active_group;

			if( !empty($config[$class]['table']))
				$this->Table = $config[$class]['table'];
			elseif(is_string($fallbackTable))
				$this->Table = $fallbackTable;
		}
		else
		{
			if(is_string($fallbackTable))
				$this->Table = $fallbackTable;

			$dbconf = $active_group;
		}

		$this->db = $this->load->database($db[$dbconf], TRUE);
	}
}