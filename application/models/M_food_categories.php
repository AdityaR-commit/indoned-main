<?php

class M_food_categories extends CI_Model
{

    protected $table = 'food_categories';
    function __construct()
    {
        parent::__construct();
        $dbUsername = getSession('dbUsername');
        if ($dbUsername) {
            $this->load->database($dbUsername, FALSE, TRUE);
        }
    }

    public function insertData($data)
    {
        $data['created_by'] = getSession('userId');
        return $this->db->insert($this->table, $data);
    }

    public function updateData($id, $data)
    {
        $this->db->where(['id' => $id]);
        $proses = $this->db->update($this->table, $data);
        return $proses;
    }

    public function deleteData($id)
    {
        $this->db->where(['id' => $id]);
        $proses = $this->db->delete($this->table);
        return $proses;
    }

    public function categoryData($id)
    {
        $this->db->where(['id' => $id]);
        $data = $this->db->get($this->table)->row_array();
        return $data;
    }

    public function listData()
    {
        $data = $this->db->get('food_categories')->result_array();
        return $data;
    }
}
