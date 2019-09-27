<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bonfire Base Model
 *
 * The Base model implements standard CRUD functions that can be
 * used and overriden by module models. This helps to maintain
 * a standard interface to program to, and makes module creation
 * faster.
 *
 * @package    Bonfire
 * @subpackage MY_Model
 * @category   Models
 * @author     Lonnie Ezell
 * @link       http://cibonfire.com/docs/guides/models.html
 *
 */
class YL_Model extends CI_Model
{
	/**
	 * The name of the db table this model primarily uses.
	 *
	 * @var string
	 * @access protected
	 */
	protected $table 	= '';
	/**
	 * The primary key of the table. Used as the 'id' throughout.
	 *
	 * @var string
	 * @access protected
	 */
	protected $key		= 'id';
	/**
	 * Field name to use to the deleted column in the DB table.
	 *
	 * @var string
	 * @access protected
	 */
	protected $deleted = 'deleted';
	/**
	 * Field name to use to the created time column in the DB table.
	 *
	 * @var string
	 * @access protected
	 */
	protected $created_field = 'date_synced';
	/**
	 * Field name to use to the modified time column in the DB table.
	 *
	 * @var string
	 * @access protected
	 */
	protected $modified_field = 'modified_on';
	/**
	 * Field name to use to the deleted time column in the DB table.
	 *
	 * @var string
	 * @access protected
	 */
	protected $deleted_field = 'deleted_on';
	/**
	 * Whether or not to auto-fill a 'created_on' field on inserts.
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $set_created	= TRUE;
	/**
	 * Whether or not to auto-fill a 'modified_on' field on updates.
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $set_modified = TRUE;
	/**
	 * The type of date/time field used for created_on and modified_on fields.
	 * Valid types are: 'int', 'datetime', 'date'
	 *
	 * @var string
	 * @access protected
	 */
	protected $date_format = 'datetime';
	/**
	 * If FALSE, the delete() method will perform a TRUE delete of that row.
	 * If TRUE, a 'deleted' field will be set to 1.
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $soft_deletes = FALSE;
	/**
	 * Stores any selects here for use by the find* functions.
	 *
	 * @var string
	 * @access protected
	 */
	protected $selects = '';
	/*
	Var: $escape
	If FALSE, the select() method will not try to protect your field or table names with backticks.
	This is useful if you need a compound select statement.
	Access:
		Protected
	*/
	protected $escape = TRUE;
	/**
	 * DB Connection details (string or array)
	 *
	 * @var mixed
	 */
	protected $db_con = '';
	//---------------------------------------------------------------
	/**
	 * Setup the DB connection if it doesn't exist
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		if (!isset($this->db))
		{
			$this->load->database();
		}
	}//end __construct()

	//---------------------------------------------------------------
	/**
	 * Searches for a single row in the database.
	 *
	 * @param string $id The primary key of the record to search for.
	 * @param int $return_type Choose the type of return type. 0 - Object, 1 - Array
	 *
	 * @return mixed An object/array representing the db row, or FALSE.
	 */
	public function find($id='', $return_type = 0)
	{
		if ($this->_function_check($id) === FALSE)
		{
			return FALSE;
		}
		$this->set_selects();
		$query = $this->db->get_where($this->table, array($this->table.'.'. $this->key => $id));
		if ($query->num_rows())
		{
			if($return_type == 0)
			{
				return $query->row();
			}
			else
			{
				return $query->row_array();
			}
		}
		return FALSE;
	}//end find()
	//---------------------------------------------------------------
	/**
	 * Returns all records in the table.
	 *
	 * By default, there is no 'where' clause, but you can filter
	 * the results that are returned by using either CodeIgniter's
	 * Active Record functions before calling this function, or
	 * through method chaining with the where() method of this class.
	 *
	 * @param int $return_type Choose the type of return type. 0 - Object, 1 - Array
	 *
	 * @return mixed An array of objects/arrays representing the results, or FALSE on failure or empty set.
	 */
	public function find_all($return_type = 0)
	{
		if ($this->_function_check() === FALSE)
		{
			return FALSE;
		}
		$this->set_selects();
		$this->db->from($this->table);
		$query = $this->db->get();
		if (!empty($query) && $query->num_rows() > 0)
		{
			if($return_type == 0)
			{
				return $query->result();
			}
			else
			{
				return $query->result_array();
			}
		}
		return FALSE;
	}//end find_all()
	//---------------------------------------------------------------
	/**
	 * A convenience method that combines a where() and find_all() call into a single call.
	 *
	 * @param mixed  $field The table field to search in.
	 * @param mixed  $value The value that field should be.
	 * @param string $type  The type of where clause to create. Either 'and' or 'or'.
	 * @param int $return_type Choose the type of return type. 0 - Object, 1 - Array
	 *
	 * @return bool|mixed An array of objects representing the results, or FALSE on failure or empty set.
	 */
	public function find_all_by($field=NULL, $value=NULL, $type='and', $return_type = 0)
	{
		if (empty($field)) return FALSE;
		// Setup our field/value check
		if (is_array($field))
		{
			foreach ($field as $key => $value)
			{
				if ($type == 'or')
				{
					$this->db->or_where($key, $value);
				}
				else
				{
					$this->db->where($key, $value);
				}
			}
		}
		else
		{
			$this->db->where($field, $value);
		}
		$this->set_selects();
		return $this->find_all($return_type);
	}//end find_all_by()
	//--------------------------------------------------------------------
	/**
	 * Returns the first result that matches the field/values passed.
	 *
	 * @param string $field Either a string or an array of fields to match against. If an array is passed it, the $value parameter is ignored since the array is expected to have key/value pairs in it.
	 * @param string $value The value to match on the $field. Only used when $field is a string.
	 * @param string $type  The type of where clause to create. Either 'and' or 'or'.
	 * @param int $return_type Choose the type of return type. 0 - Object, 1 - Array
	 *
	 * @return bool|mixed An object representing the first result returned.
	 */
	public function find_by($field='', $value='', $type='and', $return_type = 0, $like_type='both')
	{
		$like_regex = '/ like[ ]?$/i';
		if (empty($field) || (!is_array($field) && empty($value)))
		{
			return FALSE;
		}
		if (is_array($field))
		{
			$idx = 0;
			$allow_grouping = count($field) > 1 ? TRUE : FALSE;
			foreach ($field as $key => $value)
			{
				if ($type == 'or')
				{
					if($key != ($new_key = preg_replace($like_regex, '',$key)))
					{
						$key = "LOWER(`$new_key`)";
						$value = strtolower($value);
						if($idx > 0)
							$this->db->or_like($key, $value, $like_type, FALSE);
						else
							$this->db->like($key, $value, $like_type, FALSE);
					}
					else
					{
						//$value = is_numeric($value) ? $value : "'".$value."'";
						//$key = "`$key`=";
						if($idx > 0)
							$this->db->or_where($key, $value);
						else
							$this->db->where($key, $value);
					}
				}
				else
				{
					if($key != ($new_key = preg_replace($like_regex, '',$key)))
					{
						$key = $new_key;
						$value = strtolower($value);
						$this->db->like("LOWER(`$key`)", $value, $like_type , FALSE);
					}
					else
					{
						$this->db->where($key, $value);
					}
				}
				$idx++;
			}
			if(count($this->db->ar_where) > 1)
			{
				array_unshift($this->db->ar_where,' ( ');
				array_push($this->db->ar_where,' ) ');
			}
			if(count($this->db->ar_like) > 1)
			{
				array_unshift($this->db->ar_like,'( ');
				array_push($this->db->ar_like,' )');
			}
		}
		else
		{
			if($field != ($new_field = preg_replace($like_regex, '',$field)))
			{
				$field = $new_field;
				$value = strtolower($value);
				$this->db->like("LOWER(`$field`)", $value, $like_type, FALSE);
			}
			else
			{
				$this->db->where($field, $value);
			}
		}
		$this->set_selects();
		$query = $this->db->get($this->table); //debug($this->db->last_query());
		if ($query && $query->num_rows() > 0)
		{
			if($return_type == 0)
			{
				return $query->row();
			}
			else
			{
				return $query->result();
			}
		}
		return FALSE;
	}//end find_by()
	//---------------------------------------------------------------
	/**
	 * Inserts a row of data into the database.
	 *
	 * @param array $data an array of key/value pairs to insert.
	 *
	 * @return bool|mixed Either the $id of the row inserted, or FALSE on failure.
	 */
	public function insert($data=NULL)
	{
		if ($this->_function_check(FALSE, $data) === FALSE)
		{
			return FALSE;
		}
		// Add the created field
		if ($this->set_created === TRUE && !array_key_exists($this->created_field, $data))
		{
			$data[$this->created_field] = $this->set_date();
		}
		// Insert it
		$status = $this->db->insert($this->table, $data);
		if ($status != FALSE)
		{
			return $this->db->insert_id();
		}
		else
		{
			return FALSE;
		}
	}//end insert()
	//---------------------------------------------------------------
	/**
	 * Inserts a batch of data into the database.
	 *
	 * @param array $data an array of key/value pairs to insert.
	 *
	 * @return bool|mixed Either the $id of the row inserted, or FALSE on failure.
	 */
	public function insert_batch($data=NULL)
	{
		if ($this->_function_check(FALSE, $data) === FALSE)
		{
			return FALSE;
		}
		$set = array();
		// Add the created field
		if ($this->set_created === TRUE )
		{
			$set[$this->created_field] = $this->set_date();
		}
		if(!empty($set))
		{
			foreach($data as $key => $record)
			{
				$data[$key] = array_merge($set,$data[$key]);
			}
		}
		// Insert it
		$status = $this->db->insert_batch($this->table, $data);
		if ($status === FALSE)
		{
			return FALSE;
		}
		return TRUE;
	}//end insert_batch()
	//---------------------------------------------------------------
	/**
	 * Updates an existing row in the database.
	 *
	 * @param mixed   $id The primary_key value of the row to update.
	 * @param array $data An array of key/value pairs to update.
	 *
	 * @return bool TRUE/FALSE
	 */
	public function update($id=NULL, $data=NULL)
	{
		if ($this->_function_check($id, $data) === FALSE)
		{
			return FALSE;
		}
		// Add the modified field
		if ($this->set_modified === TRUE && !array_key_exists($this->modified_field, $data))
		{
			$data[$this->modified_field] = $this->set_date();
		}
		$this->db->where($this->key, $id);
		if ($this->db->update($this->table, $data))
		{
			return TRUE;
		}
		return FALSE;
	}//end update()
	//---------------------------------------------------------------
	/**
	 * A convenience method that allows you to use any field/value pair as the 'where' portion of your update.
	 *
	 * @param string $field The field to match on.
	 * @param string $value The value to search the $field for.
	 * @param array  $data  An array of key/value pairs to update.
	 *
	 * @return bool TRUE/FALSE
	 */
	public function update_where($field=NULL, $value=NULL, $data=NULL)
	{
		if (empty($field) || empty($value) || !is_array($data))
		{
			return FALSE;
		}
		// Add the modified field
		if ($this->set_modified === TRUE && !array_key_exists($this->modified_field, $data))
		{
			$data[$this->modified_field] = $this->set_date();
		}
		return $this->db->update($this->table, $data, array($field => $value));
	}//end update_where()
	//---------------------------------------------------------------
	/**
	 * Updates a batch of existing rows in the database.
	 *
	 * @param array  $data  An array of key/value pairs to update.
	 * @param string $index A string value of the db column to use as the where key
	 *
	 * @return bool TRUE/FALSE
	 */
	public function update_batch($data = NULL, $index = NULL)
	{
		if (is_null($index))
		{
			return FALSE;
		}
		if (!is_null($data))
		{
			// Add the modified field
			if ($this->set_modified === TRUE && !array_key_exists($this->modified_field, $data))
			{
				foreach ($data as $key => $record)
				{
					$data[$key][$this->modified_field] = $this->set_date();
				}
			}
			$result = $this->db->update_batch($this->table, $data, $index);
			if (empty($result))
			{
				return TRUE;
			}
		}
		return FALSE;
	}//end update_batch()
	//--------------------------------------------------------------------
	/**
	 * Performs a delete on the record specified. If $this->soft_deletes is TRUE,
	 * it will attempt to set a field 'deleted' on the current record
	 * to '1', to allow the data to remain in the database.
	 *
	 * @param mixed $id The primary_key value to match against.
	 *
	 * @return bool TRUE/FALSE
	 */
	public function delete($id=NULL)
	{
		if ($this->_function_check($id) === FALSE)
		{
			return FALSE;
		}
		if ($this->find($id) !== FALSE)
		{
			if ($this->soft_deletes === TRUE)
			{
				$data = array(
					$this->deleted => 1
				);
				// Add the modified field
				if ($this->soft_deletes === TRUE && !array_key_exists($this->deleted_field, $data))
				{
					$data[$this->deleted_field] = $this->set_date();
				}
				$this->db->where($this->key, $id);
				$result = $this->db->update($this->table, $data);
			}
			else
			{
				$result = $this->db->delete($this->table, array($this->key => $id));
			}
			if ($result)
			{
				return TRUE;
			}
		}
		return FALSE;
	}//end delete()
	//---------------------------------------------------------------
	/**
	 * Performs a delete using any field/value pair(s) as the 'where'
	 * portion of your delete statement. If $this->soft_deletes is
	 * TRUE, it will attempt to set a field 'deleted' on the current
	 * record to '1', to allow the data to remain in the database.
	 *
	 * @param array $data key/value pairs accepts an associative array or a string
	 *
	 * @example 1) array( 'key' => 'value', 'key2' => 'value2' )
	 * @example 2) ' (`key` = "value" AND `key2` = "value2") '
	 *
	 * @return bool TRUE/FALSE
	 */
	public function delete_where($data=NULL)
	{
		if (empty($data))
		{
			return FALSE;
		}
		if (is_array($data))
		{
			foreach($data as $field => $value)
			{
				$this->db->where($field,$value);
			}
		}
		else
		{
			$this->db->where($data);
		}
		if ($this->soft_deletes === TRUE)
		{
			$where = array(	$this->deleted => 1,
							$this->deleted_field => $this->set_date());
			$this->db->update($this->table,$where);
		}
		else
		{
			$this->db->delete($this->table);
		}
		$result = $this->db->affected_rows();
		if ($result)
		{
			return $result;
		}
		return FALSE;
	}//end delete_where()

