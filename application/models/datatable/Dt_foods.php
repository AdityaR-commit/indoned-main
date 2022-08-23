<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'core/Datatable_Model.php');

class Dt_foods extends Datatable_Model
{
    protected $table = 'foods';
    protected $column_order = [
        null,
        'f.food',
        'c.category',
        'f.urt',
        'f.urt_unit',
        'f.berat',
        'f.keterangan',
        null
    ];

    protected $column_search = [
        'f.food',
        'c.category',
        'f.urt',
        'f.urt_unit',
        'f.berat',
        'f.keterangan',

    ];
    protected $order = [
        'f.created_at' => 'DESC',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function _select_query()
    {
        $this->db->select([
            'f.id',
            'f.food',
            'c.category',
            'f.urt',
            'f.urt_unit',
            'f.berat',
            'f.keterangan',
            'f.is_active',
        ]);
        $this->db->from($this->table . ' f');
        $this->db->join('food_categories c', 'f.category_id = c.id', 'LEFT');
    }

    public function _custom_search_query()
    {
        $this->_filter_status();
    }

    private function _filter_status()
    {
        if ($this->input->post('filter_status') && $this->input->post('filter_status')) {
            $this->db->where("{$this->table}.status", $this->input->post('filter_status'));
        };
    }
}
