<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_And_Equipment_model extends MY_Model {

    protected $table				= "service_and_equipments";
	protected $key					= "service_and_equipment_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "service_and_equipment_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "service_and_equipment_modified_on";
	
	protected $soft_deletes         = TRUE;
	protected $deleted		 		= "service_and_equipment_deleted";
	protected $deleted_field 		= "service_and_equipment_deleted_on";

    public function custom_function () {

    }

}