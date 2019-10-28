<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Call_And_Usage_model extends MY_Model {

    protected $table				= "call_and_usages";
	protected $key					= "call_and_usage_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= FALSE;	
	protected $set_modified 		= FALSE;	
	protected $soft_deletes        	= FALSE;

	

}