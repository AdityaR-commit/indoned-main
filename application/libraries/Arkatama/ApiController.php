<?php
/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 06-03-2021
 * Time: 14:48
 */

namespace app\libraries\Arkatama;

defined('BASEPATH') OR exit('No direct script access allowed');

use app\libraries\Arkatama\rest\Controller;
use app\libraries\Arkatama\exception\HttpException;
use app\libraries\Arkatama\rest\Request;
use app\libraries\Arkatama\rest\Response;
use JWT;

class ApiController extends Controller
{
    public $userToken;
    public $systemApp;
    public $catMode;
    const MODE_LIMITED = 'limited';
    const MODE_FULL = 'full';

    protected $beforeAction = [
        'action' => '_authCheck',
        'except' => [
            'action' => ''
        ]
    ];
    protected $afterAction = [];


    function __construct()
    {
        parent::__construct();
        $this->catMode = $this->config->item('cat_mode');
    }

    public function _authCheck()
    {
        $bearerToken = $this->request->getAuthCredentialsWithBearer();
        if ($bearerToken) {
            try {
                $this->userToken = JWT::decode($bearerToken, $this->config->item('jwt_key'), ['HS256']);
                if (!$this->userToken) {
                    throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid token provided');
                }
            } catch (\Exception $e) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid token provided');
            }
        } else {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Unautorized please login');
        }
    }

    public function _checkSystemApp()
    {
        $headers   = $this->request->getHeaders();
        $appSecret = isset($headers[Request::APP_SECRET]) ? $headers[Request::APP_SECRET] : FALSE;
        $appKey    = isset($headers[Request::APP_KEY]) ? $headers[Request::APP_KEY] : FALSE;

        if ($appKey && $appSecret) {
            $systemApp = $this->db
                ->where('app_key', $appKey)
                ->join('ref_perguruan_tinggi', 'ref_perguruan_tinggi.id_perguruan_tinggi = system_app.ref_perguruan_tinggi_id')
                ->where('system_app.app_secret', $appSecret)
                ->where('system_app.is_active', 'true')
                ->get('system_app')
                ->row();
            if ($systemApp) {
                $this->systemApp = $systemApp;
            } else {
                throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid appKey or appSecret');
            }
        } else {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Please provide appKey and appSecret');
        }
    }

    public function getUserToken()
    {
        return $this->userToken;
    }
}