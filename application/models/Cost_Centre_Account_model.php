<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_Centre_Account_model extends MY_Model {

    protected $table				= "cost_centre_accounts";
	protected $key					= "cost_centre_account_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "cost_centre_account_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "cost_centre_account_modified_on";
	
	protected $soft_deletes         = TRUE;
	protected $deleted		 		= "cost_centre_account_deleted";
	protected $deleted_field 		= "cost_centre_account_deleted_on";

    function save_account ($action, $post) {	

		if ($action=='enroll') {	
			$this->data = array(
				'cost_centre_account_cost_centre_id' => $post['cost_centre_id'],
				'cost_centre_account_account_id' => $post['account_id'],
				'cost_centre_account_percentage' => $post['percentage']
			);
			$result = $this->save();
		} else {
			//get account to be updated
			$account = $this->get_by_attribute('cost_centre_account_id', $post['id']);
			$this->data = array(
				'cost_centre_account_cost_centre_id' => (isset($post['cost_centre_id']))? $post['cost_centre_id']: $account->account_name,
				'cost_centre_account_account_id' => (isset($post['account_id']))? $post['account_id']: $account->account_number,
				'cost_centre_account_percentage' => $post['percentage']
			);
			$result = $this->update(array($this->key => $account->account_id));
		}

		return $result;

    }

}