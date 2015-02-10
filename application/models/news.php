<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_Model extends CI_Model
{
	// Load the database driver because it is necessary for all operations
	public function __construct()
	{
		$this->load->library('database');
	}
	
	/**
	 * getEntries
	 * This function will fetch feed items
	 * Either a single ID or an array of ID's can ge used
	 * 
	 * @param integer $Items
	 * @param array $Items
	 * @return mixed
	 */
	
	public function getEntries($Items)
	{
		$query = $this->db->get('feed_items')->where_in($Items);
		
		if ($query->num_rows > 0)
		{
			return $query->result();
		} else {
			return false;
		}
		
	}
}