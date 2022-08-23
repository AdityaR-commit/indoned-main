<?php

class M_activity_log extends CI_Model
{

    protected $table = 'activity_log';
    function __construct()
    {
        parent::__construct();

        $dbUsername = getSession('dbUsername');
        if ($dbUsername) {
            $this->load->database($dbUsername, FALSE, TRUE);
        }
    }

    function insert($id_user, $activity, $page_url)
    {
        if ($id_user != 0) {
            $ip = getIp();

            $parameters = [
                'id_user'       => $id_user,
                'activity'      => $activity,
                'page_url'      => $page_url,
                'ip_address'    => $ip,
                'activity_time' => date('Y-m-d H:i:s'),
            ];
            return $this->db->insert($this->table, $parameters);
        } else {
            $status = false;
        }

        return $status;
    }

    function get_data($limit = null, $start = null, $nama = null, $tgl = null)
    {
        if ($nama != null) {
            $where = $this->filter($nama);
        } else {
            $where = "";
        }
        if ($tgl != null) {
            $where .= $this->filter(null, $tgl);
        } else {
            $where .= "";
        }

        if ($limit != null) {
            $result = $this->db->query("SELECT a.*, b.username, b.email FROM activity_log a INNER JOIN user b ON a.id_user = b.id_user WHERE 1 $where ORDER BY a.id_log DESC LIMIT $start, $limit");
        } else {
            $result = $this->db->query("SELECT a.*, b.username, b.email FROM activity_log a INNER JOIN user b ON a.id_user = b.id_user WHERE 1 $where ORDER BY a.id_log DESC");
        }

        return $result;
    }

    function get_nama()
    {
        $result = $this->db->query("SELECT DISTINCT a.id_user, b.username, b.email FROM activity_log a INNER JOIN user b ON a.id_user = b.id_user");

        return $result->result_array();
    }

    function filter($nama = null, $tgl = null)
    {
        if ($nama != null) {
            foreach ($nama as $m) {
                $nama_array[] = $m;
            }

            if (!empty($nama_array)) {
                $nama_array_im = implode("','", $nama_array);
                $nama_where = "a.id_user IN ('$nama_array_im')";
            } else {
                $nama_where = "";
            }

            if ($nama_where != "") {
                $where = " AND " . $nama_where;
            }
        } else if ($tgl != null) {
            $ex = explode(" - ", $tgl);
            $start = $ex[0];
            $finish = $ex[1];

            $ex_s = explode("/", $start);
            $start = $ex_s[2] . "-" . $ex_s[1] . "-" . $ex_s[0] . " 00:00:00";
            $ex_f = explode("/", $finish);
            $finish = $ex_f[2] . "-" . $ex_f[1] . "-" . $ex_f[0] . " 23:59:59";

            $where = " AND DATE(activity_time) BETWEEN '$start' AND '$finish'";
        } else {
            $where = "";
        }

        return $where;
    }
}
