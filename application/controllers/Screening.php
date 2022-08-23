<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Screening extends BaseController
{

    public $loginBehavior = true;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('datatable/Dt_screening', 'dt_screening');
        $this->load->model('M_patients', 'm_patients');
        $this->load->model('M_foods', 'm_foods');
        $this->bread[] = ['title' => 'Dashboard', 'url' => site_url('dashboard')];
        $this->bread[] = ['title' => 'Data Pasien', 'url' => site_url('patients')];
        $this->bread[] = ['title' => 'Screening', 'url' => site_url('screening')];
    }

    public function index()
    {
        $this->data['title'] = 'Screening Pasien';
        $this->data['scripts'][] = strtolower(get_class($this)) . '/js/index.js';
        $this->render('index');
    }

    public function detail($pasienId = null)
    {
        if (empty($pasienId)) {
            redirect('screening');
        }
        $this->bread[] = ['title' => 'Detail', 'url' => site_url('screening/detail')];
        $this->data['title'] = 'Screening Makan Pasien';
        $pasien = $this->m_patients->patientData($pasienId);
        $pasien['screening'] = $this->m_patients->pasienSlotMakan($pasienId);
        $this->data['pasien'] = $pasien;
        $this->data['slot_makan'] = $this->m_patients->listSlotMakan();
        $this->data['foods'] = $this->m_foods->listFood();
        $this->data['scripts'][] = strtolower(get_class($this)) . '/js/detail.js';
        $this->render('detail');
    }

    public function datatable()
    {
        $list = $this->dt_screening->get_datatables();
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
            $row[] = '<a href="' . site_url('screening/detail/' . $pel->id) . '" class="btn btn-success"><i class="fa fa-search"></i> Lihat</a>';
            $row[] = null;
            $data[] = $row;
        }

        $output = array(
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->dt_screening->count_all(),
            "recordsFiltered" => $this->dt_screening->count_filtered(),
            "data"            => $data,
        );
        //output to json format
        echo json_encode($output);
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
                        "body"  => "Kebutuhan energi pasien belum diketahui, silahkan lakukan perhitungan terlebih dahulu"
                    ]
                ]
            );
        }
        $estimasi_opsi = $data['metode'] == 'TINGGI LUTUT' ? 'tinggi_lutut' : 'panjang_badan';
        $data['estimasi_opsi'] = $estimasi_opsi;
        $data['rumus_opsi'] = $data['rumus'];
        $slot_makan = $this->m_patients->listSlotMakan($input['id']);
        // echo json_encode($data);
        return $this->responseJSON(200, [
            "message"   => [
                "title" => "Sukses",
                "body"  => "Detail pasien"
            ],
            "data"  => [
                "pasien"        => $data,
                "slot_makan"    => $slot_makan,
            ]
        ]);
    }

    public function prosesScreening()
    {
        $input = $this->input->post();
        $proses = $this->m_patients->prosesScreening($input);
        if ($proses['status']) {
            return $this->responseJSON(
                200,
                [
                    "message"   => [
                        "title" => "Sukses",
                        "body"  => $proses['message']
                    ]
                ]
            );
        } else {
            return $this->responseJSON(
                500,
                [
                    "message"   => [
                        "title" => "Gagal",
                        "body"  => $proses['message']
                    ],
                    "error"     => $proses['error']
                ]
            );
        }
    }
}