	//---------------------------------------------------------------
	//---------------------------------------------------------------
	// HELPER FUNCTIONS
	//---------------------------------------------------------------
	/**
	 * Checks whether a field/value pair exists within the table.
	 *
	 * @param string $field The field to search for.
	 * @param string $value The value to match $field against.
	 *
	 * @return bool TRUE/FALSE
	 */
	public function is_unique($field='', $value='')
	{
		if (empty($field) || empty($value))
		{
			return FALSE;
		}
		$this->db->where($field, $value);
		$query = $this->db->get($this->table);
		if ($query && $query->num_rows() == 0)
		{
			return TRUE;
		}
		return FALSE;
	}//end is_unique()

	//---------------------------------------------------------------
	/**
	 * Returns the number of rows in the table.
	 *
	 * @return int
	 */
	public function count_all()
	{
		return $this->db->count_all_results($this->table);
	}//end count_all()

	//---------------------------------------------------------------
	/**
	 * Returns the number of elements that match the field/value pair.
	 *
	 * @param string $field The field to search for.
	 * @param string $value The value to match $field against.
	 *
	 * @return bool|int
	 */
	public function count_by($field='', $value=NULL)
	{
		if (empty($field))
		{
			return FALSE;
		}
		$this->set_selects();
		$this->db->where($field, $value);
		return (int)$this->db->count_all_results($this->table);
	}//end count_by()

