<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends MY_Model {

    protected $table				= "accounts";
	protected $key					= "account_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "account_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "account_modified_on";
	
	protected $soft_deletes        = TRUE;
	protected $deleted		 		= "account_deleted";
	protected $deleted_field 		= "account_deleted_on";

    public function custom_function () {

    }

}