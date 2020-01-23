<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * Basig config request
 *
 */
class Config extends CI_Controller {
    
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public $user;
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->__resTraitConstruct();
        
        $this->methods = array(
                'index_get'  => array( 'level' => 10, 'limit' => 500 ), //select
                'index_post' => array( 'level' => 5, 'limit' => 50 ), //add, edit
            );
        $this->load->model( 'Configs_model', 'configs' );
        $this->user = $this->_getUser( ($this->input->get( 'token' )) ? $this->input->get( 'token' ) : $this->input->post( 'token' ) );
		
    }

    public function index_get () {
        $this->user;
        $name = $this->input->get( 'config_name' );
        $response = $this->configs->get_config( $name );
        $this->response( $response, 200 );
    }

    public function index_post () {
        $action = $this->input->post( 'action' );
        $value = $this->input->post( 'config_value' );
        $name = $this->input->post( 'config_name' );

        $data = array(
            'config_name'  => $name,
            'config_value' => $value,
        );

        $response = array();
        switch ( $action ) {
            case 'add':
                $response = $this->configs->save( $data );
                break;
            case 'edit':
                $response = $this->configs->update( array( 'config_name' => $name ), $data );
                break;
            default:
        }

        $this->response( $response, 200 );
    }
}        