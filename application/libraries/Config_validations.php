<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Validates configs set in the constructor
 * For example needed to replace variables etc.
 * If the config contains something like site_url() or FCPATCH
 */
class Config_validations {
	// The codeigniter object
	private $_CI;

	// Functions which should not be executed on validate_all.
	private $_validate_all_blacklist = array('__construct', 'validate_all');	

	/**
	 * Magic Method __construct();
	 * Validates all configs submitted in $validate if a validation therefore exists.
	 * @param mixed $validate BOOL(FALSE) will not validate any libraries, just construct | ARRAY(configs) will validate all configs set in the array
 	 */
	public function __construct($validate = false) 
	{
		$this->_CI =& get_instance();
		$this->_CI->load->helper('url');

		if(is_array($validate))
		{
			foreach($validate as $v)
			{
				if(method_exists($this, 'validate_' . $v))
				{
					call_user_func_array(array($this, 'validate_' . $v), array());
				}
			}
		}
	}

	/**
	 * Validates all configs (that have a function in this class)
	 */
	public function validate_all()
	{
		$methods = get_class_methods($this);
		foreach ($methods as $m) {
			if(!in_array($m, $this->_validate_all_blacklist))
			{
				call_user_func_array(array($this, $m), array());
			}
		}
	} 

	/**
	 * Validates the data-config
	 */
	public function validate_data()
	{
		$this->_CI->load->config('data', TRUE);
		$config = $this->_CI->config->item('data');


		if($config['location']['internal'] === FALSE) $config['location']['internal'] = FCPATH;
		if($config['location']['external'] === FALSE) $config['location']['external'] = site_url();

		$this->_CI->config->set_item('data', $config);
	}
}