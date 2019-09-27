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
            'user_username'     => array( 'type' => 'VARCHAR', 'constraint' => '20' ),
			'user_password'     => array( 'type' => 'VARCHAR', 'constraint' => '100' ),
			'user_first_name'   => array(
				'type'       => 'VARCHAR',
				'constraint' => '100',
			),
			'user_last_name'    => array(
				'type'       => 'VARCHAR',
				'constraint' => '100',
			),
			'user_email'        => array(
				'type'       => 'VARCHAR',
				'constraint' => '100',
			),
            'user_role' => array(
                'type' => "SET('administrator', 'standard', 'basic')",
                'null' => false,
            ),

            'user_created_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'user_modified_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'user_deleted' => array( 'type' => 'DATETIME', 'null' => false ),
            'user_deleted_on' => array( 'type' => 'DATETIME', 'null' => false )
        ));
        $this->dbforge->add_key('user_id', true);
		$this->dbforge->add_key( 'user_username' );
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}