	//---------------------------------------------------------------
	/**
	 * A convenience method to return only a single field of the specified row.
	 *
	 * @param mixed  $id    The primary_key value to match against.
	 * @param string $field The field to search for.
	 *
	 * @return bool|mixed The value of the field.
	 */
	public function get_field($id=NULL, $field='')
	{
		if (empty($id) || $id === 0 || empty($field))
		{
			return FALSE;
		}
		$this->db->select($field);
		$this->db->where($this->key, $id);
		$query = $this->db->get($this->table);
		if ($query && $query->num_rows() > 0)
		{
			return $query->row()->$field;
		}
		return FALSE;
	}//end get_field()

	//---------------------------------------------------------------
	/**
	 * A convenience method to return options for form dropdown menus.
	 *
	 * Can pass either Key ID and Label Table names or Just Label Table name.
	 *
	 * @return array The options for the dropdown.
	 */
	function format_dropdown()
	{
		$args = func_get_args();
		$add_blank = FALSE;
		if (count($args) == 2)
		{
			list($key, $value) = $args;
		}
		else if (count($args) == 3)
		{
			list($key, $value, $add_blank) = $args;
		}
		else
		{
			$key = $this->key;
			$value = $args[0];
		}
		$value_alias = $value;
		if(is_array($value) && count($value) > 0)
		{
			$value_alias = 'val';
			$value = "CONCAT(".implode(",",$value).") $value_alias";
		}
		$this->db->select(array($key, $value));
		if($this->soft_deletes)
		{
			$this->db->where($this->deleted,0);
		}
		$query = $this->db->get($this->table);
		$options = array();
		if($add_blank)
		{
			$options[''] = '';
		}
		foreach ($query->result() as $row)
		{
			$options[$row->{$key}] = $row->{$value_alias};
		}
		return $options;
	}//end format_dropdown()

