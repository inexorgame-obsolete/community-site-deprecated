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
		$this->load->library('rss');
		$this->load->helper('security');
		$this->load->model('cron/feeds');
		
		foreach ($this->feeds->getFeedList() as $feed)
		{	
			// Initialize the content
			$this->rss->Retrieve($feed->url);

			$Content = array_slice($this->rss->Content, 1);
			$Content = array_filter($Content, function(&$value) {
				if (empty($value)) {
					// Don't permit empty arrays
					return false;
				} /* This could be used to verify timestamps
				  else if {
					return (DateTime::createFromFormat(..., $value["date"]) !== FALSE); 
				}
				*/
				return true;
			});
			$Content = array_reverse($Content);
			
			foreach ($Content as $Item)
			{				
				$gmtTimezone = new DateTimeZone('GMT');
				$ItemDate = new DateTime($Item["pubDate"], $gmtTimezone);
				$LatestFeedDate = new DateTime($this->feeds->getLatestFeedDate($feed->id), $gmtTimezone);
				
				if (!$this->feeds->getLatestFeedDate($feed->id) || $ItemDate->getTimestamp() > $LatestFeedDate->getTimestamp())
				{
					$Item["title"] = xss_clean($Item["title"]);
					//$Item["link"] = xss_clean($Item["link"]); // This is not 100% necessarily
					$Item["description"] = xss_clean($Item["description"]);
					$desc = substr($Item["description"], 0, strrpos($Item['description'], ' ', 100) ); // Save only 100 words!
					$this->feeds->addItem($feed->id, array("title" => $Item["title"], "link" => $Item['link'], "pubDate" => $ItemDate->format("Y-m-d H:i:s"), "description" => $desc));
				} else if ($this->feeds->getItem($feed->id, $Item["link"]) !== FALSE && !empty($Item["lastBuiltDate"])) {
					$lastBuiltDate = new DateTime($Item["lastBuiltDate"], $gmtTimezone);
						
					if($lastBuiltDate->getTimestamp() > $ItemDate->getTimestamp()) {
							$this->feeds->updateItem($this->feeds->getItem($feed->id, $Item["link"]), $lastBuiltDate->format("Y-m-d H:i:s"));
					}
				}
			}
		}
	}
}