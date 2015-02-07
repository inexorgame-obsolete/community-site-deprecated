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
		// Use database caching because this will be called quiet often within the cron-controller
		$this->db->start_cache();
		$this->db
		->select_max('date')
		->from('feed_items')
		->where('feed_id', $id);
		$this->db->stop_cache();
		
		$query = $this->db->get();
		$row = $query->row();
		
		if (is_object($row)) {
			return $row->date;
		}		
	}
	
	/* public function getLatestItemDate($id, $link)
	{
		$this->db
		->select('date')
		->from('feed_items')
		->where(array('feed_id' => $id, 'link' => $link));
		
		$query = $this->db->get();
		$row = $query->row();
		
		if (is_object($row)) {
			return $row->date;
		}
	}*/
	
	public function addItem($id, $Item)
	{
		$data = array(
				'feed_id' => $id,
				'title' => $Item['title'],
				'link' => $Item['link'],
				'date' => $Item['date'],
				'description' => $Item['description']
		);
		
		$this->db->insert('feed_items', $data);		
	}
	
	/* TODO: Update items if they're changed afterwards ... is this necessary?
	 * public function updateItem($id, $Item)
	{
		
	}*/
}