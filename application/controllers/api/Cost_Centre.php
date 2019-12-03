<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * EDIT: This is an example of a few basic Cost_Center interaction methods you could use
 * all done with a hardcoded array
 *
 */
class Cost_Centre extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();

        $this->user = $this->_getUser( ($this->get( 'token' )) ? $this->get( 'token' ) : $this->post( 'token' ) );
    }

    public function index_get()
    {
        $this->load->model('Cost_Centre_model', 'cost_centre');        
        $this->response( array(
            'message' => $this->cost_centre->get_by_organization($this->user->user_organization_id)
        ), 200 );
    }

    public function index_post( $action='add' )
    {
        if($this->_validate($action)){
            $this->load->model('Cost_Centre_model', 'cost_centre');
            $response = $this->cost_centre->save_cost_centre($action, $this->post());            
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

    public function account_post($action = 'enroll')
    {
        if($this->_validate($action)){
            $this->load->model('Cost_Centre_Account_model', 'cost_center_account');
            $response = $this->cost_center_account->save_account($action, $this->post());
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
    

    private function _validate ( $action = "enroll" ) { //action allowed: add, update

		if ( $action == "enroll" ) {
			$this->form_validation->set_rules( 'cost_centre_id', 'cost_centre_id', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'account_id', 'account_id', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'percentage', 'percentage', 'strip_tags|trim|required' );
		} else if ( $action == "edit" ) {
			// $this->form_validation->set_rules( 'name', 'name', 'strip_tags|trim|required' );
			// $this->form_validation->set_rules( 'uuid', 'uuid', 'strip_tags|trim|required' );
        }

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }
}        