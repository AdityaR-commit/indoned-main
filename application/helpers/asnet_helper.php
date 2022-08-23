<?php

/**
 * Untuk melihat apakah suatu nilai sudah ada/ digunakan di database
 *
 * @param  mixed $value Nilai yang ingin dicek
 * @param  string $tableName Nama table yang ingin dicek valuenya
 * @param  string $columnName Nama kolom yang ingin dicek valuenya
 * @param  array $where Optional. Query pencarian tambahan
 * @return boolean True jika data ditemukan
 */
function isValueUsed($value, $tableName, $columnName, $where = null)
{
    $_this = &get_instance();
    $_this->db->where([
        $columnName => $value
    ]);

    if ($where != null) {
        $_this->db->where($where);
    }

    $_this->db->limit(1);
    $count = $_this->db->count_all_results($tableName);

    return ($count > 0) ? true : false;
}

function isPelangganHasPerubahan($id)
{
    $_this = &get_instance();
    $_this->db->where([
        "id_pelanggan"  => $id
    ]);
    $_this->db->where_in(
        "status",
        [
            "OPEN", "ACTION",
        ]
    );
    $query = $_this->db->get('pelanggan_update_layanan', 1);

    if ($query->row() != null) {
        return true;
    }

    return false;
}

function getActiveUserId()
{
    return getSessionUserId();
}

function getPrefixSPK(
    $idJenisSpk
) {
    $_this = &get_instance();
    $_this->db->select("prefix");
    $_this->db->where([
        "id"  => $idJenisSpk
    ]);

    $query = $_this->db->get("ref_jenis_spk", 1);

    return $query->row()->prefix;
}


function generateNomorJurnalAkunting()
{
    // TODO: system setting prefix inventpry dan invoice
    // 	GL 22 02 12 0004
    $prefix     = "GL";
    $pad = 4;
    $curr_year = date('y');
    $curr_month = date('m');
    $curr_date = date('d');

    // ambil inv terakhir bulan ini
    $last_nomor = lastNomorJurnal($prefix);
    $last_code = intval($last_nomor);

    // echo (PHP_EOL . $last_code);
    // $pad = strlen(($last_code + 1));

    $new_code = str_pad(($last_code + 1), $pad, "0", STR_PAD_LEFT);
    // $new_code = str_pad(5, 4, '0', STR_PAD_LEFT);
    // $new_code = $pad;
    // return $new_code;

    return $prefix . $curr_year . $curr_month . $new_code;
}

function generateNomorInventory()
{
    // STK 22 02 016
    $prefix     = getSystemSetting("prefix_nomor_inventory", "STK") . date('y') . date('m');

    // ambil inv terakhir bulan ini
    $last_nomor = lastNomor($prefix, 'inventories_batch', 'nomor_inventory');
    $last_code = intval($last_nomor);

    // echo (PHP_EOL . $last_code);
    $pad = strlen(($last_code + 1));

    $new_code = str_pad(($last_code + 1), 3, "0", STR_PAD_LEFT);

    return $prefix . $new_code;
}

function generateNomorRegistrasi()
{
    // 2125 22 03 027
    $prefix = getSystemSetting("prefix_nomor_registrasi", "2125") . date('y') . date('m');

    // ambil inv terakhir bulan ini
    $last_nomor = lastNomor($prefix, 'orders', 'nomor_registrasi');

    $exception = Date("Y-m");
    // if ($exception == "2022-07") {
    //     $last_nomor += 100;
    // }

    $last_code = intval($last_nomor);

    $new_code = str_pad(($last_code + 1), 3, "0", STR_PAD_LEFT);

    return $prefix . $new_code;
}

function generateNomorInstalasi()
{
    $prefix = 1;
    return generateNomorSPK($prefix);
}

function generateNomorPerubahan()
{
    $prefix = getSystemSetting('prefix_nomor_perubahan', PREFIX_NOMOR_PERUBAHAN) . date('y') . date('m');

    $last_code = lastNomor($prefix, 'pelanggan_update_layanan', 'nomor_perubahan');

    $new_code = str_pad(($last_code + 1), 4, "0", STR_PAD_LEFT);

    return $prefix . $new_code;
}

function generateNomorRelokasi()
{
    $prefix = getSystemSetting('prefix_nomor_relokasi', PREFIX_NOMOR_RELOKASI) . date('y') . date('m');
    $last_code = lastNomor($prefix, 'pelanggan_relokasi', 'nomor_perubahan');
    $new_code = str_pad(($last_code + 1), 4, "0", STR_PAD_LEFT);
    return $prefix . $new_code;
}

function generateNomorAktivasiUlang()
{
    $prefix = getSystemSetting('prefix_aktivasi_pelanggan_off', PREFIX_AKTIVASI_PELANGGAN_OFF) . date('y') . date('m');
    $last_code = lastNomor($prefix, 'pelanggan_aktivasi_off', 'nomor_aktivasi');
    $new_code = str_pad(($last_code + 1), 4, "0", STR_PAD_LEFT);
    return $prefix . $new_code;
}

function generateNomorBerhentiLangganan()
{
    $prefix = getSystemSetting('prefix_nomor_berhentilangganan', PREFIX_NOMOR_BERHENTILANGGANAN) . date('y') . date('m');
    $last_code = lastNomor($prefix, 'pelanggan_berhenti_berlangganan', 'nomor_berhenti');
    $new_code = str_pad(($last_code + 1), 4, "0", STR_PAD_LEFT);
    return $prefix . $new_code;
}

function generateNomorInvoice()
{
    //INV 22 04 0004
    $prefix = getSystemSetting("prefix_nomor_invoice", "INV") . date('y') . date('m');

    // ambil inv terakhir bulan ini
    $last_nomor = lastNomor($prefix, 'invoices', 'number');
    $last_code = intval($last_nomor);

    $new_code = str_pad(($last_code + 1), 4, "0", STR_PAD_LEFT);

    return $prefix . $new_code;
}

function generateNomorSPK($idJenisSpk)
{
    $prefix = getPrefixSPK($idJenisSpk) . date('y') . date('m');

    $last_nomor = lastNomorSpk($prefix, $idJenisSpk);
    $last_code = intval($last_nomor);

    $new_code = str_pad(($last_code + 1), 4, "0", STR_PAD_LEFT);

    return $prefix . $new_code;
}

function generateNomorSPKRelokasi()
{
    return generateNomorSPK(5);
}

function generateNomorGangguan()
{
    $prefix = getPrefixSPK(13) . date('y') . date('m');
    $last_code = lastNomor($prefix, 'spk_gangguan', 'nomor_ticketing');
    $new_code = str_pad(($last_code + 1), 4, "0", STR_PAD_LEFT);

    return $prefix . $new_code;
}

function generateNomorMigrasi($table = null, $jenisSPK = null)
{
    if ($jenisSPK != null)
        $idSpk = $jenisSPK;
    else
        $idSpk = "2";

    $prefix = getPrefixSPK($idSpk) . date('y') . date('m');

    if ($table != null)
        $last_code = lastNomorMigrasi($prefix, $table);
    else
        $last_code = lastNomorMigrasi($prefix);


    $new_code = str_pad(($last_code + 1), 4, "0", STR_PAD_LEFT);

    return $prefix . $new_code;
}

