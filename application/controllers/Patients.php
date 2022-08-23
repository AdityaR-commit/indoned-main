<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Patients extends BaseController
{

    public $loginBehavior = true;
    protected $module = 'pasien';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('datatable/Dt_patients', 'dt_patients');
        $this->load->model('M_patients', 'm_patients');
        $this->bread[] = ['title' => 'Dashboard', 'url' => site_url('dashboard')];
        $this->bread[] = ['title' => 'Data Pasien', 'url' => site_url('patients')];
    }

    public function index()
    {
        $this->data['title'] = 'Data Pasien';
        $this->data['scripts'][] = 'patients/js/index.js';
        $this->render('index');
    }

    public function datatable()
    {
        $list = $this->dt_patients->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $pel) {
            $no++;
            $row = array();
            $btnEdit = null;
            if (empty($pel->berat_badan_ideal)) {
                $btnEdit = '<button type="button" onclick="ubah(' . "'" . $pel->id . "'" . ')" data-toggle="tooltip" title="Ubah" class="btn btn-xs btn-warning text-light"><i class="fas fa-edit fa-fw"></i></button>';
            }
            $row[] = '<div class="text-center">
            <div class="btn-group" role="group">
                <button type="button" onclick="detail(' . "'" . $pel->id . "'" . ')" data-toggle="tooltip" title="Detail" class="btn btn-xs btn-success text-light"><i class="fas fa-eye fa-fw"></i></button>' . $btnEdit .
                '<button type="button" onclick="hapus(' . "'" . $pel->id . "'" . ')" data-toggle="tooltip" title="Hapus" class="btn btn-xs btn-danger text-light"><i class="fas fa-trash fa-fw"></i></button>
            </div></div>';
            $row[] = '<p class="text-center">' . $no . '</p>';
            $row[] = $pel->nama;
            $row[] = $pel->jenis_kelamin;
            $row[] = $pel->alamat;
            $row[] = $pel->no_hp;
            $row[] = $pel->umur;
            $row[] = str_replace('.', ',', floatval($pel->tinggi_lutut));
            $row[] = str_replace('.', ',', floatval($pel->tinggi_badan));
            // $row[] = $pel->tinggi_badan;
            $row[] = !empty($pel->berat_badan_ideal) ? $pel->berat_badan_ideal : '<button type="button" class="btn btn-success btn-xs" onclick="hitung(' . "'" . $pel->id . "'" . ')">Hitung</button>';
            $row[] = $pel->is_active == '1' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak aktif</span>';
            $data[] = $row;
        }

        $output = array(
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->dt_patients->count_all(),
            "recordsFiltered" => $this->dt_patients->count_filtered(),
            "data"            => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function ajaxTambah()
    {
        $input = $this->input->post();

        $data = $input;
        unset($data['id']);
        unset($data['estimasi_opsi']);
        unset($data['rumus_opsi']);
        if ($input['estimasi_opsi'] == 'tinggi_lutut') {
            $data['metode'] = 'TINGGI LUTUT';
        } elseif ($input['estimasi_opsi'] == 'panjang_badan') {
            $data['metode'] = 'PANJANG BADAN';
        }
        $data['rumus'] = strtoupper($input['rumus_opsi']);
        $data['tinggi_badan'] = $this->hitungEstimasi($input['rumus_opsi'], $input);

        $proses = $this->m_patients->insertData($data);
        if ($proses) {
            return $this->responseJSON(
                200,
                [
                    "message"   => [
                        "title" => "Sukses",
                        "body"  => "Berhasil menambahkan data"
                    ]
                ]
            );
        } else {
            return $this->responseJSON(
                500,
                [
                    "message"   => [
                        "title" => "Gagal",
                        "body"  => "Gagal menambahkan data"
                    ]
                ]
            );
        }
    }

    private function hitungEstimasi($rumus, $input)
    {
        $return = null;
        if ($rumus == 'CHUMLEA') {
            if ($input['jenis_kelamin'] == 'Laki-laki') {
                $return = 64.19 - (0.04 * $input['umur']) + (2.02 * $input['tinggi_lutut']);
            } elseif ($input['jenis_kelamin'] == 'Perempuan') {
                $return = 84.88 - (0.24 * $input['umur']) + (1.83 * $input['tinggi_lutut']);
            }
        } elseif ($rumus == 'OKTAVIANUS') {
            if ($input['jenis_kelamin'] == 'Laki-laki') {
                $return = 64.19 + (2.03 * $input['tinggi_lutut']) - (0.04 * $input['umur']);
            } elseif ($input['jenis_kelamin'] == 'Perempuan') {
                $return = 84.88 + (1.83 * $input['tinggi_lutut']) - (0.24 * $input['umur']);
            }
        } elseif ($rumus == 'FATMAH') {
            if ($input['jenis_kelamin'] == 'Laki-laki') {
                $return = 56.343 + (2.102 * $input['tinggi_lutut']);
            } elseif ($input['jenis_kelamin'] == 'Perempuan') {
                $return = 62.682 + (1.889 * $input['tinggi_lutut']);
            }
        }

        return $return;
    }

    public function ajaxUbah()
    {
        $input = $this->input->post();
        $data = $input;
        unset($data['id']);
        unset($data['estimasi_opsi']);
        unset($data['rumus_opsi']);
        if ($input['estimasi_opsi'] == 'tinggi_lutut') {
            $data['metode'] = 'TINGGI LUTUT';
        } elseif ($input['estimasi_opsi'] == 'panjang_badan') {
            $data['metode'] = 'PANJANG BADAN';
        }
        $data['rumus'] = strtoupper($input['rumus_opsi']);

        $data['tinggi_badan'] = $this->hitungEstimasi($input['rumus_opsi'], $input);

        $proses = $this->m_patients->updateData($input['id'], $data);

        if ($proses) {
            return $this->responseJSON(
                200,
                [
                    "message"   => [
                        "title" => "Sukses",
                        "body"  => "Berhasil mengubah data"
                    ]
                ]
            );
        } else {
            return $this->responseJSON(
                500,
                [
                    "message"   => [
                        "title" => "Gagal",
                        "body"  => "Gagal mengubah data"
                    ]
                ]
            );
        }
    }

    public function ajaxHapus()
    {
        $input = $this->input->post();

        $proses = $this->m_foodcategories->deleteData($input['id']);
        if ($proses) {
            return $this->responseJSON(
                200,
                [
                    "message"   => [
                        "title" => "Sukses",
                        "body"  => "Berhasil menghapus data"
                    ]
                ]
            );
        } else {
            return $this->responseJSON(
                500,
                [
                    "message"   => [
                        "title" => "Gagal",
                        "body"  => "Gagal menghapus data"
                    ]
                ]
            );
        }
    }

    public function ajaxDetail()
    {
        $input = $this->input->post();
        $data = $this->m_patients->patientData($input['id']);
        $estimasi_opsi = $data['metode'] == 'TINGGI LUTUT' ? 'tinggi_lutut' : 'panjang_badan';
        $data['estimasi_opsi'] = $estimasi_opsi;
        $data['rumus_opsi'] = $data['rumus'];
        echo json_encode($data);
    }

    public function ajaxHitung()
    {
        $input = $this->input->post();
        $proses = $this->m_patients->hitungBBI($input);
        if ($proses) {
            return $this->responseJSON(
                200,
                [
                    "message"   => [
                        "title" => "Sukses",
                        "body"  => "Berhasil menghitung BBI"
                    ]
                ]
            );
        } else {
            return $this->responseJSON(
                500,
                [
                    "message"   => [
                        "title" => "Gagal",
                        "body"  => "Gagal menghitung BBI"
                    ]
                ]
            );
        }
    }
}
