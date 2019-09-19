<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migrate extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('migration');
	}
	
	public function index()
	{
		if (! $this->migration->current()) 
		{
			show_error($this->migration->error_string());
		}
		else 
		{
			echo 'Done';
		}
	}
	public function rollback($version = FALSE)
	{
		if (! $version) show_404();
		if (! $this->migration->version($version)) 
		{
			show_error($this->migration->error_string());
		}
		else 
		{
			echo 'Done';
		}
	}
}