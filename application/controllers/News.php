
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller {

	/**
	 * Magic Method __construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('template');
		$this->template->add_css($this);
	}

	public function index()
	{
		$this->load->model('feed_items_model');
		
		$startDate = new DateTime();
		$endDate = new DateTime();
		$endDate->modify("-6 weeks");

		$ids = $this->feed_items_model->getEntriesInRange($startDate, $endDate);
		// TODO: No filters implemented yet, gotta do that once the login system is rolled.

		$ids = array_map(function($a) { return $a->id; }, $ids);

		$this->load->view('news/index', array("items" => $this->feed_items_model->getEntries($ids)));
	}
	
}
