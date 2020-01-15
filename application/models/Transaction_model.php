<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends MY_Model {

    protected $table                = 'transactions';
	protected $key					= "transaction_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= FALSE;	
	protected $set_modified 		= FALSE;	
	protected $soft_deletes        	= FALSE;

    public function save_transaction ($post) {
        $transactions = json_decode($post['json'], true);
        $result = $saved = array();
        $type = $post['type'];
        $this->load->model($type."_model", $type);
        $date_synced = date("D-m-y H:i:s");
        foreach($transactions as $transaction){
            $default = array(                
                $type.'_account_number' => (isset($transaction['Account Number']))?$transaction['Account Number']:$transaction['AccountNumber'], 
                $type.'_bill_issue_date' => date('Y-m-d', strtotime(str_replace('/', '-', $transaction['Bill Issue Date']))),
                $type.'_bill_number' => $transaction['Bill Number'], 
                $type.'_service_number' => $transaction['Service Number'],               
                $type.'_excl_gst' => $transaction['Excl GST $'], 
                $type.'_gst' => $transaction['GST $'], 
                $type.'_incl_gst' => $transaction['Incl GST $']
            );

            $details = array();
            if($type == 'call_and_usage') {
                $details = array(
                    'call_and_usage_service_owner' => $transaction['Service Owner'],
                    'call_and_usage_time' => $transaction['Time'],
                    'call_and_usage_date' => date('Y-m-d', strtotime(str_replace('/', '-', $transaction['Date']))),
                    'call_and_usage_called_number' => $transaction['Called Number'],
                    'call_and_usage_type' => $transaction['Call & Usage Type'],
                    'call_and_usage_duration' => $transaction['Duration (hh:mm:ss)']
                );
            } else if($type == 'chargers_and_credit'){
                $details = array(
                    'chargers_and_credit_quantity' => $transaction['Quantity'],
                    'chargers_and_credit_transaction_date'=> date('Y-m-d', strtotime(str_replace('/', '-', $transaction['Transaction Date']))),
                    'chargers_and_credit_occ_description' => $transaction['OCC Description']
                );
            } else if($type == 'service_and_equipment'){
                $details = array(
                    'service_and_equipment_quantity' => $transaction['Quantity'],
                    'service_and_equipment_service_owner'=> $transaction['Service Owner'],
                    'service_and_equipment_charge_type_description' => $transaction['Charge Type Description'],
                    'service_and_equipment_service_type' => $transaction['Service Type']
                );
            }
            
            $result[] = array_merge($default, $details);

        }

        //optimize this part
        //update saving of datetime and time
        //add checking if duplicate entry on upload
        //implement rollback upload
        //check if user is sending transaction type

        $saved_transaction = array();
        foreach ($result as $entry){
            $saved_entry = $this->{$type}->save($entry);
            $this->data = array(
                'transaction_uuid' => md5( getToken( 25 ) ),
                'transaction_type' => $type,
                'transaction_table_id' => $saved_entry->{$type.'_id'},
                'transaction_account_number' => $entry[$type.'_account_number'],
                'transaction_service_number' => $entry[$type.'_service_number'],
                'transaction_organization_id' => $post['organization_id']
            );
            $saved = $this->save();
            $saved_transaction[] = $saved;
        }
        //insert saved data
        return $saved_transaction;

    }

    function get_transactions($type, $user, $offset = 0, $limit = 10){

        //must be from the group by the user logged in

        $this->load->model("chargers_and_credit_model", 'chargers_and_credit');
        $this->load->model("service_and_equipment_model", 'service_and_equipment');
        $this->load->model("call_and_usage_model", 'call_and_usage');

        $return_array = array();
        if(in_array($type, array('call_and_usage','chargers_and_credit','service_and_equipment'))){ //if type is given
            $transactions = $this->find(array('transaction_type' => $type), $offset, $limit);
            if($transactions){                
                foreach($transactions as $transaction){
                    $return_array[] = $this->{$type}->get_by_id($transaction->transaction_table_id);
                }
            }
        } else {
            $transactions = $this->find_all( (int)$offset, (int)$limit);
            if($transactions){
                foreach($transactions as $transaction){
                    $return_array[] = $transaction/*$this->{$transaction->transaction_type}->get_by_id($transaction->transaction_table_id)*/;
                }
            }
        }

        return $return_array;

    }

    function _get_table_values($type){

        switch($type){
            case 'call_and_usage':
                $values = array('call_usage_account_number', 'call_usage_issue_date', 'call_usage_service_number', 'call_usage_service_owner',
                                'call_usage_date', 'call_usage_time', 'call_usage_called_number', 'call_usage_type', 'call_usage_duration',
                                'call_usage_excl_gst', 'call_usage_gst', 'call_usage_incl_gst');
                break;
            default:
                $values = array();
                break;
        }

        return $values;

    }

    function getSumByMonth($where = false, $table = 'call_and_usages', $groupedBy, $type='gst'){

		$this->db->select("SUM(REPLACE(REPLACE(".$table."_".$type.",'.',''),'$','')) as sum,
            MONTH(".$table."_bill_issue_date) as month, YEAR(".$table."_bill_issue_date) as year");
        $this->db->group_by($groupedBy);
        if ( $where ) $this->db->where( $where );
		$query = $this->db->get($table.'s');
        $response[] = 0;
		foreach ($query->result_array() as $result) {
			$response[] = $result['sum']/100;
		}
		return end($response);
	}

    function get_sum_by_month($type='gst', $where = false, $table = 'call_and_usages'){

		$this->db->select("REPLACE(REPLACE(".$table."_".$type.",'.',''),'$','') as sum,
								MONTH(".$table."_bill_issue_date) as month");
        $this->db->group_by('month');
        if ( $where ) $this->db->where( $where );
		$query = $this->db->get($table);
		$response = array($table => array());
		foreach ($query->result_array() as $result) {
			$response[$table][] = array(
				'month' => $result['month'],
				'sum'	=> $result['sum']/100
			);
		}
		return $response;
	}

    function get_sum_by_type($type='gst'){

        $response = array('call_and_usage' => array(),'chargers_and_credit' => array());
        
		$this->db->select("REPLACE(REPLACE(call_and_usage_".$type.",'.',''),'$','') as sum,
								call_and_usage_type as type");
		$this->db->group_by('type');
		$query = $this->db->get('call_and_usages');
		foreach ($query->result_array() as $result) {
			$response['call_and_usage_2'][] = array(
				'type' => $result['type'],
				'sum'	=> $result['sum']/100
			);
		}
        $this->db->reset_query();
        
		$this->db->select("REPLACE(REPLACE(chargers_and_credit_".$type.",'.',''),'$','') as sum,
                                        chargers_and_credit_occ_description as type");
		$this->db->group_by('type');
		$query = $this->db->get('chargers_and_credits');
		foreach ($query->result_array() as $result) {
			$response['chargers_and_credit'][] = array(
				'type' => $result['type'],
				'sum'	=> $result['sum']/100
			);
		}
        $this->db->reset_query();


		//add saving cache
		return $response;
    }
    
    function get_transactions_by_service_number($service_number){

    }

}