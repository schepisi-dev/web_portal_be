<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Users extends CI_Migration
{
    
    protected $table = 'users';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'user_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8', 'unsigned' => true, 'auto_increment' => true ),
            'user_username' => array( 'type' => 'VARCHAR', 'constraint' => '20' ),
			'user_password' => array( 'type' => 'VARCHAR', 'constraint' => '100' ),
			'user_first_name' => array( 'type' => 'VARCHAR', 'constraint' => '100' ),
			'user_last_name' => array( 'type' => 'VARCHAR', 'constraint' => '100' ),
			'user_email' => array( 'type' => 'VARCHAR', 'constraint' => '100' ),
            'user_role' => array( 'type' => "SET('administrator', 'standard', 'basic')", 'null' => false ),
            'user_organization_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8' ),

            'user_created_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'user_modified_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'user_deleted' => array( 'type' => 'TINYINT', 'constraint' => '1', 'null' => false ),
            'user_deleted_on' => array( 'type' => 'DATETIME', 'null' => false )
        ));
        $this->dbforge->add_key( 'user_id', true );
		$this->dbforge->add_key( 'user_username' );
		$this->dbforge->add_key( 'user_role' );
		$this->dbforge->add_key( 'user_email' );
		$this->dbforge->add_key( 'user_organization_id' );
        $this->dbforge->create_table($this->table);

        $role = array('administrator', 'standard', 'basic');
        for ($i = 1; $i <= 9; $i++)
		{
			$this->db->insert($this->table, [
				'user_username' => "admin00{$i}",
				'user_email' => "client-{$i}@mail.com",
				'user_password' => password_hash('password', PASSWORD_DEFAULT),
				'user_first_name' => "Firstname {$i}",
				'user_last_name' => "Lastname {$i}",
				'user_role' => $role[$i%3],
				'user_organization_id' => ($i%3)+1,
				'user_created_on' => date('Y-' . rand(1, 12) . '-' . rand(1, 28) . ' H:i:s'),
			]);
		}
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}