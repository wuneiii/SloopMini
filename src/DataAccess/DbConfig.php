<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/3
 * Time: 下午7:57
 */

namespace SloopMini\DataAccess;


class DbConfig {


    private static $configPool = array();


    public static function addConfig($name, $config = array()) {
        self::$configPool[$name] = $config;
    }

    public static function getConfig($dbSourceName) {

        if (isset(self::$configPool[$dbSourceName])) {
            return self::$configPool[$dbSourceName];
        }
        return false;

    }

}