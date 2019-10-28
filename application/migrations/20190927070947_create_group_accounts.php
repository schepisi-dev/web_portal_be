<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Group_Accounts extends CI_Migration
{
    
    protected $table = 'group_accounts';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'group_account_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8', 'unsigned' => true, 'auto_increment' => true ),
            'group_account_group_id' => array(  'type' => 'MEDIUMINT', 'constraint' => '8' ),
            'group_account_account_id' => array(  'type' => 'MEDIUMINT', 'constraint' => '8' ),
            'group_account_percentage' => array( 'type' => 'SMALLINT', 'constraint' => '3' ),

            'group_account_created_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'group_account_modified_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'group_account_deleted' => array( 'type' => 'TINYINT', 'constraint' => '1', 'null' => false ),
            'group_account_deleted_on' => array( 'type' => 'DATETIME', 'null' => false )
        ));
        $this->dbforge->add_key('group_account_id', true);
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}