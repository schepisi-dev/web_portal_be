<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Transactions extends CI_Migration
{
    
    protected $table = 'transactions';
    public function up()
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Drop table $this->table if it exists
        $this->dbforge->drop_table($this->table, true);

        // Table structure for table $this->table
        $this->dbforge->add_field(array(
            'transaction_id' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'transaction_uuid' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => false,
            ),
            'transaction_type' => array(
                'type' => "SET('call_and_usage', 'chargers_and_credit', 'service_and_equipment')",
                'null' => false,
            ),
            'transaction_table_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => false,
            ),
            'transaction_account_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => false,
            ),
            'transaction_date_synced' => array(
                'type' => 'DATETIME',
            ),
            'transaction_organization_id' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => true,
            ),

        ));
        $this->dbforge->add_key('transaction_id', true);
        $this->dbforge->add_key('transaction_uuid');
        $this->dbforge->add_key('transaction_type');
        $this->dbforge->add_key('transaction_table_id');
        $this->dbforge->add_key('transaction_account_number');
        $this->dbforge->add_key('transaction_date_synced');
        $this->dbforge->create_table($this->table);
    }

    public function down()
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->dbforge->drop_table($this->table, true);
    }
}