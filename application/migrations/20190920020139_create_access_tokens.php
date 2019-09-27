<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_Access_Tokens extends CI_Migration
{
    
    protected $table = 'access_tokens';
    
    public function up () {
        $this->dbforge->add_field( array(
            'access_token_id'            => array(
                'type'           => 'MEDIUMINT',
                'constraint'     => 8,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ),
            'access_token_token'         => array(
                'type'       => 'VARCHAR',
                'constraint' => 100
            ),
            'access_token_user_id'     => array(
                'type'       => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned'   => TRUE,
            ),
            'access_token_date_accessed' => array(
                'type' => 'timestamp'
            ),
            'access_token_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => 50
            ),
        ) );
        $this->dbforge->add_key( 'access_token_id', TRUE );
        $this->dbforge->add_key( 'access_token_token' );
        $this->dbforge->add_key( 'access_token_user_id' );
        $this->dbforge->add_key( 'access_token_date_accessed' );
        $this->dbforge->create_table( $this->table );

    }

    public function down () {
        $this->dbforge->drop_table( $this->table );
    }
}