function lastNomorSpkLainnya($datePrefix)
{
    $_this = &get_instance();
    $_this->db->select(["nomor_spk", "SUBSTR(nomor_spk, -8, 8) AS sub"]);
    $_this->db->join('ref_jenis_spk jenis', 'jenis.id = ref_jenis_spk');
    $_this->db->where('jenis.is_kepelangganan', 0);
    $_this->db->where("SUBSTR(nomor_spk, -8, 8) LIKE '$datePrefix%'");

    $_this->db->order_by("sub", 'desc');

    $query = $_this->db->get("spk", 1);

    $result = $query->row();

    if ($result != null) {
        return intval(substr($result->nomor_spk, -4));
    }
    return 0;
}

function generateNomorSpkLainnya()
{
    $datePrefix = date('y') . date('m');
    $lastNomor = lastNomorSpkLainnya($datePrefix);
    $newCode = str_pad(($lastNomor + 1), 4, "0", STR_PAD_LEFT);
    return "SV" . $datePrefix . $newCode;
}

function generateNomorCoaBank()
{

    // 	11 09
    $prefix     = "11";
    // $pad = 2;
    // $curr_year = date('y');
    // $curr_month = date('m');
    // $curr_date = date('d');

    // ambil no akun rekening terakhir
    $last_code = intval(lastNomorCoaBank($prefix));

    // echo (PHP_EOL . $last_code);
    // $pad = strlen(($last_code + 1));

    $new_code = str_pad(($last_code + 1), 2, "0", STR_PAD_LEFT);

    return $prefix . $new_code;
}

function makeInvNumber()
{
    $_this = &get_instance();
    $_this->db->from('invoices');
    $_this->db->select("MAX(RIGHT(number,4)) as 'last_code'", false);
    $_this->db->like("number", date('ym'));
    $data = $_this->db->get();

    if ($data->num_rows() > 0)
        return "INV" . date('ym') . str_pad($data->row()->last_code + 1, 4, "0", STR_PAD_LEFT);
    else
        return "INV" . date('ym') . str_pad(1, 4, "0", STR_PAD_LEFT);
}

function lastNomor($prefix, $table, $column)
{
    $_this = &get_instance();
    $_this->db->select($column);
    $_this->db->order_by($column, 'desc');
    $_this->db->where($column . ' LIKE "' . $prefix . '%"');
    $query =  $_this->db->get($table, 1);

    $result = $query->row_array();
    if ($result != null) {
        return intval(str_replace($prefix, "", $result[$column]));
    }
    return 0;
}

function lastNomorSpk($prefix, $idJenisSpk)
{
    $_this = &get_instance();
    $_this->db->select("nomor_spk");
    $_this->db->where([
        "ref_jenis_spk" => $idJenisSpk
    ]);
    $_this->db->where('nomor_spk LIKE "' . $prefix . '%"');
    $_this->db->order_by("nomor_spk", 'desc');

    $query = $_this->db->get("spk", 1);

    $result = $query->row();
    if ($result != null) {
        return intval(substr(intval(str_replace($prefix, "", $result->nomor_spk)), 0));
    }
    return 0;
}

function lastNomorJurnal($prefix)
{
    $_this = &get_instance();
    // $_this->db = $_this->load->database('asnet_accounting', true);
    $_this->db->select("n_jurnal");
    $_this->db->order_by("n_jurnal", 'desc');
    $_this->db->where('n_jurnal LIKE "' . $prefix . '%"');

    $query =  $_this->db->get(REFFBASE . ".hjurnal", 1);
    $_this->db->reset_query();

    $result = $query->row();
    if ($result != null) {
        return intval(substr(intval(str_replace($prefix, "", $result->n_jurnal)), 4));
    }
    return 0;
}

function lastNomorMigrasi($prefix, $table = null)
{
    $_this = &get_instance();

    $_this->db->select("nomor_perubahan");
    $_this->db->order_by("nomor_perubahan", 'desc');
    $_this->db->where('nomor_perubahan LIKE "' . $prefix . '%"');
    if ($table != null)
        $query = $_this->db->get($table, 1);
    else
        $query = $_this->db->get("pelanggan_update_layanan", 1);

    $_this->db->reset_query();

    $result = $query->row();

    if ($result) {
        return intval(str_replace($prefix, "", $result->nomor_perubahan));
    }

    return 0;
}

function lastNomorCoaBank($prefix)
{
    $_this = &get_instance();
    $that = $_this->load->database('asnet_accounting', true);

    $ref_grup_coa = $that->get_where('ref_grup_coa', ['prefix' => $prefix])->row_array();
    $grup = $ref_grup_coa['nama'];
    $that->select('akun');
    $that->where([
        'grup' => $grup,
    ]);
    $that->order_by('akun', 'DESC');
    $query =  $that->get('coa');
    $result = $query->row();

    $that->reset_query();

    if ($result != null) {
        /*
        1101  => '01'
        11011 => '0'
        1111  => ''
         */
        return intval(substr($result->{'akun'}, strlen($prefix)));
    };
    return 0;
}

function padStart($text = '', $length = 2, $paddedStr = '0')
{
    $res = $text;
    while (strlen($res) < $length) {
        $res = $paddedStr . $res;
    }
    return $res;
}

function getLastMonths($data, $filterCallback, $length = 4)
{

    if ($length < 0)
        return [];

    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $currentYear = date('Y');
    $currentMonth = date('m');

    $filteredData = [];

    while ($length > 0) {
        $length--;
        $datePrefix = $currentYear . '-' . $currentMonth;
        $data = array_filter($data, function ($item) use ($filterCallback, $datePrefix) {
            return $filterCallback($item, $datePrefix);
        });
        $filteredData[] = [
            "stringDate" => $months[$currentMonth - 1] . ' ' . $currentYear,
            "year" => $currentYear,
            "month" => $currentMonth,
            "data" => $data,
        ];
        $currentMonth--;
        if ($currentMonth === 0) {
            $currentMonth = 12;
            $currentYear--;
        }
    }

    return $filteredData;
}

/**
 * @param array $var Required
 * @param array $keys Required
 * @param bool $is_base_level Optional, Default FALSE
 * @return bool
 */
function is_all_set($var, $keys, $is_base_level = false)
{
    $pointer = $var;
    foreach ($keys as $key) {
        if (!isset($pointer[$key])) {
            return false;
        }
        if (!$is_base_level)
            $pointer = $pointer[$key];
    }
    return true;
}

function is_all_set_and_return($var, $keys)
{
    if (is_all_set($var, $keys)) {
        foreach ($keys as $key) {
            $var = $var[$key];
        }
        return $var;
    }
    return false;
}

function convert_json_to_datatable_query($json)
{
    // Just for IDE
    $length = 'length';
    $start = 'start';
    $filename = 'filename';
    $json['length'] = isset($json['length']) ? $json[$length] : 10;
    $query = [
        "columns" => [],
        "search" => [],
        "length" => isset($json['length']) ? $json[$length] : 10,
        "start" => isset($json['start']) ? $json[$start] : 0,
        "order" => isset($json['order']) ? json_decode($json['order'], true) : [],
        "filename" => isset($json['filename']) ? $json[$filename] : null,
    ];
    $orderables = isset($json['ordbls']) ? json_decode($json['ordbls']) : [];
    $searchables = isset($json['sbls']) ? json_decode($json['sbls']) : [];

    if (isset($json['columns'])) {
        $json['columns'] = json_decode($json['columns']);
        foreach ($json['columns'] as $index => $value) {
            $query['columns'][$index] = [
                'searchable' => in_array($index, $searchables),
                'orderable' => in_array($index, $orderables),
                'search' => [
                    'value' => $value,
                    'regex' => false,
                ]
            ];
        }
    }

    return $query;
}

