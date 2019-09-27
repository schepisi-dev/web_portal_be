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
                'type' => 'DATETIME',
            )
        ));
        $this->dbforge->add_key('config_id', true);
        $this->dbforge->create_table($this->table);

        $this->db->insert($this->table, [
            'config_name'    => "application_name",
            'config_value'   => "Schepisi Web Portal",
        ]);
        
        $this->db->insert($this->table, [
            'config_name'    => "pw_config",
            'config_value'   => serialize(array(
                'pw_min_length' => 8,
                'pw_max_length' => 32,
                'pw_allow_special_character' => 'FALSE',
                
            ))
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_table($this->table, true);
    }
}