	//--------------------------------------------------------------------
	// !CHAINABLE UTILITY METHODS
	//--------------------------------------------------------------------
	/**
	 * Sets the where portion of the query in a chainable format.
	 *
	 * @param mixed  $field The field to search the db on. Can be either a string with the field name to search, or an associative array of key/value pairs.
	 * @param string $value The value to match the field against. If $field is an array, this value is ignored.
	 *
	 * @return BF_Model An instance of this class.
	 */
	public function where($field=NULL, $value=NULL)
	{
		if (!empty($field))
		{
			if (is_string($field))
			{
				$this->db->where($field, $value);
			}
			else if (is_array($field))
			{
				$this->db->where($field);
			}
		}
		return $this;
	}//end where()
	/**
	 * Sets the where portion of the query in a chainable format.
	 *
	 * @param mixed  $field The field to search the db on. Can be either a string with the field name to search, or an associative array of key/value pairs.
	 * @param array  $value The value to match the field against. If $field is an array, this value is ignored.
	 *
	 * @return BF_Model An instance of this class.
	 */
	public function where_in($field=NULL, $value=NULL)
	{
		if (!empty($field))
		{
			if (is_string($field))
			{
				if(!is_array($value))
				{
					$value = array($value);
				}
				$this->db->where_in($field, $value);
			}
			else if (is_array($field))
			{
				foreach($field as $k => $v)
				{
					if(!is_array($v))
					{
						$v = array($v);
					}
					$this->db->where_in($k, $v);
				}
			}
		}
		return $this;
	}//end where_in()

	/**
	 * Private function for creating NULL where clauses
	 *
	 * @param mixed  $field The field to search the db on. Can be either a string with the field name to search, or an associative array of key/value pairs.
	 * @param boolean $not Sets the where clause to use IS NOT NULL
	 * @param boolean $or Sets the where clause to use OR
	 *
	 * @return BF_Model An instance of this class.
	 *
	 * @author Fred Timajo <fred@lifedata.ph>
	 */
	private function _where_null($field=NULL, $not=FALSE, $or=FALSE)
	{
		if (!empty($field))
		{
			$null = $not ? 'NOT NULL' : 'NULL';
			if (is_string($field))
			{
				if($or)
					$this->db->or_where("$field IS $null");
				else
					$this->db->where("$field IS $null");
			}
			else if (is_array($field))
			{
				foreach($field as $f)
				{
					if($or)
						$this->db->or_where("$f IS $null");
					else
						$this->db->where("$f IS $null");
				}
			}
		}
		return $this;
	}//end _where_null()

	/**
	 * Sets the where portion of the query in a chainable format.
	 *
	 * @param mixed  $field The field to search the db on. Can be either a string with the field name to search, or an array of field names.
	 *
	 * @return BF_Model An instance of this class.
	 *
	 * @author Fred Timajo <fred@lifedata.ph>
	 */
	public function where_null($field)
	{
		return $this->_where_null($field);
	}//end where_null()

	/**
	 * Sets the where portion of the query in a chainable format.
	 *
	 * @param mixed  $field The field to search the db on. Can be either a string with the field name to search, or an array of field names.
	 *
	 * @return BF_Model An instance of this class.
	 *
	 * @author Fred Timajo <fred@lifedata.ph>
	 */
	public function or_where_null($field)
	{
		return $this->_where_null($field,FALSE,TRUE);
	}//end or_where_null()
	/**
	 * Sets the where portion of the query in a chainable format.
	 *
	 * @param mixed  $field The field to search the db on. Can be either a string with the field name to search, or an array of field names.
	 *
	 * @return BF_Model An instance of this class.
	 *
	 * @author Fred Timajo <fred@lifedata.ph>
	 */
	public function where_not_null($field)
	{
		return $this->_where_null($field,TRUE);
	}//end where_not_null()
	/**
	 * Sets the where portion of the query in a chainable format.
	 *
	 * @param mixed  $field The field to search the db on. Can be either a string with the field name to search, or an array of field names.
	 *
	 * @return BF_Model An instance of this class.
	 *
	 * @author Fred Timajo <fred@lifedata.ph>
	 */
	public function or_where_not_null($field)
	{
		return $this->_where_null($field,TRUE,TRUE);
	}//end or_where_not_null()
	//--------------------------------------------------------------------
	/**
	 * Sets the select portion of the query in a chainable format. The value
	 * is stored for use in the find* methods so that child classes can
	 * have more flexibility in joins and what is selected.
	 *
	 * @param string $selects A string representing the selection.
	 * @param string $escape  A string representing the escape.
	 *
	 * @return BF_Model An instance of this class.
	 */
	public function select($selects=NULL, $escape=NULL)
	{
		if (!empty($selects))
		{
			$this->selects = $selects;
		}
		if ($escape === FALSE)
		{
			$this->escape = $escape;
		}
		return $this;
	}//end select()
	//--------------------------------------------------------------------
	/**
	 * Sets the limit portion of the query in a chainable format.
	 *
	 * @param int $limit  An int showing the max results to return.
	 * @param int $offset An in showing how far into the results to start returning info.
	 *
	 * @return BF_Model An instance of this class.
	 */
	public function limit($limit=0, $offset=0)
	{
		$this->db->limit($limit, $offset);
		return $this;
	}//end limit()
	//--------------------------------------------------------------------
	/**
	 * Generates the JOIN portion of the query.
	 *
	 * @param string $table A string containing the table name.
	 * @param string $cond  A string with the join condiction.
	 * @param string $type  A string containing the type of join - INNER, OUTER etc.
	 *
	 * @return BF_Model An instance of this class.
	 */
	public function join($table, $cond, $type = '')
	{
		$this->db->join($table, $cond, $type);
		return $this;
	}//end join()
	//--------------------------------------------------------------------
	/**
	 * Inserts a chainable order_by method from either a string or an
	 * array of field/order combinations. If the $field value is an array,
	 * it should look like:
	 *
	 * array(
	 *     'field1' => 'asc',
	 *     'field2' => 'desc'
	 * );
	 *
	 * @param string $field The field to order the results by.
	 * @param string $order Which direction to order the results ('asc' or 'desc')
	 *
	 * @return BF_Model An instance of this class.
	 */
	public function order_by($field=NULL, $order='asc')
	{
		if (!empty($field))
		{
			if (is_string($field))
			{
				$this->db->order_by($field, $order);
			}
			else if (is_array($field))
			{
				foreach ($field as $f => $o)
				{
					$this->db->order_by($f, $o);
				}
			}
		}
		return $this;
	}//end order_by()

