<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access_token_model extends MY_Model {

	var $table = 'access_tokens';
	var $id = 'access_token_id';

	function get_token ( $identifier) {
		date_default_timezone_set( 'Asia/Manila' );
		// $this->load->model( 'Configs_model' );
		
		$generated_token = md5( getToken( 25 ) );
		$this->load->model( 'User_model' );
		$user = $this->User_model->get_by_attribute('user_username', $identifier);
		 
		// if member already exists in access token, refresh token value
		$access_token = $this->find( array( 'access_token_user_id' => $user->user_id) );		
		if ( $access_token ) {
			$data = array(
				'access_token_token'                 => $generated_token,
				'access_token_ip'                    => $this->input->ip_address(),
				'access_token_date_accessed'         => date( 'Y-m-d H:i:s' ),
			);
			$token = $this->update( array( 'access_token_user_id' => $user->user_id), $data );

		} else {
			$data = array(
				'access_token_token'        	=> $generated_token,
				'access_token_user_id'      	=> $user->user_id,
				'access_token_ip'           	=> $this->input->ip_address(),
				'access_token_date_accessed'	=> date( 'Y-m-d H:i:s' ),
			);
			$token = $this->save( $data );

		}

		// $tokenExp = $this->Configs_model->get_by_attribute( 'config_name', 'token_expiry' );
		return array(
			'access_token' => $token->access_token_token,
			// 'expires_in'   => (integer)$tokenExp->config_value
		);

	}

}
