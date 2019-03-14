<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2018/9/20
 * Time: 上午10:45
 */

namespace SloopMini\Util;


class Timer {

    private static $log;
    private static $tsStart;

    public static function startTimer() {
        self::$tsStart = self::now();
        self::timeline('startTimer');
    }

    public static function timeline($msg) {
        self::$log[] = array(
            'ts'  => self::now() - self::$tsStart,
            'msg' => $msg,
        );
    }

    public static function now() {
        return microtime(true);
    }

}