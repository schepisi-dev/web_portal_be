<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Api_Keys extends CI_Migration
{
    public function up()
	{
		$this->config->load('rest');
		$table = config_item('rest_keys_table');
		$fields = array(
			'id'                           => [
				'type'           => 'INT(11)',
				'auto_increment' => true,
				'unsigned'       => true,
			],
			'user_id'                      => [
				'type'     => 'INT(11)',
				'unsigned' => true,
			],
			config_item('rest_key_column') => [
				'type'   => 'VARCHAR(' . config_item('rest_key_length') . ')',
				'unique' => true,
			],
			'level'                        => [
				'type' => 'INT(2)',
			],
			'ignore_limits'                => [
				'type'    => 'TINYINT(1)',
				'default' => 0,
			],
			'is_private_key'               => [
				'type'    => 'TINYINT(1)',
				'default' => 0,
			],
			'ip_addresses'                 => [
				'type' => 'TEXT',
				'null' => true,
			],
			'date_created'                 => [
				'type' => 'INT(11)',
			],
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table($table);
		$this->db->query(add_foreign_key($table, 'id', 'clients(id)', 'CASCADE', 'CASCADE'));
	}


	public function down()
	{
		$table = config_item('rest_key_column');
		if ($this->db->table_exists($table))
		{
			$this->db->query(drop_foreign_key($table, 'user_id'));
			$this->dbforge->drop_table($table);
		}
	}
}