<?php

function hasRole($namaGroup, $userId = null)
{
    $_this = &get_instance();
    if ($userId == null) {
        $userId = getSessionUserId();
    }

    $_this->db->select([
        "user.id_user as id_user",
        "user.id_group as id_group",
        "group.nama_group as nama_group",
    ]);
    $_this->db->from("user");

    $_this->db->where([
        "user.id_user"      => $userId,
        "group.nama_group"  => strtoupper($namaGroup)
    ]);

    $_this->db->join('user_group as group', 'group.id = user.id_group', 'LEFT');
    $_this->db->limit(1);
    $query = $_this->db->get()->row();

    if ($query != null) {
        return true;
    }

    return false;
}

function hasModuleAccess($namaModuls, $userId = null)
{
    // TODO: ambil dari session
    // return true;
    $_this = &get_instance();
    if ($userId == null) {
        $userId = getSessionUserId();
    }

    $_this->db->select([
        "user.id_group as id_group",
    ]);
    $_this->db->from("user");

    $_this->db->where([
        "user.id_user"      => $userId,
    ]);

    $_this->db->limit(1);
    $user = $_this->db->get()->row();

    if ($user != null) {
        $_this->db->select([
            "access",
        ]);
        $_this->db->from("module_permission_group mpg");

        $_this->db->where([
            "mpg.group_id"  => $user->id_group,
            "mpg.access"    => 1
        ]);

        if (gettype($namaModuls) == "string") {
            $_this->db->where([
                "mp.permission_name" => $namaModuls
            ]);
        }

        if (gettype($namaModuls) == "array") {
            $_this->db->where_in("mp.permission_name", $namaModuls);
        }

        $_this->db->join('module_permissions mp', 'mp.id = mpg.module_permission_id', 'left');

        $count = $_this->db->count_all_results();

        if ($count > 0) {
            return true;
        }

        return false;
    }

    return false;
}

function hasMainModulAccess($mainModule, $userId = null)
{
    $_this = &get_instance();
    if ($userId == null) {
        $userId = getSessionUserId();
    }

    $_this->db->select([
        "user.id_group as id_group",
    ]);
    $_this->db->from("user");

    $_this->db->where([
        "user.id_user"      => $userId,
    ]);

    $_this->db->limit(1);
    $user = $_this->db->get()->row();
    $_this->db->reset_query();

    if ($user != null) {
        $_this->db->select([
            "main_modul.modul_name",
        ]);

        $_this->db->from("main_modul");

        $_this->db->join('modul_sistem as modul', 'modul.main_modul = main_modul.modul_name', 'LEFT');
        $_this->db->join('akses_group_modul as akses', 'akses.modul_name = modul.modul_name', 'LEFT');

        $_this->db->where([
            "akses.id_group"            => $user->id_group,
            "main_modul.modul_name"     => $mainModule,
            "akses.hak_akses"           => 1,
        ]);

        $count = $_this->db->count_all_results();

        if ($count > 0) {
            return true;
        }
        return false;
    }

    return false;
}

/**
 * Mengambil group ids yang memiliki hak akses di module tertentu
 *
 * @param  array $namaModules Array nama modules (tabel modul_sistem)
 * @return array Id Groups
 */
function getGroupsHasAccess($namaModules)
{
    $_this = &get_instance();
    $_this->db->select([
        "id_group"
    ]);
    $_this->db->where_in('modul_name', $namaModules);
    $_this->db->where([
        'hak_akses', 1
    ]);

    $query = $_this->db->get('akses_group_modul');
    return $query->result();
}


/**
 * Redirect user jika tidak punya modul akses
 *
 * @param  string|array $namaModuls
 * @param  string $redirectPath
 * @param  array $flashdata
 * @return void
 */
function redirectIfHasNotModuleAccess($namaModuls, $redirectPath = "dashboard", $flashdata = null)
{
    $_this = &get_instance();
    // var_dump($namaModuls);
    // die;

    if (!hasModuleAccess($namaModuls)) {
        if ($flashdata == null) {
            $flashdata = "Anda tidak memiliki hak akses untuk mengakses halaman tersebut.";
        }

        $_this->session->set_flashdata('flash-message', [
            "icon"      => "fas fa-exclamation-triangle",
            "color"     => "alert-danger",
            "title"     => "Peringatan",
            "message"   => $flashdata
        ]);

        return redirect($redirectPath);
    }
}

/**
 * Mengembailkan response json forbidden jika tidak punya modul akses
 *
 * @param  string|array $namaModuls
 * @param  string $message
 * @return void
 */
function returnForbiddenIfHasNotModuleAccess($namaModuls, $message = null)
{
    if (!hasModuleAccess($namaModuls)) {
        return forbiddenResponseJson($message);
    }
}

function getUsersByModulNames($moduleNames)
{
    $_this = &get_instance();

    $_this->db->distinct();
    $_this->db->select([
        'u.email',
        'u.phone_number',
        'u.real_name',
    ]);


    $_this->db->where([
        "hak_akses"     => 1
    ]);

    if (gettype($moduleNames) == "string") {
        $_this->db->where([
            "modul_name"      => $moduleNames
        ]);
    }
    if (gettype($moduleNames) == "array") {
        $_this->db->where_in("modul_name", $moduleNames);
    }

    $_this->db->join('user u', 'u.id_group = a.id_group', 'left');
    $users =  $_this->db->get("akses_group_modul a")->result();
    return $users;
}

function getNotificationReceiversByPermissions($permissions)
{
    $_this = &get_instance();

    $_this->db->distinct();
    $_this->db->select([
        "concat(if(c.phonecode is null or c.phonecode = '', '62', c.phonecode),u.phone_number) as phone",
        'u.real_name as nama',
        'u.email'
    ]);

    $_this->db->where([
        "access"     => 1
    ]);

    if (gettype($permissions) == "string") {
        $_this->db->where([
            "permission_name"   => $permissions
        ]);
    }
    if (gettype($permissions) == "array") {
        $_this->db->where_in("permission_name", $permissions);
    }

    $_this->db->join('module_permissions p', 'pg.module_permission_id = p.id', 'left');
    $_this->db->join('user u', 'u.id_group = pg.group_id', 'left');
    $_this->db->join('ref_country c', 'c.id = u.ref_country_id', 'left');
    $users = $_this->db->get("module_permission_group pg")->result();

    return $users;
}
