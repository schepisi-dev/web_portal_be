<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Accounts extends CI_Migration
{
    
    protected $table = 'accounts';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'account_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8', 'unsigned' => true, 'auto_increment' => true ),
            'account_name' => array( 'type' => 'VARCHAR', 'constraint' => '100' ),           
			'account_uuid' => array( 'type'       => 'VARCHAR', 'constraint' => 32 ),
            'account_organization_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8', ), 

            'account_created_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'account_modified_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'account_deleted' => array( 'type' => 'DATETIME', 'null' => false ),
            'account_deleted_on' => array( 'type' => 'DATETIME', 'null' => false )
        ));
        $this->dbforge->add_key('account_id', true);
        $this->dbforge->add_key('account_organization_id');
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}