function redirect_once($uri = '', $method = 'auto', $code = NULL, $onError = NULL)
{
    $CI = &get_instance();

    if ($CI->uri->uri_string() === $uri) {
        if (!$onError) {
            throw new RuntimeException("Redirected more than once at the same URL.", 500);
        } else {
            $onError();
        }
    }
    redirect($uri, $method, $code);
}

function getSessionData($name)
{
    $ci = &get_instance();

    $prefix = SESSION_PREFIX;
    if ($prefix == null || $prefix == '') {
        $prefix = '';
    } else {
        $prefix .= "_";
    }
    return $ci->session->userdata($prefix . $name);
}

function getSessionUserId()
{
    return getSessionData('userId');
}

function getSessionLoginUsername()
{
    return getSessionData('username');
}

function getSessionUserName()
{
    return getSessionData('nama');
}

function getSessionEmail()
{
    return getSessionData('email');
}

function getSessionUserProfilePicture()
{
    return getSessionData('profile_picture');
}

function getSessionUserGroupId()
{
    return getSessionData('idGroup');
}
function getSessionUserGroupName()
{
    return getSessionData('namaGroup');
}

function getSessionUserGroupTableName()
{
    return getSessionData('namatable');
}

function getSessionUserGroupDbUsername()
{
    return getSessionData('dbUsername');
}

function getSessionUserGroupDashboard()
{
    return getSessionData('dashboard');
}

/**
 * Untuk mengambil fullpath file yang di upload di public/uploads.
 *
 * @param  string $path Lokasi folder file di dalam public/uploads. Misal orders/
 * @param  string $filename Nama file lengkap dengan ekstensi. Misal 634526542.jpg
 * @param  string $default (Opsional) File pengganti jika file tidak ditemukan
 * @return string Folder fullpath
 */
function getUploadedFileLocation($path, $filename, $default)
{
    if ($filename != null) {
        $uploadPath = "public/uploads/";
        $fullPath = $uploadPath . $path . $filename;
        $isFileExist = file_exists($fullPath);

        if ($isFileExist) {
            return $fullPath;
        }
    }

    return $default;
}

/**
 * Merubah nomor Hp menjadi link Wa.
 *
 * @param  string $noHp Format Nomor HP harus 08xxxxxxxx
 * @return string Url wa.me
 */
function urlWaMe($noHp, $withCountryCode = false)
{
    if (!$withCountryCode) {
        $countryCode = "62";

        if ($noHp[0] == "0") {
            $wa =  $countryCode . substr($noHp, 1);
        } else {
            $wa =  $countryCode . $noHp;
        }
    } else {
        $wa = $noHp;
    }

    return "https://wa.me/" . $wa;
}

function urlGoogleMapsByLatLong($lat, $long)
{
    return "http://maps.google.com?q=" . $lat . "," . $long;
}


/**
 * Membuat button redirect Wa.me
 *
 * @param  string $hp Format Nomor HP harus 08xxxxxxxx
 * @return string Html anchor button
 */
function waMeButton($hp, $withCountryCode = false, $name = null)
{
    if ($hp == null || $hp == '') {
        return '<a class="disabled btn btn-danger btn-xs py-1 px-2">Nomor HP Kosong</a>';
    }
    if (!empty($name)) {
        $name = '<br>(' . $name . ')';
    }
    return '<a target="_blank" href="' . urlWaMe($hp, $withCountryCode) . '" role="button" class="btn btn-secondary btn-xs py-1 px-2">' . $hp . $name . '</a>';
}

// function waListItem($hp, $withCountryCode = false)
// {
//     if ($hp == null || $hp == '') {
//         return '<a class="disabled btn btn-danger btn-xs py-1 px-2">Nomor HP Kosong</a>';
//     }
//     return '<a target="_blank" href="' . urlWaMe($hp, $withCountryCode) . '" role="button" class="btn btn-secondary btn-xs py-1 px-2">' . $hp . '</a>';
// }

// TODO: implement
function formatPhoneNumber($phone, $phonecode)
{
}

function parseDateTimeToIndonesia($datetime)
{
    date_default_timezone_set('Asia/Jakarta');
    $day = date('d', strtotime($datetime));
    $month = getMonthName(date('m', strtotime($datetime)));
    $year = date('Y', strtotime($datetime));

    return [
        "date"  => "$day $month $year",
        "time"  => date("H:i:s", strtotime($datetime))
    ];
}

function getDatetimeIndonesia($datetime)
{
    $parsed = parseDateTimeToIndonesia($datetime);
    return $parsed['date'] . ", " . $parsed['time'];
}

function getDateIndonesia($datetime)
{
    $parsed = parseDateTimeToIndonesia($datetime);
    return $parsed['date'];
}

function getTimeIndonesia($datetime)
{
    $parsed = parseDateTimeToIndonesia($datetime);
    return $parsed['time'];
}

function getDayNameIndonesia($datetime)
{
    $day = date('N', strtotime($datetime));
    $name = "Minggu";
    switch ($day) {
        case 1:
            $name = "Senin";
            break;

        case 2:
            $name = "Selasa";
            break;

        case 3:
            $name = "Rabu";
            break;

        case 4:
            $name = "Kamis";
            break;

        case 5:
            $name = "Jumat";
            break;

        case 6:
            $name = "Sabtu";
            break;

        default:
            $name = "Minggu";
            break;
    }

    return $name;
}

function setErrorAlertMessage($message)
{
    $_this = &get_instance();
    $_this->session->set_flashdata('flash-message', [
        "icon"      => "fas fa-exclamation-triangle",
        "color"     => "alert-danger",
        "title"     => "Error",
        "message"   => $message
    ]);
}

function setAlertMessage($message, $title = 'Error', $type = 'danger', $icon = 'exclamation-triangle')
{
    $_this = &get_instance();
    $_this->session->set_flashdata('flash-message', [
        "icon"      => "fa-" . $icon,
        "color"     => "alert-" . $type,
        "title"     => $title,
        "message"   => $message
    ]);
}

function hitungProrata($biayaBulanan)
{
    $startDate = new DateTime();
    $firstDate = date("Y-m-01");
    $nextMonth = date("m", strtotime("+1 month", strtotime($firstDate)));
    $nextDate = date("Y-" . $nextMonth . "-03");

    $endDate = new DateTime($nextDate);

    $difference = $endDate->diff($startDate);
    $days = $difference->format("%a") + 2;

    return $days / 30 * $biayaBulanan;
}

function getDownloadSpkUrl($jenisSpk, $idSpk)
{
    return site_url("dashboard/pelanggan/spk/download/" . $jenisSpk . "/" . $idSpk);
}


/**
 * Digunakan untuk mengambil nomor akun COA berdasarkan nama tagihan
 *
 * @param  string $namaTagihan Nama tagihan. Contoh: Biaya Registrasi
 * @return string|integer Nomor Akun COA
 */
function getCoaByNamaTagihan($namaTagihan = null)
{
    $akun = [
        "Kode unik"              => COA_PENDAPATAN_LAIN2,
        "Biaya Registrasi"       => COA_PENDAPATAN_REGISTRASI,
        "Biaya Bulanan"          => COA_PENDAPATAN_BANDWITH,
        "Biaya Perangkat: BELI"  => COA_PENDAPATAN_PERANGKAT,
        "Biaya Perangkat: SEWA"  => COA_PENDAPATAN_PERANGKAT,
    ];

    if (strtolower(substr($namaTagihan, 0, 5)) == 'kabel') {
        return COA_PENDAPATAN_PERANGKAT;
    }

    if (isset($akun[$namaTagihan])) {
        return $akun[$namaTagihan];
    }


    return COA_PENDAPATAN_LAINNYA;
}


