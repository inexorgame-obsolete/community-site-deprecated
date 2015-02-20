<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Template {

	// General data like user object etc.
	private $_data;

	// Codeigniter object
	private $_CI;

	// User objet
	private $_user = false;

	/**
	 * Magic Method __construct();
	 */
	public function __construct() {
		$this->_CI =& get_instance();
		$this->config->load('template', FALSE);
		$this->config->load('search', FALSE);
		$this->config->load('data', TRUE);
		$this->load->helper('template');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->helper('security');
		$this->load->helper('language');
		$this->load->library('permissions');
		$this->load->library('auth');
		$this->_data['logged_in'] = $this->auth->is_logged_in();

		$base = $this->config->item('data')['location']['external'];
		$this->_data['base'] = ($base === FALSE) ? base_url() : $base;
		if($this->_data['logged_in'])
		{
			$user = (array) $this->auth->user();
			$this->_user = $user;
			if($user)
				$this->_CI->permissions->set_user($user['id']);
			$unsets = array('password', 'salt', 'forgotten_password_code', 'forgotten_password_time', 'remember_code');
			foreach($unsets as $u) { unset($user[$u]); }
			$this->_data['user'] = $user;
		}
		$disablers = $this->config->item('disable_templating');
		if(empty($disablers['checked'])) $disablers['checked'] = false;
		if($disablers['this'] == false && $disablers['checked'] == false)
		{
			unset($disablers['this']);
			$uri = '';
			$i = 1;
			while($this->uri->segment($i))
			{
				$uri .= '/' . $this->uri->segment($i);
				$i++;
			}
			$uri = substr($uri, 1);
			$disable = false;
			if($uri == '') 
			{
				if(isset($disablers['index']) && $disablers['index'] == true)
				{
					$disable = true;
				}
			} 
			else
			{
				unset($disablers['index']);
				foreach($disablers as $d) {
					$search = array('*', '(:num)', '(:any)', '/');
					$replace = array('.*', '[\de]*', '[^\/]*', '\/');
					$regex = '/' . str_replace($search, $replace, $d) . '/';
					$match = preg_match($regex, $uri, $matches);
					if($match == true && $matches[0] == $uri)
					{
						$disable = true;
						break;
					}
				}
			}
			$this->config->set_item('disable_templating', array('this' => $disable, 'checked' => TRUE));
		}
	}

	/**
	 * Magic Method __get();
	 * Gets general variables from the CodeIgniter object
	 * @param string $var The variable to get
	 */
	public function __get($var)
	{
		return $this->_CI->$var;
	}

	/**
	 * Magic Method __call();
	 * Renders a block of the name of the function
	 * @param string $name Originally the Method-name, here used as block-name
	 * @param array $arguments Originally the arguments, here not used.
	 */
	public function __call($name, $arguments) {
		if(file_exists(APPPATH . 'views/templates/' . $this->config->item('template') . '/_blocks/' . $name . '.php')) {
			$this->render_block($name, array('arguments' => $arguments));
		} else {
			throw new Exception('Undefined method Template::' . $name . '() called');
		}
	}

	/**
	 * Prints something in the Javascript-console
	 * @param mixed $data The data to print out
	 */
	public function print_console($data) {
		if(is_array($data) || is_object($data)) $data = json_encode((array) $data);
		elseif (!is_numeric($data)) $data = '"' . (string) $data . '"';
		$this->add_head('<script>console.log(' . $data . ')</script>');
	}

	/**
	 * Prevents curly brackets from being replaced
	 * @param string $text The text containing the curly brackets
	 */
	public function prevent_variables($text) {
		return str_replace(array("{", "}"), array("{<", ">}"), $text);
	}

	/**
	 * Prevents curly brackets from being replaced
	 * @param string $text The text containing the curly brackets
	 */
	public function prevent_replace($text) {
		return $this->prevent_variables($text);
	}

	/**
	 * Renders the header of the site.
	 */
	public function render_header() {
		if($this->_user)
		{
			$this->load->model('shared/user_menu_links_model');
			$menu_links = $this->user_menu_links_model->get_links();
			$data['menu_links'] = array();
			foreach($menu_links as $m)
			{
				if($m->permission_id != NULL && !$this->_CI->permissions->has_user_permission($m->permission_id)) { continue; }
				$c = array();
				foreach($m->childs as $mc)
				{
					if($mc->permission_id != NULL && !$this->_CI->permissions->has_user_permission($mc->permission_id)) { continue; }
					$c[] = $mc;
				}
				$m->childs = $c;
				$data['menu_links'][] = $m;
			}
			
		}
		if($this->config->item('disable_templating')['this'] == FALSE)
		{
			$data['main_search_id'] = 'main_search';
			$data['search_form']['search'] = array(
				'id'	=> 'main-search',
				'name'  => 'search',
				'type'  => 'text',
				'placeholder' => 'Search...',
				'autocomplete' => 'off',
				'data-searchid' => $data['main_search_id']
			);
			$data['search_form']['submit'] = array(
				'name'  => 'submit',
				'value' => 'Search',
			);
			$searchdata = $this->config->item('search');
			$i = 0;
			foreach($searchdata as $n => $v)
			{
				$data['search_form']['radio']['inputs'][$i] = array(
					'name' => 'target',
					'value' => $v[0],
					'data-api' => $v[1],
					'id' => 'search_radio_' . ($i + 1)
				);
				if($i === 0) 
				{
					$data['search_form']['radio']['inputs'][$i]['checked'] = TRUE;
					$data['default_search_api'] = $v[1];
				}
				$data['search_form']['radio']['labels'][$i] = array(
					'for' => 'search_radio_' . ($i + 1),
					'value' => $n
				);
				$i++;
			}
			$this->Render_block('header', $data);
		}
		return;
	}

	/**
	 * Renders the footer of the site.
	 */
	public function render_footer() {
		$this->Render_block('footer');
		return;
	}

	/**
	 * Sets the title of the site
	 * (the <title>-Attribute)
	 */
	public function set_title($title)
	{
		$this->variable('title', $this->config->item('title')['prefix'] . htmlentities($title) . $this->config->item('title')['suffix'], TRUE);
	}

	/**
	 * Gets & Sets a variable
	 * @param string $key Variable key
	 * @param mixed $value If false & $force false returns variable ELSE if sets variable if not set or $force = TRUE
	 * @param bool $force whether to force chaning the variable or not.
	 */
	public function variable($key, $value = FALSE, $force = FALSE) {
		$variables = $this->config->item('variables');
		if(!isset($variables[$key]) || $force == TRUE)
		{
			if($value != FALSE || $force == TRUE)
			{
				$variables[$key] = $value;
				$this->config->set_item('variables', $variables);
				return true;
			}
			else
			{
				return false;
			}
		}
		return $variables[$key]; 
	}

	/**
	 * Disables templating for the site.
	 * Does not render header, footer etc.
	 */
	public function disable() {
		$this->output->set_output('');
		$this->config->set_item('disable_templating', array('this' => TRUE));
		return;
	}

	/**
	 * Renders a block
	 * @param string $block name of the block
	 * @param array $data the data to pass to the view
	 * @param bool $force
	 */
	public function render_block($block, $data = false, $force = true)
	{
		$template = $this->config->item('template');
		$view = 'templates/' . $this->config->item('template') . '/_blocks/' . $block;
		$file = APPPATH . 'views/' . $view . '.php';
		if(is_array($data))
		{
			$this->_data = array_merge($this->_data, $data);
		}
		$file_exists = file_exists($file);
		if((strlen($template)===0 || !$file_exists) && $force == true && file_exists('views/templates/default/_blocks/' . $block . '.php'))
		{
			$this->load->view('templates/default/_blocks/' . $block, $this->_data);
		}
		elseif($file_exists)
		{
			$this->load->view($view, $this->_data);
		}
		return;
	}

	/**
	 * Adds $string to the head of the HTML
	 * @param string $string The string to add.
	 */
	public function add_head($string) {
		$this->variable('head', $this->variable('head') . "\n" . $string, TRUE);
	}

	/**
	 * Checks if the file exists in the data folder and adds it if it does.
	 * @param string $file The js-file
	 * @param mixed $class Loades js file for specific class: 
	 * OBJECT(class) gets class name | 
	 * STRING(class) uses this as class name | 
	 * BOOL(FALSE) no class will be loaded
	 * @return bool TRUE: if file exists and was added to the head.
	 */
	public function add_js($file, $class = false) {
		if(is_object($class)) $class = get_class($class);
		elseif(!is_string($class) && !is_numeric($class)) $class = false;
		if($class) $file = 'modules/' . $class . '/' . $file;
		$file = strtolower($file);
		if(file_exists(FCPATH . 'data/js/' . $file . '.js')) {
			$this->add_head('<script src="' . base_url() . 'data/js/' . $file . '.js"></script>');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks if the file exists in the data folder and adds it if it does.
	 * @param string $file The css-file
	 * @param mixed $class Loades css file for specific class: 
	 * OBJECT(class) gets class name | 
	 * STRING(class) uses this as class name | 
	 * BOOL(FALSE) no class will be loaded
	 * @return bool TRUE: if file exists and was added to the head.
	 */
	public function add_css($file, $class = false) {
		if(is_object($file)) {
			$class = get_class($file);
			$class = strtolower($class);
			if(file_exists(FCPATH . 'data/css/modules/' . $class . '.css'))
			{
				$this->add_head('<link rel="stylesheet" type="text/css" href="' . base_url() . 'data/css/modules/' . $class . '.css" />');
				return true;
			}
			return false;
		}
		if(is_object($class)) $class = get_class($class);
		elseif(!is_string($class) && !is_numeric($class)) $class = false;
		if($class) $file = 'modules/' . $class . '/' . $file;
		$file = strtolower($file);
		if(file_exists(FCPATH . 'data/css/' . $file . '.css')) {
			$this->add_head('<link rel="stylesheet" type="text/css" href="' . base_url() . 'data/css/' . $file . '.css" />');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Renders an error
	 * @param string $headline headline to display to the viewer
	 * @param string $text text to display to the viewer
	 */
	public function render_error($headline, $text)
	{
		$this->render_block('error', array('headline' => $headline, 'text' => $text));
	}

	/**
	 * Renders a permission error
	 * @param mixed $headline STRING: headline wich will be displayed | NULL: Default headline
	 * @param mixed $text STRING: text wich will be displayed | NULL: Default text
	 */
	public function render_permission_error($headline = NULL, $text = NULL) 
	{
		$this->render_block('no_permissions', array('headline' => $headline, 'text' => $text));
	}

	/**
	 * Displays the page
	 */
	public function display_page()
	{
		$this->load->library('Config_validations', array('data'));
		$data_config = $this->config->item('data');
		$this->variable('base', $data_config['location']['external'], TRUE);
		$this->variable('data', $data_config['location']['external'] . $data_config['dir']['data'], TRUE);
		$this->variable('userdata', $data_config['location']['external'] . $data_config['dir']['user'], TRUE);

		$disable = $this->config->item('disable_templating')['this'];
		if($disable == FALSE)
		{
			$this->render_footer();
		}
		$output = $this->output->get_output();
		if($disable == FALSE)
		{
			$this->output->set_output($this->replace_variables($output));
		}
		$this->output->_display();
	}

	/**
	 * Replaces all {variables} with its values
	 * @param string $output The string which should be replaced
	 * @return string The replaced string
	 */
	public function replace_variables($output) 
	{
		if(strlen($this->variable('title')) < 1)
			$this->variable('title', $this->config->item('title')['default'], TRUE);
		$variables = $this->config->item('variables');
		$output = preg_replace_callback('/{([^\:{<}]*):([^{>}]*)}/', function ($hits) {
			$var = $this->variable($hits[1]);
			if(!empty($var)) return $var;
			return $hits[2];
		}, $output);
		$output = preg_replace_callback('/{([^\{}<>]*)}/', function ($hits) {
			$var = $this->variable($hits[1]);
			if(isset($var)) return $var;
			return '';
		}, $output);
		$output = str_replace(array("{<", ">}"), array("{", "}"), $output);
		return $output;
	}

	/**
	 * Generates an E-Mail based on the content
	 * General structure of the content-array:
	 * array('tag' => 'text', 'h1' => 'headline')
	 * @param array $content content-array
	 * @return string The HTML-E-Mail
	 */
	public function generate_email($content)
	{
		$data = array('content' => $content, 'email' => true);
		$email = $this->_CI->load->view('static/mail', $data, true);
		$email = $this->replace_variables($email);
		return $email;
	}

}