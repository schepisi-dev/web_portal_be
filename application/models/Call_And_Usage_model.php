<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Call_And_Usage_model extends MY_Model {

    protected $table				= "call_and_usages";
	protected $key					= "call_and_usage_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "call_and_usage_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "call_and_usage_modified_on";
	
	protected $soft_deletes        = TRUE;
	protected $deleted		 		= "call_and_usage_deleted";
	protected $deleted_field 		= "call_and_usage_deleted_on";

    public function custom_function () {

    }

}