/**
 * Mengambil kode COA berdasarkan
 *
 * @param  integer $rekeningId
 * @return string|integer
 */
function getBankCoa($rekeningId = null)
{
    $return = COA_KAS;
    $_ci = &get_instance();
    if ($rekeningId != null) {
        $data = $_ci->db->where(['id' => $rekeningId])->get('ref_rekening');
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $return = $data['coa'];
        }
    }

    return $return;
}

function getBankCoaByRekening($rekening = null)
{
    $return = COA_KAS;
    $_ci = &get_instance();
    if ($rekening != null) {
        $data = $_ci->db->where(['nomor_rekening' => $rekening])->get('ref_rekening');
        if ($data->num_rows() > 0) {
            $data = $data->row_array();
            $return = $data['coa'];
        }
    }

    return $return;
}

/**
 * Mengambil Avatar dari Gravatar jika ada
 */
function get_gravatar_image_url($email, $size = 96, $default = 'identicon')
{
    if (getenv('MODE', true) === 'production') {
        if (gettype($email) === 'string') {
            return 'https://www.gravatar.com/avatar/' . strtolower(md5($email)) . '?s=' . $size . '?d=' . urlencode($default);
        } else {
            return '';
        }
    }
    return $default;
}

/*
 * Mengambil user group detail
 */
function getGroupDetail($groupId)
{
    $_this = &get_instance();
    $_this->db->where('id_group', $groupId);
    $result = $_this->db->get('user_group');
    if ($result->num_rows() > 0) {
        return $result->row();
    }
    return null;
}

/**
 * Get User Details Based on Groups
 * @param groups => [['id_group' => ...]]
 */
function getGroupUsers($groups, $select, $excepts, $is_object = true)
{
    // Get group tables
    $tables = array_map(function ($item) {
        return getGroupDetail($item->id_group)->table_name;
    }, $groups);

    $initialState = [];
    foreach ($select as $selection) {
        $initialState[$selection] = $selection;
    }

    $_this = &get_instance();

    $_this->db->select(['id_user']);
    $_this->db->where_in($groups);
    $acceptedUserIds = $_this->db->get('user')->result();

    $userInfos = [];

    foreach ($tables as $tableName) {

        $columns = $initialState;

        if (isset($excepts[$tableName])) {
            foreach ($columns as $key => $value) {
                $columns[$key] = isset($excepts[$tableName][$key]) ? $excepts[$tableName][$key] : $value;
            }
        }

        $users = $_this->db->select(array_values($columns))
            ->where_in($acceptedUserIds)
            ->get($tableName);

        if ($is_object) {
            $users = $users->result_object();
        } else {
            $users = $users->result_array();
        }

        if (isset($excepts[$tableName])) {
            $users = array_map(function ($item) use ($is_object, $columns) {
                $newItem = [];
                foreach ($columns as $key => $value) {
                    if ($is_object)
                        $newItem[$key] = $item->$value;
                    else
                        $newItem[$key] = $item[$value];
                }
                if ($is_object) {
                    $newItem = json_decode(json_encode($newItem));
                }
                return $newItem;
            }, $users);
        }

        $userInfos = array_merge($userInfos, $users);
    }

    return $userInfos;
}

/**
 * urlDetailPelanggan
 *
 * @param  integer $id ID Pelanggan
 * @return string
 */
function urlDetailPelanggan($id)
{
    $url = "dashboard/kepelangganan/data_pelanggan/detail/";
    return $url . $id;
}

function urlDownloadSpk($id, $idJenisSpk = null)
{
    $_this = &get_instance();
    $url = "spk/download";

    if ($idJenisSpk == null) {
        $idJenisSpk = 0;
    } else {
        $_this->db->select([
            "ref_jenis_spk"
        ]);
        $_this->db->where([
            "id"    => $id
        ]);
        $spk = $_this->db->get("spk", 1)->row();

        $idJenisSpk = $spk->ref_jenis_spk;
    }
    return "$url/$idJenisSpk/$id";
}

function getAttachmentSpk($id, $idJenisSpk)
{
    // TODO: handle
}


/**
 * lastNomorForAccounting untuk mendapatkan nomor terakhir di db accounting
 *
 * @param  mixed $prefix
 * @param  mixed $table
 * @param  mixed $column
 * @return void
 */
function lastNomorForAccounting($prefix, $table, $column)
{
    $_this = &get_instance();
    // $that = $_this->load->database('asnet_accounting', true);

    // $_this->db->select("MAX(RIGHT($column, 4)) as $column");
    $_this->db->select("SUBSTRING(" . $column . ", 3) as " . $column);
    $_this->db->like($column, $prefix, 'after');
    $_this->db->order_by($column, 'desc');
    $query =  $_this->db->get(REFFBASE . '.' . $table, 1);
    $result = $query->row_array();

    $last = 0;
    if ($result != null) {
        // return intval(substr(intval(substr($result[$column], 2)), 0));
        $last = intval(substr($result[$column], -4));
    }
    return $last;
}


/*
* Generate Nomor Piutang
*/
function generateNomorPiutang()
{
    // TODO: system setting prefix inventpry dan invoice
    // 	GL 22 02 12 0004
    $prefix     = "RN";
    $pad = 4;
    $curr_year = date('y');
    $curr_month = date('m');
    $curr_date = date('d');

    // ambil piutang terakhir bulan ini
    $last_nomor = lastNomorForAccounting($prefix, 'piutang', 'n_penjualan');
    $last_code = intval($last_nomor);

    // echo (PHP_EOL . $last_code);
    $pad = strlen(($last_code + 1));

    $new_code = str_pad(($last_code + 1), $pad, "0", STR_PAD_LEFT);

    return $prefix . $curr_year . $curr_month . $new_code;
}

function generateNomorPiutangJurnal()
{
    // TODO: system setting prefix inventpry dan invoice
    // 	GL 22 02 12 0004
    $prefix     = "RY";
    $pad = 4;
    $curr_year = date('y');
    $curr_month = date('m');
    $curr_date = date('d');

    // ambil piutang terakhir bulan ini
    $last_nomor = lastNomorForAccounting($prefix, 'dpiutang', 'n_piutang');
    $last_code = intval($last_nomor);

    // echo (PHP_EOL . $last_code);
    $pad = strlen(($last_code + 1));

    $new_code = str_pad(($last_code + 1), $pad, "0", STR_PAD_LEFT);

    return $prefix . $curr_year . $curr_month . $new_code;
}

// Generate nomor untuk transaksi accounting

function generateNomorForAccounting($prefix = null, $table = null, $column = null)
{
    // PR202206020001
    if ($prefix == null) {
        $prefix = 'GL';
    }
    $pad = 4;
    $curr_year = date('Y');
    $curr_month = date('m');
    $curr_date = date('d');
    $pre = $prefix . $curr_year . $curr_month;

    // ambil last nomor terakhir bulan ini
    $last_nomor = lastNomorForAccounting($pre, $table, $column);
    $last_code = intval($last_nomor);
    if ($last_code >= 9999) {
        $last_code = 0;
    }
    $new_code = str_pad(($last_code + 1), $pad, "0", STR_PAD_LEFT);
    return $prefix . $curr_year . $curr_month . $curr_date . $new_code;
}


function getUploadedEvidenceSpk($filename)
{
    return getUploadedFileLocation(PATH_BUKTI_SPK_IMAGE, $filename, PATH_DEFAULT_IMAGE);
}
/**
 * lastNomorForInvoice untuk mendapatkan nomor terakhir di db accounting
 *
 * @param  mixed $prefix
 * @param  mixed $table
 * @param  mixed $column
 * @return void
 */
