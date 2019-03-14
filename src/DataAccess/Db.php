<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2018/10/13
 * Time: 上午11:46
 */

namespace SloopMini\DataAccess;


class Db {

    private static $pool   = [];
    private static $config = [];

    public static function setConfig($host, $user, $password, $dbname = '', $port = 3306) {
        self::$config['default'] = [
            'host'     => $host,
            'username' => $user,
            'password' => $password,
            'dbname'   => $dbname,
            'port'     => $port
        ];
    }

    public static function getConnection($confName = '') {

        if (!$confName) {
            $confName = 'default';
        }

        if (!isset(self::$pool[$confName]) || empty(self::$pool[$confName])) {
            self::$pool[$confName] = new Db($confName);
        }

        return self::$pool[$confName];
    }

    private $driverInstance;

    private function __construct($confName = '') {

        $dbConfig = self::$config;
        if (isset($dbConfig[$confName])) {
            $dbConfig = $dbConfig[$confName];
        } else {
            die('no db config [' . $confName . ']');
        }


        if (!$dbConfig) {
            die('no db config');
        }

        if (!isset($dbConfig['driver'])) {
            $dbConfig['driver'] = 'Mysqli';
        }
        $driverClassName = __NAMESPACE__ . '\Adapter\\' . $dbConfig['driver'];

        $this->driverInstance = new $driverClassName;


        if (!$this->connect($dbConfig)) {
            die('connect failed');
        }
    }


    public function __call($funcName, $arrArgv) {
        $method = array(
            'connect',
            'fetchAll',
            'fetchOne',
            'insertId',
            'execSql',
            'numRows',
            'lastError',
            'sqlLog',
            'close',
        );


        return call_user_func_array(array(
            $this->driverInstance,
            $funcName
        ), $arrArgv);
    }

}
