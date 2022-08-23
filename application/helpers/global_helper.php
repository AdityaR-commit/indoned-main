<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

	$prefix = PREFIX_SESS;
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


function uploadImage($nama_file, $path_folder, $prefix)
{
	$ci = &get_instance();

	$response['success'] = false;
	$response['file_name'] = '';
	$nama_foto = "";
	if (!empty($_FILES[$nama_file]['name'])) {
		list($width, $height) = getimagesize($_FILES[$nama_file]['tmp_name']);
		$config['upload_path'] = 'public/uploads/' . $path_folder; //path folder file upload
		$config['allowed_types'] = 'gif|jpg|jpeg|png|bmp'; //type file yang boleh di upload
		$config['max_size'] = '3000';
		$config['file_name'] = $prefix . '_' . date('ymdhis'); //enkripsi file name upload
		$ci->load->library('upload');
		$ci->upload->initialize($config);
		if ($ci->upload->do_upload($nama_file)) {
			$file_foto = $ci->upload->data();
			// $con['image_library']='gd2';
			// $con['source_image']= './public/uploads/'.$path_folder.'/'.$file_foto['file_name'];
			// $con['create_thumb']= FALSE;
			// $con['maintain_ratio']= TRUE;
			// $con['quality']= '100%';
			// $con['width']= round($width/5);
			// $con['height']= round($height/5);
			// $con['new_image']= './public/uploads/'.$path_folder.'/'.$file_foto['file_name'];
			// $ci->load->library('image_lib');
			// $ci->image_lib->initialize($con);
			$nama_foto = '/public/uploads/' . $path_folder . '/' . $file_foto['file_name'];
			$response['success'] = true;
			$response['file_name'] = $nama_foto;
		}
	}
	return $response;
}

function uploadBerkas($nama_file, $path_folder, $prefix)
{
	$ci = &get_instance();

	$response['success'] = false;
	$response['file_name'] = '';
	$nama_berkas = "";
	if (!empty($_FILES[$nama_file]['name'])) {
		list($width, $height) = getimagesize($_FILES[$nama_file]['tmp_name']);
		$file_type = explode("/", $_FILES[$nama_file]['type'])[0];
		$config['upload_path'] = 'public/uploads/' . $path_folder; //path folder file upload
		$config['allowed_types'] = 'pdf|gif|jpg|jpeg|png|bmp|webp|heic'; //type file yang boleh di upload
		$config['max_size'] = '5000';
		$config['file_name'] = $prefix . '_' . date('ymdhis'); //enkripsi file name upload
		$ci->load->library('upload');
		$ci->upload->initialize($config);
		if ($ci->upload->do_upload($nama_file)) {
			$file_foto = $ci->upload->data();
			// if($file_type == 'image'){
			//   $config['image_library']='gd2';
			//   $config['source_image']='./public/uploads/'.$path_folder.'/'.$file_foto['file_name'];
			//   $config['create_thumb']= FALSE;
			//   $config['maintain_ratio']= TRUE;
			//   $config['quality']= '100%';
			//   $config['width']= round($width/5);
			//   $config['height']= round($height/5);
			//   $config['new_image']= './public/uploads/'.$path_folder.'/'.$file_foto['file_name'];
			//   $ci->load->library('image_lib');
			//   $ci->image_lib->initialize($config);
			//   $ci->image_lib->resize();
			//   $response['file_type'] = 'image';
			// }
			$nama_berkas = '/public/uploads/' . $path_folder . '/' . $file_foto['file_name'];
			$response['success'] = true;
			$response['file_name'] = $nama_berkas;
		}
	}
	return $response;
}

if (!function_exists('auto_code')) {
	function auto_code($prefix, $delim = "", $tipe = "set", $position = "append")
	{
		$ci  = &get_instance();
		$ci->db->query("INSERT INTO counter (prefix, sequence) VALUES (?, 1) ON DUPLICATE KEY UPDATE sequence  =  sequence + 1", [$prefix]);
		if ($tipe != "set") {
			$ci->db->query("UPDATE counter set sequence = sequence - 1");
		}
		$result = $ci->db->query("SELECT sequence FROM counter WHERE prefix = ?", [$prefix]);
		$row  =  $result->row();
		if ($position == "append") {
			$result  =  strtoupper(substr($prefix, 0, -2)) . $delim . str_pad($row->sequence, 4, '0', STR_PAD_LEFT);
		} else {
			$result  =  str_pad($row->sequence, 4, '0', STR_PAD_LEFT) . $delim . strtoupper(substr($prefix, 0, -2));
		}
		return $result;
	}
}

/**
 * _session
 *
 * @key string $key session
 * @return void
 */
function getSession($key)
{
	$ci = &get_instance();
	$return = $ci->session->userdata(PREFIX_SESS . '_' . $key);
	return $return;
}