function generatekodeunikinvoiceregister()
{
    $_this = &get_instance();
    $_this->db->select('kode_unik');
    // $_this->db->order_by('tanggal', 'desc');
    $_this->db->where('jenis', "REGISTRASI");
    $_this->db->where('kode_unik!=', NULL);
    $_this->db->where('date(created_at)', date('Y-m-d'));
    $_this->db->order_by('created_at', 'desc');
    $query =  $_this->db->get('invoices', 1);

    $result = $query->row_array();
    $last = 0;
    if ($result != null) {
        // return intval(substr(intval(substr($result[$column], 2)), 0));
        $last = intval(substr($result['kode_unik'], -4));
    }
    // $last = str_pad(($last + 1), 3, "0", STR_PAD_LEFT);
    $last = $last + 1;

    return $last;
}

function pelangganByid($id_pelangggan)
{
    $_this = &get_instance();
    $_this->db->where(['id' => $id_pelangggan]);
    $data = $_this->db->get('pelanggan');
    $return = null;
    if ($data->num_rows() > 0) {
        $return = $data->row_array();
    }

    return $return;
}

function userById($id_user)
{

    $_this = &get_instance();
    $_this->db->where(['id_user' => $id_user]);
    $data = $_this->db->get('user');
    $return = null;
    if ($data->num_rows() > 0) {
        $return = $data->row_array();
    }

    return $return;
}


function uploadImageEvidenceSpk($nama_file, $path_folder, $prefix)
{
    $ci = &get_instance();
    $fileSize = ceil($_FILES[$nama_file]['size'] / 1000);

    $dirUpload = "public/uploads/" . PATH_BUKTI_SPK_IMAGE;

    $response['success'] = false;
    $response['file_name'] = '';
    $nama_foto = "";
    if (!empty($_FILES[$nama_file]['name'])) {
        list($width, $height) = getimagesize($_FILES[$nama_file]['tmp_name']);

        $config['upload_path'] = $dirUpload;
        $config['allowed_types'] = '*';
        // $config['allowed_types'] = 'webp|jpg|jpeg|png|image/webp';
        $config['max_size'] = '10000';
        $config['file_name'] = $prefix . '_' . date("ymdHis") . '_' . rand(1, 100);

        $ci->load->library('upload');
        $ci->upload->initialize($config);

        if ($ci->upload->do_upload($nama_file)) {
            $file_foto = $ci->upload->data();
            $maxFileSize = 10000;

            if ($file_foto['file_size'] > $maxFileSize) {
                // 5000 - 2000 = 3000 / 2000 = 1.5
                // 2000 / 1.5 = 1.333
                $baseResize = ($file_foto['file_size'] - $maxFileSize) / $maxFileSize;

                $con['image_library'] = 'gd2';
                $con['source_image'] = $file_foto['full_path'];
                $con['create_thumb'] = FALSE;
                $con['maintain_ratio'] = TRUE;
                $con['width'] = round($width / $baseResize);
                $con['height'] = round($height / $baseResize);
                $con['new_image'] = $file_foto['full_path'];

                $ci->load->library('image_lib', $con);

                if (!$ci->image_lib->resize()) {
                    $response['error'] = $ci->image_lib->display_errors();
                }
            }

            $nama_foto = '/public/uploads/' . $path_folder . '/' . $file_foto['file_name'];
            $response['success'] = true;
            $response['file_dir'] = $nama_foto;
            $response['file_name'] = $file_foto['file_name'];
        }
        // $response['error'] = $this->upload->display_errors();
        $response['error'] = $ci->upload->display_errors(); //?ganti var get instance
    }
    return $response;
}


function getJenisTransaksiAccounting($jenis = 'GL')
{
    $ref_jenis = [
        'GL' => 'Jurnal Memorial',
        'CT' => 'Costing',
        'PN' => 'Hutang / Fee',
        'KM' => 'Kas Masuk',
        'KK' => 'Kas Keluar',
        'BM' => 'Bank Masuk',
        'BK' => 'Bank Keluar',
        'PY' => 'Bayar Hutang / Fee',
        'RY' => 'Bayar Piutang',
        'RN' => 'Piutang',
        'PT' => 'Pembelian',
    ];

    $ref_key = array_keys($ref_jenis);
    $return = null;
    if (in_array($jenis, $ref_key)) {
        $return = $ref_jenis[$jenis];
    }

    return $return;
}

function getSpk($idSpk)
{
    $_this = &get_instance();
    $db = $_this->db;
    $db->select([
        "s.*",
        "j.jenis as nama_spk"
    ]);
    $db->where([
        "s.id"    => $idSpk
    ]);
    $db->join('ref_jenis_spk j', 'j.id = s.ref_jenis_spk', 'left');
    return $db->get("spk s")->row();
}

function getUserExecutorsByIdSpk($idSpk)
{
    $_this = &get_instance();
    $db = $_this->db;
    $db->select([
        "u.real_name as nama",
        "concat(if(c.phonecode is null, '62', c.phonecode),u.phone_number) as no_wa",
        "u.email",
    ]);
    $db->where([
        "se.id_spk"    => $idSpk
    ]);
    $db->join('user u', 'u.id_user = se.id_user', 'left');
    $db->join('ref_country c', 'c.id = u.ref_country_id', 'left');

    return $db->get("spk_executor se")->result();
}

/**
 * getUsersContact
 *
 * @param  integer|array $idUser
 * @return object
 */
function getUsersContact($idUsers)
{
    $_this = &get_instance();
    $db = $_this->db;
    $db->select([
        "real_name",
        "phone_number as phone",
        "email",
    ]);
    if (gettype($idUsers) == 'array') {
        $db->where_in("id_user", $idUsers);
        return $db->get("user")->result();
    }

    $db->where([
        "id_user"    => $idUsers
    ]);
    return $db->get("user", 1)->row();
}

function getNamaSpkByIdJenis($idJenis)
{
    switch ($idJenis) {
        case 1:
            # code...
            break;

        default:
            # code...
            break;
    }
}

function emaiIsUsed($email)
{
    $_this = &get_instance();

    $count_email = 0;

    $_this->db->where(['email' => $email]);
    $_this->db->from('user');
    $count_email = $_this->db->count_all_results();
    if ($count_email > 0) return true;

    $_this->db->where(['email' => $email]);
    $_this->db->from('pelanggan');
    $count_email = $_this->db->count_all_results();
    if ($count_email > 0) return true;


    $_this->db->where(['email' => $email, 'status !=' => 'CANCEL']);
    $_this->db->from('orders');
    $count_email = $_this->db->count_all_results();
    if ($count_email > 0) return true;

    return false;
}

/**
 * getPelangganByIdSpk
 *
 * @param  int $idSpk
 * @param  int $idJenisSpk
 * @return void
 */
