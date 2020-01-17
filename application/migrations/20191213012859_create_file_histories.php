<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_File_Histories extends CI_Migration
{
    
    protected $table = 'file_histories';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'file_history_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8', 'unsigned' => true, 'auto_increment' => true ),
            'file_history_info' => array( 'type' => 'VARCHAR', 'constraint' => '255' ),
            'file_history_type' => array( 'type' => 'VARCHAR', 'constraint' => '45' ),

            'file_history_created_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'file_history_modified_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'file_history_deleted' => array( 'type' => 'TINYINT', 'constraint' => '1', 'null' => false ),
            'file_history_deleted_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'file_history_organization_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8' ),
            'file_history_uploaded_by' => array( 'type' => 'VARCHAR', 'constraint' => '100' )
        ));
        $this->dbforge->add_key('file_history_id', true);
        $this->dbforge->add_key('file_history_deleted_on');
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}