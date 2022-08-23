<?php defined('BASEPATH') or exit('no access allowed');
/**
 * summary
 */
abstract class Datatable_Model extends CI_Model
{
    protected $table;
    protected $column_order;
    protected $column_search;
    protected $order;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query()
    {
        $this->_select_query();
        $this->_custom_search_query();

        $i = 0;

        if (count($this->column_search) > 0) {
            foreach ($this->column_search as $item) {
                if (is_all_set($_POST, ['search', 'value'])) {
                    if ($i === 0) {
                        $this->db->group_start();
                        $this->db->like($item, $_POST['search']['value']);
                    } else {
                        $this->db->or_like($item, $_POST['search']['value']);
                    }

                    if (count($this->column_search) - 1 == $i)
                        $this->db->group_end();
                }
                $i++;
            }
        }

        if (count($this->column_order)) {
            if (is_all_set($_POST, ['order', '0', 'column']) && is_all_set($_POST, ['order', '0', 'dir'])) {
                $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } else if (isset($this->order)) {
                $order = $this->order;
                $this->db->order_by(key($order), $order[key($order)]);
            }
        }
    }

    abstract protected function _select_query();

    abstract protected function _custom_search_query();

    function get_datatables($isPost = true)
    {
        if (!$isPost) {
            $_POST = $_GET;
            $query = convert_json_to_datatable_query($_POST);
            $query['filename'] = isset($_POST['filename']) ? $_POST['filename'] : '';
            $_POST = $query;
        }
        $this->_get_datatables_query();

        if (isset($_POST['length']) && isset($_POST['start']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        // var_dump($this->db->last_query());
        // die;

        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->db->count_all_results();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function filterData($column_name, $index_column, $is_array = false)
    {
        if (is_all_set_and_return($_POST, ['columns', $index_column, 'search', 'value']) != '') {
            $search = $_POST['columns'][$index_column]['search']['value'];

            if ($search != '') {
                if ($is_array) {
                    $filters = json_decode($search, true);
                    if (count($filters) > 0) {
                        $this->db->where_in($column_name, $filters);
                    }
                } else {
                    $this->db->where($column_name, $search);
                }
            }
        }
    }

    public function filterDateRange($column_name, $index_column)
    {
        if (is_all_set_and_return($_POST, ['columns', $index_column, 'search', 'value']) != '') {
            $filter = json_decode($_POST['columns'][$index_column]['search']['value']);
            if (count($filter) > 0) {
                $this->db->where($column_name . ' >=', $filter[0]);
                if (count($filter) > 1)
                    $this->db->where($column_name . ' <=', $filter[1]);
            }
        }
    }

    private function _export_excel($title, $file_name, $columns, $data)
    {
        require_once APPPATH . 'libraries/phpexcel.php';

        ob_end_clean();
        ob_start();

        $now = getDateIndonesia(date("Y-m-d"));
        $row = 4;
        $column = "A";

        $excel = new PHPExcel();



        $excel->getSheet(0)->setTitle('Sheet 1');
        $excel->setActiveSheetIndex(0);

        $excel->getActiveSheet()->setCellValue('A1', $title);
        $excel->getActiveSheet()->setCellValue('A2', "Dibuat tanggal : " . $now);

        // buat kolom
        $columnNameColumn = $column;
        foreach ($columns as $i => $col) {
            $cell = $columnNameColumn . $row;

            $styleArray = array(
                'font'  => array(
                    'bold'  => true,
                )
            );
            $excel->getActiveSheet()->setCellValue($cell, $col);
            $excel->getActiveSheet()->getStyle($cell)->applyFromArray($styleArray);

            $columnNameColumn++;
        }

        foreach ($data as $i => $data_row) {
            foreach ($data_row as $j => $r) {
                // [
                //     "value" => "sfdsf",
                //     "type"  => null
                // ]

                $cell = $column . ($row + 1);

                if (!isset($columns[$j]["type"]) || $columns[$j]["type"] == null) {
                    $type = PHPExcel_Cell_DataType::TYPE_STRING;
                } else {
                    $type = $columns[$j]["type"];
                }

                $excel->getActiveSheet()->setCellValueExplicit(
                    $cell,
                    $r,
                    $type
                );
                $column++;
            }
            $row++;
            $column = "A";
        }

        $columnNameColumn = $column;

        foreach ($columns as $i => $col) {
            if ($columnNameColumn !== 'A') {
                $excel->getActiveSheet()->getColumnDimension($columnNameColumn)->setAutoSize(true);
            }
            $columnNameColumn++;
        }

        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->setOffice2003Compatibility(true);
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"" . $file_name . "\".xlsx");
        $writer->save('php://output');

        // $writer = new PHPExcel_Writer_Excel2007($excel);       
        // $writer->save("public/uploads/capel/sadas.xlsx");
        // exit;
    }

    private function is_assoc($array)
    {
        $keys = array_keys($array);
        return $keys !== array_keys($keys);
    }

    /**
     * Export data sesuai dengan query yang tersedia pada datatable
     * Terdapat dua tipe export, yaitu pdf dan excel.
     *
     * @param $title
     * Judul Halaman Excel / PDF
     *
     * @param $filename
     * Nama File Excel / PDF
     *
     * @param string $type
     * Tipe file (Excel atau PDF)
     *
     * @param array $where
     * Kondisi untuk digunakan pada export_query()
     *
     * @param string $pdfTemplate
     * Template PDF (Jika tipe yang digunakan adalah PDF)
     */
    public function export($title, $filename, $type = 'PDF', $where = [], $pdfTemplate = 'template/pdf/datatable/datatable_default')
    {
        $this->export_query($where);
        $list = $this->db->get()->result();

        $headers = array_values($this->export_headers($type));
        $keys = $this->is_assoc($headers) ? array_keys($headers) : $headers;

        $index = 0;
        $rows = array_map(function ($row) use ($type, $keys, &$index) {
            // Get the mapped values:
            $userMappedRow = $this->export_data_map($row, $index++, $type);

            return array_map(function ($item) use ($userMappedRow) {
                return isset($userMappedRow[$item]) ? $userMappedRow[$item] : '-';
            }, $keys);
        }, $list);

        if (strtolower($type) === 'pdf') {
            $this->_export_pdf($title, $filename, $headers, $rows, $pdfTemplate);
        } else if (strtolower($type) === 'excel') {
            //            echo "PHP Excel is unavailable for current PHP version.";
            $this->_export_excel($title, $filename, $headers, $rows);
        }
    }

    private function _export_pdf($title, $filename, $headers, $data, $template = 'template/pdf/datatable/datatable_default')
    {
        generate_pdf(
            $template,
            [
                'header' => [
                    'title' => $title
                ],
                'columns' => $headers,
                'data' => $data
            ],
            $filename
        );
    }

    //    /**/
    //    public function exportPDF($title, $filename, $template = 'template/pdf/datatable/datatable_default')
    //    {
    //        date_default_timezone_set("Asia/Jakarta");
    //
    //        if (!count($_POST)) {
    //            $_POST = $_GET;
    //            $filename = isset($_POST['filename']) ? $filename : '';
    //        }
    //        $this->_get_datatables_query();
    //
    //        if (isset($_POST['length']) && isset($_POST['start']) && $_POST['length'] != -1)
    //            $this->db->limit($_POST['length'], $_POST['start']);
    //
    //        $list = $this->db->get()->result();
    //
    //        // Columns and rows
    //        $columns = $this->exportLabels;
    //        $keys = $this->exportKeys;
    //
    //        $rows = array_map(function ($row) use ($keys) {
    //            return array_map(function ($item) use ($row) {
    //                return $row->$item;
    //            }, $keys);
    //        }, $list);
    //
    //        generate_pdf(
    //            $template,
    //            [
    //                'header' => [
    //                    'title' => $title
    //                ],
    //                'columns' => $columns,
    //                'data' => $rows
    //            ],
    //            $filename
    //        );
    //    }

    //    public function export()
    //    {
    //        $list = $this->get_datatables(false);
    //
    //        $columns = $this->excelLabels;
    //        $keys = $this->excelKeys;
    //
    //        $columns = array_map(function ($item) {
    //            return array(
    //                "name" => $item,
    //            );
    //        }, $columns);
    //        $rows = array_map(function ($row) use ($keys) {
    //            return array_map(function ($item) use ($row) {
    //                return $row->$item;
    //            }, $keys);
    //        }, $list);
    //
    //        $this->exportExcel(
    //            isset($_GET['filename']) ? $_GET['filename'] : $this->excelFileName,
    //            $this->excelTitle,
    //            $columns,
    //            $rows
    //        );
    //    }

    /**
     * Defines Query for Export
     *
     * @return void
     * @param array $where
     */
    public function export_query($where = [])
    {
    }

    /**
     * Defines Data Map Based on Headers.
     * if export_headers returns 1D array, the returned mapped keys should contain the same name as each headers or will
     * return empty string as default value
     * @param object $item Query Result
     * @param integer $index Query Result Index
     *
     * @return array<string>
     * <table class='doctable table'>
     * <tr>
     * <td>Array Associative</td>
     * <td class='empty'>&nbsp;</td>
     * <td>Key akan dikenali sebagai key dari <b><i>export_data_map</i></b> dan Value akan dikenali sebagai label.</td>
     * </tr>
     * </table>
     */
    protected function export_data_map($item, $index, $type)
    {
        return [];
    }

    /**
     * Defines Header Labels.
     *
     * @param $type string pdf or excel
     * @return array<string>
     * <table class='doctable table'>
     * <tr>
     * <td>array 1D</td>
     * <td class='empty'>&nbsp;</td>
     * <td>Key akan dikenali sebagai label pada tabel serta menjadi key pada <b><i>export_data_map</i></b></td>
     * </tr>
     * <tr>
     * <td>Array Associative</td>
     * <td class='empty'>&nbsp;</td>
     * <td>Key akan dikenali sebagai key dari <b><i>export_data_map</i></b> dan Value akan dikenali sebagai label.</td>
     * </tr>
     * </table>
     */
    public function export_headers($type)
    {
        return [];
    }
}
