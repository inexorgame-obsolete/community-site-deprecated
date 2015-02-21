<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Irc_model extends MY_Model {

	// Main-irc-messages table
	private $_table = 'irc';

	// IRC-user-connections table
	private $_uc_table = 'irc_user_connections';

	// IRC-rename-table
	private $_ur_table = 'irc_user_renamings';


	/**
	 * Magic Method __construct();
	 */
	public function __construct() {

		parent::__construct();
		$this->load_config();
	}

	/**
	 * Gets users next to a specific date
	 * @param string $date datesting; The nearest possible date
	 * @return object user-connections-object
	 */
	public function get_users($date)
	{
		$this->db->order_by('timestamp', 'DESC');
		$this->db->where('timestamp <=', $date);
		$this->db->limit(1, 0);
		return $this->db->get($this->_uc_table)->row();
	}

	/**
	 * Gets messages from all 3 tables and returns in the following order
	 * |-------------------------------|--------------------------------------------------------------------------------------------------|
	 * | names for columns             | id | nickname      | text            | type    | receiver | reason | newnick | timestamp | mtype |
	 * |-------------------------------|--------------------------------------------------------------------------------------------------|
	 * | originals from:  - irc-table  | id | nickname      | text            | type    | receiver | NULL   | NULL    | timestamp | mtype |
	 * |-----------------              |--------------------------------------------------------------------------------------------------|
	 *   ur = user-     | - ur-table   | id | affected_user | connected_users | matters | NULL     | reason | NULL    | timestamp | mtype |
	 *        renaming  |              |--------------------------------------------------------------------------------------------------|
	 *   uc = user-     | - uc-table   | id | oldnick       | NULL            | NULL    | NULL     | NULL   | newnick | timestamp | mtype |
	 *      connections |--------------|--------------------------------------------------------------------------------------------------|
	 * @param int $offset offset
	 * @param int $limit results-per-query
	 * @return array containing table-objects
	 */
	public function get($offset = 0, $limit = 100) {
		isint($offset, 0);
		isint($limit, 100);
		$query = 
'SELECT id, nickname, `text`, type, receiver, NULL AS `reason`, NULL AS `newnick`, `timestamp`, \'user_message\' AS `mtype` FROM `' . $this->_table . '`
UNION
SELECT id, affected_user AS nickname, connected_users, matter, NULL, reason, NULL, `timestamp`, \'user_connection\' AS `mtype` FROM `' . $this->_uc_table . '`
UNION
SELECT id, oldnick AS nickname, NULL, NULL, NULL, NULL, newnick, `timestamp`, \'user_renaming\' AS `mtype` FROM `' . $this->_ur_table . '`
ORDER BY `timestamp` DESC LIMIT ' . $offset . ' , ' . $limit . ';';
		return $this->db->query($query)->result();
	}

	/**
	 * Returns the max-pagination for queries from all 3 tables
	 * @param int $results results per page
	 * @return int maximum pagination
	 */
	public function max_pagination($results = 100) {
		isint($results, 100);
		$query = 
'SELECT id FROM irc
UNION
SELECT id FROM irc_user_connections
UNION
SELECT id FROM irc_user_renamings';
		return ceil($this->db->query($query)->num_rows() / $results);
	}
}

/* End of file irc_model.php */
/* Location: ./application/models/irc_model.php */
