<?php

function responseJson($status, $response)
{
    $_this = &get_instance();
    return $_this->output
        ->set_content_type('application/json')
        ->set_status_header($status)
        ->set_output(json_encode($response));
}

function createdResponseJson($message, $data = null)
{
    $response =  [
        "message"   => [
            "title" => "Sukses",
            "body"  => $message
        ]
    ];

    if ($data != null) {
        $response['data'] = $data;
    }

    return responseJson(201, $response);
}

function successResponseJson($message, $data = null)
{
    $response =  [
        "message"   => [
            "title" => "Sukses",
            "body"  => $message
        ]
    ];

    if ($data != null) {
        $response['data'] = $data;
    }

    return responseJson(200, $response);
}

function forbiddenResponseJson($message = null)
{
    if ($message == null) {
        $message = "Anda tidak memiliki hak akses.";
    }
    $response = [
        "message"   => [
            "title" => "Error",
            "body"  => $message
        ]
    ];

    return responseJson(403, $response);
}

function notFoundResponseJson($message = null)
{
    if ($message == null) {
        $message = "Data tidak ditemukan";
    }
    $response = [
        "message"   => [
            "title" => "Error",
            "body"  => $message
        ]
    ];

    return responseJson(404, $response);
}

function methodNotAllowedResponseJson($message = null)
{
    if ($message == null) {
        $message = "Metode HTTP Request tidak diijinkan server.";
    }
    $response = [
        "message"   => [
            "title" => "Error",
            "body"  => $message
        ]
    ];

    return responseJson(405, $response);
}

function badRequestsResponseJson($message = null)
{
    if ($message == null) {
        $message = "Format HTTP Request salah.";
    }
    $response = [
        "message"   => [
            "title" => "Error",
            "body"  => $message
        ]
    ];

    return responseJson(400, $response);
}

function internalServerErrorResponseJson($message = null, $data = null)
{
    if ($message == null) {
        $message = "Terjadi kesalahan server. Silahkan menghubungi bagian IT.";
    }

    $response =  [
        "message"   => [
            "title" => "Error",
            "body"  => $message
        ]
    ];

    if ($data != null) {
        $response['data'] = $data;
    }

    return responseJson(500, $response);
}
