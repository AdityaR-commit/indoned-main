<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends BaseController
{

    public $loginBehavior = false;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!empty(getSession('userId'))) {
            redirect('dashboard');
        }
        $this->data['title'] = 'Login';
        $this->form_validation->set_rules('username', 'Username', ['required']);
        $this->form_validation->set_message('required', '{field} tidak boleh kosong.');
        $this->form_validation->set_rules('password', 'Password', ['required']);
        $this->form_validation->set_message('required', '{field} tidak boleh kosong.');
        $is_valid['success'] = true;
        if ($this->form_validation->run() && $this->input->post()) {
            if ($is_valid['success']) {
                $validasi = $this->validateLogin();
                if ($validasi['success']) {
                    redirect('dashboard', 'refresh');
                } else {
                    $this->form_validation->add_error('invalid', $validasi['message']);
                }
            } else {
                $this->form_validation->add_error('invalid', 'Captcha tidak valid!');
            }
        }
        $this->render('index');
    }

    public function validateLogin()
    {
        $post     = $this->input->post();
        $username = isset($post['username']) ? $post['username'] : FALSE;
        $password = hash('sha256', isset($post['password']) ? $post['password'] : FALSE);
        $return['success'] = false;
        $return['message'] = null;
        $result   = $this->db
            ->select('user.id_user,
                     user.username,
                     user.email,
                     user.real_name,
                     user.id_group,
                     user.is_active,
                     user_group.nama_group,
                     user_group.keterangan,
                     user_group.dbusername,')
            ->join('user_group', 'user_group.id_group = user.id_group', 'inner')
            ->where("(user.username =" . $this->db->escape($username) . " OR user.email = " . $this->db->escape($username) . ")")
            ->where('user.password', $password)
            ->get('user')
            ->row();
        if ($result) {
            if ($result->is_active == '1') {
                $this->session->set_userdata(PREFIX_SESS . "_userId", $result->id_user);
                $this->session->set_userdata(PREFIX_SESS . "_username", $result->username);
                $this->session->set_userdata(PREFIX_SESS . "_email", $result->email);
                $this->session->set_userdata(PREFIX_SESS . "_realName", $result->real_name);
                $this->session->set_userdata(PREFIX_SESS . "_idGroup", $result->id_group);
                $this->session->set_userdata(PREFIX_SESS . "_isActive", $result->is_active);
                $this->session->set_userdata(PREFIX_SESS . "_groupName", $result->nama_group);
                $this->session->set_userdata(PREFIX_SESS . "_groupDescription", $result->keterangan);
                $this->session->set_userdata(PREFIX_SESS . "_dbUsername", $result->dbusername);

                // $this->session->set_userdata("bgskin_image", '/public/assets/img/logo-bgskin.png');
                // if ($result->id_group != '1') {
                //     $gambar = $this->getImageMember($result->id_member);
                //     $this->session->set_userdata("bgskin_image", $gambar);
                // }
                $this->config->set_item('database_name', $this->session->userdata(PREFIX_SESS . '_dbUsername'));
                $this->setDatabase();
                $this->setUserData();
                $this->setLog($result->id_user, [
                    'activity' => 'LOG IN',
                    'page_url' => site_url('login')
                ]);
                $return['success'] = true;
                $return['message'] = 'Berhasil login';
            } else {
                $return['success'] = false;
                $return['message'] = 'Akun anda dinon-aktifkan silahkan hubungi admin';
            }
        } else {
            $return['success'] = false;
            $return['message'] = 'Username atau password Anda salah, silahkan ulangi';
        }
        return $return;
    }

    public function logout()
    {
        $id_user = getSession('userId');
        $activity = "LOG OUT";
        $page_url = site_url("?logout=true");

        $this->m_activity_log->insert($id_user, $activity, $page_url);

        $this->unSetUserData();
        $this->load->database("default", FALSE, TRUE); //CHANGE DB TO DEFAULT sialogin
        redirect("?logout=true");
    }
}
