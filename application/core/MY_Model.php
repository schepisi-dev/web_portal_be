<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class MY_Model extends CI_Model {
		
		var $data = array();

		public function __construct () {
			parent::__construct();
			$this->load->database();
		}

		public function _get_datatables_query () {

			$this->db->from( $this->table );

			$i = 0;

			foreach ( $this->column as $item ) {
				if ( $_POST['search']['value'] ) ($i === 0) ? $this->db->like( $item, $_POST['search']['value'] ) : $this->db->or_like( $item, $_POST['search']['value'] );
				$column[ $i ] = $item;
				$i++;
			}

			if ( isset( $_POST['order'] ) ) {
				$this->db->order_by( $column[ $_POST['order']['0']['column'] ], $_POST['order']['0']['dir'] );
			} else if ( isset( $this->order ) ) {
				$order = $this->order;
				$this->db->order_by( key( $order ), $order[ key( $order ) ] );
			}
		}

		public function get_datatables () {
			$this->_get_datatables_query();
			if ( $_POST['length'] != -1 ) $this->db->limit( $_POST['length'], $_POST['start'] );
			$query = $this->db->get();
			return $query->result();
		}


		public function count_filtered () {
			$this->_get_datatables_query();
			$query = $this->db->get();
			return $query->num_rows();
		}

		public function count_all () {
			$this->db->from( $this->table );
			return $this->db->count_all_results();
		}

		public function find ( $criteria, $offset = 0, $limit = 10 ) {
			$query = $this->db->get_where( $this->table, $criteria, $limit, $offset );
			if ( $query->result() != NULL ) {
				return $query->result();
			} else {
				return FALSE;
			}
		}

		public function find_all ( $offset = 0, $limit = 0 ) {
			$query = $this->db->get( $this->table, $limit, $offset );
			if ( $query->result() != NULL ) {
				return $query->result();
			} else {
				return FALSE;
			}
		}

		public function get_by_id ( $id ) {
			$this->db->from( $this->table );
			$this->db->where( $this->key, $id );
			$query = $this->db->get();
			$row = $query->row();

			return (isset( $row )) ? $row : FALSE;
		}

		public function save ($data=false) {
			($this->set_created)? $this->data[$this->created_field] = date('Y-m-d H:i:s'): '';
			$this->db->insert( $this->table, ($data)? $data:$this->data );
			$query = $this->db->get_where( $this->table, array( $this->key => $this->db->insert_id() ) );
			return $query->row();
		}

		public function insert_batch ( $data ) {
			$this->db->insert_batch($this->table, $data);
			return TRUE;
		}

		public function update ( $where ) {
			($this->set_modified)? $this->data[$this->modified_field] = date('Y-m-d H:i:s'): '';
			$this->db->update( $this->table, $this->data, $where );
			
			$query = $this->db->get_where( $this->table, $where );
			return $query->row();
		}

		public function update_batch ( $where, $data ) {
			$this->db->update( $this->table, $data, $where );
			$query = $this->db->get_where( $this->table, $where );
			return $query->result();
		}

		public function delete_by_id ( $id ) {
			$query = $this->db->delete( $this->table, array( $this->key => $id ) );
			return $query;
		}

		public function delete_where ( $where ) {
			$query = $this->db->delete( $this->table, $where );
			return $query;
		}

		public function get_by_attribute ( $field, $value ) {
			$this->db->from( $this->table );
			$this->db->where( $field, $value );
			$query = $this->db->get();
			$row = $query->row();

			return (isset( $row )) ? $row : FALSE;
		}

		public function custom_query ( $selectQuery, $where = false, $groupBy = false, $orderBy = false ) {
			$this->db->select( $selectQuery );
			if ( $where ) $this->db->where( $where );
			if ( $groupBy ) $this->db->group_by( $groupBy );
			if ( $orderBy ) $this->db->order_by( $orderBy );
			$query = $this->db->get( $this->table );

			return $query->result();
		}
	}

	   	 	 	 	 	 	 	 	 	 	 	 	
