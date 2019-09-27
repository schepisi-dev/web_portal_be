<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {

	var $table = 'users';
	var $column = array( 'user_id', 'user_username', 'user_first_name', 'user_last_name', 'user_email', 'user_date_created' );
	var $order = array( 'user_id' => 'desc' );

	public function _save ( $data ) {
		$user = array(
			'user_username'   => $data['username'],
			'user_password'   => password_hash( $data['password'], PASSWORD_BCRYPT ),
			'user_first_name' => $data['first_name'],
			'user_last_name'  => $data['last_name'],
			'user_email'      => $data['email'],
		);
		$this->db->insert( $this->table, $user);
        $query = $this->db->get_where( $this->table, array( 'user_id' => $this->db->insert_id() ) );
        return $query->row();
	}

	public function login ( $username, $password ) {
		$userFound = $this->get_by_attribute( 'user_username', $username );
		if ( $userFound ) {
			$hashed_password = $userFound->user_password;
			if ( password_verify( $password, $hashed_password ) ) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
