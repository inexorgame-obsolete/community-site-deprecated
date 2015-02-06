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
		$this->rss->Retrieve("http://darkkeepers.dk/component/content/category/18-tournament.feed?type=rss");

		var_dump($this->rss->Content);
	}
}