	//--------------------------------------------------------------------
	//---------------------------------------------------------------
	// !UTILITY FUNCTIONS
	//---------------------------------------------------------------
	/**
	 * A utility method that does some error checking and cleanup for other methods:
	 *
	 * * Makes sure that a table has been set at $this->table.
	 * * If passed in, will make sure that $id is of the valid type.
	 * * If passed in, will verify the $data is not empty.
	 *
	 * @param mixed      $id   The primary_key value to match against.
	 * @param array|bool $data Array of data
	 *
	 * @access protected
	 *
	 * @return bool
	 */
	protected function _function_check($id=FALSE, &$data=FALSE)
	{
		// Does the model have a table set?
		if (empty($this->table))
		{
			return FALSE;
		}
		// Check the ID, but only if it's a non-FALSE value
		if ($id !== FALSE)
		{
			if (empty($id) || $id == 0)
			{
				return FALSE;
			}
		}
		// Check the data
		if ($data !== FALSE)
		{
			if (!is_array($data) || count($data) == 0)
			{
				return FALSE;
			}
		}
		return TRUE;
	}//end _function_check()
	//---------------------------------------------------------------
	/**
	 * A utility function to allow child models to use the type of
	 * date/time format that they prefer. This is primarily used for
	 * setting created_on and modified_on values, but can be used by
	 * inheriting classes.
	 *
	 * The available time formats are:
	 * * 'int'		- Stores the date as an integer timestamp.
	 * * 'datetime'	- Stores the date and time in the SQL datetime format.
	 * * 'date'		- Stores teh date (only) in the SQL date format.
	 *
	 * @param mixed $user_date An optional PHP timestamp to be converted.
	 *
	 * @access protected
	 *
	 * @return int|null|string The current/user time converted to the proper format.
	 */
	protected function set_date($user_date=NULL)
	{
		$curr_date = !empty($user_date) ? $user_date : time();
		switch ($this->date_format)
		{
			case 'int':
				return $curr_date;
				break;
			case 'datetime':
				return date('Y-m-d H:i:s', $curr_date);
				break;
			case 'date':
				return date( 'Y-m-d', $curr_date);
				break;
		}
	}//end set_date()
	//--------------------------------------------------------------------
	/**
	 * Allows you to set the table to use for all methods during runtime.
	 *
	 * @param string $table The table name to use (do not include the prefix!)
	 *
	 * @return void
	 */
	public function set_table($table='')
	{
		$this->table = $table;
	}//end set_table()
	//--------------------------------------------------------------------
	/**
	 * Allows you to get the table name
	 *
	 * @return string $this->table (current model table name)
	 */
	public function get_table()
	{
		return $this->table;
	}//end get_table()
	//--------------------------------------------------------------------
	/**
	 * Allows you to get the table primary key
	 *
	 * @return string $this->key (current model table primary key)
	 */
	public function get_key()
	{
		return $this->key;
	}//end get_key()
	//--------------------------------------------------------------------
	/**
	 * Sets the date_format to use for setting created_on and modified_on values.
	 *
	 * @param string $format String describing format. Valid values are: 'int', 'datetime', 'date'
	 *
	 * @return bool
	 */
	public function set_date_format($format='int')
	{
		if ($format != 'int' && $format != 'datetime' && $format != 'date')
		{
			return FALSE;
		}
		$this->date_format = $format;
		return TRUE;
	}//end set_date_format()
	//--------------------------------------------------------------------
	/**
	 * Sets whether to auto-create modified_on dates in the update method.
	 *
	 * @param bool $modified
	 *
	 * @return bool
	 */
	public function set_modified($modified=TRUE)
	{
		if ($modified !== TRUE && $modified !== FALSE)
		{
			return FALSE;
		}
		$this->set_modified = $modified;
		return TRUE;
	}//end set_modified()
	//--------------------------------------------------------------------
	/**
	 * Sets whether soft deletes are used by the delete method.
	 *
	 * @param bool $soft
	 *
	 * @return bool
	 */
	public function set_soft_deletes($soft=TRUE)
	{
		if ($soft !== TRUE && $soft !== FALSE)
		{
			return FALSE;
		}
		$this->soft_deletes = $soft;
		return TRUE;
	}//end set_soft_deletes()
	//--------------------------------------------------------------------
	/**
	 * Takes the string in $this->selects, if not empty, and sets it
	 * with the ActiveRecord db class. If $this->escape is FALSE it
	 * will not try to protect your field or table names with backticks.
	 *
	 * Clears the string afterword to make sure it's clean for the next call.
	 *
	 * @access protected
	 */
	protected function set_selects()
	{
		if (!empty($this->selects) && $this->escape === FALSE)
		{
			$this->db->select($this->selects, FALSE);
			// Clear it out for the next process.
			$this->selects = NULL;
			$this->escape = NULL;
		}
		elseif (!empty($this->selects))
		{
			$this->db->select($this->selects);
			// Clear it out for the next process.
			$this->selects = NULL;
		}
	}//end set_selects()

	//--------------------------------------------------------------------
	/**
	 * Sets whether to use distinct method
	 *
	 * @return bool
	 */
	public function distinct()
	{
		return $this->db->distinct();
	}//end distinct()
	//--------------------------------------------------------------------
	/**
	 * Sets the grouping portion of the query in a chainable format.
	 *
	 * @param int $group  The Name of the group ID
	 * @return BF_Model An instance of this class.
	 *
	 *
	 */
	public function group_by($group)
	{
		$this->db->group_by($group);
		return $this;
	}//end group_by()
	//--------------------------------------------------------------------
	/**
	 * Sets the table portion of the query in a chainable format.
	 *
	 * @param int $table  The Name of the database Table
	 * @return BF_Model An instance of this class.
	 *
	 *
	 */
	public function from($table)
	{
		$this->db->from($table);
		return $this;
	}//end from()
	//--------------------------------------------------------------------
}//end BF_model
//--------------------------------------------------------------------

/**
 * MY_Model
 *
 * This simply extends BF_Model for backwards compatibility,
 * and to provide a placeholder class that your project can customize
 * extend as needed.
 *
 * @package    Bonfire
 * @subpackage MY_Model
 * @category   Models
 * @author     Lonnie Ezell
 * @link       http://cibonfire.com/docs/guides/models.html
 *
 */
