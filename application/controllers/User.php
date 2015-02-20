<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	// The data submitted to the views if _render_page is used and no data is submitted
	public $viewdata;

	/**
	 * Magic Method __construct();
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->helper('template');
		$this->load->helper('language');
		$this->load->helper('captcha');
		$this->load->library('fileinfo');
		$this->load->library('auth');
		$this->load->library('permissions');
		$this->load->library('reCaptcha');
		$this->load->library('template');
		$this->template->add_css($this);
	}

	/**
	 * Index of the page containing a login-form and a register-link
	 */
	function index()
	{
		$this->form_validation->set_rules('search', 'search field', 'required');
		if ($this->form_validation->run() == true) {
			$search = $this->input->post('search');
			redirect('/user/search/' . urlencode($search));
		}
		$user = $this->auth->user();
		if($user) {
			redirect('/user/' . $user->id);
		} else {
			redirect('/user/register');
		}
	}

	/**
	 * Activates the user if $username and $verification matches
	 * @param string $username Username to be acivated
	 * @param string $verification The verification-code of the user containing 128 lower-/uppercase letters and numbers
	 */
	function activate($username = false, $verification = false)
	{
		if($username && $verification)
		if($this->auth->activate(urldecode($username), $verification))
		{
			$this->_render_page('user/activation', array('success' => true));
			return;
		}
		$this->_render_page('user/activation', array('success' => false));
	}

	/**
	 * Login page
	 * @param string $site The site to be redirected to if is set | NOT IMPLEMENTED
	 */
	function login($site = false) {
		if($user = $this->auth->user()) redirect('user/' . $user->id);
		$data = $this->_get_login_form();
		$data['errors'] = false;
		if(isset($_POST['submit'])) {
			if($this->auth->login($_POST['username_email'], $_POST['password'], isset($_POST['stay_logged_in']))) {
				redirect('/user/');
			} else {
				$data['errors'] = array('Your password does not match to your e-mail or username.');
			}
		}

		$this->_render_page('user/login', $data);
	}

	/**
	 * Logs the user out
	 * @param bool $redirected Reload the page so all parts are rendered again without logged in
	 */
	function logout($redirected = false) {
		$this->auth->logout();
		if($redirected === false) redirect('user/logout/true');
		$this->_render_page('user/logout');
	}

	/**
	 * Register a user
	 */
	function register()
	{
		$this->template->set_title("Create User");
		$this->data['errors'] = false;
		if(isset($_POST['submit'])) {
			$this->recaptcha->check_answer();
			$errors = $this->auth->register_user(
				$_POST['email'], 
				$_POST['username'], 
				$_POST['password'],
				$_POST['password_confirm'],
				$this->recaptcha->is_valid
				);
			if(count($errors) > 0 && is_array($errors)) {
				$this->data['errors'] = $errors;
			} else {
				$this->_render_page('user/register_success', $this->data);
				return;
			}
		}

		$this->data = array_merge($this->data, $this->_get_register_form());
		$this->_render_page('user/register', $this->data);
	}

	/**
	 * Search for a user
	 * @param string $string the searchstring
	 */
	function search($string = false)
	{
		$string = urldecode($string);
		
		$this->form_validation->set_rules('search', 'search field', 'required');
		if ($this->form_validation->run() == true) {
			redirect('/user/search/' . urlencode($this->input->post('search')));
		} else {
			$data['search_form']['validation_message'] = validation_errors();
		}
		$data['search_form']['search'] = array(
			'name'  => 'search',
			'type'  => 'text',
			'placeholder' => 'Search...',
			'value' => $string
		);
		$data['search_form']['submit'] = array(
			'name'  => 'submit',
			'value' => 'Search',
		);

		$data['users'] = $this->auth->users_like($string);

		$this->_render_page('user/list', $data);
	}

	/**
	 * View a user
	 * @param int $id the userid
	 */
	function view($id = NULL)
	{
		$user = $this->auth->user();
		if(!$id) redirect('/user/view/' . $user->id, 'refresh');
		if(!$user || $id != $user->id) $view = $this->auth->user($id); else $view = $user;
		if(!is_object($view))
		{
			show_404();
			return;
		}
		$unsets = array('password', 'salt', 'forgotten_password_code', 'forgotten_password_time', 'remember_code');
		$user = (array) $user;
		$view = (array) $view;
		foreach($unsets as $u) { unset($user[$u]); unset($view[$u]); }
		$data['user'] = $user;
		$data['view'] = $view;
		$userbackground = iimage($id, 'background');
		if($userbackground) $this->template->variable('eyecatcher_image', $userbackground, true);
		$this->_render_page('user/view_user', $data);
	}

	/**
	 * Edit a user
	 * @param mixed $slug Used as command-recognition; If int it will be used as userid else it will execute special functions for ajax-usage
	 * @param mixed $value Value for the command
	 * @param bool $ajax will output json on true
	 */
	function edit($slug = false, $value = false, $ajax = false) {
		$user = $this->auth->user();
		if($user == false) {
			redirect('user/register');
			return;
		}
		if($slug == 'picture') {
			if(strtolower($value) == 'profile' || strtolower($value) == 'avatar') $value = 'avatar';
			else $value = 'background';

			if($this->input->post('delete')) {
				if(file_exists(FCPATH . 'data/users/' . $value . '/' . $user->id . '.png')) unlink(FCPATH . 'data/users/' . $value . '/' . $user->id . '.png');
				if(file_exists(FCPATH . 'data/users/' . $value . '/' . $user->id . '.jpg')) unlink(FCPATH . 'data/users/' . $value . '/' . $user->id . '.jpg');
				$returnarray = array("success" => true, "action" => "delete", "type" => $value);
				if($value == 'avatar') $returnarray["path"] = base_url() . 'data/users/avatar/no-avatar.png';
				if($value == 'background') $returnarray["path"] = base_url() . 'data/images/eyecatcher.png';
			} else {
				if(isset($_FILES['picture'])) {
					$this->fileinfo->filepath = $_FILES['picture']['tmp_name'];
					if($this->fileinfo->is_file_type('jpg') == true) {
						move_uploaded_file($this->fileinfo->filepath, FCPATH . 'data/users/' . $value . '/' . $user->id . '.jpg');
						if(file_exists(FCPATH . 'data/users/' . $value . '/' . $user->id . '.png')) unlink(FCPATH . 'data/users/' . $value . '/' . $user->id . '.png');
						$returnarray = array("success" => true, "action" => "change", "path" => base_url() . 'data/users/' . $value . '/' . $user->id . '.jpg',  "type" => $value);
					} elseif($this->fileinfo->is_file_type('png') == true) {
						move_uploaded_file($this->fileinfo->filepath, FCPATH . 'data/users/' . $value . '/' . $user->id . '.png');
						if(file_exists(FCPATH . 'data/users/' . $value . '/' . $user->id . '.jpg')) unlink(FCPATH . 'data/users/' . $value . '/' . $user->id . '.jpg');
						$returnarray = array("success" => true, "action" => "change", "path" => base_url() . 'data/users/' . $value . '/' . $user->id . '.png', "type" => $value);
					} else {
						$returnarray = array("success" => false, "error" => "File signature does not fit png or jpg files.");
					}
				} else {
					$returnarray = array("success" => false, "error" => "No file selected.");
				}
			}

			if($ajax) 
			{
				$this->template->disable();
				$this->output->set_content_type('application/json')->set_output(json_encode($returnarray));
			} else {
				redirect('user/edit');
			}
		} 

		if(isint($slug)) {
			$this->permissions->set_user($user->id);
			if(!$this->permissions->has_user_permission('edit_others_profile'))
			{
				$this->template->render_permission_error();
				return;
			}
			$edit_user = $this->auth->user($slug);

			if($value == 'profile_picture') {
				if($this->input->post('delete') && $this->permissions->has_user_permission('delete_others_profile_picture')) {
					$returnarray = array("success" => true, "action" => "delete", "type" => 'profile');
					if(file_exists(FCPATH . 'data/users/avatar/' . $edit_user->id . '.png')) unlink(FCPATH . 'data/users/avatar/' . $edit_user->id . '.png');
					if(file_exists(FCPATH . 'data/users/avatar/' . $edit_user->id . '.jpg')) unlink(FCPATH . 'data/users/avatar/' . $edit_user->id . '.jpg');
					$returnarray = array("success" => true, "action" => "delete", "type" => 'avatar');
					$returnarray["path"] = base_url() . 'data/users/avatar/no-avatar.png';
				} else {
					$returnarray = array("success" => false, "error" => "You have no permissions to remove the avatar-image.");
				}

				if($ajax) {
					$this->template->disable();
					$this->output->set_content_type('application/json')->set_output(json_encode($returnarray));
				} else {
					redirect('user/edit/' . $edit_user->id);
				}
				return;
			} elseif($value == 'background_picture') {
				if($this->input->post('delete') && $this->permissions->has_user_permission('delete_others_background_picture')) {
					$returnarray = array("success" => true, "action" => "delete", "type" => 'background');
					if(file_exists(FCPATH . 'data/users/avatar/' . $edit_user->id . '.png')) unlink(FCPATH . 'data/users/avatar/' . $edit_user->id . '.png');
					if(file_exists(FCPATH . 'data/users/avatar/' . $edit_user->id . '.jpg')) unlink(FCPATH . 'data/users/avatar/' . $edit_user->id . '.jpg');
					$returnarray = array("success" => true, "action" => "delete", "type" => 'avatar');
					$returnarray["path"] = base_url() . 'data/users/images/eyecatcher.png';
				} else {
					$returnarray = array("success" => false, "error" => "You have no permissions to remove the background-image.");
				}

				if($ajax) {
					$this->template->disable();
					$this->output->set_content_type('application/json')->set_output(json_encode($returnarray));
				} else {
					redirect('user/edit/' . $edit_user->id);
				}
				return;
			}

			$permissions_array = $this->permissions->has_user_permissions(array(
				'edit_others_email',
				'edit_others_username',
				'edit_others_ingame_name',
				'edit_others_about',
				'edit_others_password',
				'delete_others_profile_picture',
				'delete_others_background_picture',
				'change_activation_status'
			), false, true);
			$this->_get_edit_others_data($data, $edit_user, $permissions_array);

			if(isset($_POST['submit'])) {
				$update_data = array();
				$validate_array = array();
				$this->_update_if_allowed($update_data, $data['edit_form']['username'], 'username', $validate_array);
				$this->_update_if_allowed($update_data, $data['edit_form']['password_verification'], 'password_verification', $validate_array);
				if(isset($update_data['password_verification']))
					$this->_update_if_allowed($update_data, $data['edit_form']['password'], 'password', $validate_array);
				$this->_update_if_allowed($update_data, $data['edit_form']['email'], 'email', $validate_array);
				$this->_update_if_allowed($update_data, $data['edit_form']['about'], 'about', $validate_array);
				$this->_update_if_allowed($update_data, $data['edit_form']['ingame_name'], 'ingame_name', $validate_array);
				$this->_update_if_allowed($update_data, $data['edit_form']['active'], 'active', $validate_array);

				$errors = $this->auth->update_user($update_data, $edit_user->id, $validate_array);

				$edit_user = $this->auth->user($edit_user->id);
				$this->_get_edit_others_data($data, $edit_user, $permissions_array);
				$data['errors'] = $errors;
			}

			$data['edit_user'] = $edit_user;

			$data['edit_user_permissions'] = false;
			if($this->permissions->has_user_permission('edit_permissions'))
			{
				if($this->permissions->has_user_permission('edit_permission_editors_permissions') || !$this->permissions->has_user_permission('edit_permissions', $edit_user->id))
				{
					$data['edit_user_permissions'] = true;
				}
			}

			$this->_render_page('user/edit_others', $data);

		

		} elseif($ajax != true) {
			$data['user'] = $user;
			$this->_get_edit_data($data, $user);

			if($this->input->post('submit'))
			{
				$this->config->load('auth');
				$error = array();
				$update_data['username']				= $this->input->post($data['edit_form']['username']['name']);
				$update_data['password']				= $this->input->post($data['edit_form']['password']['name']);
				$update_data['password_verification'] 	= $this->input->post($data['edit_form']['password_verification']['name']);
				$update_data['about']					= $this->input->post($data['edit_form']['about']['name']);
				$update_data['ingame_name']				= $this->input->post($data['edit_form']['ingame_name']['name']);

				$data['edit_form']['username']['value'] 	= $update_data['username'];
				$data['edit_form']['about']['value'] 		= $update_data['about'];

				if(strlen($update_data['password_verification']) == 0) 	unset($update_data['password']);
				if($update_data['username']    == $user->username) 		unset($update_data['username']);
				if($update_data['ingame_name'] == $user->ingame_name) 		unset($update_data['ingame_name']);

				if($this->auth->check_password_id($user->id, $this->input->post($data['edit_form']['old_password']['name']))) {
					$errors = $this->auth->update_user($update_data, false, array_keys($update_data));
					$data['form_validation'] = array('success' => TRUE);
					if($errors['count'] > 0) {
						$data['form_validation']['errors'] = TRUE;
						$data['form_validation']['messages'] = $errors['messages'];
					} else {
						$data['form_validation']['messages'] = array('Successfully updated. Reload to see full changes.');
					}
				} else {
					$data['form_validation'] = array('success' => FALSE, 'errors' => TRUE, 'messages' => array('The old password is wrong.'));
				}
			} 


			$this->template->add_js('jquery.form');
			$this->template->add_js('ajax_upload.settings', $this);
			$userbackground = iimage($user->id, 'background');
			if($userbackground) $this->template->variable('eyecatcher_image', $userbackground, true);
			$this->_render_page('user/edit', $data);
		}
	}

	/**
	 * Get form data for the register form
	 * @return array form-data
	 */
	private function _get_register_form() {
		$data['username'] = array(
			'name'  => 'username',
			'id'    => 'username',
			'type'  => 'text',
			'value' => isset($_POST['username']) ? $_POST['username'] : ''
		);
		$data['email'] = array(
			'name'  => 'email',
			'id'    => 'email',
			'type'  => 'text',
			'value' => isset($_POST['email']) ? $_POST['email'] : '',
		);
		$data['password'] = array(
			'name'  => 'password',
			'id'    => 'password',
			'type'  => 'password',
			'value' => isset($_POST['password']) ? $_POST['password'] : '',
		);
		$data['password_confirm'] = array(
			'name'  => 'password_confirm',
			'id'    => 'password_confirm',
			'type'  => 'password',
			'value' => isset($_POST['password_verification']) ? $_POST['password_verification'] : '',
		);
		$data['captcha'] = $this->template->prevent_variables($this->recaptcha->get_html());
		return $data;
	}

	/**
	 * Get form data for the login form
	 * @return array form-data
	 */
	private function _get_login_form() {
		$data['username_email'] = array(
			'name'  => 'username_email',
			'id'    => 'username_email',
			'type'  => 'text',
			'value' => isset($_POST['username_email']) ? $_POST['username_email'] : ''
		);
		$data['password'] = array(
			'name'  => 'password',
			'id'    => 'password',
			'type'  => 'password',
			'value' => isset($_POST['password']) ? $_POST['password'] : '',
		);
		$data['stay_logged_in'] = array(
			'name'  => 'stay_logged_in',
			'id'    => 'stay_logged_in',
			'type'  => 'checkbox',
		);
		if(isset($_POST['stay_logged_in'])) {
			$data['stay_logged_in']['checked'] = 'checked';
		}
		return $data;
	}

	/**
	 * Renders the page
	 * @param string $view the view to render
	 * @param array $data the data to pass to the view
	 * @param bool $render FALSE: Direct output 
	 * @return mixed NULL when $render true; string when $render false
	 */
	function _render_page($view, $data=null, $render=false)
	{
		if(!empty($data)) $this->viewdata = $data;
		$view_html = $this->load->view($view, $this->viewdata, $render);
		if (!$render) return $view_html;
	}

	/**
	 * Remaps the site
	 * @param string $method the method to be executed
	 * @param array $params the params to pass to the function
	 */
	function _remap($method, $params)
	{
		if(method_exists($this, $method))
		{
			return call_user_func_array(array($this, $method), $params);
		} 
		elseif(isint($method))
		{
			$params['id'] = $method;
			return call_user_func_array(array($this, 'view'), $params);
		}
		else
		{
			$params['string'] = $method;
			return call_user_func_array(array($this, 'search'), $params);
		}
	}

	/**
	 * Adds edit-form-data to the data array
	 * @param array &$data the data array to add the form-array to
	 * @param object $user the object of the user which should be edited
	 */
	private function _get_edit_data(&$data, $user) {
		$data['edit_form']['username'] = array(
			'type' => 'text',
			'value' => $user->username,
			'autocomplete' => 'off',
			'name' => 'username',
			'id' => 'edit_username'
		);
		$data['edit_form']['ingame_name'] = array(
			'type' => 'text',
			'value' => $user->ingame_name,
			'autocomplete' => 'off',
			'name' => 'ingame_name',
			'id' => 'edit_ingame_name'
		);
		$data['edit_form']['email'] = array(
			'type' => 'email',
			'value' => $user->email,
			'autocomplete' => 'off',
			'name' => 'email',
			'id' => 'edit_email'
		);
		$data['edit_form']['password'] = array(
			'type' => 'password',
			'autocomplete' => 'off',
			'name' => 'password',
			'id' => 'password'
		);
		$data['edit_form']['password_verification'] = array(
			'type' => 'password',
			'autocomplete' => 'off',
			'name' => 'password_verification',
			'id' => 'password_verification',
			'placeholder' => 'Verification'
		);
		$data['edit_form']['old_password'] = array(
			'type' => 'password',
			'autocomplete' => 'off',
			'name' => 'old_password',
			'id' => 'old_password'
		);
		$data['edit_form']['about'] = array(
			'value' => $user->about,
			'autocomplete' => 'off',
			'name' => 'about',
			'id' => 'edit_about',
			'class' => 'about'
			);
		$data['edit_form']['submit'] = array(
			'value' => "Update information",
			'name' => 'submit',
			'type' => 'submit'
			);
		$data['change_picture']['profile']['upload'] = array(
			'type' => 'file',
			'name' => 'picture',
			);
		$data['change_picture']['profile']['submit'] = array(
			'type' => 'submit',
			'name' => 'submit',
			'value' => 'Change profile-picture'
			);
		$data['change_picture']['profile']['delete'] = array(
			'type' => 'submit',
			'name' => 'delete',
			'value' => 'Delete profile-picture'
			);
		$data['change_picture']['background']['upload'] = array(
			'type' => 'file',
			'name' => 'picture',
			);
		$data['change_picture']['background']['submit'] = array(
			'type' => 'submit',
			'name' => 'submit',
			'value' => 'Change background-picture'
			);
		$data['change_picture']['background']['delete'] = array(
			'type' => 'submit',
			'name' => 'delete',
			'value' => 'Delete background-picture'
		);
	}

	/**
	 * Adds edit-form-data to the data array (to edit OTHER users)
	 * @param array &$data the data array to add the form-array to
	 * @param object $user the object of the user which should be edited
	 * @param array $permissions the permissions which the user do (not) have to properly disable the field.
	 */
	private function _get_edit_others_data(&$data, $user, $permissions) {
		$data['edit_form']['active'] = array(
			'type' => 'checkbox',
			'name' => 'active',
			'id' => 'activate_user',
			'value' => 'true',
		);
		if($user->active == true) $data['edit_form']['active']['checked'] = 'checked';
		if(!$permissions['change_activation_status']) $this->_add_disabled($data['edit_form']['active']);

		$data['edit_form']['email'] = array(
			'type' => 'text',
			'value' => $user->email,
			'autocomplete' => 'off',
			'name' => 'email',
			'id' => 'edit_email'
		);
		if(!$permissions['edit_others_email']) $this->_add_disabled($data['edit_form']['email']);

		$data['edit_form']['username'] = array(
			'type' => 'text',
			'value' => $user->username,
			'autocomplete' => 'off',
			'name' => 'username',
			'id' => 'edit_username'
		);
		if(!$permissions['edit_others_username']) $this->_add_disabled($data['edit_form']['username']);

		$data['edit_form']['ingame_name'] = array(
			'type' => 'text',
			'value' => $user->ingame_name,
			'autocomplete' => 'off',
			'name' => 'ingame_name',
			'id' => 'edit_ingame_name'
		);
		if(!$permissions['edit_others_ingame_name']) $this->_add_disabled($data['edit_form']['ingame_name']);

		$data['edit_form']['password'] = array(
			'type' => 'password',
			'autocomplete' => 'off',
			'name' => 'password',
			'id' => 'password'
		);
		$data['edit_form']['password_verification'] = array(
			'type' => 'password',
			'autocomplete' => 'off',
			'name' => 'password_verification',
			'id' => 'password_verification',
			'placeholder' => 'Verification'
		);
		if(!$permissions['edit_others_password']) { 
			$this->_add_disabled($data['edit_form']['password']); 
			$this->_add_disabled($data['edit_form']['password_verification']); 
		}

		$data['edit_form']['about'] = array(
			'value' => $user->about,
			'autocomplete' => 'off',
			'name' => 'about',
			'id' => 'edit_about',
			'class' => 'about'
		);
		if(!$permissions['edit_others_about']) { $this->_add_disabled($data['edit_form']['about']); }

		$data['edit_form']['submit'] = array(
			'value' => "Update information",
			'name' => 'submit',
			'type' => 'submit'
		);

		$data['change_picture']['profile']['delete'] = array(
			'type' => 'submit',
			'name' => 'delete',
			'value' => 'Delete profile-picture'
		);
		if(!$permissions['delete_others_profile_picture']) { $this->_add_disabled($data['change_picture']['profile']['delete']); }

		$data['change_picture']['background']['delete'] = array(
			'type' => 'submit',
			'name' => 'delete',
			'value' => 'Delete background-picture'
		);
		if(!$permissions['delete_others_background_picture']) { $this->_add_disabled($data['change_picture']['background']['delete']); }
	}

	/**
	 * Adds disabled to an input and adds a title that the user does not have the permissions
	 * @param array &$data The input-array
	 */
	private function _add_disabled(&$data) {
		$data['disabled'] = 'disabled';
		$data['title'] = 'You are not allowed to edit this field.';
	}

	/**
	 * Checks if a user has the permission to update a submitted value
	 * @param array &$update the update array
	 * @param array $input the input of the submitted update-data-form
	 * @param string $index The index which should be set if the user has the permissions
	 * @param array &$validate_array An array which will return the valid data indices
	 */
	private function _update_if_allowed(&$update, $input, $index, &$validate_array = array()) {
		if(strlen($this->input->post($input['name'])) == 0 && $input['type'] != 'checkbox') return false;
		if(!isset($input['disabled'])) { 
			if(isset($input['type']) && $input['type'] == 'checkbox') $update[$index] = $this->input->post($input['name']) ? 1 : 0;
			else $update[$index] = $this->input->post($input['name']);
			$validate_array[] = $index;
			return true; 
		}
		return false;
	}
}
