<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Organizations extends CI_Migration
{
    
    protected $table = 'organizations';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'organization_id' => array( 'type' => 'MEDIUMINT', 'constraint' => '8', 'unsigned' => true, 'auto_increment' => true ),
            'organization_name' => array( 'type' => 'VARCHAR', 'constraint' => '100' ),

            'organization_created_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'organization_modified_on' => array( 'type' => 'DATETIME', 'null' => false ),
            'organization_deleted' => array( 'type' => 'TINYINT', 'constraint' => '1', 'null' => false ),
            'organization_deleted_on' => array( 'type' => 'DATETIME', 'null' => false )
        ));
        $this->dbforge->add_key('organization_id', true);
        $this->dbforge->create_table($this->table);

        $role = array('administrator', 'standard', 'basic', 'dev');
        for ($i = 0; $i <= 3; $i++)
		{
			$this->db->insert($this->table, [
				'organization_name' => $role[$i]. '_organization',
			]);
		}
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}