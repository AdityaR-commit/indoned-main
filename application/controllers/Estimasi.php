<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Estimasi extends BaseController
{

    public $loginBehavior = true;
    protected $module = 'estimasi';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('datatable/Dt_estimasi', 'dt_estimasi');
        $this->load->model('M_patients', 'm_patients');
        $this->bread[] = ['title' => 'Dashboard', 'url' => site_url('dashboard')];
        $this->bread[] = ['title' => 'Data Pasien', 'url' => site_url('patients')];
        $this->bread[] = ['title' => 'Kebutuhan Pasien', 'url' => site_url('estimasi')];
    }

    public function index()
    {
        $this->data['title'] = 'Perhitungan Estimasi Kebutuhan Pasien';
        // $pasien = $this->m_patients->listPasienEnergiNotExist();
        // $this->data['pasien'] = $pasien;
        $this->data['scripts'][] = strtolower(get_class($this)) . '/js/index.js';
        $this->render('index');
    }

    public function datatable()
    {
        $list = $this->dt_estimasi->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $pel) {
            $no++;
            $row = array();
            $row[] = '<p class="text-center">' . $no . '</p>';
            $row[] = $pel->nama . '<br>No HP: ' . $pel->no_hp;
            $row[] = $pel->jenis_kelamin;
            $row[] = $pel->alamat;
            $row[] = $pel->berat_badan_ideal;
            $energi = $this->m_patients->getKebutuhan($pel->id, 'ENERGI');
            $karbohidrat = $this->m_patients->getKebutuhan($pel->id, 'KARBOHIDRAT');
            $protein = $this->m_patients->getKebutuhan($pel->id, 'PROTEIN');
            $lemak = $this->m_patients->getKebutuhan($pel->id, 'LEMAK');
            $row[] = !empty($energi) ? $energi['kebutuhan'] : '<button type="button" onclick="hitung(' . "'" . $pel->id . "','ENERGI'" . ')" class="btn btn-success btn-xs"><i class="fa fa-calculator"></i> Hitung</button>';
            $row[] = !empty($karbohidrat) ? $karbohidrat['kebutuhan'] : '<button type="button" onclick="hitung(' . "'" . $pel->id . "','KARBOHIDRAT'" . ')" class="btn btn-success btn-xs"><i class="fa fa-calculator"></i> Hitung</button>';
            $row[] = !empty($protein) ? $protein['kebutuhan'] : '<button type="button" onclick="hitung(' . "'" . $pel->id . "','PROTEIN'" . ')" class="btn btn-success btn-xs"><i class="fa fa-calculator"></i> Hitung</button>';
            $row[] = !empty($lemak) ? $lemak['kebutuhan'] : '<button type="button" onclick="hitung(' . "'" . $pel->id . "','LEMAK'" . ')" class="btn btn-success btn-xs"><i class="fa fa-calculator"></i> Hitung</button>';
            $data[] = $row;
        }

        $output = array(
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->dt_estimasi->count_all(),
            "recordsFiltered" => $this->dt_estimasi->count_filtered(),
            "data"            => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function hitungEstimasi()
    {
        $input = $this->input->post();
        $proses = $this->m_patients->insertEstimasi($input);
        if ($proses) {
            return $this->responseJSON(
                200,
                [
                    "message"   => [
                        "title" => "Sukses",
                        "body"  => "Berhasil menghitung estimasi kebutuhan " . strtolower($input['jenis'])
                    ]
                ]
            );
        } else {
            return $this->responseJSON(
                500,
                [
                    "message"   => [
                        "title" => "Gagal",
                        "body"  => "Gagal menghitung estimasi kebutuhan " . strtolower($input['jenis'])
                    ]
                ]
            );
        }
    }

    public function pasienData()
    {
        $input = $this->input->post();
        $data = $this->m_patients->patientData($input['id']);
        if (empty($data['berat_badan_ideal'])) {
            return $this->responseJSON(
                500,
                [
                    "message"   => [
                        "title" => "Gagal",
                        "body"  => "BBI pasien belum diketahui, silahkan lakukan perhitungan terlebih dahulu"
                    ]
                ]
            );
        }
        $estimasi_opsi = $data['metode'] == 'TINGGI LUTUT' ? 'tinggi_lutut' : 'panjang_badan';
        $data['estimasi_opsi'] = $estimasi_opsi;
        $data['rumus_opsi'] = $data['rumus'];
        // echo json_encode($data);
        return $this->responseJSON(200, [
            "message"   => [
                "title" => "Sukses",
                "body"  => "Detail pasien"
            ],
            "data"  => [
                "data" => $data,
            ]
        ]);
    }
}