function getPelangganByIdSpk($idSpk, $idJenisSpk)
{
    $_this = &get_instance();
    $db = $_this->db;
    switch ($idJenisSpk) {

            // INSTALASI PELANGGAN BARU
        case 1:
            $db->select([
                "orders.nama as nama",
                "orders.no_hp as no_wa",
                "orders.email",
            ]);
            $db->where([
                "spk_instalasi.ref_spk_id"    => $idSpk
            ]);
            $db->join("orders", "orders.id = spk_instalasi.ref_order_id", "LEFT");

            return $db->get("spk_instalasi")->result();
            break;

            // MIGRASI
        case 2:
            $db->select([
                "pelanggan.nama as nama",
                "pelanggan.no_hp as no_wa",
                "pelanggan.email",
            ]);
            $db->where([
                "spk_migrasi_jaringan.id_spk"    => $idSpk
            ]);
            $db->join("pelanggan", "pelanggan.id = spk_migrasi_jaringan.id_pelanggan", "LEFT");

            return $db->get("spk_migrasi_jaringan")->result();
            break;

            // AKTIVASI ULANG
        case 4:
            $db->select([
                "pelanggan.nama as nama",
                "pelanggan.no_hp as no_wa",
                "pelanggan.email",
            ]);
            $db->where([
                "spk_aktivasi_pelanggan_off.id_spk"    => $idSpk
            ]);
            $db->join("pelanggan", "pelanggan.id = spk_aktivasi_pelanggan_off.id_pelanggan", "LEFT");

            return $db->get("spk_aktivasi_pelanggan_off")->result();
            break;

            //Relokasi
        case 5:
            $db->select([
                "pelanggan.nama as nama",
                "pelanggan.no_hp as no_wa",
                "pelanggan.email",
            ]);
            $db->where([
                "spk_relokasi.id_spk"    => $idSpk
            ]);
            $db->join("pelanggan", "pelanggan.id = spk_relokasi.id_pelanggan", "LEFT");

            return $db->get("spk_relokasi")->result();
            break;

            // GANGGUAN
        case 13:
            $db->select([
                "pelanggan.nama as nama",
                "pelanggan.no_hp as no_wa",
                "pelanggan.email",
            ]);
            $db->where([
                "spk_gangguan.id_spk"    => $idSpk
            ]);
            $db->join(
                "pelanggan",
                "pelanggan.id = spk_gangguan.id_pelanggan",
                "LEFT"
            );

            return $db->get("spk_gangguan")->result();
            break;

        default:
            # code...
            break;
    }
}

function getCapelById($idOrder)
{
    $ci = &get_instance();
    $db = $ci->db;
    $db->select([
        "nama",
        "no_hp as phone",
        "email"
    ]);
    $db->where([
        "id"    => $idOrder
    ]);

    return $db->get("orders", 1)->row();
}

function getMenus($namaMainModule)
{
    $ci = &get_instance();
    $db = $ci->db;
    $idgroup = getSessionUserGroupId();
    // $db->distinct();
    $db->select([
        "ms.*"
    ]);
    $db->where([
        "ms.main_modul" => $namaMainModule,
        "ms.is_active"  => 1,
        "mg.access"     => 1,
    ]);
    $db->order_by("ms.order asc");
    $db->join('module_permissions mp', 'mp.module_sistem_id = ms.id', 'left');
    $db->join('module_permission_group mg', 'mg.module_permission_id = mp.id', 'left');
    $menus = $db->get("modul_sistem ms")->result_array();

    if ($menus == null) {
        return [];
    }

    $parentMenu = array();
    $childrenMenu = array();

    foreach ($menus as $i => $menu) {
        if ($menu['parent_id'] ==  null || $menu['parent_id'] == "") {
            $menu['children'] = array();
            $parentMenu[] = $menu;
        } else {
            $childrenMenu[] = $menu;
        }
    }

    // return $parentMenu;

    foreach ($childrenMenu as $i => $child) {
        foreach ($parentMenu as $j => $parent) {
            if ($child["parent_id"] == $parent["id"]) {
                $parentMenu[$j]["children"][] = $child;
            }
        }
    }

    return $parentMenu;
}

function getDynamicNavMenus()
{
    $ci = &get_instance();
    $db = $ci->db;
    $idgroup = getSessionUserGroupId();
    $db->distinct();
    $db->select([
        "mm.nama",
        "mm.icon",
        "mm.uri_string",
        "mm.order",
    ]);
    $db->join("module_permissions mp", "mp.id = mpg.module_permission_id", "LEFT");
    $db->join("modul_sistem ms", "ms.id = mp.module_sistem_id", "LEFT");
    $db->join("main_modul mm", "mm.modul_name = ms.main_modul", "LEFT");
    $db->where([
        "mpg.access"    => 1,
        "mpg.group_id"  => getSessionUserGroupId()
    ]);
    $db->order_by("mm.order asc");
    $navs =  $db->get("module_permission_group mpg")->result();

    return $navs;
}

// function getParentMenus($namaMainModule)
// {
//     $ci = &get_instance();
//     $db = $ci->db;
//     $db->where([
//         "main_modul"    => $namaMainModule,
//         "is_active"     => 1,
//         "parent_id"     => null
//     ]);
//     $db->order_by("order asc");
//     $parents = $db->get("modul_sistem")->result_array();

//     foreach ($parents as $i => $parent) {
//         $nChild = checkChildren($parent['id']);
//         $parents[$i]['has_children'] = $nChild > 0;
//     }

//     return $parents;
// }

function getParentMenus($namaMainModule)
{
    $ci = &get_instance();
    $db = $ci->db;
    $db->distinct();
    $db->select([
        "ms.*"
    ]);
    $db->join("module_permissions mp", "mp.module_sistem_id = ms.id", "left");
    $db->join("module_permission_group mpg", "mpg.module_permission_id = mp.id", "left");

    $db->where([
        "ms.parent_id"  => null,
        "ms.is_active"  => 1,
        "mpg.group_id"  => getSessionUserGroupId(),
        "mpg.access"    => 1,
        "ms.main_modul" => $namaMainModule,

    ]);
    $db->order_by("order asc");
    return $db->get("modul_sistem ms")->result_array();
}

function getParentMenus2($namaMainModule)
{
    $ci = &get_instance();
    $db = $ci->db;

    $db->where([
        "parent_id" => null,
        "is_active" => 1,
        "main_modul"    => $namaMainModule
    ]);
    $db->order_by("order asc");
    $parents =  $db->get("modul_sistem")->result_array();

    return $parents;
}

function getChildren2($moduleSystemId)
{
    $ci = &get_instance();
    $db = $ci->db;
    $db->distinct();
    $db->select([
        "ms.*"
    ]);

    $db->join('module_permissions mp', 'mp.module_sistem_id = ms.id', 'left');
    $db->join('module_permission_group mpg', 'mpg.module_permission_id = mp.id', 'left');

    $db->where([
        "ms.is_active"  => 1,
        "ms.parent_id"  => $moduleSystemId,
        "mpg.access"    => 1,
        "mpg.group_id"  => getSessionUserGroupId(),
    ]);
    $db->order_by("order asc");
    return $db->get("modul_sistem ms")->result_array();
}

function checkParentMenuAccess($moduleSystemId)
{
    $ci = &get_instance();
    $db = $ci->db;
    $db->select([
        "ms.*"
    ]);

    $db->join('module_permissions mp', 'mp.id = mpg.module_permission_id', 'left');
    $db->join('modul_sistem ms', 'ms.id = mp.module_sistem_id', 'left');

    $db->where([
        "ms.is_active"  => 1,
        "ms.id"         => $moduleSystemId,
        "mpg.access"    => 1,
        "mpg.group_id"  => getSessionUserGroupId(),
    ]);
    $db->order_by("ms.order asc");
    return $db->count_all_results("module_permission_group mpg");
}

function getChildren($moduleSystemId)
{
    $ci = &get_instance();
    $db = $ci->db;
    $db->distinct();
    $db->select([
        "ms.*"
    ]);

    $db->join('module_permissions mp', 'mp.module_sistem_id = ms.id', 'left');
    $db->join('module_permission_group mpg', 'mpg.module_permission_id = mp.id', 'left');

    $db->where([
        "ms.is_active"  => 1,
        "ms.parent_id"  => $moduleSystemId,
        "mpg.access"    => 1,
        "mpg.group_id"  => getSessionUserGroupId(),
    ]);
    $db->order_by("order asc");
    return $db->get("modul_sistem ms")->result_array();
}

