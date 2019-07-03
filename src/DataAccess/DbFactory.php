<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/3
 * Time: 上午10:41
 */

namespace SloopMini\DataAccess;


class DbFactory {


    private static $instance;

    private function __construct() {
    }

    public static function getInstance() {

        if (!self::$instance) {
            self::$instance = new DbFactory();
        }
        return self::$instance;

    }


    private $dbConnPool = array();


    public function getConnection($dbSourceName = 'default') {
        $dsn = DbConfig::getConfig($dbSourceName);
        if (!$dsn) {
            ErrorCode::logError(ErrorCode::DSN_MISSING);
            return false;
        }
        $dbSign = md5(serialize($dsn));
        if (!isset($this->dbConnPool[$dbSign])) {

            $driver = isset($dsn['driver']) ? $dsn['driver'] : 'Mysqli';

            $className = __NAMESPACE__ . '\\Adapter\\' . ucfirst($driver);
            if (!class_exists($className)) {
                ErrorCode::logError(ErrorCode::DRIVER_MISSING);
                return false;
            }

            $connection = new $className;

            if (!$connection->connect($dsn)) {
                ErrorCode::logError(ErrorCode::DB_CONN_FAIL);
                return false;
            }
            $this->dbConnPool[$dbSign] = $connection;

        }
        return $this->dbConnPool[$dbSign];
    }


}