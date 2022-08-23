<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'core/Datatable_Model.php');

class Dt_screening extends Datatable_Model
{
    protected $table = 'pasien';
    protected $column_order = [
        null,
        'p.nama',
        'p.jenis_kelamin',
        'p.alamat',
        'b.berat_badan_ideal',
    ];

    protected $column_search = [
        'p.nama',
        'p.jenis_kelamin',
        'b.berat_badan_ideal',
    ];
    protected $order = [
        'p.nama' => 'ASC',
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
        ]);
        $this->db->from($this->table . ' p');
        $this->db->join('pasien_bbi b', 'p.id = b.pasien_id');
        $this->db->where(['p.is_complete' => 1]);
        // $this->db->join('pasien_energi e', 'p.id=e.pasien_id');
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