class MY_Model extends YL_Model {
	protected $encrypted_fields = Array();
	function __construct() {
		parent::__construct();
	}
	/**
	 * Inserts a row of data into the database.
	 *
	 * @param array $data an array of key/value pairs to insert.
	 *
	 * @return bool|mixed Either the $id of the row inserted, or FALSE on failure.
	 */
	public function insert($data=NULL)
	{
		if ($this->_encryption_check())
		{
			$data = $this->_encrypt_data($data);
		}
		if ($this->_function_check(FALSE, $data) === FALSE)
		{
			return FALSE;
		}
		// Add the created field
		if ($this->set_created === TRUE && !array_key_exists($this->created_field, $data))
		{
			$data[$this->created_field] = $this->set_date();
		}
		// Insert it
		$status = $this->db->insert($this->table, $data);
		if ($status != FALSE)
		{
			// retrieve last inserted data
			$insert_id = $this->db->insert_id();
			$this->db->where($this->key, $insert_id);
			$this->db->limit(1);
			$res = $this->db->get($this->table)->row();
			return $insert_id;
		}
		else
		{
			return FALSE;
		}
	}//end insert()

	//---------------------------------------------------------------
	/**
	 * Inserts a batch of data into the database.
	 *
	 * @param array $data an array of key/value pairs to insert.
	 *
	 * @return bool|mixed Either the $id of the row inserted, or FALSE on failure.
	 */
	public function insert_batch($data=NULL)
	{
		if ($this->_encryption_check())
		{
			$data = $this->_encrypt_data($data);
		}
		if ($this->_function_check(FALSE, $data) === FALSE)
		{
			return FALSE;
		}
		$set = array();
		// Add the created field
		if ($this->set_created === TRUE )
		{
			$set[$this->created_field] = $this->set_date();
		}
		if(!empty($set))
		{
			foreach($data as $key => $record)
			{
				$data[$key] = array_merge($set,$data[$key]);
			}
		}
		// Insert it
		$status = $this->db->insert_batch($this->table, $data);
		if ($status === FALSE){
			return FALSE;
		}
		return TRUE;
	}//end insert_batch()

	//---------------------------------------------------------------
	/**
	 * Updates an existing row in the database.
	 *
	 * @param mixed   $id The primary_key value of the row to update.
	 * @param array $data An array of key/value pairs to update.
	 *
	 * @return bool TRUE/FALSE
	 */
	public function update($id=NULL, $data=NULL)
	{
		if ($this->_encryption_check())
		{
			$data = $this->_encrypt_data($data);
		}
		if ($this->_function_check($id, $data) === FALSE)
		{
			return FALSE;
		}
		// Add the modified field
		if ($this->set_modified === TRUE && !array_key_exists($this->modified_field, $data))
		{
			$data[$this->modified_field] = $this->set_date();
		}
		$this->db->where($this->key, $id);
		$qry = $this->db->limit(1)->get($this->table);
		if ($qry->num_rows() > 0)
		{
			$old_data = $qry->row();
			$this->db->where($this->key, $id);
			if ($this->db->update($this->table, $data))
			{
				$this->db->where($this->key, $id);
				$qry = $this->db->limit(1)->get($this->table);
				$new_data = $qry->row();
				return $new_data;
			}
			return FALSE;
		}
		else
		{
			return FALSE;
		}
	}//end update()
	//---------------------------------------------------------------
	/**
	 * A convenience method that allows you to use any field/value pair as the 'where' portion of your update.
	 *
	 * @param string $field The field to match on.
	 * @param string $value The value to search the $field for.
	 * @param array  $data  An array of key/value pairs to update.
	 *
	 * @return bool TRUE/FALSE
	 */
	public function update_where($field=NULL, $value=NULL, $data=NULL)
	{
		if ($this->_encryption_check())
		{
			$data = $this->_encrypt_data($data);
		}
		if (empty($field) || empty($value) || !is_array($data))
		{
			return FALSE;
		}
		// Add the modified field
		if ($this->set_modified === TRUE && !array_key_exists($this->modified_field, $data))
		{
			$data[$this->modified_field] = $this->set_date();
		}

		$this->db->where($field, $value);
		$this->db->where($this->deleted, 0);
		$qry = $this->db->get($this->table);
		$old_data = Array();
		foreach($qry->result() as $result)
		{
			$old_data[$result->{$this->key}] = $result;
		}
		$return = $this->db->update($this->table, $data, array($field => $value));
		return $return;
	}//end update_where()

	//---------------------------------------------------------------
	/**
	 * Updates a batch of existing rows in the database.
	 *
	 * @param array  $data  An array of key/value pairs to update.
	 * @param string $index A string value of the db column to use as the where key
	 *
	 * @return bool TRUE/FALSE
	 */
	public function update_batch($data = NULL, $index = NULL)
	{
		if ($this->_encryption_check())
		{
			$data = $this->_encrypt_data($data);
		}
		if (is_null($index))
		{
			return FALSE;
		}
		if (!is_null($data))
		{
			// Add the modified field
			if ($this->set_modified === TRUE && !array_key_exists($this->modified_field, $data))
			{
				foreach ($data as $key => $record)
				{
					$data[$key][$this->modified_field] = $this->set_date();
				}
			}
			$result = $this->db->update_batch($this->table, $data, $index);
			if (empty($result))
			{
				return TRUE;
			}
		}
		return FALSE;
	}//end update_batch()
	/**
	 * Performs a delete on the record specified. If $this->soft_deletes is TRUE,
	 * it will attempt to set a field 'deleted' on the current record
	 * to '1', to allow the data to remain in the database.
	 *
	 * @param mixed $id The primary_key value to match against.
	 *
	 * @return bool TRUE/FALSE
	 */
	public function delete($id=NULL)
	{
		if ($this->_function_check($id) === FALSE)
		{
			return FALSE;
		}
		if ($this->find($id) !== FALSE)
		{
			if ($this->soft_deletes === TRUE)
			{
				$data = array(
					$this->deleted => 1
				);
				// Add the modified field
				if ($this->soft_deletes === TRUE && !array_key_exists($this->deleted_field, $data))
				{
					$data[$this->deleted_field] = $this->set_date();
				}
				$this->db->where($this->key, $id);
				$result = $this->db->update($this->table, $data);
			}
			else
			{
				$this->db->where($this->key, $id);
				$qry = $this->db->limit(1)->get($this->table);
				$result = $this->db->delete($this->table, array($this->key => $id));
			}
			if ($result){
				return TRUE;
			}
		}
		return FALSE;
	}//end delete()

