<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Call_And_Usage_model extends MY_Model {

    protected $table				= "call_and_usages";
	protected $key					= "call_and_usage_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= FALSE;	
	protected $set_modified 		= FALSE;	
	protected $soft_deletes        	= FALSE;

	

	function categorized($month, $type='excl_gst'){
		$call_usages = $this->custom_query(
			"SUM(REPLACE(REPLACE(call_and_usage_".$type.",'.',''),'$','')) as sum, MONTH(call_and_usage_bill_issue_date) as month, call_and_usage_type as type" , //select
			"MONTH(call_and_usage_bill_issue_date)=".$month, //where
			"type" , //group_by
		);
		$response = array();
		foreach ($call_usages as $result) {
			$call_usage_accounts = $this->custom_query(
				"SUM(REPLACE(REPLACE(call_and_usage_".$type.",'.',''),'$','')) as account_sum, call_and_usage_account_number as account_number" , //select
				"MONTH(call_and_usage_bill_issue_date)=".$month." AND call_and_usage_type LIKE '". $result->type."'", //where
				"call_and_usage_account_number" , //group_by
			);
			$accounts_response = array();
			foreach ($call_usage_accounts as $account_result) {
				$accounts_response[] = array(
					'number'	=> $account_result->account_number,
					'total'	=> ($account_result->account_sum)/100
				);
			}
			$response[] = array(
				// 'month' => $result->month,
				'name'	=> $result->type,
				'total'	=> ($result->sum)/100,
				'accounts' => $accounts_response
			);
		}
		return $response;
	}

	function total($month, $type='excl_gst'){
		$call_usages = $this->custom_query(
			"SUM(REPLACE(REPLACE(call_and_usage_".$type.",'.',''),'$','')) as sum, MONTH(call_and_usage_bill_issue_date) as month" , //select
			"MONTH(call_and_usage_bill_issue_date)=".$month, //where
			"month" , //group_by
		);
		$total = 0;
		foreach ($call_usages as $result) {
			$total	= ($result->sum)/100;
		}
		return $total;
	}
}