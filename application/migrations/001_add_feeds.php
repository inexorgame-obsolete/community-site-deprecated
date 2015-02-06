<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_feeds extends CI_Migration
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
		
		$this->dbforge->add_field(array(
			'id' => array(
					'type' => 'INT',
					'null' => FALSE,
					'auto_increment' => TRU
			),
			'feed_id' => array(
					'type' => 'INT',
					'null' => FALSE,
			),
			'title' => array(
					'type' => 'VARCHAR',
					'constraint' => '255',
					'null' => FALSE
			),
			'link' => array(
					'type' => 'TEXT',
					'null' => FALSE
			),
			'description' => array(
					'type' => 'VARCHAR',
					'constraint' => '255',
					'null' => FALSE
			)
		));
		
		$this->dbforge->create_table('feed_items');
	}
	
	public function down()
	{
		$this->dbforge->drop_table('feeds');
		$this->dbforge->drop_table('feed_items');
	}
}