<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

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
        $this->user = $this->_getUser( ($this->get( 'token' )) ? $this->get( 'token' ) : $this->post( 'token' ) );
    
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

    public function month_get($month = FALSE, $year = FALSE)
    {
        $month = ($month)? $month: date('m');
        $year = ($year)? $year: date('Y');
        $this->load->model('Call_And_usage_model', 'call_and_usage');
        $this->load->model('Chargers_And_Credit_model', 'chargers_and_credit');
        $this->load->model('Service_And_Equipment_model', 'service_and_equipment');
        
        $this->response( array(
            'call_and_usage' => array(
                'total' => $this->call_and_usage->total($month, $year), 
                'types' => $this->call_and_usage->categorized($month, $year)
            ),
            'chargers_and_credit' => array(
                'total' => $this->chargers_and_credit->total($month, $year), 
                'types' => $this->chargers_and_credit->categorized($month, $year)
            ),
            'service_and_equipment' => array(
                'total' => $this->service_and_equipment->total($month, $year), 
                'types' => $this->service_and_equipment->categorized($month, $year)
            )
        ), 200 );
        
    }

    public function year_get($year = FALSE)
    {
        $year = ($year)? $year: date('Y');
        $this->load->model('Call_And_usage_model', 'call_and_usage');
        $this->load->model('Chargers_And_Credit_model', 'chargers_and_credit');
        $this->load->model('Service_And_Equipment_model', 'service_and_equipment');
        $month_array = array('1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May',
                            '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October',
                            '11' => 'November', '12' => 'December');
        
        $response = array();
        foreach ($month_array as $k => $value) {

            $response[] = array(
                $value,
                $this->call_and_usage->total($k, $year),
                $this->chargers_and_credit->total($k, $year),
                $this->service_and_equipment->total($k, $year)
            );
        }
        $this->response( $response, 200 );
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