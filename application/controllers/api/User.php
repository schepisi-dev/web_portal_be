<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

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
        if ($this->user->user_role){}
        
    }

    public function count_get()
    {
        $this->load->model('User_model');
        $response = $this->User_model->find_all(0, 0);
        $breakdown = $this->User_model->get_count_breakdown();
        $this->response( array(
            'message' => array(
                'count' => count($response),
                'breakdown' => $breakdown
            )
        ), 200 );
        
    }

    public function index_get($username=false)
    {
        $this->load->model('User_model');
        $response = $this->User_model->_find_all($this->get('offset'), $this->get('limit'), $this->user->user_organization_id);            
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
        //if admin, set organization id as 0
        $this->form_validation->set_rules( 'first_name', 'first_name', 'strip_tags|trim|required');
        $this->form_validation->set_rules( 'last_name', 'last_name', 'strip_tags|trim|required');

		if ( $action == "add" ) {
            $this->form_validation->set_rules( 'username', 'username', 'strip_tags|trim|required|is_unique[users.user_username]');
            $this->form_validation->set_rules( 'password', 'password', 'strip_tags|trim|required'/*|callback_password_check*/);
            $this->form_validation->set_rules( 'email', 'email', 'strip_tags|trim|required|valid_email|is_unique[users.user_email]');
            $this->form_validation->set_rules( 'role', 'role', 'strip_tags|trim|required');
            $this->form_validation->set_rules( 'organization_id', 'organization_id', 'strip_tags|trim|required|numeric|callback_organization_check[]');
		} else if ( $action == "edit" ) {
            $this->form_validation->set_rules( 'id', 'id', 'strip_tags|trim|required|callback_user_check[]' );
            $this->form_validation->set_rules( 'email', 'email', 'strip_tags|trim|required|valid_email|callback_email_check['. $this->post('id') .']');
            $this->form_validation->set_rules( 'username', 'username', 'strip_tags|trim|required|callback_username_check['. $this->post('id') .']');
            //validate is user id is valid
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
    
    public function user_check($id=null)
    {
        $this->load->model('User_model');
        $user = $this->User_model->get_by_attribute('user_id', $id);
        if ($user){
            return TRUE; 
        } else {
            $this->form_validation->set_message('user_check', 'User does not exists');
            return FALSE;
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

    public function username_check($username, $id=null)
    {
        $this->load->model('User_model');
        $user = $this->User_model->get_by_attribute('user_username', $username);
        if($user){
            if($id==null OR $user->user_id != $id){
                $this->form_validation->set_message('username_check', 'username already exists');
                return FALSE;
            }
        } 
        return TRUE;
    }

    public function organization_check($id=null)
    {
        $this->load->model('Organization_model');
        $organization = $this->Organization_model->get_by_attribute('organization_id', $id);
        if ($organization){
            if($organization->organization_deleted == 1){            
                $this->form_validation->set_message('organization_check', 'Organization already deleted');
                return FALSE;
            } 
        } else {
            $this->form_validation->set_message('organization_check', 'Organization does not exists');
                return FALSE;
        }
        return TRUE;
    }

    public function email_check($email, $id=null)
    {
        $this->load->model('User_model');
        $user = $this->User_model->get_by_attribute('user_email', $email);
        if($user){
            if($id==null OR $user->user_id != $id){
                $this->form_validation->set_message('email_check', 'email already exists');
                return FALSE;
            }
        } 
        return TRUE;
    }
}        