<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {

    protected $table				= "users";
	protected $key					= "user_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "user_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "user_modified_on";
	
	protected $soft_deletes         = TRUE;
	protected $deleted		 		= "user_deleted";
	protected $deleted_field 		= "user_deleted_on";

	public function save_user ( $action, $data ) {
		$this->data = array(
			'user_username' => $data['username'],
			'user_password' => password_hash( $data['password'], PASSWORD_BCRYPT ),
			'user_first_name' => $data['first_name'],
			'user_last_name' => $data['last_name'],
			'user_email' => $data['email'],
			'user_role' => $data['role'],
			'user_organization_id' => $data['organization_id']
		);
		if ($action=='add') {
			$response = $this->save();
			unset($response->user_modified_on);
		} else {
			$response = $this->update(array($this->key => $data['id']));
		}
		unset($response->user_password, $response->user_deleted, $response->user_deleted_on, $response->user_id, $response->user_organization_id);
        return $response;
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

	public function _find_all($offset, $limit){
		$users = $this->find_all($offset, $limit);
		$formatted_user = array();
		$this->load->model('Organization_model');
		foreach($users as $user) {
			$organization = $this->Organization_model->get_by_id($user->user_organization_id);
			$formatted_user[] = array(
				'user_id' => $user->user_id,
				'user_username'=> $user->user_username,
				'user_email'=> $user->user_email,
				'user_first_name'=> $user->user_first_name,
				'user_last_name'=> $user->user_last_name,
				'user_organization_name'=> (isset($organization))? $organization->organization_name: 'NA',
				'user_role'=> $user->user_role,

			);

		}
		return $formatted_user;
	}

}