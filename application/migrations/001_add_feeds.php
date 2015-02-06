<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Feeds extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_field(array(
				'id' => array(
						'type' => 'INT',
						'null' => FALSE,
						'auto_increment' => TRUE
				),
				'title' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
				),
				'url' => array(
						'type' => 'TEXT',
						'null' => FALSE
				),
				'status' => array(
						'type' => 'BOOLEAN',
						'null' => FALSE,
				),
		));
		
		$this->dbforge->create_table('feeds');
		$this->dbforge->_reset();
	}
	
	public function down()
	{
		$this->dbforge->drop_table('feeds');
	}
}