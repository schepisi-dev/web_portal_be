<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Call_And_Usage extends CI_Migration
{
    
    protected $table = 'call_and_usages';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'call_and_usage_id' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'call_and_usage_account_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
            ),
            'call_and_usage_bill_issue_date' => array(
                'type' => 'DATE'
            ),
            'call_and_usage_bill_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ),
            'call_and_usage_service_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ), 
            'call_and_usage_service_owner' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ), 
            'call_and_usage_date' => array(
                'type' => 'DATE',
            ), 
            'call_and_usage_time' => array(
                'type' => 'TIME',
            ),
            'call_and_usage_called_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ),
            'call_and_usage_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ),
            'call_and_usage_duration' => array(
                'type' => 'TIME',
            ), 
            'call_and_usage_excl_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ), 
            'call_and_usage_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ), 
            'call_and_usage_incl_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),         
            'call_and_usage_date_synced' => array(
                'type' => 'DATETIME',
                'null' => false,
            )
        ));
        $this->dbforge->add_key('call_and_usage_id', true);
        $this->dbforge->add_key('call_and_usage_account_number');
        $this->dbforge->add_key('call_and_usage_service_number');
        $this->dbforge->add_key('call_and_usage_service_owner');
        $this->dbforge->add_key('call_and_usage_bill_number');
        $this->dbforge->add_key('call_and_usage_bill_issue_date');
        $this->dbforge->add_key('call_and_usage_called_number');
        $this->dbforge->add_key('call_and_usage_type');
        $this->dbforge->add_key('call_and_usage_date_synced');
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}