	//---------------------------------------------------------------
	/**
	 * Performs a delete using any field/value pair(s) as the 'where'
	 * portion of your delete statement. If $this->soft_deletes is
	 * TRUE, it will attempt to set a field 'deleted' on the current
	 * record to '1', to allow the data to remain in the database.
	 *
	 * @param array $data key/value pairs accepts an associative array or a string
	 *
	 * @example 1) array( 'key' => 'value', 'key2' => 'value2' )
	 * @example 2) ' (`key` = "value" AND `key2` = "value2") '
	 *
	 * @return bool TRUE/FALSE
	 */
	public function delete_where($data=NULL)
	{
		if (empty($data))
		{
			return FALSE;
		}
		if (is_array($data))
		{
			foreach($data as $field => $value)
			{
				$this->db->where($field,$value);
			}
		}
		else
		{
			$this->db->where($data);
		}
		$qry = $this->db->limit(1)->get($this->table);
		if (is_array($data))
		{
			foreach($data as $field => $value)
			{
				$this->db->where($field,$value);
			}
		}
		else
		{
			$this->db->where($data);
		}
		$this->db_tmp = clone $this->db;
		if ($this->soft_deletes === TRUE)
		{
			//$this->db->get($this->table);
			$update = array($this->deleted => 1,
							$this->deleted_field => $this->set_date());
			$this->db->update($this->table, $update);
			if (is_array($data))
			{
				foreach($data as $field => $value)
				{
					$this->db->where($field,$value);
				}
			}
			else
			{
				$this->db->where($data);
			}
			$qry = $this->db->limit(1)->get($this->table);
		}
		else
		{
			$this->db->delete($this->table);
		}
		$result = $this->db->affected_rows();
		if ($result)
		{
			if(method_exists($this,'_on_delete'))
			{
				$result = $this->db_tmp->get($this->table);
				if($result->num_rows() > 0)
				{
					$this->_on_delete($result->result());
				}
			}
			unset($this->db_tmp);
			return $result;
		}
		return FALSE;
	}//end delete_where()

	/**
	 * Overrides the find method, decrypting data from the database.
	 *
	 *
	 * @access public
	 */
	public function find($id)
	{
		$result = parent::find($id);
		if($this->_encryption_check())
		{
			$result = $this->_decrypt_data($result);
		}
		return $result;
	}

	//--------------------------------------------------------------------
	/**
	 * Overrides the find_all method, decrypting multiple rows of data from the database.
	 *
	 *
	 * @access public
	 */
	public function find_all($use_as_index = FALSE)
	{
		$results = parent::find_all();
		if(($results !== FALSE) && $this->_encryption_check())
		{
			$results = $this->_decrypt_batch($results);
		}
		if(($results !== FALSE) && ($use_as_index !== FALSE))
		{
			$_results = $results;
			$results = Array();
			foreach($_results as $result)
			{
				$results[$result->$use_as_index] = $result;
			}
		}
		return $results;
	}

