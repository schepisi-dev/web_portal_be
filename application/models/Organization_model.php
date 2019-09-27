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
	
	protected $soft_deletes        = TRUE;
	protected $deleted		 		= "organization_deleted";
	protected $deleted_field 		= "organization_deleted_on";

    public function custom_function () {

    }

}