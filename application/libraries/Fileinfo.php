<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fileinfo {

	// Array of magic numbers for different filetypes
	public $magicnumbers;

	// Path to the file
	public $filepath;

	// Dir to the file
	public $dir;

	// Base name of the file
	public $basename;

	// Extension of the file
	public $extension;

	// Name of the file
	public $filename;

	// The codeigniter object
	private $_CI;

	// Maxlength of the file to read 
	// -> so not everything of a big file needs to be loaded;
	// -> only until the magic number ends
	private $_maxlength;

	// Content of the file (as HEX); only until $this->_maxlength is reached
	private $_filecontent;

	// Filesize in bytes
	private $_filesize;

	// FALSE: File was not initialized
	private $_initalized = false;

	/**
	 * Magic Method __construct();
	 */
	public function __construct()
	{
		$this->_CI =& get_instance();
		$this->_CI->load->config('magicnumbers');
		$this->magicnumbers = $this->_CI->config->item('magicnumbers');
	}

	/**
	 * Initialzes the file with $this->filepath;
	 */
	public function init()
	{
		if(!is_string($this->filepath)) throw new Exception("No file set.");
		$this->_initalized = $this->filepath;
		$this->_filesize = filesize($this->filepath);
		$maxlength = $this->getLongestMagicNumber();
		$this->_maxlength = ($maxlength > $this->_filesize) ? $this->_filesize : $maxlength ;
		$handle = fopen($this->filepath, "r");
		$contents = fread($handle, $this->_maxlength);
		fclose($handle);
		$file = '';
		for($i = 0; $i < strlen($contents); $i++) {
			$f = dechex(ord($contents[$i]));
			if(strlen($f) == 1) $file .= 0;
			$file .= dechex(ord($contents[$i]));
		}
		$file = strtoupper($file);
		$this->_filecontent = $file;
		$pathinfo = pathinfo($this->filepath);
		$this->extension = $pathinfo['extension'];
		$this->basename = $pathinfo['basename'];
		$this->dir = $pathinfo['dirname'];
		$this->name = $pathinfo['filename'];
	}

	/**
	 * Gets real filetype of the file
	 * @param array $magicnumbers the array to check the filetype for. If not array it will check for all available in the config
	 */
	public function get_real_file_type($magicnumbers = false) {
		$this->checkInit();
		$results = array();
		if(!is_array($magicnumbers)) $magicnumbers = $this->magicnumbers;
		foreach($magicnumbers as $k => $v) {
			unset($magicnumber);
			$offset = 0;
			if(is_array($v)) {
				foreach($v as $kk => $vv) {
					if(is_int($kk)) {
						$magicnumber = $vv;
					} else {
						$magicnumber = $kk;
						$offset = $vv * 2; // $vv is in Bytes, but $offset is needed for 4 bit blocks
					}
				}
			} else {
				$magicnumber = $v;
			}
			for($i = 0; $i < strlen($magicnumber); $i++) {
				if($this->_filecontent[$i+$offset] != $magicnumber[$i] && ($magicnumber[$i] != 'n' || !is_numeric($this->_filecontent[$i+$offset])) && $magicnumber[$i] != 'x') break; // So if magicnumber does not match with content AND magicnumber is not n while content is numeric AND magicnumber is not x: break
				if(!isset($magicnumber[$i+1])) $results[] = $k;
			}
		}
		return $results;
	}

	/**
	 * Checks if a file is a filetype
	 * @param array $types allowed types
	 * @return array types matching the file or BOOL(FALSE)
	 */
	public function is_file_type($types) {
		$this->checkInit();
		if(is_string($types)) $types = (array) $types;
		$magicnumbers = array();
		foreach($types as $t) {
			if(isset($this->magicnumbers[$t])) {
				$magicnumbers[$t] = $this->magicnumbers[$t];
			}
		}
		$result = $this->get_real_file_type($magicnumbers);
		if(count($result)==0) return false;
		return $result;
	}

	/**
	 * Checks if a type is set in the config
	 * @param string $type the file type
	 * @return bool
	 */
	public function check_type_available($type) {
		$this->checkInit();
		if(isset($this->magicnumbers[$type])) return true;
		return false;
	}

	/**
	 * Checks if a type matches its extension
	 * @return array types matching the file or BOOL(FALSE)
	 */
	public function type_matches_extension() {
		$this->checkInit();
		if($this->check_type_available($this->extension) == false) {
			trigger_error('There is not a file signature set for the filetype: ' . $this->extension, E_USER_NOTICE);
		}
		return $this->is_file_type($this->extension);
	}

	/**
	 * Returns filesize of file
	 * @return int filesize in bytes
	 */
	public function filesize() {
		return $this->_filesize;
	}

	/**
	 * Checks if file is already initialized and initializes it if not.
	 */
	private function checkInit() {
		if($this->_initalized == false || $this->_initalized != $this->filepath) $this->init();
	}

	/**
	 * Returns the longest matching magic number (in bytes)
	 * @param bool $including_offste Include offset to the length or not
	 * @return int magic-number in bytes
	 */
	private function getLongestMagicNumber($including_offset = true) {
		$longest = 0;
		foreach($this->magicnumbers as $k => $v) {
			if(is_array($v)) {
				foreach($v as $kk => $vv) {
					if(is_int($kk)) {
						if($longest < strlen($vv)) {
							$longest = strlen($vv) / 2;
							continue;
						}
					} else {
						if($including_offset == true) {
							if($longest < strlen($kk) + $vv) {
								$longest = strlen($kk) / 2 + $vv;
								continue;
							}
						} else {
							if($longest < strlen($kk)) {
								$longest = strlen($kk) / 2;
								continue;
							}
						}
					}
				}
			} else {
				if($longest < strlen($v)) {
					$longest = strlen($v);
				}
			}
		}
		return $longest;
	}
}