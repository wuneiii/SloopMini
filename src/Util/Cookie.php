<?php

namespace SloopMini\Util;

class Cookie {

    public static function get($key) {
        if (!isset($_COOKIE[$key])) {
            return false;
        }
        return str_replace('\\', '', $_COOKIE[$key]);
    }

    public static function set($key, $value, $expire = 86400, $domain = '') {
        $expire += time();
        setcookie($key, $value, $expire, $domain);
    }

    public static function delete($key) {
        setcookie($key, null, time() - 86400 * 100);
    }


}
