<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organization_model extends MY_Model {

    protected $table				= "organizations";
	protected $key					= "organization_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "organization_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "organization_modified_on";
	
	protected $soft_deletes         = TRUE;
	protected $deleted		 		= "organization_deleted";
	protected $deleted_field 		= "organization_deleted_on";

	
    public function save_organization ($action, $post) {		
		$this->data['organization_name'] = $post['name'];

		if ($action=='add') {
			$result = $this->save();			
		} else {
			$result = $this->update(array($this->key => $post['id']));
		}

		return $result;

	}
	
    public function get_count_breakdown () {		
		$breakdown = $this->custom_query(
			"COUNT(organization_id) as count, MONTH(organization_created_on) as month" , //select
			"YEAR(organization_created_on) = ". date('Y'), //where
			"MONTH(organization_created_on)" , //group_by
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