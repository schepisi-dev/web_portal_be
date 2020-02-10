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
		if ($action=='add') {
			$this->data = array(
				'user_username' => $data['username'],
				'user_password' => password_hash( $data['password'], PASSWORD_BCRYPT ),
				'user_first_name' => $data['first_name'],
				'user_last_name' => $data['last_name'],
				'user_email' => $data['email'],
				'user_role' => $data['role'],
				'user_organization_id' => $data['organization_id']
			);
			$response = $this->save();
			unset($response->user_modified_on);
		} else {
			$this->data = array(
				'user_username' => $data['username'],
				'user_first_name' => $data['first_name'],
				'user_last_name' => $data['last_name'],
				'user_email' => $data['email']
			);
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

	public function _find_all($offset, $limit, $organization_id){
		$users = ($organization_id==0)? $this->find_all() :$this->find(array('user_organization_id' => $organization_id, 'user_deleted' => 0), $offset, $limit);
		$formatted_user = array();
		$this->load->model('Organization_model');
		if ($users) {
			foreach($users as $user) {
				$organization = $this->Organization_model->get_by_id($user->user_organization_id);
				$formatted_user[] = array(
					'user_id' => $user->user_id,
					'user_username'=> $user->user_username,
					'user_email'=> $user->user_email,
					'user_first_name'=> $user->user_first_name,
					'user_last_name'=> $user->user_last_name,
					'user_organization_name'=> ($user->user_organization_id!=0)? $organization->organization_name: 'NA',
					'user_role'=> $user->user_role,
					'user_date_created' => $user->user_created_on
	
				);
	
			}
		}
		return $formatted_user;
	}

	
	
    public function get_count_breakdown () {		
		$breakdown = $this->custom_query(
			"COUNT(user_id) as count, MONTH(user_created_on) as month" , //select
			"YEAR(user_created_on) = ". date('Y'), //where
			"MONTH(user_created_on)" , //group_by
		);
		$response = array();
		$formatted_response = array();
		foreach ($breakdown as $result) {
			$response[$result->month] = $result->count;
		}
		$month_array = array('1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May',
                            '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October',
							'11' => 'November', '12' => 'December');

		foreach($month_array as $month => $name) {
			$formatted_response[] = array(
				'month' => $name,
				'count' => isset($response[$month])? (integer)$response[$month]: 0
			);
		}
		return $formatted_response;

	}

}