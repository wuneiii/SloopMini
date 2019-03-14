<?php

namespace SloopMini\Util;


class Xml {

    static function xml2array($xml) {
        if (preg_match_all("#<([^>]*)>(.*)</\\1>#s", $xml, $match)) {
            $arr = array();
            $cnt = count($match[1]);
            for ($i = 0; $i < $cnt; $i++) {
                $key = $match[1][$i];
                $value = trim($match[2][$i]);
                $arr[$key] = self::xml2array($value);
            }
            return $arr;

        } else if (preg_match_all('#^<!\[CDATA\[(.*)\]\]>$#', $xml, $match)) {
            return $match[1][0];
        } else {
            return $xml;
        }
    }

    static function array2xml($array) {
        return "<xml>\n" . self::array2xml2($array) . "</xml>";
    }

    static function array2xml2($array) {
        $xml = "";
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                // news类型的，articles 字段，内包含多个同级的无key的数组
                if (is_numeric($k)) {
                    $xml .= self::array2xml2($v);
                } else {
                    $xml .= '<' . $k . '>' . "\n" . self::array2xml2($v) . "" . '</' . $k . '>' . "\n";
                }
            } else {
                if (is_string($v)) {
                    $v = '<![CDATA[' . $v . ']]>';
                }
                $xml .= '<' . $k . '>' . $v . '</' . $k . '>' . "\n";
            }
        }
        return $xml;

    }
}