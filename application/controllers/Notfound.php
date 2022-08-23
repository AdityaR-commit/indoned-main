<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notfound extends BaseController
{

    protected $template = "app_front";

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_index');
        $this->load->model('m_activity_log');
    }

    public function index()
    {
        if (getSession('userId')) {
            $this->template = 'app_front';
        }
        $this->data['title'] = 'Not Found';
        $this->render('index');
    }
}
