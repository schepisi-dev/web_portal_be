<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_And_Equipment_model extends MY_Model {

    protected $table				= "service_and_equipments";
	protected $key					= "service_and_equipment_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= FALSE;	
	protected $set_modified 		= FALSE;	
	protected $soft_deletes        	= FALSE;


    function categorized($month, $type='excl_gst'){
		// $call_usages = $this->custom_query(
		// 	"SUM(REPLACE(REPLACE(service_and_equipment_".$type.",'.',''),'$','')) as sum, MONTH(service_and_equipment_bill_issue_date) as month, service_and_equipment_service_type as type" , //select
		// 	"MONTH(service_and_equipment_bill_issue_date)=".$month, //where
		// 	"type" , //group_by
		// );
		// $response = array();
		// foreach ($call_usages as $result) {
		// 	$response[] = array(
		// 		// 'month' => $result->month,
		// 		'type'	=> $result->type,
		// 		'total'	=> ($result->sum)/100,
		// 	);
		// }
		// return $response;
	}

	function total($month, $type='excl_gst'){
		$call_usages = $this->custom_query(
			"SUM(REPLACE(REPLACE(service_and_equipment_".$type.",'.',''),'$','')) as sum, MONTH(service_and_equipment_bill_issue_date) as month" , //select
			"MONTH(service_and_equipment_bill_issue_date)=".$month, //where
			"month" , //group_by
		);
		$total = 0;
		foreach ($call_usages as $result) {
			$total	= ($result->sum)/100;
		}
		return $total;
	}

}