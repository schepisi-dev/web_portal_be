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

    public function index_get($uuid=false)
    {
        
        
    }

    public function account_get($organization_id)
    {
        $this->load->model('Cost_Centre_model', 'cost_centre');
        
        $this->response( array(
            'message' => $this->cost_centre->get_by_organization($organization_id)
        ), 200 );
        
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