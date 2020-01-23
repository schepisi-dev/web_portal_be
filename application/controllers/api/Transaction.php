<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Transaction extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public $user;
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

    public function index_get()
    {
        $this->load->model('Transaction_model', 'transaction');
        $response = $this->transaction->get_transactions($this->get('type'), $this->user, $this->get('offset'), $this->get('limit'));
        $this->response( array(
            'message' => $response
        ), 200 );
    }

    public function index_post($action='add')
    {
        if($this->_validate($action)){
            $this->load->model('Transaction_model', 'transaction');

            $response = $this->transaction->save_transaction($this->post());
            //save to file history
            
            $file_history['info'] = array(
                'type' => $this->post('type'),
                'first_id' => $response[0]->transaction_uuid,
                'last_id' => end($response)->transaction_uuid,
                'date_uploaded' => date('Y-m-d H:i:s')
            );
            $file_history['type'] = $this->post('type');
            $file_history['organization_id'] = $this->post('organization_id');
            $file_history['uploaded_by'] = $this->user->user_first_name.' '.$this->user->user_last_name;

            $this->load->model('File_History_model', 'file_history');
            $this->file_history->save_history($file_history);

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

    
    private function _validate ( $action = "add" ) {

		if ( $action == "add" ) {
			$this->form_validation->set_rules( 'json', 'json', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'type', 'type', 'strip_tags|trim|required' );
			$this->form_validation->set_rules( 'organization_id', 'organization_id', 'strip_tags|trim|required' );
		} else if ( $action == "get" ) {            
			$this->form_validation->set_rules( 'type', 'type', 'strip_tags|trim|required' );
        }

		$this->form_validation->set_error_delimiters( '', '' );
		if ( $this->form_validation->run( $this ) == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
    }
}        