<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * EDIT: This is an example of a few basic User interaction methods you could use
 * all done with a hardcoded array
 *
 */
class User extends CI_Controller {
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
    }

    public function index_get($password = 'Test1234')
    {
        pr(checkPassword($password));

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

    public function password_post($action = "change"){

    }

    
    private function _validate ( $action = "add" ) { //action allowed: add, update

		if ( $action == "add" ) {
            //required fields upon user creation = user role, user username, user password
			$this->form_validation->set_rules( 'json', 'json', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'type', 'type', 'strip_tags|trim|required' );
		} else if ( $action == "" ) ;

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }
}        