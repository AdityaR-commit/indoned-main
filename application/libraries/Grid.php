<?php
/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 26-02-2021
 * Time: 00:34
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');
include(APPPATH . 'libraries/GroceryCrudEnterprise/autoload.php');

use GroceryCrud\Core\GroceryCrud;

class Grid extends GroceryCrud
{
    private function _dbTransformation($dbConfig)
    {
        $db = [];
        include(APPPATH . 'config/database.php');
        return [
            'adapter' => [
                'driver'   => 'Pdo_Mysql',
                'host'     => $db[$dbConfig]['hostname'],
                'database' => $db[$dbConfig]['database'],
                'username' => $db[$dbConfig]['username'],
                'password' => $db[$dbConfig]['password'],
                'charset'  => 'utf8'
            ]
        ];
    }

    public function __construct($db = 'default')
    {
        $config = include(APPPATH . 'config/grid.php');
        $db     = $this->_dbTransformation($db);
        $this->unsetBootstrap();
      //  $this->unsetJquery();
      //  $this->unsetJqueryUi();
        try {
            parent::__construct($config, $db);
        } catch (\GroceryCrud\Core\Exceptions\Exception $e) {
        }
    }
}