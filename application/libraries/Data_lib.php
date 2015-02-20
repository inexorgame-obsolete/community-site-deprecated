<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_lib {
	// Owner of a dir or file
	public $owner;

	// Current user trying to access the file
	public $user;

	// Dir info - base: link to file; filecontroller: absolute path (with the filecontroller); relative: same as fc, but without FCPATH
	private $_dir = array(
		'internal' => false,
		'external' => false,

		'base' => false,
		'filecontroller' => false,
		'relative' => false
	);

	// The codeigniter object
	private $_CI;

	// Info of a dir with all the subdirs (contains files, filesizes, subdirs)
	private $_dirinfo;

	// $this->_CI->load->config('data', TRUE); $this->config->item('data');
	private $_config;

	// Path and info of a file
	private $_file;	

	// Extension matching to the file.
	private $_fs_extension;

	// Location of the owners dir
	private $_ownerdir = NULL;

	// Final name of a file which will be moved.
	private $_final_name;

	/**
	 * Magic Method __construct();
	 */
	public function __construct() 
	{
		$this->_CI =& get_instance();
		$this->_CI->load->library('auth');
		$this->_CI->load->library('fileinfo');
		$this->_CI->config->load('data', true);
		$this->_config = $this->_CI->config->item('data');
		foreach($this->_config['dir'] as $i => $v) {
			$this->_parse_dir($this->_config['dir'][$i]);
		}
	}

	/**
	 * Magic Method __call();
	 * Passes trough to the Fileinfolib so it doesn't needed to be loaded externally.
	 * @param string $name Name of the method to be called
	 * @param array $arguments Arguments which should be passed
	 */
	public function __call($name, $arguments)
	{
		if (!method_exists($this->_CI->fileinfo, $name) )
		{
			trigger_error('Undefined method Data_lib::' . $name . '() called.', E_USER_WARNING);
			return;
		}
		return call_user_func_array( array($this->_CI->fileinfo, $name), $arguments);
	}

	/**
	 * Sets a file for the further object
	 * @param mixed $file ARRAY(upload_file) array of the file in format of $_FILE | STRING(file-location)
	 */
	public function set_file($file)
	{
		if(is_array($file) && isset($file['name']))
		{
			$this->_file = $file;
			$this->_file['path'] = $file['tmp_name'];
		} elseif(is_string($file)) {
			$this->_file['path'] = $file;
		} else {
			return false;
		}
		$this->_CI->fileinfo->filepath = $this->_file['path'];
		$this->_CI->fileinfo->init();
	}

	/**
	 * Checks if a file is a special filetype and sets the extension
	 * Checks also the File signature (also known as Magic numbers)
	 * @param string $types type-groups set in the config; Example: image will be .gif, .png, .jpg, .jpeg etc.
	 * @return array matching filetypes
	 */
	public function is_file_type($types)
	{
		if(is_string($types) && isset($this->_config['filetypes'][$types]))
		{
			$types = $this->_config['filetypes'][$types];
		}
		if($result = $this->_CI->fileinfo->is_file_type($types)) {
			$this->_fs_extension = $result[0];
		}
		return $result;
	}

	/**
	 * Sets the ownerdir to the default user-dir if $set is true, Returns the owner-dir-location
	 * @param bool $set TRUE: (Re-)Set the dir-location
	 * @return string Dir-location
	 */
	public function ownerdir($set = false) {
		if($this->_ownerdir == NULL || $set) {
			if($this->owner) {
				$this->_ownerdir = $this->_config['dir']['user'];
				$this->_parse_dir($this->_ownerdir, false);
				$this->_ownerdir = FCPATH . $this->_ownerdir . $this->owner->unique_id . '/';
			} else {
				$this->_ownerdir = false;
			}
		}
		return $this->_ownerdir;
	}

	/**
	 * Moves file to a dir, by default to the users dir
	 * @param mixed $dir STRING(dir) Dir on the system; not relative to anything | BOOL(FALSE) will use ownerdir
	 * @param mixed $name STRING(name) the name the file should get | BOOL(FALSE) the name it already has
	 * @param bool $use_fs_extension TRUE: use file-signature-extension FALSE: use default extension
	 * @return bool Returns false if dir does not exist
	 */
	public function move_file($dir = false, $name = false, $use_fs_extension = true)
	{
		if($dir == false) {
			$dir = $this->ownerdir();
		} else {
			$this->_parse_dir($dir, false);
		}
		if(is_dir($dir))
		{
			if($name != false) $name = pathinfo($name);
			elseif(isset($this->_file['name'])) $name = pathinfo($this->_file['name']);
			else $name = pathinfo($this->_file['path']);
			if($use_fs_extension == true) $name['extension'] = $this->_fs_extension;
			$name = $name['filename'] . '.' . $name['extension'];
			$file_location = $dir . $name;
			$this->_final_name = $name;
			move_uploaded_file($this->_file['path'], $file_location);
			return true;
		}
		return false;
	}

	/**
	 * Returns $this->_final_name;
	 * @return string $this->_final_name
	 */
	public function filename() {
		return $this->_final_name;
	}

	public function insert_file_into_database()
	{
		
	}

	/**
	 * sets user (needed if is not owner)
	 * @param int $userid userid
	 */
	public function set_user($userid = NULL)
	{
		$this->user = $this->_CI->auth->user($userid);
	}

	/**
	 * sets owner
	 * @param int $userid userid
	 */
	public function set_owner($userid = NULL)
	{
		$this->owner = $this->_CI->auth->user($userid);
		$this->ownerdir(true);
	}

	/**
	 * returns how many files the user may still upload
	 * @return int file-number the user may still upload
	 */
	public function files_left()
	{
		if($this->owner->file_limit < 1) $filesleft = $this->_config['default_file_limit'] - $this->get_content_info()['filenumber'];
		else $filesleft = $this->owner->file_limit - $this->get_content_info()['filenumber'];
		if($filesleft > 0) return $filesleft;
		return 0;
	}

	/**
	 * returns how many folders the user may still create
	 * @return int folder-number the user may still create
	 */
	public function folders_left()
	{
		if($this->owner->folder_limit < 1) $foldersleft = $this->_config['default_folder_limit'] - $this->get_content_info()['dirnumber'];
		else $foldersleft = $this->owner->folder_limit - $this->get_content_info()['dirnumber'];
		if($foldersleft > 0) return $foldersleft;
		return 0;
	}

	/**
	 * Gets info of a dir
	 * @param mixed $dir STRING(dir) location of the dir ELSE owner-dir
	 * @return array dir-info
	 */
	public function get_content_info($dir = NULL)
	{
		$this->_dir($dir);
		if(!isset($this->_dirinfo[$dir])) {
			$this->get_dir_content($dir);
		}
		return $this->_dirinfo[$dir]['info'];
	}

	/**
	 * Gets dir content (recursive)
	 * @param MIXED $dir STRING(dir) location of the dir ELSE owner-dir
	 * @param array &$contentinfo number of files and dirs inside a dir
	 * @param bool $optimizeJSON TRUE: creates empty stdClass on dirs so the dirs are objects in JSON, not arrays
	 */
	public function get_dir_content($dir = NULL, &$contentinfo = array(), $optimizeJSON = false)
	{
		$this->_dir($dir);
		$this->_parse_dir($dir, FALSE);
		if(isset($this->_dirinfo[$dir]))
		{
			$contentinfo = $this->_dirinfo[$dir]['info'];
			return $this->_dirinfo[$dir]['content'];
		}
		if(!isset($contentinfo) || !is_array($contentinfo)) $contentinfo = array();
		if(!isset($contentinfo['dirnumber']) || !is_int($contentinfo['dirnumber'])) $contentinfo['dirnumber'] = 0;
		if(!isset($contentinfo['filenumber']) || !is_int($contentinfo['filenumber'])) $contentinfo['filenumber'] = 0;
		if(!is_dir($dir)) return false;
		$content = scandir($dir);
		$return = array("dirs" => array(), "files" => array());
		foreach($content as $v)
		{
			if($v == '.' || $v == '..') continue;
			if(is_dir($dir . $v)) {
				$contentinfo['dirnumber']++;
				$return['dirs'][$v] = $this->get_dir_content($dir . $v, $contentinfo);
				if(count($return['dirs'][$v]) == 0 && $optimizeJSON == true) $return['dirs'][$v] = new stdClass;
			} else {
				$contentinfo['filenumber']++;
				$return['files'][$v] = filesize($dir . $v);
			}
		}
		$content = array_merge($return['dirs'], $return['files']); // This way dirs will always be displayed first, at top.
		$this->_dirinfo[$dir] = array('content' => $content, 'info' => $contentinfo);
		return $content;
	}

	/**
	 * Sets the owner dir in this class
	 * @return BOOL(FALSE) if owner is not set
	 */
	public function set_to_owner_dir()
	{
		if(isset($this->owner->unique_id))
		{
			$this->set_dir($this->_config['dir']['user'] . $this->owner->unique_id);
		} else {
			return false;
		}
	}

	/**
	 * Sets dir info inside this class
	 * @param string $dir location of the dir
	 */
	public function set_dir($dir)
	{
		$this->_parse_dir($dir);

		$this->_dir['filecontroller'] = $this->_config['location']['internal'] . $dir;
		$this->_dir['base']	= $this->_config['location']['external'] . $dir;

		$this->_dir['internal'] = $this->_config['location']['internal'] . $dir;
		$this->_dir['external'] = $this->_config['location']['external'] . $dir;
		$this->_dir['relative'] = $dir;
	}

	/**
	 * Gets dir via type; 
	 * Filecontroller will be internal; 
	 * Base will be link-format (external); 
	 * relative will be relative so FCPATH or site_url() needs to be added
	 * @param mixed $type STRING(type) the type which should be loaded ELSE will return relative
	 * @return string dir-path
	 */
	public function get_dir($type = NULL)
	{
		switch (strtolower($type))
		{
			case 'fc':
			case 'filecontroller':
			case 'internal':
			return $this->_dir['internal'];
			case 'base':
			case 'base_url':
			case 'external':
			return $this->_dir['external'];
			default:
			return $this->_dir['relative'];
		}
	}

	/**
	 * Checks if $name is a dir name is valid
	 * @param string $name dir-name to be checked 
	 * @return bool
	 */
	public function is_valid_dir_name($name)
	{
		if(preg_match($this->_config['dir_regex'], $name, $matches) && $matches[0] == $name){
			return true;
		}
		return false;
	}

	/**
	 * Checks if $name is a valid parent-dir name
	 * @param string $name dir-name to be checked 
	 * @return bool
	 */
	public function is_valid_parent_dir_name($name)
	{
		if(preg_match($this->_config['parent_dir_regex'], $name, $matches) && $matches[0] == $name){
			return true;
		}
		return false;
	}

	/**
	 * Checks if $name is a valid file-name
	 * @param string $name file-name to be checked 
	 * @return bool
	 */
	public function is_valid_file_name($name)
	{
		if(preg_match($this->_config['dir_regex'], $name, $matches) && $matches[0] == $name){
			return true;
		}
		return false;
	}

	/**
	 * Creates a sub-directory relative to the owner-dir if $name is valid
	 * @param string $name Name of the new dirs
	 * @param string $parent String of the dirs in which the new dir should be
	 * @return array $return['success'] will dertermine if it was successful
	 */
	public function create_sub_dir($name, $parent = false)
	{
		$this->_parse_dir($parent, false, true);
		if(!$this->is_valid_parent_dir_name($parent)) return false;
		if($this->folders_left() != false)
		{
			if($this->is_valid_dir_name($name))
			{
				$name = $parent . $name;
				if(is_dir($this->get_dir('internal') . $name))
				{
					return array("success" => false, "message" => "A folder with this already exists.");
				}
				if(file_exists($this->get_dir('internal') . $name))
				{
					return array("success" => false, "message" => "A file with this name already exists, delete the file first to create a folder with this name.");
				}
				mkdir($this->get_dir('internal') . $name);
				return array("success" => true);
			} else {
				return array("success" => false, "message" => array($this->_config['dir_regex_fail']));
			}
		} else {
			return array("success" => false, "message" => "You have too much folders to create a new one. Please delete a folder first or ask an admin.");
		}
	}

	/**
	 * Sets this->_dir variables
	 * @param string &$dir The dir wich should be parsed and set
	 */
	private function _dir(&$dir = NULL)
	{
		if(!$this->_dir['internal'] || !$this->_dir['external'] || !$this->_dir['relative'])
		{
			if(isset($this->owner->unique_id)) {
				$this->set_to_owner_dir();
			} else {
				trigger_error("No file-direcotry & no owner defined.", E_USER_ERROR);
				exit();
			}
		}
		if(!is_string($dir)) $dir = $this->_dir['internal'];
	}

	/**
	 * Parses a dir for valid beginning and ending-slashes
	 * @param string &$dir The dir wich should be parsed
	 * @param bool $beginning Wheater the beginning should be parsed or not
	 * @param bool $ending Wheater the ending should be parsed or not
	 */
	private function _parse_dir(&$dir, $beginning = true, $ending = true)
	{
		if(strlen($dir)>0)
		{
			if($beginning && $dir[0] == '/')              $dir = substr($dir, 1);
			if($ending    && $dir[strlen($dir)-1] != '/') $dir .= '/';
		}
	}


}