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

        $this->user = $this->_getUser( ($this->get( 'token' )) ? $this->get( 'token' ) : $this->post( 'token' ) );
        
    }

    public function index_get($username=false)
    {
        $this->load->model('User_model');
        $response = $this->User_model->_find_all();            
        $this->response( array(
            'message' => $response
        ), 200 );
        
    }

    public function index_post($action='add')
    {
        if($this->_validate($action)){
            $this->load->model('User_model');
            $response = $this->User_model->save_user($action, $this->post());
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

    public function password_post($action = "change")
    {

    }

    
    private function _validate ( $action = "add" )
    { 
        //action allowed: add, update        
        //upon saving user, check if user role is not admin
        //if not admin, require organization id
        //username,email must be unique
        //check organization if valid

        $this->form_validation->set_rules( 'username', 'username', 'strip_tags|trim|required' );
        $this->form_validation->set_rules( 'first_name', 'first_name', 'strip_tags|trim|required' );
        $this->form_validation->set_rules( 'last_name', 'last_name', 'strip_tags|trim|required' );
        $this->form_validation->set_rules( 'email', 'email', 'strip_tags|trim|required|valid_email' );
        $this->form_validation->set_rules( 'role', 'role', 'strip_tags|trim|required' );
        $this->form_validation->set_rules( 'organization_id', 'organization_id', 'strip_tags|trim|required|numeric' );

		if ( $action == "add" ) {
            $this->form_validation->set_rules( 'password', 'password', 'strip_tags|trim|required'/*|callback_password_check*/ );
		} else if ( $action == "edit" ) {
            //validate is user id is valid
            $this->form_validation->set_rules( 'id', 'id', 'strip_tags|trim|required' );
        }

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }

    private function _checkPermission($user)
    {
        $withError = FALSE;

        if($user->user_role!='administrator'){
            $error[] = 'User is not an Administrator!';
            $withError = TRUE;
        }

        if ($withError){
            $this->response( array(
                'errors' => $error
            ), 400 );
        }

    }

    public function password_check($password)
    {
        $result = checkPassword($password);
        if($result){
            return TRUE;
        } else {
            $this->form_validation->set_message('password_check', $result);
            return FALSE;
        }
    }
}        