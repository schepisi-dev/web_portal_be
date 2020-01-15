<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

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
        $cost_centres = $this->cost_centre->get_by_organization($this->user->user_organization_id);
        $this->response( array(
            'message' => $this->_buildTree($cost_centres)
        ), 200 );
    }

    public function index_post( $action='add' )
    {
        $user = $this->user;
        if($this->_validate($action)){
            $this->load->model('Cost_Centre_model', 'cost_centre');
            $response = $this->cost_centre->save_cost_centre($action, $this->post(), $user->user_organization_id);            
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
			$this->form_validation->set_rules( 'name', 'name', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'id', 'id', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'parent_id', 'parent_id', 'strip_tags|trim|required' );
        } else if ( $action == "add" ) {
			$this->form_validation->set_rules( 'name', 'name', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'parent_id', 'parent_id', 'strip_tags|trim|required' );
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

    private function _buildTree(array $elements, $parentId = 0) {
        $branch = array();
    
        foreach ($elements as $j => $element) {
            if ($element->cost_centre_parent_id == $parentId) {
                $children = $this->_buildTree($elements, $element->cost_centre_id);
                if ($children) {
                    $element->cost_centre_children = $children;
                } else {
                    $element->cost_centre_children = array();
                }
                unset($element->cost_centre_organization_id);
                $branch[] = $element;
            }
        }
    
        return $branch;
    }
}        