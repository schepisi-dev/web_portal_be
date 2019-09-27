<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Groups extends CI_Migration
{
    
    protected $table = 'groups';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'group_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8', 'unsigned' => true, 'auto_increment' => true ),
            'group_name' => array( 'type' => 'VARCHAR', 'constraint' => '100' ),
			'group_organization_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8' ),

            'group_created_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'group_modified_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'group_deleted' => array( 'type' => 'DATETIME', 'null' => false ),
            'group_deleted_on' => array( 'type' => 'DATETIME', 'null' => false )
        ));
        $this->dbforge->add_key('group_id', true);
        $this->dbforge->add_key('group_organization_id');
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}