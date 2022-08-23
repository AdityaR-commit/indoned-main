<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'core/Datatable_Model.php');

class Dt_food_categories extends Datatable_Model
{
    protected $table = 'food_categories';
    protected $column_order = [
        null,
        'c.category',
        'c.energi',
        'c.karbohidrat',
        'c.protein',
        'c.lemak',
        'c.keterangan',
        null
    ];

    protected $column_search = [
        'c.category',
        'c.energi',
        'c.karbohidrat',
        'c.protein',
        'c.lemak',
        'c.keterangan',

    ];
    protected $order = [
        'c.created_at' => 'DESC',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function _select_query()
    {
        $this->db->select([
            'c.id',
            'c.category',
            'c.energi',
            'c.karbohidrat',
            'c.protein',
            'c.lemak',
            'c.keterangan',
            'c.is_active',
        ]);
        $this->db->from($this->table . ' c');
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
