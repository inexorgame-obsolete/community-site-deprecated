<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends MY_Controller {
	public function index()
	{
		$this->load->model('news');
		
		$this->display('news', null);
	}
	
}