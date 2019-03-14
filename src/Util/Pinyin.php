<?php

namespace SloopMini\Util;

class Pinyin {
    public static function getFristLetter($str) {
        $str = iconv('utf8', 'gb2312', $str);
        $asc = ord(substr($str, 0, 1));
        if ($asc < 160) //非中文
        {
            if ($asc >= 48 && $asc <= 57) {
                return '1';  //数字
            } elseif ($asc >= 65 && $asc <= 90) {
                return chr($asc);   // A--Z
            } elseif ($asc >= 97 && $asc <= 122) {
                return chr($asc - 32); // a--z
            } else {
                return '~'; //其他
            }
        } else   //中文
        {
            $asc = $asc * 1000 + ord(substr($str, 1, 1));
            //获取拼音首字母A--Z
            if ($asc >= 176161 && $asc < 176197) {
                return 'a';
            } elseif ($asc >= 176197 && $asc < 178193) {
                return 'b';
            } elseif ($asc >= 178193 && $asc < 180238) {
                return 'c';
            } elseif ($asc >= 180238 && $asc < 182234) {
                return 'd';
            } elseif ($asc >= 182234 && $asc < 183162) {
                return 'e';
            } elseif ($asc >= 183162 && $asc < 184193) {
                return 'f';
            } elseif ($asc >= 184193 && $asc < 185254) {
                return 'g';
            } elseif ($asc >= 185254 && $asc < 187247) {
                return 'h';
            } elseif ($asc >= 187247 && $asc < 191166) {
                return 'j';
            } elseif ($asc >= 191166 && $asc < 192172) {
                return 'k';
            } elseif ($asc >= 192172 && $asc < 194232) {
                return 'l';
            } elseif ($asc >= 194232 && $asc < 196195) {
                return 'm';
            } elseif ($asc >= 196195 && $asc < 197182) {
                return 'n';
            } elseif ($asc >= 197182 && $asc < 197190) {
                return 'o';
            } elseif ($asc >= 197190 && $asc < 198218) {
                return 'p';
            } elseif ($asc >= 198218 && $asc < 200187) {
                return 'q';
            } elseif ($asc >= 200187 && $asc < 200246) {
                return 'r';
            } elseif ($asc >= 200246 && $asc < 203250) {
                return 's';
            } elseif ($asc >= 203250 && $asc < 205218) {
                return 't';
            } elseif ($asc >= 205218 && $asc < 206244) {
                return 'w';
            } elseif ($asc >= 206244 && $asc < 209185) {
                return 'x';
            } elseif ($asc >= 209185 && $asc < 212209) {
                return 'y';
            } elseif ($asc >= 212209) {
                return 'z';
            } else {
                return '~';
            }
        }
    }

}
