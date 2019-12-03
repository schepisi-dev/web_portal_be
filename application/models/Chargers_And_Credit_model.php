<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chargers_And_Credit_model extends MY_Model {

    protected $table				= "chargers_and_credits";
	protected $key					= "chargers_and_credit_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= FALSE;	
	protected $set_modified 		= FALSE;	
	protected $soft_deletes        	= FALSE;


	function categorized($month, $type='excl_gst'){
		$chargers_credit = $this->custom_query(
			"SUM(REPLACE(REPLACE(chargers_and_credit_".$type.",'.',''),'$','')) as sum, MONTH(chargers_and_credit_bill_issue_date) as month, chargers_and_credit_occ_description as type" , //select
			"MONTH(chargers_and_credit_bill_issue_date)=".$month.' AND chargers_and_credit_occ_description <> "" ', //where
			"type" , //group_by
		);
		$response = array();
		foreach ($chargers_credit as $result) {
			$chargers_credit_accounts = $this->custom_query(
				"SUM(REPLACE(REPLACE(chargers_and_credit_".$type.",'.',''),'$','')) as account_sum, chargers_and_credit_account_number as account_number" , //select
				"MONTH(chargers_and_credit_bill_issue_date)=".$month." AND chargers_and_credit_occ_description LIKE '". $result->type."'", //where
				"chargers_and_credit_account_number" , //group_by
			);
			$accounts_response = array();
			foreach ($chargers_credit_accounts as $account_result) {
				$accounts_response[] = array(
					'account_number'	=> $account_result->account_number,
					'total'	=> ($account_result->account_sum)/100
				);
			}
			$response[] = array(
				// 'month' => $result->month,
				'type'	=> $result->type,
				'total'	=> ($result->sum)/100,
				'accounts' => $accounts_response
			);
		}
		return $response;
	}

	function total($month, $type='excl_gst'){
		$call_usages = $this->custom_query(
			"SUM(REPLACE(REPLACE(chargers_and_credit_".$type.",'.',''),'$','')) as sum, MONTH(chargers_and_credit_bill_issue_date) as month" , //select
			"MONTH(chargers_and_credit_bill_issue_date)=".$month, //where
			"month" , //group_by
		);
		$total = 0;
		foreach ($call_usages as $result) {
			$total	= ($result->sum)/100;
		}
		return $total;
	}

}