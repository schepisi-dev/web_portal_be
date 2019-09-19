<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Configs extends CI_Migration
{
    protected $table = 'configs';

    public function up()
    {
        $this->dbforge->drop_table('table_name', true);

        $this->dbforge->add_field(array(
            'config_id' => array(
                'type' => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'config_name' => array(
                'type' => 'VARCHAR(100)',
                'null' => false,
            ),
            'config_value' => array(
                'type' => 'TEXT'
            ),
            'config_created_at' => array(
                'type' => 'TIMESTAMP',
            )
        ));
        $this->dbforge->add_key('config_id', true);
        $this->dbforge->create_table($this->table);

        $this->db->insert($this->table, [
            'config_name'    => "application_name",
            'config_value'   => "Schepisi Web Portal",
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_table($this->table, true);
    }
}