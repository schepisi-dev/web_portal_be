<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_History_model extends MY_Model {

    protected $table				= "file_histories";
	protected $key					= "file_history_id";
	protected $date_format			= "datetime";
	
	protected $set_created			= TRUE;
	protected $created_field 		= "file_history_created_on";
	
	protected $set_modified 		= TRUE;
	protected $modified_field 		= "file_history_modified_on";
	
	protected $soft_deletes         = TRUE;
	protected $deleted		 		= "file_history_deleted";
	protected $deleted_field 		= "file_history_deleted_on";

    public function save_history ($info) {
		$this->data['file_history_info'] = serialize($info['info']);
		$this->data['file_history_type'] = $info['type'];
		$this->data['file_history_organization_id'] = $info['organization_id'];
		$this->data['file_history_uploaded_by'] = $info['uploaded_by'];

		$result = $this->save();

		return $result;

	}
	
	public function get_history ($type=FALSE){
		
		$this->db->from( $this->table );
		($type)? $this->db->where( 'file_history_type', $type ):'';
		$query = $this->db->get();

		$response = array();
		
		foreach ($query->result() as $row){
			$info = unserialize($row->file_history_info);
			$response[] = array(
				'id' => $row->file_history_id,
				'date_uploaded' => $info['date_uploaded'],
				'uploaded_by' => $row->file_history_uploaded_by,
				'type' => $info['type'],
				'info' => $info
			);
		}
		return $response;
	}
	
	public function get_notifications ($organization_id){
		
		$this->db->from( $this->table );
		($organization_id==0)? '':$this->db->where( 'file_history_organization_id', $organization_id );
		$this->db->where( 'MONTH(file_history_created_on) = '.date('m').' AND YEAR(file_history_created_on) = '.date('Y').' ');
		$query = $this->db->get();

		$response = array();
		
		foreach ($query->result() as $row){
			$info = unserialize($row->file_history_info);
			$response[] = array(
				'date_uploaded' => $info['date_uploaded'],
				'uploaded_by' => $row->file_history_uploaded_by,
				'type' => $info['type']
			);
		}
		return $response;
	}

}