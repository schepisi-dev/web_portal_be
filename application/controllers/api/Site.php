<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

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

    function index_get () {
		// do not remove this

		show_404();
    }

    public function login_get () {
        $user = $this->_getUser( $this->input->get( 'token' ) );
		if ( $user ) {
			$this->response( array(
				'response' => $user
			), 200);
		} else {
            echo $this->input->get( 'token' );
        }
	}

	public function login_post () {
		if ( $this->_validate( 'login' ) ) {
			$username = $this->input->post( 'username' );
			$password = $this->input->post( 'password' );

            if($resp = $this->_validate_credentials()){                
                $this->load->model("Access_token_model");
                //get user using username, check if Organization is still active
                //if role is admin, disregard
                $this->response( array(
                    'message' => $resp,
					'token'    => $this->Access_token_model->get_token( $username )
                ), 200 );
            } else {
                $error = "Provided information was incorrect. Kindly provide the correct information to continue.";
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
            $user = $this->_getUserByUsername($this->post( 'username' ));
            $this->load->model('Organization_model');
			$organization = $this->Organization_model->get_by_id($user->user_organization_id);
            $data = array(
                'username' => $this->post( 'username' ),
                'is_logged_in' => TRUE,
                'date_logged_in' => date( "Y-m-d H:i:s" ),
                'organization_id' => $user->user_organization_id,
                'organization_name' => isset($organization->organization_name)? $organization->organization_name: 'Admin',
                'role' => $user->user_role
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

    private function _getUserByUsername( $username ){
        $this->load->model('User_model', 'user');
        $user_logged_id = $this->user->get_by_attribute('user_username', $username);

        return $user_logged_id;
    }


}        