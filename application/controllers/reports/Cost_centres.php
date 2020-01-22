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
class Cost_centres extends CI_Controller {
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

    public function index_get($action='get', $month=false, $year=false, $cost_centre_id)
    {        
        $response = array();
        
        $this->load->model('Report_model', 'reports');

        if ($action =='get') {
            $response = array(
                'account_numbers' => $this->reports->getTotalByCostCentre($month, $year, $cost_centre_id, 'account_number'),
                'service_numbers' => $this->reports->getTotalByCostCentre($month, $year, $cost_centre_id, 'service_number')
            );
        } else if ($action =='cost') {
            $this->load->model('Cost_Centre_model', 'cost_centre');
            $response =  array(
                'cost' => $this->reports->getTotalCostByCostCentre($month, $year, $cost_centre_id)
            );
        } else if ($action == 'month' OR $action == 'year' ) {
            // $this->load->model('Call_And_usage_model', 'call_and_usage');
            // $this->load->model('Chargers_And_Credit_model', 'chargers_and_credit');
            // $this->load->model('Service_And_Equipment_model', 'service_and_equipment');
            
            // $response = array(
            //     'call_and_usage' => array(
            //         'total' => $this->call_and_usage->{'total'.$action}($month, $year, $cost_centre_ids),
            //     ),
            //     'chargers_and_credit' => array(
            //         'total' => $this->chargers_and_credit->{'total'.$action}($month, $year, $cost_centre_ids),
            //     ),
            //     'service_and_equipment' => array(
            //         'total' => $this->chargers_and_credit->{'total'.$action}($month, $year, $cost_centre_ids),
            //     )
            // );
        } else {
            //
        }

        $this->response( $response, 200 );
    }

    public function index_post( $action='add' ) {

    }

    private function _validate ( $action = "add" ) { 

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