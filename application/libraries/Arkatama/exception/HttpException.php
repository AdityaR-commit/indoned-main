<?php

/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 08-03-2021
 * Time: 15:59
 */

namespace app\libraries\Arkatama\exception;

use app\libraries\Arkatama\exception\CustomException;
use app\libraries\Arkatama\rest\Response;


class HttpException extends CustomException
{
    private $_errorName = 'API ERROR';

    function __construct($code = Response::HTTP_BAD_REQUEST, $message = null)
    {
        parent::__construct($message, $code);
        set_exception_handler([$this, 'api_exception_handler']);

    }

    function api_exception_handler($exception)
    {
        return $this->api_error($this->_errorName, $exception->getMessage(), $exception->getCode());
    }

    function api_error($name, $error, $code)
    {

        $response = new Response();
        $response->setStatusCode($code);
        $response->setFormat(Response::FORMAT_JSON);
        $response->setData([
            'name'    => $this->_errorName,
            'message' => $error,
            'error'   => $error,
        ]);
        return $response
            ->send();
    }
}