	//--------------------------------------------------------------------
	/**
	 * Overrides the find_by method, encrypting passed parameters and decrypts result data.
	 * Comparison are always equal, any LIKE parameters will be overriden.
	 *
	 *
	 * @access public
	 */
	public function find_by($field='', $value='', $type='and', $return_type = 0, $like_type='both')
	{
		$like_regex = '/ (like)[ ]?$/i';
		if(is_array($field))
		{
			$data = array();
			$field_ref = array();
			foreach($field as $f=>$v)
			{
				if(!in_array($f, $this->encrypted_fields))
				{
					$new_field = preg_replace($like_regex, '', $f);
					$field_ref[$new_field] = $f;
					$data[$new_field] = $v;
				}
				else
				{
					$data[$f] = $v;
				}
			}
			$field = $this->_encrypt_data($data);
		}
		else
		{
			if(!in_array($field, $this->encrypted_fields))
			{
				$new_field = preg_replace($like_regex, '', $field);
				$field_ref[$new_field] = $field;
				$field = $new_field;
			}
			$field = $this->_encrypt_data(array($field => $value));
			$value = '';
		}
		$field_name_fixed = Array();
		foreach($field as $f=>$v)
		{
			$field_name = $field_ref[$f];
			$field_name_fixed[$field_name] = $v;
		}
		$field = $field_name_fixed;
		$results = parent::find_by($field,$value,$type,$return_type);
		if(($results !== FALSE) && $this->_encryption_check())
		{
			$results = $this->_decrypt_batch($results);
		}
		return $results;
	}
	//--------------------------------------------------------------------
	/**
	 * Checks if any fields where declared as to be encrypted
	 *
	 * Returns boolean
	 *
	 * @access protected
	 */
	protected function _encryption_check()
	{
		return (count($this->encrypted_fields) > 0) ? TRUE : FALSE;
	}//end _encryption_check()
	//--------------------------------------------------------------------
	/**
	 * Encrypts data of fields specified in the list of encrypted fields
	 *
	 * @access protected
	 */
	protected function _encrypt_data($data)
	{
		$iv = $this->config->item('encryption_iv');
		foreach($this->encrypted_fields as $this->encrypted_field)
		{
			if(isset($data[$this->encrypted_field]))
			{
				$data[$this->encrypted_field] = $this->encrypt->encode(strtoupper($data[$this->encrypted_field]),'',$iv);
			}
		}
		return $data;
	}
	//--------------------------------------------------------------------
	/**
	 * Encrypts a batch data
	 *
	 * @access protected
	 */
	protected function _encrypt_batch($data)
	{
		foreach($data as $idx=>$row)
		{
			$data[$idx] = $this->_encrypt_data($row);
		}
		return $data;
	}
	//--------------------------------------------------------------------
	/**
	 * Decrypts data of fields specified in the list of encrypted fields
	 *
	 * @access protected
	 */
	protected function _decrypt_data($data)
	{
		foreach($this->encrypted_fields as $this->encrypted_field)
		{
			if(isset($data->{$this->encrypted_field}))
			{
				$data->{$this->encrypted_field} = $this->encrypt->decode($data->{$this->encrypted_field});
			}
		}
		return $data;
	}
	//--------------------------------------------------------------------
	/**
	 * Decrypts a batch data
	 *
	 * @access protected
	 */
	protected function _decrypt_batch($data)
	{
		foreach($data as $idx=>$row)
		{
			$data[$idx] = $this->_decrypt_data($row);
		}
		return $data;
	}
	//---------------------------------------------------------------
	/**
	 * Datatables server side processing
	 *
	 * @param array $fields List of fields that will display in the datatable
	 * @param boolean/array $result_callback Collection of methods that will be called after result data
	 * @param boolean/array $rearrange_fields Collection of field names from the list of $fields to re-arrange after result is processed in callback methods
	 *
	 * @return string JSON Encoded data for datatables
	 *
	 * @author Nicolai Vasquez, updates by Fred Timajo
	 * 
	 */
	public function datatables($fields = Array(), $result_callback = FALSE, $rearrange_fields=FALSE)
	{
		if (is_array($fields) && count($fields) == 0)
		{
			$not_included = Array(
				$this->created_field,
				$this->modified_field,
				$this->deleted,
				$this->deleted_field,
			);
			foreach($this->db->list_fields($this->table) as $fld)
			{
				if (!in_array($fld, $not_included))
					$fields[] = $fld;
			}
		}
		$aColumns = $fields;
		$aColumns_alias = Array();
		foreach($aColumns as $aColumn_idx=>$aColumn)
		{
			if(preg_match("/ ([0-9-a-z\_]+)$/im", $aColumn, $matches) > 0)
			{
				$aColumns_alias[$aColumn_idx] = $matches[1];
			}
			else
			{
				$aColumn = preg_replace("/^([0-9-a-z\_]+)\./im", '', $aColumn);
				$aColumns_alias[$aColumn_idx] = $aColumn;
			}
		}
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		$iSortCol_0 = $this->input->get('iSortCol_0');
		$iSortingCols = $this->input->get('iSortingCols');
		$sSearch = $this->input->get('sSearch');
		$sEcho = $this->input->get('sEcho');
		// paging
		if (isset($iDisplayStart) && $iDisplayLength != '-1')
		{
			$this->db->limit($iDisplayLength, $iDisplayStart);
		}
		// ordering
		if(isset($iSortCol_0))
		{
			for($i=0; $i<intval($iSortingCols); $i++)
			{
				$iSortCol = $this->input->get('iSortCol_'.$i);
				$bSortable = $this->input->get('bSortable_'.intval($iSortCol));
				$sSortDir = $this->input->get('sSortDir_'.$i);
				if($bSortable == 'true')
				{
					$this->db->order_by($aColumns[intval($this->db->escape_str($iSortCol))], $this->db->escape_str($sSortDir));
				}
			}
		}
		// filtering
		if(isset($sSearch) && !empty($sSearch))
		{
			$likes = array();
			for($i=0; $i<count($aColumns); $i++)
			{
				$bSearchable = $this->input->get('bSearchable_'.$i);
				// Individual column filtering
				if(isset($bSearchable) && $bSearchable == 'true')
				{
					$likes[] = "{$aColumns[$i]} LIKE '%{$this->db->escape_like_str($sSearch)}%'";
					// $this->db->or_like($aColumns[$i], $this->db->escape_like_str($sSearch));
				}
				
			}
			$where = '(' . implode(' OR ', $likes) . ')';
			$this->db->where($where);
		}
		// select
		$this->db->select('SQL_CALC_FOUND_ROWS '.str_replace(' , ', ' ', implode(', ', $aColumns)), false);
		$rResult = count($this->db->ar_from) ? $this->db->get() : $this->db->where($this->deleted, 0)->get($this->table);
		debug($this->db->last_query());
		// total filtered rows
		$this->db->select('FOUND_ROWS() AS found_rows');
		$iFilteredTotal = $this->db->get()->row()->found_rows;
		// total rows
		$iTotal = $this->db->count_all($this->table);
		// output
		$output = array(
			'sEcho' => intval($sEcho),
			'iTotalRecords' => $iTotal,
			'iTotalDisplayRecords' => $iFilteredTotal,
			'aaData' => array()
		);
		$result = $rResult->result_array();
		if(($result_callback !== FALSE) && count($result_callback))
		{
			foreach($result_callback as $callback)
			{
				$params = Array();
				if(isset($callback['params']))
				{
					$params = $callback['params'];
				}
				array_unshift($params, $result);
				$method = FALSE;
				if(isset($callback['method']))
				{
					$method = $callback['method'];
				}
				if($method)
				{
					$result = call_user_func_array($method,$params);
				}
			}
		}
		if(isset($iSortCol_0) && ($rearrange_fields !== FALSE))
		{
			if(!is_array($rearrange_fields))
				$rearrange_fields = array($rearrange_fields);
			$sorted_column = $aColumns_alias[$iSortCol_0];
			$sorted_columns = Array();
			if(in_array($sorted_column,$rearrange_fields))
			{
				foreach($result as $idx=>$row)
				{
					$sorted_columns[$idx] = $row[$sorted_column];
				}
				array_multisort($sorted_columns, ( ($sSortDir=='asc') ? SORT_ASC : SORT_DESC ), $result);
			}
		}
		foreach($result as $aRow)
		{	//debug($aRow);
			$row = array();            
			foreach($aColumns_alias as $col)
			{
				$row[] = $aRow[$col];
			}
			// additional row for the action buttons
			$row[] = '';
			$output['aaData'][] = $row;
		}
		return json_encode($output);
	}
}
// END: Class MY_model
/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */