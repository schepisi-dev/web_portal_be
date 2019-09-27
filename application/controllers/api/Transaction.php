<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * EDIT: This is an example of a few basic Transaction interaction methods you could use
 * all done with a hardcoded array
 *
 */
class Transaction extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public $user;
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

    public function index_get()
    {
        if($this->get('type')){
            $this->load->model('Transaction_model', 'transaction');
            $response = $this->transaction->get_transactions($this->get('type'));
            $this->response( array(
                'message' => $response
            ), 200 );
        } else {
			$error = "Type is required.";
		}
		$this->response( array(
			'message' => $error
		), 400 );
    }

    public function index_post($action='add')
    {
        if($this->_validate($action)){
            $this->load->model('Transaction_model', 'transaction');

            $response = $this->transaction->save_transaction($this->post());
            
            $this->response( array(
                'message' => $response
            ), 200 );
        } else {
			$error = validation_errors();
		}
		$this->response( array(
			'message' => $error
		), 400 );

    }

    
    private function _validate ( $action = "add" ) {

		if ( $action == "add" ) {
			$this->form_validation->set_rules( 'json', 'json', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'type', 'type', 'strip_tags|trim|required' );
		} else if ( $action == "get" ) {            
			$this->form_validation->set_rules( 'type', 'type', 'strip_tags|trim|required' );
        }

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }
}        