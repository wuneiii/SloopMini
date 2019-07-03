<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/3
 * Time: 上午10:54
 */

namespace SloopMini\DataAccess;


class ErrorCode {


    private static $error = array();

    public static function logError($code, $msg = '', $file = '', $line = '') {

        self::$error[] = array(
            'code' => $code,
            'msg'  => $msg,
            'file' => $file,
            'line' => $line
        );
    }

    const DSN_MISSING    = 13;
    const DRIVER_MISSING = 14;

    const MODEL_NOT_INIT = 1;

    const CANNOT_SCAN_ALL_TABLE   = 10;
    const CANNOT_DELETE_ALL_TABLE = 11;

    const DB_CONN_FAIL = 12;

    public static function getError() {
        return self::$error;
    }

}