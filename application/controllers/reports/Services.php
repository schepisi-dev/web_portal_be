<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * EDIT: This is an example of a few basic Services interaction methods you could use
 * all done with a hardcoded array
 *
 */
class Services extends CI_Controller {
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

    }

    public function index_post($action = 'generate')
    {
        if($this->_validate($action)){
            $this->load->model('Report_model', 'report');
            $response = $this->report->getServiceReportByCostCentre($this->post('cost_centre_id'));            
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
    
    public function account_get($organization_id)
    {
        $organization_id = isset($organization_id)? $organization_id: $this->user->user_organization_id;
        $this->load->model('Cost_Centre_model', 'cost_centre');
        
        $this->response( array(
            'message' => $this->cost_centre->get_by_organization($organization_id)
        ), 200 );
        
    }

    private function _validate ( $action = "generate" )
    {
        if ( $action == "generate" ) {
			$this->form_validation->set_rules( 'cost_centre_id', 'cost_centre_id', 'strip_tags|trim|required' );
		} else if ( $action == "" ) {

        }

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }

    private function _checkPermission($user) {
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