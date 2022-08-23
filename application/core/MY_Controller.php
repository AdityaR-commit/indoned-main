<?php

use app\libraries\Arkatama\Recaptcha;

class BaseController extends CI_Controller
{

    protected $template = "app";
    protected $module = "";
    protected $data = array();
    protected $bread = [];
    private $whitelistUrl = [
        '',
        'forgot_password',
        'forgot_password_reset',
        'aktifkan_akun',
        'logout',
        'index/login',
        'front/pre_order'
    ];
    public $loginBehavior = true;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_activity_log');
        $this->data['requiredLabel'] = '<b class="text-danger">*</b>';
        $userId = getSession('userId');
        if (uri_string() == "" && $this->input->post("login-button") != null) { // login form submit
            $username = $this->input->post("username");
            $password = trim($this->input->post("password"));
            $this->db->select([
                "a.id_user", "a.username", "a.email", "a.real_name", "a.id_group", "a.is_active",
                "b.nama_group", "b.keterangan", "b.dbusername"
            ]);
            $this->db->where('((u.username IS NOT NULL AND u.username = ' . '"' . $username . '"' . ') OR (u.email IS NOT NULL AND u.email = ' . '"' . $username . '"' . '))');
            $this->db->join('user_group ug', 'ug.id_group = u.id_group');
            $this->db->where(['u.password' => hash('SHA256', $password), 'u.is_active' => '1']);
            $result = $this->db->get('user u');

            if ($result->num_rows() == 0) {
                $this->data["errorMessage"] = "Gagal login, silahkan periksa kembali informasi login Anda.";
                $this->data["user_temp"] = $username;
                $this->data["pass_temp"] = $password;
                $this->template = "login";
                $this->render("index");
            } else {
                $row = $result->first_row();
                $id_user = $row->id_user;
                $user = $row->username;
                $email = $row->email;
                $real_name = $row->real_name;
                $id_group = $row->id_group;
                $is_active = $row->is_active;
                $nama_group = $row->nama_group;
                $keterangan = $row->keterangan;
                $dbusername = $row->dbusername;

                $this->session->set_userdata(PREFIX_SESS . "_userId", $id_user);
                $this->session->set_userdata(PREFIX_SESS . "_username", $user);
                $this->session->set_userdata(PREFIX_SESS . "_email", $email);
                $this->session->set_userdata(PREFIX_SESS . "_realName", $real_name);
                $this->session->set_userdata(PREFIX_SESS . "_idGroup", $id_group);
                $this->session->set_userdata(PREFIX_SESS . "_isActive", $is_active);
                $this->session->set_userdata(PREFIX_SESS . "_groupName", $nama_group);
                $this->session->set_userdata(PREFIX_SESS . "_groupDescription", $keterangan);
                $this->session->set_userdata(PREFIX_SESS . "_dbUsername", $dbusername);
                $this->config->set_item('database_name', $this->session->userdata(PREFIX_SESS . '_dbUsername'));

                $this->setDatabase();
                //CHANGE DATABASE BASED ON USER

                $this->setUserData();
                $this->setLog($id_user, ['activity' => 'LOG IN', 'page_url' => site_url('auth')]);
                redirect('dashboard');
            }
        } else if (!$userId && uri_string() == "dashboard") { // Accessing index page and there is no user session (login form state)
            $this->template = "app_front";
            if ($this->input->get("access_without_login") == "true") {
                $this->data["errorMessage"] = "Session anda telah berakhir, silahkan login kembali untuk masuk ke dashboard.";
            } else if ($this->input->get("logout") == "true") {
                $this->data["successMessage"] = "Anda telah keluar.";
            } else if ($this->input->get("forgot_password") == "true") {
                $this->data["successMessage"] = "Password anda berhasil diperbarui. Silahkan login dengan password baru.";
            }
            $this->data['title'] = 'Login';
            // $this->render("index");
            redirect('index');
        } else if (!$userId && !in_array(uri_string(), $this->whitelistUrl) && $this->loginBehavior) { // Accessing user page and there is no user session
            $this->template = "app_front";
            redirect("?access_without_login=true");
        } else if ($userId != null) { // Accessing user page and there is user session
            $this->setUserData();
        }

