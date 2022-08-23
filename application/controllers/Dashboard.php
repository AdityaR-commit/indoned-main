<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends BaseController
{

    protected $template = "app";
    public $loginBehavior = true;
    protected $bread = [];

    public function __construct()
    {
        parent::__construct();
        $this->bread[] = ['title' => 'Dashboard', 'url' => site_url('dashboard')];
    }

    public function index()
    {
        $this->data['title'] = 'Dashboard';
        if (empty(getSession("userId"))) {
            redirect('auth');
        } else {
            $this->render("index");
        }
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
