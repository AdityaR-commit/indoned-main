<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'core/Datatable_Model.php');

class Dt_capel extends Datatable_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->table = 'orders';

        $this->column_order = [
            "{$this->table}.nama",
            "{$this->table}.created_at",
            'ref_paket_layanan_nama',
            'ref_desa_pemasangan_nama',
            "{$this->table}.no_hp",
            "{$this->table}.status",
        ];

        $this->column_search = [
            "{$this->table}.nama",
            'r_prdcts.name',
            'r_des_pem.nama_desa',
            "{$this->table}.no_hp",
            "{$this->table}.status",
        ];

        $this->order = [
            "{$this->table}.created_at" => 'DESC',
        ];
    }

    public function _select_query()
    {
        $this->db->select([
            "{$this->table}.nama",
            "{$this->table}.created_at",
            'r_prdcts.name       AS layanan',
            'r_des_pem.nama_desa AS desa_pemasangan',
            "{$this->table}.no_hp",
            "{$this->table}.status",
            "if(rc.phonecode is null or rc.phonecode = '', '62', rc.phonecode)",
            "concat(if(rc.phonecode is null or rc.phonecode = '', '62', rc.phonecode), {$this->table}.no_hp) as phone"
        ]);
        $this->db->from("{$this->table}");

        $this->db->where("$this->table.status", 'CAPEL');

        $this->db->join('ref_desa r_des_pem   ', "r_des_pem.kode_desa = {$this->table}.ref_desa_pemasangan_kode", 'LEFT');
        $this->db->join('ref_products r_prdcts', "r_prdcts.id         = {$this->table}.ref_product_id  ", 'LEFT');
        $this->db->join('ref_country rc', "rc.id = {$this->table}.ref_country_id  ", 'LEFT');
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
