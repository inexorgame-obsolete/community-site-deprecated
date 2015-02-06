<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_auth extends CI_Migration
{	
	public function up()
	{
		// Table users
		$this->dbforge->add_field(array(
				'id' => array(
						'type' => 'INT',
						'null' => FALSE,
						'auto_increment' => TRUE
				),
				'name' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
				),
				'username' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
				),
				'email' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
				),
				'password' => array(
						'type' => 'VARCHAR',
						'constraint' => '60',
						'null' => FALSE
				),
				'status' => array(
						'type' => 'INT',
						'null' => TRUE,
						'default' => NULL
				),
				'activation' => array(
						'type' => 'INT',
						'null' => TRUE,
						'default' => '1'
				),
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users');
		$this->dbforge->_reset();
		
		// Table recover
		$this->dbforge->add_field(array(
			'user_id' => array(
					'type' => 'INT',
					'null' => FALSE,
			),
			'token' => array(
					'type' => 'VARCHAR',
					'constraint' => '255',
					'null' => FALSE
			),
			'password' => array(
					'type' => 'VARCHAR',
					'constraint' => '255',
					'null' => FALSE
			),
		));
		
		$this->dbforge->create_table('recover');
		$this->dbforge->_reset();
		
		// Table activation
		$this->dbforge->add_field(array(
				'user_id' => array(
						'type' => 'INT',
						'null' => FALSE,
				),
				'token' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
				),
		));
		
		$this->dbforge->create_table('activation');
		
		//$this->db->simple_query('ALTER TABLE `'.$this->db->dbprefix.'_activation` ADD UNIQUE (`user_id`)');
		// This is (more or less) a trivial hack because DBForge doesn't natively support unique keys
	}
	
	public function down()
	{
		$this->dbforge->drop_table('users');
		$this->dbforge->drop_table('recover');
		$this->dbforge->drop_table('activation');
	}
}
