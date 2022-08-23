<?php defined('BASEPATH')OR exit('no access allowed');
/**
  * summary
  */
 class MY_Model extends CI_Model
 {
 	protected $_table_name;
 	protected $_order_by;
 	protected $_order_by_type;
 	protected $_primary_filtered = 'intval';
 	protected $_primary_key;
 	protected $_type;
     /**
      * summary
      */
     public function __construct()
     {
         parent::__construct();
         $this->db->query("SET sql_mode = '' ");
     }

     public function insert($data, $batch = false)
     {
     	if ($batch == true) {
     		$this->db->insert($this->_table_name, $data);
     	}else{
     		$this->db->set($data);
     		$this->db->insert($this->_table_name);
     		if ($this->db->affected_rows()) {
                    return true;
               }else{
                    return false;
               }
     	}
     }

     public function update($data, $where = array())
     {
     	$this->db->set($data);
     	$this->db->where($where);
     	$this->db->update($this->_table_name);
          if ($this->db->affected_rows()) {
               return true;
          }else{
               return false;
          }
     }

     public function get($id = null, $single = false)
     {
     	if ($id  != null) {
     		$filter = $this->_primary_filtered;
     		$this->db->where($this->_primary_key, $id);
     		$methos = 'row_array';

     	}else if($single == true){
     		$methos = 'row_array';

     	}else{
     		$methos = 'result_array';

     	}

     	if ($this->_order_by_type) {
     		$this->db->order_by($this->_order_by, $this->_order_by_type);
     	}else{
     		$this->db->order_by($this->order_by);
     	}

     	return $this->db->get($this->_table_name)->$methos();
     }

     public function get_by($where = null, $limit = null, $offset = null, $single = false, $select = null)
     {
     	if ($select != null) {
     		$this->db->select($select);
     	}

     	if ($where) {
     		$this->db->where($where);
     	}

     	if (($limit) && ($offset)) {
     		$this->db->limit($limit, $offset);
     	}else if($limit){
     		$this->db->limit($limit);
     	}
     	return $this->db->get(null, $single);
     }


     public function delete($id)
     {
     	$filter = $this->_primary_filtered;
     	// $id = $filter($id);

     	// if (!$id) {
     	// 	return false;
     	// }

     	$this->db->where($this->_primary_key, $id);
     	// $this->db->limit(1);
     	$this->db->delete($this->_table_name);
          if ($this->db->affected_rows()) {
               return true;
          }else{
               return false;
          }
     }

     public function delete_by($where = null)
     {
     	if ($where) {
     		$this->db->where($where);
     	}

     	$this->db->delete($this->_table_name);
     }

     public function count($where = null)
     {
     	if ($where) {
     		$this->db->where($where);
     	}

     	$this->db->from($this->_table_name);
     	return $this->db->count_all_results();
     }


 } ?>