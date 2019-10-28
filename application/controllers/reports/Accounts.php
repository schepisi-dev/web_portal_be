<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * EDIT: This is an example of a few basic Account interaction methods you could use
 * all done with a hardcoded array
 *
 */
class Accounts extends CI_Controller {
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
        //$this->user = $this->_getUser( ($this->get( 'token' )) ? $this->get( 'token' ) : $this->post( 'token' ) );
    
    }

    public function index_get($uuid=false)
    {
        $this->load->model('Account_model', 'account');
        $account = $this->account->get_by_attribute('account_uuid', $uuid);
        $response = $this->account->{($uuid)?'get_by_id':'find_all'}($account->account_id);            
        $this->response( array(
            'message' => $response
        ), 200 );
        
    }

    public function test_get()
    {
        $this->load->model('transaction_model', 'transaction');
        $response = $this->transaction->get_sum_by_type();            
        $this->response( array(
            'message' => $response
        ), 200 );
        
    }

    public function index_post( $action='add' )
    {
        $this->_checkPermission($this->user);
        if($this->_validate($action)){
            $this->load->model('Account_model', 'account');
            $account = array_merge($this->post(), array('organization_id' => $this->user->user_organization_id ));
            //set organization id based on the logged in user
            //check user if logged in as standard user
            $response = $this->account->save_account($action, $account);            
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

    private function _validate ( $action = "add" ) { //action allowed: add, update

		if ( $action == "add" ) {
			$this->form_validation->set_rules( 'name', 'name', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'number', 'number', 'strip_tags|trim|required' ); //must be unique
		} else if ( $action == "edit" ) {
			// $this->form_validation->set_rules( 'name', 'name', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'uuid', 'uuid', 'strip_tags|trim|required' );
        }

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }

    private function _checkPermission($user){
        $withError = FALSE;

        if($user->user_role!='standard'){
            $error[] = 'User is not a Standard User!';
            $withError = TRUE;
        }

        if ($withError){
            $this->response( array(
                'errors' => $error
            ), 400 );
        }

    }
}        