<?php
class Feeds extends CI_Model
{
	// Load the database driver because it is necessary for all operations
	public function __construct()
	{
		$this->load->database();
	}
	
	/**
	 * getFeedList
	 * 
	 * Returns a list of activated feeds (title, url)
	 * @return mixed
	 */
	public function getFeedList()
	{
		$query = $this->db->get_where('feeds', array("status" => ACTIVATED));
		return $query->result();
	}
		
	/**
	 * getFeedDate
	 * 
	 * Returns the date of the last added feed item for feed $id
	 * @param integer $id
	 * @return date
	 */
	public function getLatestFeedDate($id)
	{
		$this->db->start_cache();
		$this->db->select_max('pubDate')->from('feed_items');
		$this->db->stop_cache();
		// Make above a prepared statement
		$this->db->where('feed_id', $id);
		
		$query = $this->db->get();
		$row = $query->row();

		if ($query->num_rows() > 0) {
			return $row->pubDate;
		} else {
			return false;
		}
	}
	
	/**
	 * Add feed item
	 * @param integer $id
	 * @param mixed $Item
	 */
	public function addItem($id, $Item)
	{
		$data = array(
				'feed_id' => $id,
				'title' => $Item['title'],
				'link' => $Item['link'],
				'pubDate' => $Item['pubDate'],
				'description' => $Item['description']
		);
		
		$this->db->insert('feed_items', $data);		
	}
	
	/**
	 * getItem
	 * 
	 * Returns the feed item by link or false
	 * @param integer $id
	 * @param string $link
	 * @return mixed
	 */
	public function getItem($id, $link)
	{
		$this->db
		->select('id')
		->from('feed_items')
		->where(array('feed_id' => $id, 'link' => $link));
		
		$query = $this->db->get();
		$row = $query->row();
		
		if ($query->num_rows() > 0) {
			return $row->id;
		} else {
			return false;
		}
	}
	
	/**
	 * Updates the lastBuiltDate of a given feed item
	 * @param integer $id
	 * @param date $date
	 */
	public function updateItem($id, $date)
	{
		$this->db->where('id', $id);
		$this->db->update('feed_items', array('lastBuiltDate' => $date));
	}
}