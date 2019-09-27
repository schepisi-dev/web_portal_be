<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends MY_Model {

    var $table = 'transactions';
    var $id = 'transaction_id';

    public function save_transaction ($post) {
        $transactions = json_decode($post['json'], true);
        $result = $saved = array();
        $type = $post['type'];
        $this->load->model($type."_model", $type);
        $date_synced = date("D-m-y H:i:s");
        foreach($transactions as $transaction){
            $default = array(                
                $type.'_account_number' => $transaction['Account Number'], 
                $type.'_bill_issue_date' => $transaction['Bill Issue Date'], 
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
                    'call_and_usage_date' => $transaction['Date'],
                    'call_and_usage_called_number' => $transaction['Called Number'],
                    'call_and_usage_type' => $transaction['Call & Usage Type'],
                    'call_and_usage_duration' => $transaction['Duration (hh:mm:ss)']
                );
            } else if($type == 'chargers_and_credit'){
                $details = array(
                    'chargers_and_credit_quantity' => $transaction['Quantity'],
                    'chargers_and_credit_transaction_date'=> $transaction['Transaction Date'],
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

        foreach ($result as $entry){
            $saved_entry = $this->{$type}->save($entry);
            $transactions = array(
                'transaction_uuid' => 'uuid1234',
                'transaction_type' => $type,
                'transaction_table_id' => $saved_entry->{$type.'_id'},
                'transaction_account_number' => $entry[$type.'_account_number'],
            );
            $saved[] = $this->save($transactions);
        }
        return $saved;

    }

    function get_transactions($type, $group = '', $month = ''){

        //must be from the group by the user logged in

        $transactions = $this->find(array('transaction_type' => $type));
        $this->load->model($type."_model", $type);
        $return_array = array();
        foreach($transactions as $transaction){
            $return_array[] = $this->{$type}->get_by_id($transaction->transaction_table_id);
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

}