function isPDF($param)
{
	$file = 'gambar';
	$panjang =  strlen($param);
	if (strpos($param, '.pdf')) {
		$file = 'pdf';
	}
	return $file;
}

function tampil_sebagian($param, $panjang)
{
	//$panjang = strlen($param);
	$tampil = substr($param, 0, $panjang);
	return $tampil;
}

function konversi($tanggal)
{
	$bulan = array(
		1 => 'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	if ($tanggal == null || empty($tanggal)) {
		return "0000-00-00";
	} else {
		$split = explode('-', $tanggal);
		if (count($split) > 0) {
			$tanggal = substr($split[2], 0, 2);
			if ($split[1] == "00") {
				return $tanggal . ' ' . $split[1] . ' ' . $split[0];
			} else {
				return $tanggal . ' ' . $bulan[(int) $split[1]] . ' ' . $split[0];
			}
		} else {
			return "0000-00-00";
		}
	}
}

function get_tahun($tanggal)
{
	$thn = explode("-", $tanggal);
	return $thn[0];
}

function jam($tanggal)
{
	if ($tanggal == null || empty($tanggal)) {
		return "00:00:00";
	} else {
		$split = explode(' ', $tanggal);
		return $split[1];
	}
}

function konversi_tgl_jam($tanggal)
{
	if ($tanggal != '0000-00-00 00:00:00' || $tanggal == NULL) {
		$bulan = array(
			1 => 'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$split = explode('-', $tanggal);
		$tanggal = substr($split[2], 0, 2);
		$jam = substr($split[2], 3);
		return $tanggal . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0] . ' ' . $jam;
	} else
		return NULL;
}

function get_hari_from_date($date = null)
{ // yyyy-mm-dd
	$day = date('D', strtotime($date));
	$dayList = array(
		'Sun' => 'Minggu',
		'Mon' => 'Senin',
		'Tue' => 'Selasa',
		'Wed' => 'Rabu',
		'Thu' => 'Kamis',
		'Fri' => 'Jumat',
		'Sat' => 'Sabtu'
	);
	return $dayList[$day];
}

