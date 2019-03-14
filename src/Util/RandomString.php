<?php

namespace SloopMini\Util;

class RandomString {

    static public function randString($length = 6) {
        $char = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghcklmnpqrstuvwxyz123456789';
        $len = strlen($char) - 1;
        $ret = '';
        if (intval($length) <= 0) $length = 6;
        for ($i = 1; $i <= $length; $i++) {
            $ret .= $char[mt_rand(0, $len)];
        }
        return $ret;

    }

    static public function randHexString($length = 6) {
        if (intval($length) <= 0) $length = 6;
        $ret = '';
        for ($i = 1; $i <= $length; $i++) {
            $ret .= dechex($i);
        }
        return $ret;
    }

    static public function randCode($length) {
        $str = "0123456789";
        $len = strlen($str);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $str[mt_rand() % $len];
        }
        return $code;
    }

    static public function getFileExt($file) {
        $fileParts = explode(".", $file);
        return strtolower($fileParts[count($fileParts) - 1]);
    }

    static public function getChineseNumber($num) {
        $map = array(
            '零',
            '一',
            '二',
            '三',
            '四',
            '五',
            '六',
            '七',
            '八',
            '九',
            '十'
        );
        return $map[$num];
    }
}
