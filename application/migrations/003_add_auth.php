<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_auth extends CI_Migration
{	
	public function up() {
		//Table permissions
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '192',
				'null' => FALSE
			),
			'description' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),
			'parent' => array(
				'type' => 'INT',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'default' => array(
				'type' => 'TINYINT',
				'null' => FALSE,
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('permissions');

		// Table pgroups
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '192',
				'null' => FALSE,
			),
			'description' => array(
				'type' => 'TEXT',
				'null' => FALSE
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('pgroups');
	
		// Table pgroups_permissions
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'pgroup_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE
			),
			'permissions_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE
			),
			'value' => array(
				'type' => 'TINYINT',
				'null' => FALSE
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('pgroups_permissions');
		
		// Table users
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => '254',
				'null' => FALSE
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => '128',
				'null' => FALSE
			),
			'ingame_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '128',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => '128',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'unique_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'about' => array(
				'type' => 'TEXT',
				'null' => FALSE
			),
			'register_ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => FALSE
			),
			'latest_ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => FALSE
			),
			'timestamp' => array(
				'type' => 'TIMESTAMP',
				'null' => FALSE,
				'default' => 'CURRENT_TIMESTAMP'
			),
			'country_code' => array(
				'type' => 'VARCHAR',
				'constraint' => '3',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'active' => array(
				'type' => 'TINYINT',
				'null' => FALSE
			),
			'lastloggedin' => array(
				'type' => 'TIMESTAMP',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'file_limit' => array(
				'type' => 'INT',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'folder_limit' => array(
				'type' => 'INT',
				'null' => TRUE,
				'default' => 'NULL'
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users');
		
		// Table user_permissions
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'user_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE
			),
			'permissions_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE
			),
			'value' => array(
				'type' => 'TINYINT',
				'null' => FALSE
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users_permissions');
		
		// Table users_pgroups
		$this->dbforge->add_fields(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'user_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE
			),
			'group_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE
			),
			'significance' => array(
				'type' => 'INT',
				'null' => TRUE,
				'default' => 'NULL'
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users_pgroups');
		
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'user_id' => array(
				'type' => 'INT',
				'null' => FALSE
			),
			'code' => array(
				'type' => 'VARCHAR',
				'constraint' => '128',
				'null' => FALSE
			),
			'expiration' => array(
				'type' => 'INT',
				'null' => FALSE
			),
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '64',
				'null' => FALSE
			),
			'browser' => array(
				'type' => 'VARCHAR',
				'constraint' => '256',
				'null' => FALSE
			),
			'system' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => FALSE
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users_stay_logged_in');
		
		// Table login attempts
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'ip_address' => array(
				'type' => 'VARCHAR',
				'constraint' => '15',
				'null' => FALSE
			),
			'login' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => FALSE
			),
			'time' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => TRUE,
				'default' => 'NULL'
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('login_attempts');
		
		// Table user_activation
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'user_id' => array(
				'type' => 'INT',
				'unsigned' => TRUE,
				'null' => FALSE
			),
			'code' => array(
				'type' => 'VARCHAR',
				'constraint' => '128',
				'null' => FALSE
			),
			'expiration' => array(
				'type' => 'TIMESTAMP',
				'null' => FALSE,
				'default' => 'CURRENT_TIMESTAMP'
				// ON UPDATE CURRENT_TIMESTAMP IS A MYSQL SPECIFIC SUBSET!
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('user_activation');
		
		// Table user_menu_links
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'order' => array(
				'type' => 'INT',
				'null' => FALSE
			),
			'unique_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '192',
				'null' => FALSE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '192',
				'null' => FALSE
			),
			'link' => array(
				'type' => 'VARCHAR',
				'constraint' => '512',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'permissions_id' => array(
				'type' => 'INT',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'parent_id' => array(
				'type' => 'INT',
				'null' => TRUE,
				'default' => 'NULL'
			),
			'default' => array(
				'type' => 'TINYINT',
				'null' => FALSE
			)
		));
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('user_menu_links');
	}
	
	public function down()
	{
		$this->dbforge->drop_table('permissions');
		$this->dbforge->drop_table('pgroups');
		$this->dbforge->drop_table('pgroup_permissions');
		
		$this->dbforge->drop_table('users');
		$this->dbforge->drop_table('users_permissions');
		$this->dbforge->drop_table('users_pgroups');
		
		$this->dbforge->drop_table('users_stay_logged_in');		
		$this->dbforge->drop_table('login_attempts');
		$this->dbforge->drop_table('user_activation');
		
		$this->dbforge->drop_table('user_menu_links');
	}
}

?>
