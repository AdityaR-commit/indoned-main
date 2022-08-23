<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Foods extends BaseController
{

    public $loginBehavior = true;
    protected $module = 'foods';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('datatable/Dt_foods', 'dt_foods');
        $this->load->model('M_food_categories', 'm_foodcategories');
        $this->load->model('M_foods', 'm_foods');

        $this->bread[] = ['title' => 'Dashboard', 'url' => site_url('dashboard')];
        $this->bread[] = ['title' => 'Bahan Makanan', 'url' => site_url('foods')];
    }

    public function index()
    {
        $this->data['title'] = 'Bahan Makanan';
        $this->data['scripts'][] = 'foods/js/index.js';
        $this->data['food_categories'] = $this->m_foodcategories->listData();
        $this->render('index');
    }

    public function datatable()
    {
        $list = $this->dt_foods->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $pel) {
            $no++;
            $row = array();
            $row[] = '<div class="text-center">
            <div class="btn-group" role="group">
                <button type="button" onclick="detail(' . "'" . $pel->id . "'" . ')" data-toggle="tooltip" title="Detail" class="btn btn-xs btn-success text-light"><i class="fas fa-eye fa-fw"></i></button>
                <button type="button" onclick="ubah(' . "'" . $pel->id . "'" . ')" data-toggle="tooltip" title="Ubah" class="btn btn-xs btn-warning text-light"><i class="fas fa-edit fa-fw"></i></button>
                <button type="button" onclick="hapus(' . "'" . $pel->id . "'" . ')" data-toggle="tooltip" title="Hapus" class="btn btn-xs btn-danger text-light"><i class="fas fa-trash fa-fw"></i></button>
            </div></div>';
            $row[] = '<p class="text-center">' . $no . '</p>';
            $row[] = $pel->food;
            $row[] = $pel->category;
            $row[] = str_replace('.', ',', floatval($pel->urt)) . ' ' . $pel->urt_unit;
            // $row[] = 
            $row[] = $pel->berat;
            $row[] = $pel->keterangan;
            $row[] = $pel->is_active == '1' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak aktif</span>';
            $data[] = $row;
        }

        $output = array(
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->dt_foods->count_all(),
            "recordsFiltered" => $this->dt_foods->count_filtered(),
            "data"            => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function ajaxTambah()
    {
        $input = $this->input->post();
        $proses = $this->m_foods->insertData($input);
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

    public function ajaxDetail()
    {
        $input = $this->input->post();
        $data = $this->m_foods->foodData($input['id']);
        echo json_encode($data);
    }

    public function ajaxUbah()
    {
        $input = $this->input->post();
        $data = $input;
        unset($data['id']);
        $proses = $this->m_foods->updateData($input['id'], $data);
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

        $proses = $this->m_foods->deleteData($input['id']);
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
}
