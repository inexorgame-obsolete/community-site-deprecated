<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller
{	
	public function _remap($method, $params = array())
	{
		if ($this->input->is_cli_request()) {
			if (method_exists($this, $method))
			{
				return call_user_func_array(array($this, $method), $params);
			}	
		} else {
			show_404();
		}
	}
	
	public function feeds()
	{
		$this->load->library('RSS');
		$this->load->model('cron/feeds');
		
		foreach ($this->feeds->getFeedList() as $feed)
		{	
			// Initialize the content
			$this->rss->Retrieve($feed->url);
			
			// We use LimitIterator to skip the description!
			$Content = new ArrayIterator($this->rss->Content);
			
			foreach (new LimitIterator($Content, 1) as $Item)
			{
				if ($Item["date"] > $this->feeds->getLatestItemDate($feed->id))
				{
					$desc = substr($Item["description"], 0, strrpos($title, ' ', 100) ); // Save only 100 words!
					$this->feeds->addItem($feed->id, array("title" => $Item["title"], "link" => $Item['link'], "date" => $Item["date"], "description" => $desc));
				}
			}
		}
	}
}