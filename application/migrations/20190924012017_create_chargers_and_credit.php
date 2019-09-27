<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Chargers_And_Credit extends CI_Migration
{
    
    protected $table = 'chargers_and_credits';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'chargers_and_credit_id' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'chargers_and_credit_account_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
            ),
            'chargers_and_credit_bill_issue_date' => array(
                'type' => 'DATE'
            ),
            'chargers_and_credit_bill_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ),
            'chargers_and_credit_service_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ), 
            'chargers_and_credit_transaction_date' => array(
                'type' => 'DATE',
            ), 
            'chargers_and_credit_occ_description' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'chargers_and_credit_quantity' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => true,
            ),
            'chargers_and_credit_excl_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'chargers_and_credit_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'chargers_and_credit_incl_gst' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
            ),
            'chargers_and_credit_date_synced' => array(
                'type' => 'DATETIME',
                'null' => false,
            ) 
        ));
        $this->dbforge->add_key('chargers_and_credit_id', true);
        $this->dbforge->add_key('chargers_and_credit_account_number');
        $this->dbforge->add_key('chargers_and_credit_service_number');
        $this->dbforge->add_key('chargers_and_credit_bill_number');
        $this->dbforge->add_key('chargers_and_credit_bill_issue_date');
        $this->dbforge->add_key('chargers_and_credit_occ_description');
        $this->dbforge->add_key('chargers_and_credit_transaction_date');
        $this->dbforge->add_key('chargers_and_credit_date_synced');
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}