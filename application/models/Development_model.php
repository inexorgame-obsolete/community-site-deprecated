<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Development_model extends MY_Model {
	/**
	 * Magic Method __construct()
	 */
	public function __construct() {
		parent::__construct();
		$this->load_config('development');
	}

	/**
	 * Gets data for all users 
	 * @return array containing objects
	 */
	public function get()
	{
		$return = array(
			'main'    => array(), 
			'data'    => array(), 
			'website' => array()
		);
		$data = $this->get_all();
		foreach($data as $d)
		{
			if(!isset($return[$d->type][$d->user_id]))
			{
				$return[$d->type][$d->user_id] = array(
					'done'   => array(),
					'undone' => array()
				);
			}
			if($d->done == true) $group = 'done';
			else $group = 'undone';
			$return[$d->type][$d->user_id][$group][$d->id] = $d->text;
		}
		return $return;
	}

	/**
	 * Adds a new task to the database
	 * @param id $userid user-id
	 * @param string $text text of the new task
	 * @param string $type the project-type
	 */
	public function add_task($userid, $text, $type)
	{
		$data = array(
			'user_id' => $userid,
			'text' => $text,
			'done' => false,
			'type' => $type

		);
		$this->db->insert($this->Table, $data);
	}

	/**
	 * Changes the done-status
	 * @param int $id task-id
	 * @param bool $newstatus TRUE for done
	 */
	public function change_status($id, $newstatus)
	{
		$this->db->where('id', $id);
		$this->db->update($this->Table, array('done' => $newstatus));
	}

	/**
	 * Deletes a task
	 * @param int $id task-id
	 */
	public function delete_task($id)
	{
		$this->db->delete($this->Table, array('id' => $id));
	}

	/**
	 * Gets all data from the table
	 * @return object
	 */
	public function get_all() {
		return $this->db->get($this->Table)->result();
	}

}

/* End of file development_model.php */
/* Location: ./application/models/development_model.php */