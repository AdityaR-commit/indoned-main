<?php

class M_foods extends CI_Model
{
    protected $table = 'foods';
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

    public function foodData($id)
    {
        $this->db->select(['foods.*', 'c.category']);
        $this->db->join('food_categories c', 'foods.category_id = c.id', 'left');
        $this->db->where(['foods.id' => $id]);
        $data = $this->db->get('foods')->row_array();
        return $data;
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

    public function listFood()
    {
        $this->db->order_by('category_id', 'ASC');
        $data = $this->db->get('foods')->result_array();
        return $data;
    }
}