function checkChildren($idModulSistem)
{
    $ci = &get_instance();
    $db = $ci->db;
    $db->where([
        "is_active"     => 1,
        "parent_id"     => $idModulSistem
    ]);
    return $db->count_all_results("modul_sistem");
}

function getChildrenPermission($idModule)
{

    $ci = &get_instance();
    $db = $ci->db;
    $db->join('module_permissions mp', 'mpg.module_permission_id = mp.id', 'left');
    $db->where([
        "mpg.access"            => 1,
        "mpg.group_id"          => getSessionUserGroupId(),
        "mp.module_sistem_id"   => $idModule
    ]);
    $db->count_all_results('module_permission_group mpg');
}

function getDynamicMenus2($namaMainModule)
{
    $parents = getParentMenus2($namaMainModule);
    foreach ($parents as $i => $parent) {
        $parents[$i]["access"] = false;
        $child = getChildren2($parent["id"]);
        if ($child != null) {
            $parents[$i]["access"] = true;
            $parents[$i]["children"] = $child;
        } else {
            if (checkParentMenuAccess($parent["id"]) > 0) {
                $parents[$i]["access"] = true;
            }
        }
    }

    return $parents;
}

function getDynamicMenus($namaMainModule)
{
    $parents = getParentMenus($namaMainModule);
    foreach ($parents as $i => $parent) {
        $child = getChildren($parent["id"]);
        if ($child != null) {
            $parents[$i]["children"] = $child;
        }
    }

    return $parents;
}

function getInvoiceIdSpkBySpk($idSpk)
{
    $ci = &get_instance();
    if (empty($idSpk)) {
        return false;
    }
    $ci->db->where(['spk_id' => $idSpk]);
    $data = $ci->db->get('invoice_spk_detail', 1)->row();
    if (!empty($data)) {
        return $data->ref_invoice_id;
    }
    return false;
}

/**
 * getRekeningByMoota
 *
 * @param  string $mootaBankId dari moota
 * @return void
 */
function getRekeningByMoota($mootaBankId)
{
    $ci = &get_instance();

    $ci->db->where(['moota_bank_id' => $mootaBankId]);
    $data = $ci->db->get('ref_rekening', 1)->row_array();
    $return = null;
    if (!empty($data)) {
        $return = $data;
    } else {
        $return = getRekeningById('1');
    }
    return $return;
}

function getRekeningById($id)
{
    $ci = &get_instance();
    $ci->db->where(['id' => $id]);
    $data = $ci->db->get('ref_rekening')->row_array();
    return $data;
}

function getAtasNamaByInvoiceNumber($number)
{
    $ci = &get_instance();
    $ci->db->where(['number' => $number]);
    $data = $ci->db->get('invoices')->row();
    $return = null;
    if (!empty($data)) {
        $return = $data->atas_nama;
    }
    return $return;
}

function generateNomorPelanggan()
{
    $prefix = "";
    $last_code = lastNomor($prefix, 'pelanggan', 'nomor_pelanggan');
    $new_code = str_pad(($last_code + 1), 10, "0", STR_PAD_LEFT);
    return $prefix . $new_code;
}


function getPointRewardGangguanByTrouble($troubleId)
{
    $ci = &get_instance();
    $ci->db->select('point');
    $ci->db->where('id', $troubleId);
    $point = $ci->db->get('ref_troubles')->row();
    $return = 0;
    if (!empty($point)) {
        $return = $point->point;
    }
    return $return;
}

/**
 * Generate action button group
 *     $buttons = [
 *        (object) [
 *            "url"        => "dasd",
 *            "icon"       => "",
 *            "color"      => "",
 *            "tooltip"    => "",
 *            "permissions"   => "",
 *        ]
 *    ];
 * @param  array $buttons
 * @return string
 */
function generateButtonGroup($buttons)
{
    $buttonGroup = '<div class="text-center">';
    $buttonGroup .= '<div class="btn-group" role="group">';

    foreach ($buttons as $i => $button) {
        $button = (object) $button;

        if ($button->permissions != null || $button->permissions != '') {
            if (hasModuleAccess($button->permissions)) {
                $buttonGroup .= '<a role="button" href="' . $button->url . '" data-bs-original-title="' . $button->tooltip . '" data-bs-toggle="tooltip" data-bs-placement="right" title="' . $button->tooltip . '" class="btn btn-xs ' . $button->color . ' px-2 py-1"> ';

                $buttonGroup .= '<i class="' . $button->icon . ' fa-fw" data-bs-original-title="' . $button->tooltip . '" data-bs-toggle="tooltip" data-bs-placement="right" title="' . $button->tooltip . '"></i>';

                $buttonGroup .= ' </a>';
            }
        }
    }

    $buttonGroup .= '</div>';
    $buttonGroup .= '</div>';

    return $buttonGroup;
}

function getCountries()
{
    $ci = &get_instance();
    $db = $ci->db;

    $db->order_by("name asc");
    return  $db->get('ref_country')->result();
}

function getAgents()
{
    $ci = &get_instance();
    $db = $ci->db;

    $db->order_by("nama asc");
    return  $db->get('agents')->result();
}

function getMarketings()
{
    $ci = &get_instance();
    $db = $ci->db;

    $db->order_by("nama asc");
    return  $db->get('marketing')->result();
}

function getSearchDataDesa($query, $limit = 20)
{
    $ci = &get_instance();
    $db = $ci->db;

    $db->distinct();
    $db->select([
        "d.id as id_desa",
        "d.kode_desa",
        "d.nama_desa",
        "d.kode_pos",
        "kc.id as id_kec",
        "kc.kode_kec",
        "kc.nama_kec",
        "kb.id as id_kab",
        "kb.kode_kab",
        "kb.nama_kab",
        "p.id as id_prop",
        "p.kode_prop",
        "p.nama_prop"
    ]);

    $db->join("ref_kecamatan kc", "d.kode_kec = kc.kode_kec", "left");
    $db->join("ref_kabupaten kb", "d.kode_kab = kb.kode_kab", "left");
    $db->join("ref_propinsi p", "d.kode_prop = p.kode_prop", "left");

    $db->like("d.nama_desa", $query);
    $db->order_by('d.nama_desa', 'asc');
    $db->order_by('kc.nama_kec', 'asc');
    $db->order_by('kb.nama_kab', 'asc');
    $db->order_by('p.nama_prop', 'asc');
    $result = $db->get("ref_desa d", $limit)->result();

    return $result;
}

function getLayanan()
{
    $ci = &get_instance();
    $db = $ci->db;
    $db->select([
        "p.*",
        "c.registration_fee",
        "c.name as kategori_layanan"
    ]);
    $db->join("ref_product_categories c", "c.id = p.ref_product_categories_id", "left");
    return $db->get("ref_products p")->result();
}

function getJenisJaringan()
{
    $ci = &get_instance();
    $db = $ci->db;
    return $db->get("ref_jaringan j")->result();
}

function getRouters()
{
    $ci = &get_instance();
    $db = $ci->db;
    return $db->get("ref_routers r")->result();
}

