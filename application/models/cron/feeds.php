<?php
class Feeds extends CI_Model
{
	public function __construct()
	{
		$this->load->database();
	}
	
	public function getFeedList()
	{
		return $this->db->get('feeds');
	}
		
	public function getLatestItemDate($id)
	{
		// Use database caching because this will be called quiet often within the cron-controller
		$this->db->start_cache();
		$this->db
		->select('date')
		->from('feed_items')
		->where('id', $id)
		->group_by('date')
		->limit(1);
		$this->db->stop_cache();
		
		return $this->db->get();
	}
	
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