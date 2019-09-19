<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configs_model extends MY_Model {

	var $table = 'configs';
	var $id = 'config_id';

	public function get_config ( $value ) {
		$this->db->from( $this->table );
		$this->db->where( 'config_name', $value );
		$query = $this->db->get();
		$row = $query->row();

		return (isset( $row )) ? $row->config_value : FALSE;
	}


}
