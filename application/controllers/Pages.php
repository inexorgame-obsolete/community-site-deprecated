<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Pages extends CI_Controller {

	/**
	 * Magic Method __construct();
	 */
	public function __construct() {
		parent::__construct();
		$this->config->load('static_pages');
		$this->load->library('template');
		$this->template->add_css($this);
	}

	/**
	 * Load static pages
	 * @param string $page the page to load
	 */
	public function view($page = 'home') {
		if($page == 'mail') $this->template->disable();
		if(!file_exists(APPPATH.'/views/static/' . $page . '.php'))
		{
			show_404();
		}
		$config = $this->config->item($page);
		if($config)
		{
			if(isset($config['title']))
			{
				$this->template->set_title($config['title']);
			}
			if(isset($config['js'])) {
				if(is_array($config['js']))
				{
					foreach($config['js'] as $js)
					{
						$this->template->add_js($js);
					}
				} else {
					$this->template->add_js($config['js']);
				}
			}
			if(isset($config['css'])) {
				if(is_array($config['css']))
				{
					foreach($config['css'] as $css)
					{
						$this->template->add_css($css);
					}
				} else {
					$this->template->add_css($config['css']);
				}
			}
			if(isset($config['head']))
			{
				if(is_array($config['head']))
				{
					$config['head'] = implode("\n", $config['head']);
				}
				$this->template->add_head($config['head']);
			}
		}

		$this->load->view('static/' . $page);
	}

}