function getAllKecamatan($keyword = null)
{
	$ci = &get_instance();
	$query = $ci->db->query("SELECT kec.id as kode_kec, CONCAT_WS(' - ',kec.name, kab.name, prov.name) as nama_kec FROM districts kec
    LEFT JOIN regencies kab ON kab.id=kec.regency_id
    LEFT JOIN provinces prov ON prov.id=kab.province_id
    WHERE kec.name LIKE '%$keyword%'")->result_array();
	return $query;
}

function getKecKabProv($kode_kecamatan = null)
{
	$ci = &get_instance();
	$query = $ci->db->query("SELECT kec.id as kode_kec, CONCAT_WS(' - ',kec.name, kab.name, prov.name) as nama_kec FROM districts kec
    LEFT JOIN regencies kab ON kab.id=kec.regency_id
    LEFT JOIN provinces prov ON prov.id=kab.province_id
    WHERE kec.id=?", $kode_kecamatan)->row_array();
	return $query;
}

function getKecamatan($kode_kecamatan = null)
{
	$ci = &get_instance();
	$query = null;
	if ($kode_kecamatan) {
		$query = $ci->db->get_where('districts', array('id' => $kode_kecamatan))->row_array();
	} else {
		$query = $ci->db->get_where('districts')->result_array();
	}
	return $query;
}

function getKabupatenKota($kode_kabupaten = null)
{
	$ci = &get_instance();
	$query = null;
	if ($kode_kabupaten) {
		$query = $ci->db->get_where('regencies', array('id' => $kode_kabupaten))->row_array();
	} else {
		$query = $ci->db->get_where('regencies')->result_array();
	}
	return $query;
}

function getTahunPWMP($iscurrent = null)
{
	$ci = &get_instance();
	$query = null;
	if ($iscurrent == '1') {
		$query = $ci->db->get_where('ref_tahun', array('is_active' => '1', 'is_current' => '1'))->row_array();
	} else {
		$query = $ci->db->get_where('ref_tahun', array('is_active' => '1'))->result_array();
	}
	return $query;
}


function randomPassword()
{
	$alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';
	$pass = array();
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}

function randomPassword_number()
{
	$alphabet = '1234567890';
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}

function getUUID()
{
	$ci = &get_instance();
	$result = $ci->db->query("SELECT UUID()")->row_array()['UUID()'];
	return $result;
}

function ceknik($nik)
{
	$return['pesan'] = '';
	$return['status'] = false;
	$ci = &get_instance();
	if (strlen($nik) != 16) {
		$return['pesan'] = 'NIK Harus 16 Digit';
		$return['status'] = false;
	} else {
		$return['pesan'] = '';
		$return['status'] = true;
	}
	return $return;
}

function active_page($page, $class)
{
	$_this = &get_instance();
	if ($page == $_this->uri->segment(1)) {
		return $class;
	}
}

function getDayName($day_of_week)
{
	switch ($day_of_week) {
		case 1:
			return 'Senin';
			break;

		case 2:
			return 'Selasa';
			break;

		case 3:
			return 'Rabu';
			break;

		case 4:
			return 'Kamis';
			break;

		case 5:
			return 'Jumat';
			break;

		case 6:
			return 'Sabtu';
			break;

		case 0:
			return 'Minggu';
			break;

		default:
			return 'Senin';
			break;
	}
}

function getMonthName($month)
{
	switch ($month) {
		case 1:
			return 'Januari';
			break;

		case 2:
			return 'Februari';
			break;

		case 3:
			return 'Maret';
			break;

		case 4:
			return 'April';
			break;

		case 5:
			return 'Mei';
			break;

		case 6:
			return 'Juni';
			break;

		case 7:
			return 'Juli';
			break;

		case 8:
			return 'Agustus';
			break;

		case 9:
			return 'September';
			break;

		case 10:
			return 'Oktober';
			break;

		case 11:
			return 'November';
			break;

		case 12:
			return 'Desember';
			break;

		default:
			# code...
			break;
	}
}

function parseTanggal($date)
{
	date_default_timezone_set('Asia/Jakarta');
	$day_name = getDayName(date('w', strtotime($date)));
	$day = date('d', strtotime($date));
	$month = getMonthName(date('m', strtotime($date)));
	$year = date('Y', strtotime($date));
	return "$day_name, $day $month $year";
}

function get_enum_values($table, $field)
{
	$ci = &get_instance();
	$type = $ci->db->query("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'")->row(0)->Type;
	preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
	$enum = explode("','", $matches[1]);
	return $enum;
}

function getCountryCode()
{
	$ci = &get_instance();
	$ci->db->select([
		"id",
		"concat(nicename, ' ( +', phonecode, ' )') as label",
		"nicename",
		"phonecode",
		"is_default",
	]);
	$ci->db->order_by('label asc');
	return $ci->db->get("ref_country")->result();
}

function getIp()
{
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


/**
 * get struktur semua kolom di $table dari $database
 *
 * @param string $table, required
 * @param string|null $database, optional, default NULL, fill NULL to $ci->db->datatabase
 */
function get_all_column_from($table, $database = NULL)
{
	$ci = &get_instance();
	$database = $database ? $database : $ci->db->database;
	return $ci->db->query("SHOW COLUMNS FROM $database.$table")->result();
}

/**
 * Untuk generate string kolom seperti $table.* AS $prefix$table$postfix
 * @param string $table required
 * @param string $prefix optional, default ''
 * @param string $postfix optional, default ''
 * @param string|null $database optional, default NULL, fill NULL to $this->db->database
 * @return string
 */
function generate_all_column_as($table, $prefix = '', $postfix = '', $database = NULL, $table_alias = NULL)
{
	$ci = &get_instance();

	$database = $database ? $database : $ci->db->database;

	$column = get_all_column_from($table, $database);
	$column = array_map(function ($row) use ($table, $prefix, $postfix, $table_alias) {
		return "`" . ($table_alias ? $table_alias : $table) . "`.`{$row->Field}` AS '$prefix{$row->Field}$postfix'";
	}, $column);
	$column = implode(', ', $column);
	return $column;
}

/**
 * @param string $tableName Required. This must be tablename or aliasname
 * @param string $prefix Optional. Default ''. This will use for prefix column
 * @param string $postfox Optional. Default ''. This will use for prostfix column
 * @param array $columns Optional. Default []
 * @return string query select column
 */
function generate_select_column($tableName, $prefix = '', $postfix = '', $columns = [])
{
	return implode(' , ', array_map(function ($column) use ($tableName, $prefix, $postfix) {
		return " `$tableName`.`$column` AS '$prefix$column$postfix' ";
	}, $columns));
}

function currencyIDR($nominal, $symbol = true)
{
	$val = "";
	if ($symbol) {
		$val .= "Rp. ";
	}

	$val .= number_format($nominal, 0, ",", ".");
	return $val;
}

function getSystemSetting($name, $default = null)
{
	$ci = &get_instance();
	// $ci->db = $ci->load->database('root', true);
	$ci->db->where([
		"name"  => $name
	]);

	$query = $ci->db->get("system_settings", 1);

	if ($query->num_rows() > 0) {
		return $query->row()->value;
	}

	return $default;
}

function setSystemSetting($name, $value)
{
	$ci = &get_instance();
	$ci->db = $ci->load->database('root', true);
	$ci->db->where([
		"name"  => $name
	]);

	$query = $ci->db->get("system_settings", 1);

	if ($query->num_rows() > 0) {
		$ci->db->update('system_settings', ['value' => $value], ['name' => $name]);
	} else {
		$ci->db->insert('system_settings', [
			'name' => $name,
			'value' => $value
		]);
	}
}
