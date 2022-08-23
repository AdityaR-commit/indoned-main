<?php
/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 17-03-2021
 * Time: 14:48
 */

namespace app\libraries\Arkatama;

require_once APPPATH . 'third_party/dompdf/autoload.inc.php';

use Dompdf\Dompdf;


defined('BASEPATH') OR exit('No direct script access allowed');

class PDF extends Dompdf
{
    public $filename;

    public function __construct()
    {
        parent::__construct();
        $this->filename = "report.pdf";
    }

    /**
     * @access    protected
     * @return    void
     */
    protected function ci()
    {
        return get_instance();
    }

    /**
     * @access    public
     * @param string $view The view to load
     * @param array  $data The view data
     * @return    void
     */
    public function load_view($view, $data = array())
    {
        $html = $this->ci()->load->view($view, $data, TRUE);
        $this->load_html($html);
        $this->render();
        $this->stream($this->filename, ["Attachment" => false]);
    }
}