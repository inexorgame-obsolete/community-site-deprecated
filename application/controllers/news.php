<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends MY_Controller {
	public function index()
	{
		$this->load->model('news_model', 'news');
		
		$startDate = new DateTime();
		$endDate = $startDate->modify("-6 weeks");
		$ids = $this->news->getEntriesInRange($startDate, $endDate);
		// TODO: No filters implemented yet, gotta do that once the login system is rolled.
		
		$this->display('news', array("Items" => $this->news->getEntries($ids)));
	}
	
}