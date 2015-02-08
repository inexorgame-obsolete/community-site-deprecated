<?php
class Feeds extends CI_Model
{
	public function __construct()
	{
		$this->load->database();
	}
	
	public function getFeedList()
	{
		$query = $this->db->get_where('feeds', array("status" => ACTIVATED));
		return $query->result();
	}
		
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
	
	
	public function updateItem($id, $date)
	{
		$this->db->where('id', $id);
		$this->db->update('feed_items', array('lastBuiltDate' => $date));
	}
}