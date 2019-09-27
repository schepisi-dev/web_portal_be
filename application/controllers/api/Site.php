<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * EDIT: This is an example of a few basic Site interaction methods you could use
 * all done with a hardcoded array
 *
 */
class Site extends CI_Controller {
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

        $this->load->model("User_model", "users");
    }

    public function login_get()
    {
        $this->response('method called', 200);
    }

    // function index_get () {
	// 	// do not remove this

	// 	show_404();
	// }

	public function login_post () {
		if ( $this->_validate( 'login' ) ) {
			$username = $this->input->post( 'username' );
			$password = $this->input->post( 'password' );

            //
            if($resp = $this->_validate_credentials()){                
                $this->load->model("Access_token_model");
                $this->response( array(
                    'message' => $resp,
					'token'    => $this->Access_token_model->get_token( $username )
                ), 200 );
            } else {
                $error = "Incorrect Credentials. Please try again.";
            }
		} else {
			$error = validation_errors();
		}
		$this->response( array(
			'message' => $error
		), 400 );

    }
    
    private function _validate ( $action = "login" ) {

		if ( $action == "login" ) {
			$this->form_validation->set_rules( 'username', 'username', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'password', 'password', 'strip_tags|trim|required' );
		} else if ( $action == "logout" ) $this->form_validation->set_rules( 'token', 'token', 'required' );

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }
    
    private function _validate_credentials () {
        date_default_timezone_set( 'Asia/Manila' );

        if ( $this->users->login($this->input->post('username'), $this->input->post('password'))) {
            $data = array(
                'username'       => $this->input->post( 'username' ),
                'is_logged_in'   => TRUE,
                'date_logged_in' => date( "Y-m-d H:i:s" ),
            );

            return $data;
        }

        return FALSE;
    }

    function _checkWhitespace($str){
        if ( preg_match('/^\s*$/',$str) ) {
            $this->form_validation->set_message( '_checkWhitespace', 'Must not contain whitespaces!' );
            return FALSE;
        } else {
            return TRUE;
        }
    }


}        