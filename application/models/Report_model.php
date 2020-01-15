<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends MY_Model {

    public function getServiceReportByCostCentre ($cost_centre_id) {
		$this->load->model('Cost_Centre_Account_model', 'cost_centre_account');
		$cost_centre_accounts = $this->cost_centre_account->find_all_where(array('cost_centre_account_cost_centre_id' => $cost_centre_id));

		//get account ids at accounts
		$this->load->model('Account_model', 'account');
		foreach($cost_centre_accounts as $cost_centre_account){
			$accounts[] = ($this->account->get_by_id($cost_centre_account->cost_centre_account_id))->account_number;
		}

		//get service numbers using account ids in transaction_model
		$this->load->model('Transaction_model', 'transaction');
		$transactions = $this->transaction->find_all_where_in( 
			'transaction_service_number, transaction_type, transaction_account_number', //select
			'transaction_account_number', $accounts, //where in
			'transaction_type, transaction_account_number, transaction_service_number' //group by
		);

		foreach($transactions as $transaction){
			$report[$transaction->transaction_service_number][] = array(
				'type' => $transaction->transaction_type,
				'account_number' => $transaction->transaction_service_number,
				'report' => $this->transaction->getSumByMonth(
					array(
						$transaction->transaction_type.'_service_number' => $transaction->transaction_service_number,
						$transaction->transaction_type.'_account_number' => $transaction->transaction_account_number,
					),//where
					$transaction->transaction_type//table
					//type = optional
				)
			);
		}

		return $report;

    }

    public function getTotalByCostCentre ($month, $year, $cost_centre_id, $get) {
		$cost_centre_ids = array($cost_centre_id);
		$this->load->model('Cost_Centre_Account_model', 'cost_centre_account');
		$cost_centre_accounts = $this->cost_centre_account->join(
			'accounts.account_number as account_number',
			'accounts', 'accounts.account_id = cost_centre_accounts.cost_centre_account_account_id',
			'cost_centre_accounts.cost_centre_account_cost_centre_id', $cost_centre_ids //attribute, array
		);
		
		
		//if parent, get account ids of children instead
		$accounts = array();
		foreach($cost_centre_accounts as $j => $k){
			$accounts[] = $k->account_number;
		}

		//get service numbers using account ids in transaction_model
		$this->load->model('Transaction_model', 'transaction');
		$transactions = $this->transaction->find_all_where_in( 
			'transaction_type, transaction_'.$get, //select
			'transaction_account_number', $accounts, //where in
			'transaction_'.$get //group by
		);
		foreach($transactions as $transaction){
			$report[] = array(
				$transaction->{'transaction_'.$get},
				$this->transaction->getSumByMonth(
					'MONTH(call_and_usage_bill_issue_date)='.$month.' AND '.'YEAR(call_and_usage_bill_issue_date)='.$year.' AND '.
					'call_and_usage_'.$get.' LIKE '. "'".$transaction->{'transaction_'.$get}."'",
					'call_and_usage',//table
					'call_and_usage_'.$get.' ',//groupedBy
					//type = optional
				),//call and usage
				$this->transaction->getSumByMonth(
					'MONTH(chargers_and_credit_bill_issue_date)='.$month.' AND '.'YEAR(chargers_and_credit_bill_issue_date)='.$year.' AND '.
					'chargers_and_credit_'.$get.' LIKE '. "'".$transaction->{'transaction_'.$get}."'",
					'chargers_and_credit',//table
					'chargers_and_credit_'.$get.' ',//groupedBy
					//type = optional
				),//chargers and credit
				$this->transaction->getSumByMonth(
					'MONTH(service_and_equipment_bill_issue_date)='.$month.' AND '.'YEAR(service_and_equipment_bill_issue_date)='.$year.' AND '.
					'service_and_equipment_'.$get.' LIKE '. "'".$transaction->{'transaction_'.$get}."'",
					'service_and_equipment',//table
					'service_and_equipment_'.$get.' ',//groupedBy
					//type = optional
				),//service and equipments
			);
		}

		return $report;

	}
	
	
    public function getTotalCostByCostCentre ($month, $year, $cost_centre_id) {
		$cost_centre_ids = array($cost_centre_id);
		$this->load->model('Cost_Centre_Account_model', 'cost_centre_account');
		$cost_centre_accounts = $this->cost_centre_account->join(
			'accounts.account_number as account_number',
			'accounts', 'accounts.account_id = cost_centre_accounts.cost_centre_account_account_id',
			'cost_centre_accounts.cost_centre_account_cost_centre_id', $cost_centre_ids //attribute, array
		);
		
		$accounts = array();
		foreach($cost_centre_accounts as $j => $k){
			$accounts[] = $k->account_number;
		}

		//get service numbers using account ids in transaction_model
		$this->load->model('Transaction_model', 'transaction');
		$transactions = $this->transaction->find_all_where_in( 
			'transaction_type, transaction_account_number', //select
			'transaction_account_number', $accounts, //where in
			'transaction_account_number' //group by
		);
		$cost = 0;
		foreach($transactions as $transaction){
			$cost += $this->transaction->getSumByMonth(
				'MONTH(call_and_usage_bill_issue_date)='.$month.' AND '.'YEAR(call_and_usage_bill_issue_date)='.$year.' AND '.
				'call_and_usage_account_number LIKE '. "'".$transaction->transaction_account_number."'",
				'call_and_usage',//table
				'call_and_usage_account_number ',//groupedBy
			) + $this->transaction->getSumByMonth(
				'MONTH(chargers_and_credit_bill_issue_date)='.$month.' AND '.'YEAR(chargers_and_credit_bill_issue_date)='.$year.' AND '.
				'chargers_and_credit_account_number LIKE '. "'".$transaction->transaction_account_number."'",
				'chargers_and_credit',//table
				'chargers_and_credit_account_number ',//groupedBy
				//type = optional
			) + $this->transaction->getSumByMonth(
				'MONTH(service_and_equipment_bill_issue_date)='.$month.' AND '.'YEAR(service_and_equipment_bill_issue_date)='.$year.' AND '.
				'service_and_equipment_account_number LIKE '. "'".$transaction->transaction_account_number."'",
				'service_and_equipment',//table
				'service_and_equipment_account_number ',//groupedBy
				//type = optional
			);//service and equipments;
		}

		return $cost;

    }

}