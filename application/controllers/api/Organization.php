<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * EDIT: This is an example of a few basic Organization interaction methods you could use
 * all done with a hardcoded array
 *
 */
class Organization extends CI_Controller {
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

    
    public function count_get()
    {
        $this->load->model('Organization_model');
        $response = $this->Organization_model->find_all(0, 0);
        $breakdown = $this->Organization_model->get_count_breakdown();
        $this->response( array(
            'message' => array(
                'count' => count($response),
                'breakdown' => $breakdown
                )
        ), 200 );
        
    }

    public function index_get($id=false)
    {
        //TODO: if logged in user is standard/basic, always set org id as the logged in user
        $this->load->model('Organization_model', 'organization');
        $response = $this->organization->{($id)?'get_by_id':'find_all'}($id);
        $this->response( array(
            'message' => $response
        ), 200 );
        
    }

    public function index_post( $action='add' )
    {
        if($this->_validate($action)){
            $this->load->model('Organization_model', 'organization');
            $response = $this->organization->save_organization($action, $this->post());
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

    public function test_delete( $action='add' )
    {
        $this->response( array(
			'message' => 'Deleted'
		), 400 );

    }

    private function _validate ( $action = "add" ) { //action allowed: add, edit, delete

		if ( $action == "add" ) {
            //required fields upon user creation = user role, user username, user password
			$this->form_validation->set_rules( 'name', 'name', 'strip_tags|trim|required|is_unique[organizations.organization_name]' );
		} else if ( $action == "edit" ) {
			$this->form_validation->set_rules( 'name', 'name', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'id', 'id', 'strip_tags|trim|required' );
        } else if ( $action == "delete" ) {
			$this->form_validation->set_rules( 'id', 'id', 'strip_tags|trim|required' );
        }

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }
}        