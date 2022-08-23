<?php
/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 06-03-2021
 * Time: 03:59
 */

namespace app\libraries\Arkatama\rest;
defined('BASEPATH') OR exit('No direct script access allowed');

use Exception;

class Response
{
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_REQUEST_TIMEOUT = 408;
    const INTERNAL_SERVER_ERROR = 500;

    public static $httpStatuses = [
        self::HTTP_OK                 => 'OK',
        self::HTTP_BAD_REQUEST        => 'Bad Request',
        self::HTTP_UNAUTHORIZED       => 'Unauthorized',
        self::HTTP_FORBIDDEN          => 'Forbidden',
        self::HTTP_NOT_FOUND          => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::HTTP_NOT_ACCEPTABLE     => 'Not Acceptable',
        self::HTTP_REQUEST_TIMEOUT    => 'Request Time-out',
        self::INTERNAL_SERVER_ERROR   => 'Internal Server Error',
    ];
    /**
     * @var string HTTP response formats
     */
    const FORMAT_RAW = 'raw';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_JSONP = 'jsonp';
    const FORMAT_XML = 'xml';
    /**
     * @var object CI_Controller
     */
    public $ci;
    /**
     * @var array the formatters that are supported by default
     */
    public $contentTypes = [
        self::FORMAT_RAW   => 'text/plain;',
        self::FORMAT_HTML  => 'text/html;',
        self::FORMAT_JSON  => 'application/json;',
        self::FORMAT_JSONP => 'application/javascript;',
        self::FORMAT_XML   => 'application/xml;'
    ];
    /**
     * @var string the response format. This determines how to convert [[data]] into [[content]]
     */
    private $_format = self::FORMAT_JSON;
    /**
     * @var int the HTTP status code to send with the response.
     */
    private $_statusCode = self::HTTP_OK;

    function __construct()
    {
        // CI_Controller initialization
        $this->ci = &get_instance();
    }

    /**
     * Set Response Format into CI_Output
     */
    public function setFormat($format)
    {
        $this->_format = $format;
        // Use formatter content type if exists
        if (isset($this->contentTypes[$this->_format])) {
            $this->ci->output
                ->set_content_type($this->contentTypes[$this->_format]);
        }

        return $this;
    }

    /**
     * Set Response Data into CI_Output
     *
     * @param mixed Response data
     * @return object self
     */
    public function setData($data)
    {
        // Format data
        $data = $this->format($data, $this->_format);
        // CI Output
        $this->ci->output->set_output($data);

        return $this;
    }

    /**
     * Get Response Body from CI_Output
     *
     * @return string Response body data
     */
    public function getOutput()
    {
        // CI Output
        return $this->ci->output->get_output();
    }

    /**
     * @return int the HTTP status code to send with the response.
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * Sets the response status code.
     * This method will set the corresponding status text if `$text` is null.
     * @param int    $code the status code
     * @param string $text HTTP status text base on PHP http_response_code().
     * @return $this the response object itself
     * @throws Exception if the status code is invalid.
     */
    public function setStatusCode($code, $text = null)
    {
        if ($code === null) {
            $code = self::HTTP_OK;
        }
        // Save code into property
        $this->_statusCode = (int)$code;
        // Check status code
        if ($this->getIsInvalid()) {
            throw new Exception("The HTTP status code is invalid: " . $this->_statusCode);
        }
        // Set HTTP status code with options
        if ($text) {
            // Set into CI_Output
            $this->ci->output->set_status_header($this->_statusCode, $text);
        } else {
            // Use PHP function with more code support
            http_response_code($this->_statusCode);
        }

        return $this;
    }

    /**
     * @return bool whether this response has a valid [[statusCode]].
     */
    public function getIsInvalid()
    {
        return $this->getStatusCode() < 100 || $this->getStatusCode() >= 600;
    }

    /**
     * Sends the response to the client.
     */
    public function send()
    {
        $this->ci->output->_display();
        exit;
    }

    /**
     * @param array  Pre-handle array data
     */
    public function format($data, $format)
    {
        // Case handing. ex. json => Json
        $format     = ucfirst(strtolower($format));
        $formatFunc = "format" . $format;
        // Use formatter if exists
        if (method_exists($this, $formatFunc)) {

            $data = $this->{$formatFunc}($data);
        } elseif (is_array($data)) {
            // Use JSON while the Formatter not found and the data is array
            $data = $this->formatJson($data);
        }

        return $data;
    }

    /**
     * Common format funciton by format types. {FORMAT}Format()
     *
     * @param array Pre-handle array data
     * @return string Formatted data
     */
    public static function formatJson($data)
    {
        return json_encode($data);
    }

    public function json($data, $statusCode = null)
    {
        // Set Status Code
        if ($statusCode) {
            $this->setStatusCode($statusCode);
        }

        $this->setFormat(Response::FORMAT_JSON);

        if (!is_null($data)) {
            $this->setData($data);
        }

        return $this->send();
    }

    /**
     * Return an instance with the specified header appended with the given value.
     * @param string          $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     */
    public function withAddedHeader($name, $value)
    {
        $this->ci->output->set_header("{$name}: {$value}");

        return $this;
    }
}