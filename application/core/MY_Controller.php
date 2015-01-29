<?php

class MY_Controller extends CI_Controller
{
	/**
	 * Define standard objects which can be manipulated during the call
	 * 
	 * @var string $title
	 * @var stdClass $meta
	 * @var stdClass $menu
	 * @var stdClass $footer
	 */
	public $title;
	public $meta;
	public $menu;
	public $footer;
	
	protected $language;
	
	public function __construct()
	{
		parent::__construct(); // Load the original controller
		
		//Load additional resources
		$this->load->library(array('session', 'lang'));
		$this->config->load('site');
		
		/*
		 * Retrieve the currently set user language from the session
		 * If none is set we use "english" as a default value
		 */		
		$this->language = (!empty($this->session->userdata("language"))) ? $this->session->userdata("language") : "english";
		
		// Loads the title
		$this->title = $this->config->item('title');
		
		/*
		 * Generates a set of meta information which can be individualised per page
		 * The first set (author, publisher, date, page_topic, content_language ..) should be set individually per entry
		 */
		$this->meta = new stdClass();
		
		$this->meta->author = "";
		$this->meta->publisher = "";
		$this->meta->copyright = "";
		$this->meta->page_topic = "";
		$this->meta->content_language = "";
		
		$this->meta->keywords = $this->config->item('keywords');
		$this->meta->description = $this->config->item('description');
		$this->meta->revisit = $this->config->item('revisit');
		$this->meta->robots = $this->config->item('robots');
		
		/*
		 * Loads the menu
		 * Items are stored within the following scheme (using an stdClass object)
		 * 
		 * $menu = array(
		 * 	"topic" = array(
		 * 		entry = array("name" => "name", "source" => "someview")
		 * 	),
		 *  
		 *  entry => "name"
		 * )
		 * 
		 * and will be mapped as <a href="view"> NAME </a>
		 * (an optional view parameter can be applied)
		 */
		$this->menu = new stdClass();
		$this->menu->title = $this->config->item('title');
		
		// This is a tiny hack to offer acces via $object->footer->item but initialize using the PHP array syntax :P
		// When loading from the config (TODO) this should be converted (if an array) or mapped (if an object)
		$this->menu->items = (object)array(
			"Blog" => "https://inexor.org",
			"Organisation" => array(
				"Code" => "https://github.com/inexor-game/code",
				"Wiki" => "https://github.com/inexor-game/code/wiki"
			)
		);
		
		$this->footer = (object)array(
			"Blog" => "https://inexor.org",
			"Community Site" => "https://community.inexor.org",
			"Github Page" => "https://github.com/inexor-game/"
		);
	}
	
	public function display($view, $data = null)
	{
		/*
		 * Proccess internal objects to be passed to our views
		 */
		
		$headerData = array(
			"title" => $this->title,
			"lang" => $this->language,
			"meta" => array(),
			"menu" => array(
				"title" => $this->menu->title,
				"items" => menu_links($this->menu)
			)	
		);
		
		
		foreach ((array)$this->meta as $key => $item) {
			array_push($headerData['meta'], array("name" => $key, "content" => $item));
		}
		
		
		$this->load->view("templates/header", $headerData);
		//$this->load->view("templates/sidebar");
		$this->load->view($view, $data);
		$this->load->view("templates/footer", array("footer_items" => menu_links($this->footer)));
	}
	
}