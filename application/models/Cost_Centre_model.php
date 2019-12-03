<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_Centre_model extends MY_Model {

    protected $table				= "cost_centres";
	protected $key					= "cost_centre_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "cost_centre_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "cost_centre_modified_on";
	
	protected $soft_deletes         = TRUE;
	protected $deleted		 		= "cost_centre_deleted";
	protected $deleted_field 		= "cost_centre_deleted_on";

    public function get_by_organization ($id) {
		$cost_centres = $this->find_all(array('cost_centre_organization_id' => $id));
		foreach($cost_centres as $cost_centre){
			unset($cost_centre->cost_centre_created_on);
			unset($cost_centre->cost_centre_modified_on);
			unset($cost_centre->cost_centre_deleted);
			unset($cost_centre->cost_centre_deleted_on);
		}
		return $cost_centres;
	}
	
	public function save_cost_centre ($action, $post) {		
		$this->data['cost_centre_name'] = $post['name'];

		if ($action=='add') {
			$result = $this->save();			
		} else {
			$result = $this->update(array($this->key => $post['id']));
		}

		return $result;
	}
    

}