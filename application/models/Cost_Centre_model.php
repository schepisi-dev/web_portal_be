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
		$cost_centres = $this->find_all(array('cost_centre_organization_id' => $id, 'cost_centre_deleted' => 0));
		foreach($cost_centres as $cost_centre){
			unset($cost_centre->cost_centre_created_on);
			unset($cost_centre->cost_centre_modified_on);
			unset($cost_centre->cost_centre_deleted);
			unset($cost_centre->cost_centre_deleted_on);
		}
		return $cost_centres;
	}
	
	public function save_cost_centre ($action, $post, $organization_id) {		

		if ($action=='add') {
			$this->data['cost_centre_name'] = $post['name'];	
			$this->data['cost_centre_parent_id'] = isset($post['parent_id'])? $post['parent_id'] : 0;	
			$this->data['cost_centre_organization_id'] = $organization_id;
			$result = $this->save();			
		} else if ($action=='edit') {
			$this->data['cost_centre_name'] = $post['name'];	
			$this->data['cost_centre_parent_id'] = isset($post['parent_id'])? $post['parent_id'] : 0;	
			$this->data['cost_centre_organization_id'] = $organization_id;
			$result = $this->update(array($this->key => $post['id']));
		} else if ($action=='delete') {
			$this->data['cost_centre_deleted'] = 1;
			$result = $this->update(array($this->key => $post['id']));
		}

		return $result;
	}
    

}