        if (is_array($this->input->get())) {
            foreach ($this->input->get() as $key => $value) {
                $this->data[$key] = $value;
            }
        }

        if (is_array($this->input->post())) {
            foreach ($this->input->post() as $key => $value) {
                if ($key == "description" || $key == "email" || $key == "is_active" || $key == "is_soft_delete" || $key == "username" || $key == "real_name") {
                    $this->data[$key . "Input"] = $value;
                } else {
                    $this->data[$key] = $value;
                }
            }
        }
    }

    protected function setUserData()
    {
        $this->data[PREFIX_SESS . "_userId"]            = $this->session->userdata(PREFIX_SESS . "_userId");
        $this->data[PREFIX_SESS . "_username"]          = $this->session->userdata(PREFIX_SESS . "_username");
        $this->data[PREFIX_SESS . "_email"]             = $this->session->userdata(PREFIX_SESS . "_email");
        $this->data[PREFIX_SESS . "_realName"]          = $this->session->userdata(PREFIX_SESS . "_realName");
        $this->data[PREFIX_SESS . "_idGroup"]           = $this->session->userdata(PREFIX_SESS . "_idGroup");
        $this->data[PREFIX_SESS . "_isActive"]          = $this->session->userdata(PREFIX_SESS . "_isActive");
        $this->data[PREFIX_SESS . "_groupName"]         = $this->session->userdata(PREFIX_SESS . "_groupName");
        $this->data[PREFIX_SESS . "_groupDescription"]  = $this->session->userdata(PREFIX_SESS . "_groupDescription");
        $this->data[PREFIX_SESS . "_dbUsername"]        = $this->session->userdata(PREFIX_SESS . "_dbUsername");

        // $gambar = $this->getImageMember($this->session->userdata(PREFIX_SESS . '_memberId'));
        // $this->session->set_userdata(PREFIX_SESS . "_image", $gambar);
        // $this->data[PREFIX_SESS . "_image"] = $this->session->userdata(PREFIX_SESS . "_image");

        $this->db->distinct();
        $this->db->select(['nama_modul', 'hak_akses']);
        $this->db->where(['id_group' => getSession('idGroup')]);
        $this->db->order_by('nama_modul', 'DESC');
        $result = $this->db->get('akses_group_modul')->result();

        $this->data["userMenus"] = array();
        if ($result) {
            foreach ($result as $row) {
                $this->data["userMenus"][] = $row->nama_modul . "." . $row->hak_akses;
            }
        }
    }

    protected function unSetUserData()
    {
        $this->session->unset_userdata(PREFIX_SESS . "_userId");
        $this->session->unset_userdata(PREFIX_SESS . "_username");
        $this->session->unset_userdata(PREFIX_SESS . "_email");
        $this->session->unset_userdata(PREFIX_SESS . "_realName");
        $this->session->unset_userdata(PREFIX_SESS . "_idGroup");
        $this->session->unset_userdata(PREFIX_SESS . "_isActive");
        $this->session->unset_userdata(PREFIX_SESS . "_groupName");
        $this->session->unset_userdata(PREFIX_SESS . "_groupDescription");
        $this->session->unset_userdata(PREFIX_SESS . "_dbUsername");
        // $this->session->unset_userdata(PREFIX_SESS . "_image");
    }

    protected function render($filename = null)
    {
        $this->data['breadcrumb'] = $this->bread;
        if (empty(getSession("userId"))) {
            $this->template = "app_front";
        } else {
            if ($this->uri->segment(1) == 'notfound') {
                $this->template = 'app_front';
            }
        }

        if (!in_array(uri_string(), $this->whitelistUrl) && $this->loginBehavior) {
            $this->template = 'app';
        } else {
            $this->template = 'app_front';
        }

        $template = $this->load->view("template/" . $this->template, $this->data, true);

        //$content = $this->load->view(($this->module != "" ? $this->module . "/" : "") . strtolower(get_class($this)) . "/" . $filename, $this->data, true);
        $content = $this->load->view(strtolower(get_class($this)) . "/" . $filename, $this->data, true);

        if ($this->module != NULL) {
            if (in_array($this->module . ".access", $this->data["userMenus"]) == 0) {
                $message = "Maaf, Anda tidak memiliki akses ke halaman tersebut.";
                $this->session->set_flashdata('false', $message);
                $message = "Maaf, Anda tidak memiliki akses ke halaman ini.";
                echo "<script type='text/javascript'>alert('$message');</script>";
                redirect('dashboard');
            }
        }
        exit(str_replace("{CONTENT}", $content, $template));
    }

    protected function renderTo($filename = null)
    {
        if (empty(getSession("userId"))) {
            $this->template = "app_front";
        } elseif (!in_array(uri_string(), $this->whitelistUrl) && $this->loginBehavior) {
            $this->template = 'app';
        }

        $template = $this->load->view("template/" . $this->template, $this->data, true);
        $content = $this->load->view($filename, $this->data, true);

        if ($this->module != NULL) {
            if (in_array($this->module . ".access", $this->data["userMenus"]) == 0) {
                $message = "Maaf, Anda tidak memiliki akses ke halaman tersebut.";
                $this->session->set_flashdata('false', $message);
                $message = "Maaf, Anda tidak memiliki akses ke halaman ini.";
                echo "<script type='text/javascript'>alert('$message');</script>";
                redirect('dashboard');
            }
        }
        exit(str_replace("{CONTENT}", $content, $template));
    }

    protected function cek_hak_akses($hak_akses)
    {
        $cek = $this->db->query("SELECT * FROM `akses_group_modul` WHERE nama_modul=? AND hak_akses=? AND id_group=?", array($this->module, $hak_akses, $this->session->userdata(PREFIX_SESS . "_idGroup")))->row_array();
        if (empty($cek)) {
            $message = "Maaf, Anda tidak memiliki akses ke halaman tersebut.";
            $this->session->set_flashdata('false', $message);
            $message = "Maaf, Anda tidak memiliki akses ke halaman ini.";
            echo "<script type='text/javascript'>alert('$message');</script>";
            redirect();
        } else {
            $hak_akses = $this->db->query("SELECT hak_akses FROM `akses_group_modul` WHERE nama_modul=? AND id_group=?", array($this->module, $this->session->userdata(PREFIX_SESS . "_idGroup")))->result_array();
            foreach ($hak_akses as $row) {
                $hasil[] = $row['hak_akses'];
            }
            return $hasil;
        }
    }

    protected function setDatabase()
    {
        $dbUsername = getSession('dbUsername');
        $this->load->database($dbUsername, FALSE, TRUE); //CHANGE DATABASE BASED ON USER
    }

    protected function setOutput($output = null, $view = 'index')
    {
        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }
        $content = (strtolower(get_class($this)) . "/" . $view);
        $x = array_merge($this->data, ['output' => $output]);
        $this->layout->set_template('template/app');

        $this->layout->CONTENT->view($content, $x);
        if ($this->module != NULL) {
            if (in_array($this->module . ".access", $this->data["userMenus"]) == 0) {
                $this->session->set_flashdata('false', 'Maaf, Anda tidak memiliki akses ke halaman tersebut.');
                redirect();
            }
        }
        $this->layout->publish();
    }

    protected function setOutputTo($output = null, $view = null)
    {

        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }
        $content = $view;
        $x = array_merge($this->data, ['output' => $output]);
        $this->layout->set_template('template/app');

        $this->layout->CONTENT->view($content, $x);
        if ($this->module != NULL) {
            if (in_array($this->module . ".access", $this->data["userMenus"]) == 0) {
                $this->session->set_flashdata('false', 'Maaf, Anda tidak memiliki akses ke halaman tersebut.');
                redirect();
            }
        }
        $this->layout->publish();
    }

    protected function getImageMember($id_member)
    {
        $result = '/public/assets/img/logo-bgskin.png';
        $image = $this->db->where(['id_member' => $id_member, 'id_syarat' => '2'])
            ->get('members_syarats_files');
        if ($image->num_rows() > 0) {
            $result = $image->row_array()['url_syarat'];
        }

        return $result;
    }

    protected function setLog($id_user, $params)
    {
        $activity = $params['activity'];
        $page_url = $params['page_url'];

        $this->m_activity_log->insert($id_user, $activity, $page_url);
    }

    protected function responseJSON($status, $response)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($response));
    }
}
