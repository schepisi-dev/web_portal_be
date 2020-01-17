<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * EDIT: This is an example of a few basic File_Histories interaction methods you could use
 * all done with a hardcoded array
 *
 */
class File_Histories extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();

        // Configure limits and level on our controller methods                   
        $this->methods = array(
         'index_get'  => array( 'level' => 10, 'limit' => 500 ), //select
         'index_post' => array( 'level' => 5, 'limit' => 50 ), //add, edit
        );
        
        $this->user = $this->_getUser( ($this->get( 'token' )) ? $this->get( 'token' ) : $this->post( 'token' ) );
    }

    public function index_get($type=FALSE)
    {
        $this->load->model('File_History_model', 'file_history');        
        $this->response( array(
            'message' => $this->file_history->get_history($type)
        ), 200 );
    }

    public function notifications_get()
    {
        $this->load->model('File_History_model', 'file_history');        
        $this->response( array(
            'message' => $this->file_history->get_notifications($this->user->user_organization_id)
        ), 200 );
    }

    public function index_post()
    {
        error_404();
    }
}        