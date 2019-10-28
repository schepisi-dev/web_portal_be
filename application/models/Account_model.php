<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends MY_Model {

    protected $table				= "accounts";
	protected $key					= "account_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "account_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "account_modified_on";
	
	protected $soft_deletes        = TRUE;
	protected $deleted		 		= "account_deleted";
	protected $deleted_field 		= "account_deleted_on";

    function save_account ($action, $post) {	

		if ($action=='add') {	
			$this->data = array(
				'account_name' => $post['name'],
				'account_number' => $post['number'],
				'account_organization_id' => $post['organization_id'],
				'account_uuid' => md5( getToken( 25 ) )
			);
			$result = $this->save();
		} else {
			//get account to be updated
			$account = $this->get_by_attribute('account_uuid', $post['uuid']);
			$this->data = array(
				'account_name' => (isset($post['name']))? $post['name']: $account->account_name,
				'account_number' => (isset($post['number']))? $post['number']: $account->account_number,
				'account_uuid' => $post['uuid']
			);
			$result = $this->update(array($this->key => $account->account_id));
		}

		return $result;

    }

}