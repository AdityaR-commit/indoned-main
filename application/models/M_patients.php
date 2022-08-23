<?php

class M_patients extends CI_Model
{

    protected $table = 'pasien';
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

    public function patientData($id)
    {
        $this->db->select(['p.*', 'b.berat_badan_ideal']);
        $this->db->where(['p.id' => $id]);
        $this->db->join('pasien_bbi b', 'p.id = b.pasien_id', 'left');
        $data = $this->db->get($this->table . ' p')->row_array();
        return $data;
    }

    public function listData()
    {
        $data = $this->db->get($this->table)->result_array();
        return $data;
    }

    public function listPasienEnergiNotExist()
    {
        $pasien = $this->pasienCompleteEstimasi();
        $this->db->where_not_in('p.id', $pasien);
        $data = $this->db->get('pasien p')->result_array();
        return $data;
    }

    private function pasienCompleteEstimasi()
    {
        $patiens = $this->db->get('pasien')->result_array();
        $pasienId = [];
        foreach ($patiens as $key => $pasien) {
            $this->db->where(['pasien_id' => $pasien['id']]);
            $this->db->where_in('jenis', ['ENERGI', 'KARBOHIDRAT', 'PROTEIN', 'LEMAK']);
            $cek = $this->db->get('pasien_estimasi')->result_array();
            if (count($cek) == 4) {
                $pasienId[] = $cek[0]['pasien_id'];
            }
        }
        return $pasienId;
        // $this->db->select([
        //     'pm.id',
        //     'pm.jenis',
        //     'p.id as pasien_id',
        //     'p.nama',
        // ]);
        // $this->db->join('pasien p', 'pm.pasien_id = p.id');
        // $pasien = $this->db->get('pasien_estimasi pm')->result_array();
        // $return = null;
        // if (!empty($pasien)) {
        //     foreach ($pasien as $i => $d) {
        //         $return[] = $d['pasien_id'];
        //     }
        // }
        // return $return;
    }

    public function hitungBBI($input)
    {
        $dataPasien = $this->patientData($input['id']);
        $bbi = null;
        if ($dataPasien['jenis_kelamin'] == 'Laki-laki') {
            $bbi = ($dataPasien['tinggi_badan'] - 100) - (($dataPasien['tinggi_badan'] - 100) * 10 / 100);
        } elseif ($dataPasien['jenis_kelamin'] == 'Perempuan') {
            $bbi = ($dataPasien['tinggi_badan'] - 100) - (($dataPasien['tinggi_badan'] - 100) * 15 / 100);
        }
        $data = [
            'pasien_id'         => $dataPasien['id'],
            'berat_badan_ideal' => $bbi,
            'created_by'        => getSession('userId'),
        ];
        return $this->db->insert('pasien_bbi', $data);
    }

    public function insertEstimasi($input)
    {
        $dataPasien = $this->m_patients->patientData($input['pasien_id']);
        $estimasi = 0;
        // if ($input['jenis'] == 'ENERGI') {
        $estimasi = $input['konstanta'] * $dataPasien['berat_badan_ideal'];
        // } elseif ($input['jenis'] == 'KARBOHIDRAT') {
        //     $estimasi = $input['kostanta'] * $dataPasien['berat_badan_ideal'];
        // }
        $this->db->trans_begin();
        $data = [
            'pasien_id'         => $dataPasien['id'],
            'konstanta'         => $input['konstanta'],
            'jenis'             => $input['jenis'],
            'kebutuhan'         => $estimasi,
            'created_by'        => getSession('userId'),
        ];
        $this->db->insert('pasien_estimasi', $data);

        $this->db->where_in('jenis', ['ENERGI', 'KARBOHIDRAT', 'PROTEIN', 'LEMAK']);
        $this->db->where(['pasien_id' => $dataPasien['id']]);
        $cek = $this->db->get('pasien_estimasi')->result_array();

        if (count($cek) == 4) {
            $this->db->where(['id' => $dataPasien['id']]);
            $this->db->update($this->table, ['is_complete' => 1]);
        }
        // $this->trans_complete();
        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
        // return 
    }

    public function listPasienScreening()
    {
        $pasien = $this->pasienCompleteEstimasi();
        $this->db->where_in('id', $pasien);
        $data = $this->db->get($this->table)->result_array();
        return $data;
    }

