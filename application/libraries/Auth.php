<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth
{
	// Boolean that dertimines if the user wants to stay logged in
	private $_stay_logged_in;

	// CodeIgniter object
	private $_CI;

	// Checks if a email-verification is still required
	private $_require_email_verification = 0;

	// Disallowed username characters
	private $_username_disallowed_characters;

	// Disallowed ingame-name characters
	private $_ingame_disallowed_characters;

	// 'and' for other languages (used in error-displaying; maybe used in later versions)
	private $_language_and = 'and';

	// columns which an admin is allowed to edit
	private $_editable_user_columns = array(
		'email',
		'username',
		'ingame_name',
		'password',
		'about',
		'country_code',
		'active'
	);

	/**
	 * Magic Method __construct();
	 */
	public function __construct() {
		$this->_CI =& get_instance();
		$this->_CI->load->model('shared/users_model');
		$this->_CI->load->model('shared/users_stay_logged_in_model');
		$this->_CI->load->helper('email');
		$this->_CI->load->config('auth');
		// CodeIgniter's session-class will become a driver in CI 3
		if (substr(CI_VERSION, 0, 1) == '2') $this->_CI->load->library('session');
		else $this->_CI->load->driver('session');

		$this->_require_email_verification 		= $this->_CI->config->item('require_email_verification');
		$this->_username_disallowed_characters 	= $this->_CI->config->item('username_disallowed_characters');
		$this->_ingame_disallowed_characters 	= $this->_CI->config->item('ingame_disallowed_characters');
		$this->_username_regex 					= $this->_CI->config->item('username_regex');
		$this->_ingame_regex 					= $this->_CI->config->item('ingame_name_regex');
		$this->_stay_logged_in 					= $this->_CI->config->item('stay_logged_in_time');

	}

	/**
	 * Magic Method __call(); Pass-trough to the users_model
	 * @param string $method Method in the users_model
	 * @param array $arguments The arguments to pass trough
	 * @return mixed The return of $method-function
	 */
	public function __call($method, $arguments)
	{
		if (!method_exists( $this->_CI->users_model, $method) )
		{
			throw new Exception('Undefined method Auth::' . $method . '() called');
		}

		return call_user_func_array(array($this->_CI->users_model, $method), $arguments);
	}

	/**
	 * Trys to log the user in
	 * @param string $username username or email
	 * @param string $password the not-hashed password
	 * @param bool $stay_logged_in TRUE: Sets user cookie and adds entry so the user stays logged in
	 * @return mixed BOOL(FALSE) if login fails | OBJECT(user) if login succeeds
	 */
	public function login($username, $password, $stay_logged_in = false) {
		if(strpos($username, "@") !== false) {
			// checks email, not username
			if(isint($id = $this->_CI->users_model->check_password($username, $password, true, true))  && $id !== 0) {
				return $this->_login($id, $stay_logged_in);
			}
		}
		if(isint($id = $this->_CI->users_model->check_password($username, $password)) && $id !== 0)
		{
			return $this->_login($id, $stay_logged_in);
		}
		return false;
	}

	/**
	 * Trys to register a user
	 * @param string $email user-email
	 * @param string $username user-name
	 * @param string $password user-password
	 * @param string $password_verification password-verification
	 * @param bool $captcha FALSE: Captcha is wrong -> used for errors 
	 * @return mixed BOOL(TRUE) on success | ARRAY(errors) error-string on fail
	 */
	public function register_user($email, $username, $password, $password_verification = false, $captcha = NULL) {
		
		$error = $this->_validate_data(array(
			'email' => $email,
			'username' => $username,
			'password' => $password,
			'password_verification' => $password_verification,
			'captcha' => $captcha
		))['messages'];



		if(count($error) > 0) return $error;
		
		$ingame_name = $this->_validate_data(array('ingame_name' => $username), false, array('ingame_name'));
		if($ingame_name['count'] == 0) $ingame_name = $username;
		else $ingame_name = NULL;

		$this->_CI->users_model->create($email, $username, $ingame_name, $password, ip(), NULL, !$this->_require_email_verification);
		$newuser = $this->_CI->users_model->user_by_username($username);
		if($this->_require_email_verification == true)
		{
			$this->_CI->load->model('shared/user_activation_model');
			$vcode = '';
			$chars = 'abcdefghijklmnopqrstuvwxyz';
			$chars_caps = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$numbers = '0123456789';
			$i = 0;
			while($i < 128) {
				$cs = substr(str_shuffle($chars), 0, 10);
				$csc = substr(str_shuffle($chars_caps), 0, 10);
				$cn = str_shuffle($numbers);
				$vcode .= substr(str_shuffle($cs.$csc.$cn), 0, 1);
				$i++;
			}

			$this->_CI->user_activation_model->insert($newuser->id, $vcode);

			$this->send_registration_email($email, $username, $vcode);
		}
		return true;
	}

	/**
	 * Sends a registration-mail
	 * @param string $email E-Mail of the receiver
	 * @param string $username Username
	 * @param string $activation_code The activation-code
	 */
	public function send_registration_email($email, $username, $activation_code) {
		$this->_CI->load->library('template');
		$this->_CI->load->library('email');

		$config = array(
			'mailtype' => 'html',
			'wordwrap' => false
		);
		$this->_CI->email->initialize($config);
		$this->_CI->email->from('no-reply@' . mail_host());
		$this->_CI->email->to($email);
		$this->_CI->email->subject('Account Verification');

		$this->_CI->email->message($this->_CI->template->generate_email(array(
			array('tag' => 'h1', 'content' => 'Account Verification'),
			array('tag' => 'p', 'content' => 'Welcome ' . htmlentities($username) . ','),
			array('tag' => 'p', 'content' => 'You have been successfully registered! To activate your account please click on the verification-link below: <br />' . "\r\n" . site_url('user/activate/' . urlencode($username) . '/' . $activation_code))
			)));
		$this->_CI->email->send();
	}

	/**
	 * Gets a user-object if exists
	 * @param int $id userid; if not int current viewing user will be returned
	 * @return mixed BOOL(FALSE) if user does not exists or user is not logged in and $id was not int; | OBJECT(user-object)
	 */
	public function user($id = false) {
		if(isint($id)) {
			return $this->_CI->users_model->user($id);
		} else {
			if(!$this->is_logged_in()) return false;
			$userid = $this->_CI->session->userdata('userid');
			if(isint($userid) && $this->_CI->session->userdata('user_unique_id')) {
				$user = $this->user($userid);
				if($user->unique_id == $this->_CI->session->userdata('user_unique_id')) {
					return $user;
				}
			}
		}
		return false;
	}

	/**
	 * Updates the user if the validation is correct
	 * Only invalid data will not be updated. So if one is valid and another not only the invalid will not be updated.
	 * @param array $data data-array which should be updated
	 * @param int $id userid
	 * @param array $validate_array array of what should be validated
	 * @return mixed ARRAY(errors) if error are  occuring | BOOL(TRUE)
	 */
	public function update_user($data, $id = false, $validate_array = array('email', 'username', 'password', 'captcha')) {
		$update_data = array();
		$validate = array();
		if(!isint($id)) {
			$id = $this->user()->id;
		}
		foreach($data as $i => $d) $validate[] = $i; 
		$errors = $this->_validate_data($data, $id, $validate_array);
		if($errors['count'] != 0) return $errors;
		foreach($this->_editable_user_columns as $c) {
			if(isset($data[$c])) $update_data[$c] = $data[$c];
		}
		$this->_CI->users_model->update_user($id, $update_data);
		return true;
	}

	/**
	 * Checks if the user is logged in.
	 * @return bool
	 */
	public function is_logged_in() {
		if(intis($this->user_session())) return true;
		$uid = $this->_CI->input->cookie('user_id');
		$ucd = $this->_CI->input->cookie('user_code');
		if($this->_CI->users_stay_logged_in_model->check($uid, $ucd, true)) {
			$this->set_session($uid);
			return true;
		}
		return false;
	}

	/**
	 * Checks if the user is logged in.
	 * @return bool
	 */
	public function logged_in() {
		return $this->is_logged_in();
	}

	/**
	 * Checks if a user session exists.
	 * @return mixed INT(userid) if exists | BOOL(FALSE) if not
	 */
	public function user_session() {
		$userid = $this->_CI->session->userdata('userid');
		if(isint($userid) && $this->_CI->session->userdata('user_unique_id')) {
			$user = $this->user($userid);
			if($user->unique_id == $this->_CI->session->userdata('user_unique_id')) {
				return $userid;
			}
		}
		return false;
	}

	/**
	 * Logs the current user out and deletes all it cookies etc.
	 */
	public function logout() {
		$this->_CI->session->unset_userdata(array('userid', 'user_unique_id'));
		$this->_CI->session->sess_destroy();
		$this->_CI->users_stay_logged_in_model->remove($this->_CI->input->cookie('user_id'), $this->_CI->input->cookie('user_code'));
		$this->_CI->input->set_cookie(array(
			'name' 	=> 'user_id',
			'value' => false,
			'expire' => -100,
			'secure' => false
		));
		$this->_CI->input->set_cookie(array(
			'name' 	=> 'user_code',
			'value' => false,
			'expire' => -100,
			'secure' => false
		));
	}

	/**
	 * Trys to activate a user
	 * @param string $username username of the user which should be activated
	 * @param string $activationcode The activation code needed to activate the user
	 * @return bool TRUE: User already activated or is now activated | FALSE: user is not activated and activationcode is wrong OR user does not exist
	 */
	public function activate($username, $activationcode)
	{
		$this->_CI->load->model('shared/user_activation_model');
		$u = $this->_CI->users_model->user_by_username($username);
		if($u->active == true) return true;
		if($this->_CI->user_activation_model->is_valid($u->id, $activationcode, true) == true)
		{
			$this->_CI->users_model->update_user($u->id, array('active' => true));
			return true;
		}
		return false;
	}

	/**
	 * Sets a session for the user
	 * @param int $uid userid
	 * @return bool FALSE: user does not exist | TRUE: session is set
	 */
	public function set_session($uid) {
		if(!isint($uid)) return false;
		$user = $this->user($uid);
		if(!$user) return false;
		$newdata = array('userid' => $uid, 'user_unique_id' => $user->unique_id);
		$this->_CI->session->set_userdata($newdata);
		return true;
	}

	/**
	 * Returns not matching characters in a regex.
	 * @param string $regex The regular expression.
	 * @param string $text The string to be checked
	 * @return array Array of each non-matching character
	 */
	private function _return_non_matching_characters($regex, $text) {
		$array = array();
		$text_length = strlen($text);
		for($i = 0; $i < $text_length; $i++) {
			$new_text = str_pad(str_pad($text[$i], $i, 'a'), $text_length, 'a');
			preg_match($regex, $new_text, $new_matches);
			if($new_matches[0] != $new_text && !in_array($text[$i], $array)) {
				$array[] = $text[$i];
			}
		}
		return $array;
	}

	/**
	 * Logs a user in (does not check password)
	 * @param int $uid user-id
	 * @param bool $stay_logged_in TRUE: user should stay logged in
	 * @param int $expiration When should the stay_logged_in-cookie expire?
	 */
	private function _login($uid, $stay_logged_in = false, $expiration = false) {
		if(!isint($expiration)) $expiration = $this->_stay_logged_in;
		if(isint($uid)) {
			$user = $this->user($uid);
			$this->set_session($uid);
		}

		if($stay_logged_in) {
			$code = $this->_CI->users_stay_logged_in_model->insert($uid);
			$id_cookie = array(
				'name' 	=> 'user_id',
				'value' => $uid,
				'expire' => $expiration,
				'secure' => true
			);
			$code_cookie = array(
				'name' => 'user_code',
				'value' => $code,
				'expire' => $expiration,
				'secure' => true
			);
			if(ENVIRONMENT != 'production')
			{
				$id_cookie['secure'] = false;
				$code_cookie['secure'] = false;
			}
			$this->_CI->input->set_cookie($id_cookie);
			$this->_CI->input->set_cookie($code_cookie);
		}

		return true;
	}

	/**
	 * Validated an data-array
	 * @param array $d data-array
	 * @param int $id user-id
	 * @param array $validate_array the data which should be validated
	 * @return array array of errors: $return['count'] will be 0 if no errors occur
	 */
	public function _validate_data($d, $id = false, $validate_array = array('email', 'username', 'password', 'captcha')) {
		if(isint($id)) $user = $this->user($id);
		else $user = false;

		$error_messages = $this->_CI->config->item('error_messages');
		$error = array('messages' => array(), 'count' => 0, 'fields' => array());

		// Check for E-Mail
		if(in_array('email', $validate_array)) {
			$error['fields']['email'] = false;
			if(!((bool) filter_var($d['email'], FILTER_VALIDATE_EMAIL))) 
			{
				$error['messages']['invalid_email'] = $error_messages['invalid_email'];
				$error['fields']['email'] = true;
			}
			if(($user == false || $user->email != $d['email']) && count($this->_CI->users_model->user_by_email($d['email'])) > 0) 
			{
				$error['messages']['email_exists'] = sprintf($error_messages['email_exists'], htmlentities($d['email']));
				$error['fields']['email'] = true;
			}

		}

		// Check for username
		if(in_array('username', $validate_array)) {
			if($user == false || $user->username != $d['username'])
				$error['fields']['username'] = !$this->_validate_name($d['username'], false, $error['messages']);
		}

		// Check for ingame-name
		if(in_array('ingame_name', $validate_array)) {
			if($user == false || $user->ingame_name != $d['ingame_name'])
				$error['fields']['ingame_name'] = !$this->_validate_name($d['ingame_name'], true, $error['messages']);
		}

		// Check for password
		if(in_array('password', $validate_array)) {
			$error['fields']['password'] = false;
			if(strlen($d['password']) < $this->_CI->config->item('password_min_length')) {
				$error['messages']['password_too_short'] = sprintf($error_messages['password_too_short'], $this->_CI->config->item('password_min_length'));
				$error['fields']['password'] = true;
			}
			if($d['password_verification'] !== false && $d['password_verification'] != $d['password']) {
				$error['messages']['passwords_do_not_match'] = $error_messages['passwords_do_not_match'];
				$error['fields']['password'] = true;
			}
		}

		// Check for captcha
		if(in_array('captcha', $validate_array)) {
			if($d['captcha'] != true) {
				$error['messages']['wrong_captcha'] = $error_messages['wrong_captcha'];
				$error['fields']['captcha'] = true;
			}
		}

		$error['count'] = count($error['messages']);
		return $error;
	}

	/**
	 * Checks if a name is valid and adds error messages to $error
	 * @param string $name The name to be checked
	 * @param bool $ingame_name TRUE: $name should be validated as ingame_name, not as user_name
	 * @param array &$error_array adds error messages if errors occur.
	 * @return bool
	 */
	private function _validate_name($name, $ingame_name = false, &$error_array = array()) {
		$error_messages = $this->_CI->config->item('error_messages');
		$hit_disallowed_characters = array();
		
		if($ingame_name) { 
			$dis_c_array = $this->_ingame_disallowed_characters;
			$dis_c_regex = $this->_ingame_regex;
			$maxlength = $this->_CI->users_model->max_ingame_name_length();
			$ep = 'ingame_';
		} else {
			$dis_c_array = $this->_username_disallowed_characters;
			$dis_c_regex = $this->_username_regex;
			$maxlength = $this->_CI->users_model->max_username_length();
			$ep = 'username_';
		}

		if($this->_CI->users_model->user_exists($name) && $ingame_name == false) 
			$error[$ep . 'exists'] = sprintf($error_messages[$ep . 'exists'], htmlentities($name));
		elseif($this->_CI->users_model->ingame_name_exists($name) && $ingame_name == true)
			$error[$ep . 'exists'] = sprintf($error_messages[$ep . 'exists'], htmlentities($name));

		for($i = 0; $i < strlen($name); $i++) {
			if(in_array($name[$i], $dis_c_array)) {
				$hit_disallowed_characters[] = $name[$i];
			}
		}
		
		$dis_chars_count = count($hit_disallowed_characters);
		if($dis_chars_count == 1) {
			$error[$ep.'disallowed_char'] = sprintf($error_messages[$ep.'disallowed_char'], '\'' . $hit_disallowed_characters[0] . '\'');
		} elseif($dis_chars_count > 1) {
			$last_dis_char = $hit_disallowed_characters[$dis_chars_count-1];
			unset($hit_disallowed_characters[$dis_chars_count-1]);
			$replace = '\'' . implode('\', \'', $hit_disallowed_characters) . '\'';
			$replace .= ' ' . $this->_language_and . ' \'' . $last_dis_char . '\'';
			$error[$ep.'disallowed_chars'] = sprintf($error_messages[$ep.'disallowed_chars'], $replace);
		}

		$match = preg_match($dis_c_regex, $name, $matches);
		if(!$match || $matches[0] != $name) {
			$hit_disallowed_characters = $this->_return_non_matching_characters($dis_c_regex, $name);
			$dis_chars_count = count($hit_disallowed_characters);
			if($dis_chars_count == 1) {
				$error[$ep.'regex_disallowed_char'] = sprintf($error_messages[$ep.'regex_disallowed_char'], '\'' . $hit_disallowed_characters[0] . '\'');
			} elseif($dis_chars_count > 1) {
				$last_dis_char = $hit_disallowed_characters[$dis_chars_count-1];
				unset($hit_disallowed_characters[$dis_chars_count-1]);
				$replace = '\'' . implode('\', \'', $hit_disallowed_characters) . '\'';
				$replace .= ' ' . $this->_language_and . ' \'' . $last_dis_char . '\'';
				$error[$ep.'regex_disallowed_chars'] = sprintf($error_messages[$ep.'regex_disallowed_chars'], $replace);
			}
		}

		if(strlen($name) < $this->_CI->config->item($ep.'min_length')) {
			$error[$ep.'too_short'] = sprintf($error_messages[$ep.'too_short'], $this->_CI->config->item('username_min_length'), $maxlength);
		}
		if(strlen($name) > $maxlength) {
			$error[$ep.'too_long'] = sprintf($error_messages[$ep.'too_long'], $maxlength, $this->_CI->config->item('username_min_length'));
		}
		if(count($error) == 0) return true;
		array_merge($error, $error_array);
		return false;
	}
}


