<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Clients extends CI_Migration
{
    protected $table = 'clients';

	public function up()
	{
		$fields = array(
			'id'         => [
				'type'           => 'INT(11)',
				'auto_increment' => true,
				'unsigned'       => true,
			],
			'email'      => [
				'type'   => 'VARCHAR(255)',
				'unique' => true,
			],
			'password'   => [
				'type' => 'VARCHAR(64)',
			],
			'firstname'  => [
				'type' => 'VARCHAR(32)',
			],
			'lastname'   => [
				'type' => 'VARCHAR(32)',
			],
			'created_at' => [
				'type' => 'DATETIME',
			],
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table($this->table, true);

		for ($i = 1; $i <= 5; $i++)
		{
			$this->db->insert($this->table, [
				'email'      => "client-{$i}@mail.com",
				'password'   => password_hash('codeigniter', PASSWORD_DEFAULT),
				'firstname'  => "Firstname {$i}",
				'lastname'   => "Lastname {$i}",
				'created_at' => date('Y-' . rand(1, 12) . '-' . rand(1, 28) . ' H:i:s'),
			]);
		}
	}


	public function down()
	{
		if ($this->db->table_exists($this->table))
		{
			$this->dbforge->drop_table($this->table);
		}
	}
}