function generateBadgeStatusInvoice($status)
{
    switch ($status) {
        case 'LUNAS':
            $color = 'bg-primary';
            break;

        case 'LEWAT':

        case 'CANCEL':
            $color = 'bg-danger';
            break;

        default:
            $color = "badge-warning text-dark";
            break;
    }

    return '<span class="badge ' . $color . '">' . $status . '</span>';
}




function curPostRequest($url, $data)
{

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
}

function isBukaGenerate($now = null)
{
    if (empty($now)) {
        $now = date('Y-m-d');
    }
    $month = date('m');
    $year = date('Y');
    $day = getSystemSetting('tanggal_akhir_generate_invoice_bulanan');
    $max = $year . '-' . $month . '-' . $day;
    $return = null;
    if ($now <= $max) {
        $return = true;
    } else {
        $return = false;
    }
    return $return;
}


function cetakStrukregistrasi($invoiceNumber, $numberOfPrint = 2)
{
    $ci = &get_instance();
    $ci->load->model('M_invoice_general');
    $ci->load->model('M_pelanggan_general');

    $urlPrinter = getSystemSetting("printer_pos_url");
    $namaPrinter = getSystemSetting("printer_pos_name");

    // var_dump($urlPrinter);
    // die;

    $invoice = $ci->M_invoice_general->getInvoiceByNumber($invoiceNumber);
    $invoice->details = $ci->M_invoice_general->detailInvoiceRegistrasi($invoice->id);
    $capel = $ci->M_pelanggan_general->getCapel($invoice->details[0]->ref_order_id);
    $order = $ci->M_pelanggan_general->getDetailCapel($capel);

    $data = [
        "printer"       => [
            "name"          => $namaPrinter,
            "number_print"  => $numberOfPrint,
        ],
        "invoice"       => $invoice,
        "order"         => $order,
        "jenis"         => "struk_registrasi",
        "header"        => [
            "logo"      => base_url("public/assets/asnet/asnet.png"),
            "nama"      => getSystemSetting('nama_perusahaan', "PT. ASNET DATA SOLUTION"),
            "lokasi"    => getSystemSetting('lokasi_perusahaan', "Dsn. Kranggan RT/RW 01/01, Pojok Garum, Blitar"),
            "telp"      => getSystemSetting('telp_perusahaan', "0342-8176041"),
            "hp"        => getSystemSetting('hp_perusahaan', "0821-1394-8178"),
            "email"     => getSystemSetting('email_perusahaan', "info@as-net.id"),
        ],
        "title"         => "Struk Pembayaran Registrasi Pelanggan Baru",
        "footer"        => "ASNET menyatakan struk ini sebagai bukti" . PHP_EOL . "pembayaran yang sah. Mohon disimpan."
    ];


    // return responseJson(200, $data);

    $post = curPostRequest($urlPrinter, $data);
    return successResponseJson("data", $post);
}

function cetakStrukBulanan($invoiceNumber)
{
    $ci = &get_instance();
    $ci->load->model('M_invoice_general');
    $ci->load->model('M_pelanggan_general');

    $urlPrinter = getSystemSetting("printer_pos_url");
    $namaPrinter = getSystemSetting("printer_pos_name");

    $invoice = $ci->M_invoice_general->getInvoiceByNumber($invoiceNumber);
    $invoice->details = $ci->M_invoice_general->detailInvoiceRegistrasi($invoice->id);
    $capel = $ci->M_pelanggan_general->getCapel($invoice->details[0]->ref_order_id);
    $order = $ci->M_pelanggan_general->getDetailCapel($capel);

    $data = [
        "printer"       => [
            "name"          => $namaPrinter,
            // "copies"        => $numberOfPrint,
        ],
        "invoice"       => $invoice,
        "order"         => $order,
        "jenis"         => "struk_registrasi",
        "header"        => [
            "logo"      => base_url("public/assets/asnet/asnet.png"),
            "nama"      => getSystemSetting('nama_perusahaan', "PT. ASNET DATA SOLUTION"),
            "lokasi"    => getSystemSetting('lokasi_perusahaan', "Dsn. Kranggan RT/RW 01/01, Pojok Garum, Blitar"),
            "telp"      => getSystemSetting('telp_perusahaan', "0342-8176041"),
            "hp"        => getSystemSetting('hp_perusahaan', "0821-1394-8178"),
            "email"     => getSystemSetting('email_perusahaan', "info@as-net.id"),
        ],
        "title"         => "Struk Pembayaran Registrasi Pelanggan Baru",
        "footer"        => "ASNET menyatakan struk ini sebagai bukti pembayaran yang sah. Mohon disimpan."
    ];


    // return responseJson(200, $data);

    $post = curPostRequest($urlPrinter, $data);
    return successResponseJson("data", $post);
}

function generateKodeUnikRegistrasi($productId)
{
}

function generateKodeUnikBulanan($productId)
{
    $ci = &get_instance();
    $db = $ci->db;
    $year = date('Y');
    $month = date("m");

    $db->select([
        "i.number",
        "i.kode_unik",
        "ib.id",
        "p.ref_product_id",
        "ib.invoice_month",
        "ib.invoice_year"
    ]);
    $db->join("invoices i", "i.id = ib.ref_invoice_id", "left");
    $db->join("pelanggan p", "ib.id_pelanggan = p.id", "left");

    $db->where([
        "p.ref_product_id"  => $productId,
        "ib.invoice_month"  => $month,
        "ib.invoice_year"   => $year,
    ]);
    $db->order_by("i.kode_unik desc");
    $invoice = $db->get("invoice_bulanan ib", 1)->row();
    $kodeUnik = 1;

    if ($invoice != null) {
        $kodeUnik = $invoice->kode_unik + 1;

        // if ($invoice->kode_unik >= 999) {
        //     $kodeUnik = 1;
        // }
    }

    return $kodeUnik;
}


/**
 * getUrlInvoiceHash
 *
 * @param  array $invoice
 * @return url
 */
function getUrlInvoiceHash($invoiceNumber)
{
    return site_url('index/invoice?inv=' . base64_encode($invoiceNumber));
}


function getPelangganIsolir()
{
    $ci = &get_instance();
    $db = $ci->db;

    $db->select([
        "p.id",
        "p.nama",
        "p.email",
        "concat(if(c.phonecode is null, '62', c.phonecode), no_hp) as phone"
    ]);
    $db->join("ref_country c", "c.id = p.ref_country_id", "left");

    $db->where([
        "status"    => "ISOLIR"
    ]);
    return $db->get("pelanggan p")->result();
}

function getDaysInterval($date1, $date2)
{
    $date1 = date_create($date1);
    $date2 = date_create($date2);

    $diff = date_diff($date1, $date2);

    return intval($diff->format("%a")) + 1;
}

function calculateProrateBulanan($tglGenerate, $tglAktivasi, $biayaBulanan)
{
    $maxDay = 30;
    $aktivasi = date("Y-m-d", strtotime($tglAktivasi));
    $generate = date("Y-m-d", strtotime($tglGenerate));
    $interval = abs(getDaysInterval($generate, $aktivasi));

    if ($interval > $maxDay) {
        $interval = $maxDay;
    }

    $prorate = ceil($interval / $maxDay * $biayaBulanan);

    return $prorate;
}

function listRekeningTransfer()
{
    $ci = &get_instance();
    $ci->db->select([
        'ref_rekening.*',
        'ref_banks.name as nama_bank'
    ]);
    $ci->db->join('ref_banks', 'ref_rekening.ref_bank_code = ref_banks.code', 'left');
    $ci->db->where(['is_used_mutation' => 1]);
    $ci->db->where('moota_bank_id is not null');
    $data = $ci->db->get('ref_rekening')->result();
    return $data;
}
