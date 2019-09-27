<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Service_And_Equipment extends CI_Migration
{
    
    protected $table = 'service_and_equipments';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'service_and_equipment_id' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'service_and_equipment_account_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
            ),
            'service_and_equipment_bill_issue_date' => array(
                'type' => 'DATE'
            ),
            'service_and_equipment_bill_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ),
            'service_and_equipment_service_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ), 
            'service_and_equipment_service_owner' => array(
                'type' => 'DATE',
            ), 
            'service_and_equipment_charge_type_description' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'service_and_equipment_service_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ),
            'service_and_equipment_quantity' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => true,
            ),
            'service_and_equipment_excl_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'service_and_equipment_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'service_and_equipment_incl_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'service_and_equipment_date_synced' => array(
                'type' => 'DATETIME',
                'null' => false,
            ) 
        ));
        $this->dbforge->add_key('service_and_equipment_id', true);
        $this->dbforge->add_key('service_and_equipment_account_number');
        $this->dbforge->add_key('service_and_equipment_bill_number');
        $this->dbforge->add_key('service_and_equipment_bill_issue_date');
        $this->dbforge->add_key('service_and_equipment_service_number');
        $this->dbforge->add_key('service_and_equipment_service_owner');
        $this->dbforge->add_key('service_and_equipment_service_type');
        $this->dbforge->add_key('service_and_equipment_date_synced');
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}