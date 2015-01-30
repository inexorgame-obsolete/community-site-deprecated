<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Seems like a bit of irony. These few lines will auto-create the database as you desire.
class MigrationCreateDatabase extends CI_Migration
{
	public function up() {
		$this->dbforge->create_database($this->db->database);
	}
	
	public function down()
	{
		$this->dbforge->drop_database($this->db->database);
	}
}
?>