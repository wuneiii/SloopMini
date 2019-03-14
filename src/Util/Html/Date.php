<?php

namespace SloopMini\Util\Html;

class Date {

    /**
     * @desc 产生一个日的下拉菜
     *
     */
    static function return_select_30day($name = '', $selected = 1, $class = '') {
        $ret = '<select name="' . $name . '" class="' . $class . '">';
        for ($day = 1; $day <= 31; $day++) {
            $s = '';
            if ($day == $selected) {
                $s = 'selected="selected"';
            }
            $ret .= "<option " . $s . " value=" . $day . ">" . $day . "日</option>";
        }
        $ret .= '</select>';
        return $ret;
    }

    /**
     * @desc  产生一个选择月份的下拉菜单
     */

    static function return_select_12month($name = '', $selected = 1, $class = '') {
        $ret = '<select name="' . $name . '" class="' . $class . '">';
        for ($day = 1; $day <= 12; $day++) {
            $s = '';
            if ($day == $selected) {
                $s = 'selected="selected"';
            }
            $ret .= "<option " . $s . " value=" . $day . ">" . $day . "月</option>";
        }
        $ret .= '</select>';
        return $ret;
    }

    /**
     * 产生一个选择年份的下拉菜单
     *
     * @param mixed $name
     * @param mixed $selected
     */
    static function return_select_years($name = '', $selected = 1, $class = '') {
        $ret = '<select name="' . $name . '" class="' . $class . '">';
        for ($day = 1980; $day <= 2015; $day++) {
            $s = '';
            if ($day == $selected) {
                $s = 'selected="selected"';
            }
            $ret .= "<option " . $s . " value=" . $day . ">" . $day . "年</option>";
        }
        $ret .= '</select>';
        return $ret;
    }

    static function timestampToTag($timestamp) {
        $now = time();
        $detal = $now - $timestamp;
        switch (true) {
            case $detal < 300 :
                return '刚刚';
            case 300 <= $detal && $detal < 3600:
                return ceil($detal / 60) . '分钟前';
            case 3600 <= $detal && $detal < 86400:
                return ceil($detal / 3600) . '小时' . ceil(($detal % 3600) / 60) . '分钟前';
            default:/* 86400 <= $detal:*/
                return date('Y-m-d h:i', $timestamp);
        }
    }

    static function return_select_age($name = '', $selected = '', $class = "", $min = 1, $max = 100) {
        $ret = '<select name="' . $name . '" class="' . $class . '">';
        for ($day = $min; $day <= $max; $day++) {
            $s = '';
            if ($day == $selected) {
                $s = 'selected="selected"';
            }
            $ret .= "<option " . $s . " value=" . $day . ">" . $day . "岁</option>";
        }
        $ret .= '</select>';
        return $ret;
    }

    static function getIosCurrentTime() {
        return date('Y-m-d H:i:s', time());
    }

    static function getIosFromTimeStamp($st = '', $default = '') {

        if ($st == '') {
            return $default;
        }
        return date('Y-m-d H:i:s', $st);
    }

    static function getTimeStampFromIos($iosTime) {
        $iosTime = str_replace(array(
            ':',
            ' '
        ), '-', trim($iosTime));
        list($year, $month, $day, $hour, $min, $sec) = explode('-', $iosTime);
        return mktime($hour, $min, $sec, $month, $day, $year);
    }

    static function isToday($ts) {
        if (!$ts) return false;
        //天数相同
        return date('Ymd', time()) === date('Ymd', $ts);
    }

    static function isYesterday($ts) {
        if (!$ts) return false;
        //天数相同
        return date('Ymd', (time() - 86400)) === date('Ymd', $ts);
    }

    static function isDayBeforeYesterday($ts) {
        if (!$ts) return false;
        //天数相同
        return date('Ymd', (time() - 86400 * 2)) === date('Ymd', $ts);
    }

    static function getTodayStartTs() {
        $date = date('Y_m_d', time());
        list($year, $month, $day) = explode('_', $date);
        return mktime(0, 0, 0, $month, $day, $year);
    }

    static function getTomorrowStartTs() {
        $date = date('Y_m_d', (time() + 86400));
        list($year, $month, $day) = explode('_', $date);
        return mktime(0, 0, 0, $month, $day, $year);
    }

    static function formatTime($timeDetal) {
        $retStr = '';

        $year = $timeDetal / 86400 / 365;
        if ($year > 1) {
            $retStr .= floor($year) . '年';
            $timeDetal = $timeDetal % (86400 * 365);
        }

        $month = $timeDetal / 86400 / 30;
        if ($month > 1) {
            $retStr .= floor($month) . '月';
            $timeDetal = $timeDetal % (86400 * 30);
        }

        $day = $timeDetal / 86400;
        if ($day > 1) {
            $retStr .= floor($day) . '天';
            $timeDetal = $timeDetal % (86400);
        }

        $hour = $timeDetal / 3600;
        if ($hour > 1) {
            $retStr .= floor($hour) . '时';
            $timeDetal = $timeDetal % (3600);
        }

        $minute = $timeDetal / 60;
        if ($minute > 1) {
            $retStr .= floor($minute) . '分';
            $timeDetal = $timeDetal % (60);
        }

        $second = $timeDetal;
        if ($second != 0) {
            $retStr .= $second . '秒';
        }
        return $retStr;
    }
}
