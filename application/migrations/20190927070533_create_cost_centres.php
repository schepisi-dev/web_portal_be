<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Cost_Centres extends CI_Migration
{
    
    protected $table = 'cost_centres';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'cost_centre_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8', 'unsigned' => true, 'auto_increment' => true ),
            'cost_centre_name' => array( 'type' => 'VARCHAR', 'constraint' => '100' ),
			'cost_centre_organization_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8' ),
			'cost_centre_parent_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8' ),

            'cost_centre_created_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'cost_centre_modified_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'cost_centre_deleted' => array( 'type' => 'TINYINT', 'constraint' => '1', 'null' => false ),
            'cost_centre_deleted_on' => array( 'type' => 'DATETIME', 'null' => false )
        ));
        $this->dbforge->add_key('cost_centre_id', true);
        $this->dbforge->add_key('cost_centre_organization_id');
        $this->dbforge->create_table($this->table);

        $this->db->insert($this->table, [
            'cost_centre_name' => 'Administration',
            'cost_centre_organization_id' => 4,
            'cost_centre_parent_id' => 0,
        ]);
        $this->db->insert($this->table, [
            'cost_centre_name' => 'Administration - HR',
            'cost_centre_organization_id' => 4,
            'cost_centre_parent_id' => 1,
        ]);
        $this->db->insert($this->table, [
            'cost_centre_name' => 'Administration - HR - Managers',
            'cost_centre_organization_id' => 4,
            'cost_centre_parent_id' => 2,
        ]);
        $this->db->insert($this->table, [
            'cost_centre_name' => 'Administration - HR - Recruiters',
            'cost_centre_organization_id' => 4,
            'cost_centre_parent_id' => 2,
        ]);
        $this->db->insert($this->table, [
            'cost_centre_name' => 'Customer Service',
            'cost_centre_organization_id' => 4,
            'cost_centre_parent_id' => 0,
        ]);
        $this->db->insert($this->table, [
            'cost_centre_name' => 'Management',
            'cost_centre_organization_id' => 4,
            'cost_centre_parent_id' => 0,
        ]);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}