<?php

/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 06-03-2021
 * Time: 03:59
 */

namespace app\libraries\Arkatama\rest;

defined('BASEPATH') OR exit('No direct script access allowed');

use app\libraries\Arkatama\exception\HttpException;
use app\libraries\Arkatama\rest\Request;
use app\libraries\Arkatama\rest\Response;


class Controller extends \CI_Controller
{
    public $_enableCors = true;
    public $_origin = ['*'];
    public $_allowedHeader = ['Authorization', 'Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Access-Control-Request-Method', 'Access-Control-Allow-Origin'];
    public $_allowedMethod = ['GET', 'POST', 'OPTIONS'];


    public $methodName;
    public $className;

    protected $format;

    protected $bodyFormat = true;

    protected $request;

    protected $response;

    protected $beforeAction;
    protected $afterAction;
    protected $verbs;


    function __construct()
    {
        parent::__construct();
        $this->request  = new Request;
        $this->response = new Response;
        // Response setting
        if ($this->format) {
            $this->response->setFormat($this->format);
        }
        $this->methodName = $this->router->fetch_method();
        $this->className  = $this->router->fetch_class();
    }


    protected function json($data = [], $bodyFormat = null, $statusCode = null, $message = null)
    {
        $bodyFormat = ($bodyFormat !== null) ? $bodyFormat : $this->bodyFormat;
        if ($bodyFormat) {
            $data = $this->_format($statusCode, $message, $data);
        } else {
            $data = is_array($data) ? $data : [$data];
        }
        return $this->response->json($data, $statusCode);
    }

    protected function _format($statusCode = null, $message = null, $body = false)
    {
        $format         = [];
        $format['code'] = ($statusCode) ?: $this->response->getStatusCode();
        if ($message) {
            $format['message'] = $message;
        }
        if ($body !== false) {
            $format['data'] = $body;
        }

        return $format;
    }

    protected function pack($data, $statusCode = 200, $message = null)
    {
        $packBody = [];

        if ($statusCode) {
            $packBody['code'] = $statusCode;
        }
        if ($message) {

            $packBody['message'] = $message;
        }
        if (is_array($data) || is_string($data)) {

            $packBody['data'] = $data;
        }

        return $packBody;
    }

    protected function _defaultAction()
    {
        show_404();
    }

    public function _remap($method, $params = array())
    {
        if ($this->_enableCors) { //cors
            $this->response = $this->response
                ->withAddedHeader('Access-Control-Allow-Origin', implode(',', $this->_origin))
                ->withAddedHeader('Access-Control-Allow-Headers', implode(',', $this->_allowedHeader))
                ->withAddedHeader('Access-Control-Allow-Methods', implode(',', $this->_allowedMethod));
            if ($this->request->getMethod() === Request::REQUEST_METHOD_OPTIONS) {
                return $this->response;
            }
        }
        $this->beforeAction();
        $this->verbs();
        if (method_exists($this, $method)) {
            empty($params) ? $this->{$method}() : call_user_func_array(array($this, $method), $params);
        }
        $this->afterAction();
    }

    public function __call($method, $args)
    {
        if (in_array($method, array('beforeAction', 'afterAction'))) {
            if (isset($this->{$method}) && !empty($this->{$method})) {
                $this->filter($method, isset($args[0]) ? $args[0] : $args);
            }
        } else if ($method === 'verbs') {
            $this->verbFilter($method, isset($args[0]) ? $args[0] : $args);
        } else {
            log_message('error', "Call to nonexistent method " . get_called_class() . "::{$method}");
            return false;
        }
    }

    protected function verbFilter($filter_type, $params)
    {
        $called_action = $this->methodName;
        $allowedVerbs  = isset($this->{$filter_type}[$called_action]) ? $this->{$filter_type}[$called_action] : [];
        $filterType    = isset($this->{$filter_type}) ? isset($this->{$filter_type}) : [];
        if ($filterType && $allowedVerbs) {
            if (!(in_array($this->request->getMethod(), $allowedVerbs))) {
                throw new HttpException(Response::HTTP_FORBIDDEN, 'Method not allowed,allowed method : ' . implode(', ', $allowedVerbs));
            }
        }

    }

    protected function filter($filter_type, $params)
    {
        $called_action = $this->methodName;

        if ($this->multiple_filter_actions($filter_type)) {
            foreach ($this->{$filter_type} as $filter) {
                $this->run_filter($filter, $called_action, $params);
            }
        } else {
            $this->run_filter($this->{$filter_type}, $called_action, $params);
        }
    }

    protected function run_filter(array &$filter, $called_action, $params)
    {
        if (method_exists($this, $filter['action'])) {
            // Set flags
            $only   = isset($filter['only']);
            $except = isset($filter['except']);

            if ($only && $except) {
                log_message('error', "Only and Except are not allowed to be set simultaneously for action ({$filter['action']} on " . $this->router->fetch_method() . ".)");
                return false;
            } elseif ($only && in_array($called_action, $filter['only'])) {
                empty($params) ? $this->{$filter['action']}() : $this->{$filter['action']}($params);
            } elseif ($except && !in_array($called_action, $filter['except'])) {
                empty($params) ? $this->{$filter['action']}() : $this->{$filter['action']}($params);
            } elseif (!$only && !$except) {
                empty($params) ? $this->{$filter['action']}() : $this->{$filter['action']}($params);
            }

            return true;
        } else {
            log_message('error', "Invalid action {$filter['action']} given to filter system in controller " . get_called_class());
            return false;
        }
    }

    protected function multiple_filter_actions($filter_type)
    {
        return !empty($this->{$filter_type}) && array_keys($this->{$filter_type}) === range(0, count($this->{$filter_type}) - 1);
    }


}