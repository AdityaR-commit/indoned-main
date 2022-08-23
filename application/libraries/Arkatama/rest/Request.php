<?php
/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 06-03-2021
 * Time: 03:59
 */

namespace app\libraries\Arkatama\rest;
defined('BASEPATH') OR exit('No direct script access allowed');

class Request
{
    /**
     * @var string raw HTTP request body
     */
    private $_rawBody;

    /**
     * @var array Body params
     */
    private $_bodyParams;

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_PUT = 'PUT';
    const REQUEST_METHOD_PATCH = 'PATH';
    const REQUEST_METHOD_OPTIONS = 'OPTIONS';

    const APP_SECRET = 'app-secret';
    const APP_KEY = 'app-key';


    public function getMethod()
    {
        if (isset($_SERVER['HTTP_X-Http-Method-Override'])) {
            return strtoupper($_SERVER['HTTP_X-Http-Method-Override']);
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return self::REQUEST_METHOD_GET;
    }

    public function getContentType()
    {
        return isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;
    }

    /**
     * Returns the raw HTTP request body.
     * @return string the request body
     */
    public function getRawBody()
    {
        if ($this->_rawBody === null) {
            $this->_rawBody = file_get_contents('php://input');
        }
        return $this->_rawBody;
    }

    /**
     * Returns the request parameters given in the request body.
     * @return array the request parameters given in the request body.
     */
    public function getBodyParams()
    {
        if ($this->_bodyParams === null) {

            $contentType = $this->getContentType();

            if (strcasecmp($contentType, 'application/json') == 0) {
                // JSON content type
                $this->_bodyParams = json_decode($this->getRawBody(), true);
            } elseif ($this->getMethod() === 'POST') {
                // PHP has already parsed the body so we have all params in $_POST
                $this->_bodyParams = $_POST;
            } else {
                $this->_bodyParams = [];
                mb_parse_str($this->getRawBody(), $this->_bodyParams);
            }
        }

        return $this->_bodyParams;
    }

    /**
     * Alias of getRawBody()
     */
    public function input()
    {
        return $this->getBodyParams();
    }

    /**
     * Get Credentials with HTTP Basic Authentication
     *  list($username, $password) = $request->getAuthCredentialsWithBasic();
     */
    public function getAuthCredentialsWithBasic()
    {
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            return [$_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']];
        }

        $authToken = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
        $authToken = (!$authToken && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            : $authToken;

        if ($authToken !== null && strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'basic ') === 0) {

            $parts = array_map(function ($value) {
                return strlen($value) === 0 ? null : $value;
            }, explode(':', base64_decode(mb_substr($authToken, 6)), 2));

            if (count($parts) < 2) {
                return [$parts[0], null];
            }

            return $parts;
        }

        return [null, null];
    }

    /**
     * Get Credentials with Bearer Token
     *
     * @return string Bearer Token
     */
    public function getAuthCredentialsWithBearer()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function getHeaders()
    {
        $headers = null;
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $headers = array_combine(array_keys($headers), array_values($headers));
        }

        return $headers;
    }
}