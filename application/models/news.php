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
	
	/**
	 * getEntriesInRange
	 * This function returns all item ID's within the pubDate range of startDate - endDate
	 * Input can either be a DateTime object or a string
	 * 
	 * @param mixed $startDate
	 * @param mixed $endDate
	 * @return mixed
	 */
	public function getEntriesInRange($startDate, $endDate)
	{
		// Make sure both datetime strings are converted to UTC
		$gmtTimezone = new DateTimeZone('GMT');
		
		if (!$startDate instanceof DateTime)
		{
			$startDate = new DateTime($startDate, $gmtTimezone);
		}
		
		if (!$endDate instanceof DateTime)
		{
			$endDate = new DateTime($endDate, $gmtTimezone);
		}
		
		$this->db
		->select('id')
		->where('pubDate >=', $startDate->format("Y-m-d H:i:s"))
		->where('pubDate <=', $endDate->format("Y-m-d H:i:s"));
		
		$query = $this->db->get();
		
		if ($query->num_rows > 0)
		{
			return $query->result();
		} else {
			return false;
		}
	}
}