    public function listSlotMakan()
    {
        $data = $this->db->get('ref_slot_makan')->result_array();
        return $data;
    }

    // public function listSlotMakan($pasienId)
    // {
    //     $this->db->select([
    //         'm.id as slot_id',
    //         'slot_makan',
    //         'p.*'
    //     ]);
    //     $this->db->where(['pm.pasien_id' => $pasienId]);
    //     $this->db->join('pasien_makan pm', 'pm.slot_makan_id = m.id', 'left');
    //     $this->db->join('pasien p', 'pm.pasien_id = p.id', 'left');
    //     $data = $this->db->get('ref_slot_makan m')->result_array();
    //     //         SELECT m.id AS slot_id, m.slot_makan, p.*
    //     // FROM ref_slot_makan m
    //     // LEFT JOIN pasien_makan pm ON pm.slot_makan_id = m.id
    //     // LEFT JOIN pasien p ON p.id = pm.pasien_id
    //     return $data;
    // }

    public function getKebutuhan($pasienId, $jenis)
    {
        $this->db->where(['pasien_id' => $pasienId, 'jenis' => $jenis]);
        $data = $this->db->get('pasien_estimasi')->row_array();
        return $data;
    }

    public function pasienSlotMakan($pasienId)
    {
        $this->db->select([
            'pm.*',
            'p.nama',
            'm.slot_makan'
        ]);
        $this->db->join('ref_slot_makan m', 'pm.slot_makan_id = m.id', 'left');
        $this->db->join($this->table . ' p', 'pm.pasien_id = p.id', 'left');
        $this->db->where(['pm.pasien_id' => $pasienId]);
        $data = $this->db->get('pasien_makan pm')->result_array();
        if (!empty($data)) {
            foreach ($data as $key => $detail) {
                $this->db->where(['makan_id' => $detail['id']]);
                $data[$key]['detail'] = $this->db->get('pasien_makan_detail')->result_array();
            }
        }
        return $data;
    }

    public function prosesScreening($data)
    {
        $this->db->debug = false;
        $error = null;
        $return = ['status' => null, 'message' => null, 'error' => null];
        $this->db->trans_begin();
        $makanId = null;
        $whereMakan = ['slot_makan_id' => $data['slot_makan_id'], 'pasien_id' => $data['pasien_id']];
        $this->db->where($whereMakan);
        $cek = $this->db->get('pasien_makan')->row_array();
        // echo '<pre>';
        // print_r($cek);
        // echo '</pre>';
        // die;
        if (!empty($cek)) {
            $makanId = $cek['id'];
            $this->db->where(['makan_id' => $cek['id']]);
            $this->db->delete('pasien_makan_detail');
            if ($this->db->error()["code"] > 0) {
                $error[] = [
                    'error' => $this->db->error(),
                    'query' => $this->db->last_query()
                ];
            }
        } else {
            $pasienMakan = [
                'pasien_id'     => $data['pasien_id'],
                'slot_makan_id' => $data['slot_makan_id'],
                'created_by'    => getSession('userId'),
            ];
            $this->db->insert('pasien_makan', $pasienMakan);
            $makanId = $this->db->insert_id();
            if ($this->db->error()["code"] > 0) {
                $error[] = [
                    'error' => $this->db->error(),
                    'query' => $this->db->last_query()
                ];
            }
        }

        for ($i = 0; $i < count($data['food']); $i++) {
            $foodMakan = [
                'makan_id'      => $makanId,
                'food_id'       => $data['food'][$i],
                'berat'         => $data['berat'][$i],
                'keterangan'    => $data['keterangan'][$i],
                'created_by'    => getSession('userId'),
            ];
            $this->db->insert('pasien_makan_detail', $foodMakan);
            if ($this->db->error()["code"] > 0) {
                $error[] = [
                    'error' => $this->db->error(),
                    'query' => $this->db->last_query()
                ];
            }
        }

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $return['status'] = true;
            $return['message'] = 'Proses screening berhasil';
            // return true;
        } else {
            $this->db->trans_rollback();
            $return['status'] = false;
            $return['message'] = 'Proses screening gagal';
            $return['error'] = $error;
            // return false;
        }

        return $return;
    }
}
