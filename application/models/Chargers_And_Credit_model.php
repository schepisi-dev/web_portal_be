<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chargers_And_Credit_model extends MY_Model {

    protected $table				= "chargers_and_credits";
	protected $key					= "chargers_and_credit_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "chargers_and_credit_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "chargers_and_credit_modified_on";
	
	protected $soft_deletes        = TRUE;
	protected $deleted		 		= "chargers_and_credit_deleted";
	protected $deleted_field 		= "chargers_and_credit_deleted_on";

    public function custom_function () {

    }

}