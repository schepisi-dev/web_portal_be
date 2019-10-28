<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chargers_And_Credit_model extends MY_Model {

    protected $table				= "chargers_and_credits";
	protected $key					= "chargers_and_credit_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= FALSE;	
	protected $set_modified 		= FALSE;	
	protected $soft_deletes        	= FALSE;


	function get_sum(){
		
	}

}