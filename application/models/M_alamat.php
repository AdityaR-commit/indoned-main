<?php

class M_alamat extends CI_Model
{

  function __construct()
  {
    parent::__construct();
    $dbUsername = getSession('dbUsername');
    if ($dbUsername) {
      $this->load->database($dbUsername, FALSE, TRUE);
    }
  }

  public function list_provinsi()
  {
    $data = $this->db->get('ref_provinces')->result();
    return $data;
  }

  public function getProvinsiById($id)
  {
    $data = $this->db->where(['id' => $id])->get('ref_provinces');
    if ($data->num_rows() != 0) {
      return $data->row_array();
    }
    return null;
  }

  public function getKabupatenById($id)
  {
    $data = $this->db->where(['id' => $id])->get('ref_regencies');
    if ($data->num_rows() != 0) {
      return $data->row_array();
    }
    return null;
  }

  public function getAlamatById($id_member)
  {
    $data = $this->db
      ->select('members.alamat, ref_districts.name')
      ->from('members')
      ->join('ref_districts', 'members.kode_kecamatan = ref_districts.id')
      ->where(['members.id_member' => $id_member])
      ->get();

    if ($data->num_rows() != 0) {
      return $data->row_array();
    } else {
      return null;
    }
  }

  public function getKabupaten($id_provinsi)
  {
    $data = $this->db->where(['province_id' => $id_provinsi])->get('ref_regencies')->result_array();
    return $data;
  }

  public function getKecamatan($id_kabupaten)
  {
    $data = $this->db->where(['regency_id' => $id_kabupaten])->get('ref_districts')->result_array();
    return $data;
  }
}
