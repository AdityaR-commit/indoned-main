
<?php

class Arkatama {

    function getDaerahByKodeKecamatan($kode_kecamatan, $object = null) {
        $ci = & get_instance();
        $query = $ci->db->query("SELECT CONCAT(kec.name, ' - ', kab.name, ' - ', prov.name) AS daerah FROM districts kec
                                    LEFT JOIN regencies kab ON kab.id=kec.regency_id
                                    LEFT JOIN provinces prov ON prov.id=kab.province_id
                                    WHERE kec.id=?", array($kode_kecamatan))->row_array();
        if (!empty($query)) {
            return $query['daerah'];
        } else {
            return "-";
        }
    }

    function getIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

}
