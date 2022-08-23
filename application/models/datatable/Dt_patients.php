<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'core/Datatable_Model.php');

class Dt_patients extends Datatable_Model
{
    protected $table = 'pasien';
    protected $column_order = [
        null,
        'p.nama',
        'p.jenis_kelamin',
        'p.alamat',
        'p.no_hp',
        'p.umur',
        'p.tinggi_lutut',
        'p.tinggi_badan',
        'b.berat_badan_ideal',
        null
    ];

    protected $column_search = [
        'p.nama',
        'p.jenis_kelamin',
        'p.alamat',
        'p.no_hp',
        'p.umur',
        'p.tinggi_lutut',
        'p.tinggi_badan',
        'b.berat_badan_ideal',
    ];
    protected $order = [
        'p.created_at' => 'DESC',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function _select_query()
    {
        $this->db->select([
            'p.id',
            'p.nama',
            'p.jenis_kelamin',
            'p.alamat',
            'p.no_hp',
            'p.umur',
            'p.tinggi_lutut',
            'p.tinggi_badan',
            'b.berat_badan_ideal',
            'p.is_active'
        ]);
        $this->db->from($this->table . ' p');
        $this->db->join('pasien_bbi b', 'p.id=b.pasien_id', 'left');
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
