<?php

namespace SloopMini\Util;


class Util {

    static public function fristStrToUpper($string) {
        if (strlen($string) <= 0) {
            return false;
        }
        return strtoupper(substr($string, 0, 1)) . substr($string, 1);
    }



    static function getRealSize($size) {

        $kb = 1024;         // Kilobyte
        $mb = 1024 * $kb;   // Megabyte
        $gb = 1024 * $mb;   // Gigabyte
        $tb = 1024 * $gb;   // Terabyte

        if ($size < $kb) {
            return sprintf('%.2fB', $size);
        } else if ($size < $mb) {
            return round($size / $kb, 2) . " KB";
        } else if ($size < $gb) {
            return round($size / $mb, 2) . " MB";
        } else if ($size < $tb) {
            return round($size / $gb, 2) . " GB";
        } else {
            return round($size / $tb, 2) . " TB";
        }

    }


    static function getFileExt($filename) {
        $fileParts = explode(".", $filename);
        return strtolower($fileParts[count($fileParts) - 1]);
    }


    function noRobot() {
        if (!defined('IS_ROBOT')) {
            $kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
            $kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
            if (preg_match("/($kw_browsers)/", $_SERVER['HTTP_USER_AGENT'])) {
                return;
            } elseif (preg_match("/($kw_spiders)/", $_SERVER['HTTP_USER_AGENT'])) {
                exit(header("HTTP/1.1 403 Forbidden"));
            } else {
                return;
            }
        }
    }

}