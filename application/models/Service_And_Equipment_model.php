<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_And_Equipment_model extends MY_Model {

    protected $table				= "service_and_equipments";
	protected $key					= "service_and_equipment_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= FALSE;	
	protected $set_modified 		= FALSE;	
	protected $soft_deletes        	= FALSE;


    public function custom_function () {

    }

}