<?php
namespace SloopMini\Util;

class Probe {
    static private $_data;
    static private $_clock;

    static public function here($where) {
        self::$_data[$where] = microtime(true);
    }

    static public function report() {
        echo '<div><p>[Debug]Core-Framework Flow Track:</p><ol>';

        foreach (self::$_data as $where => $when) {
            if (!isset($_last)) {
                $_last = $when;

            }
            $_timeDetal = sprintf('%.5f', $when - $_last);

            echo '<li>[' . $_timeDetal . 's]' . $where . '</li>';
        }
        echo '</ol><div>';
    }


    static public function startTimer() {
        self::$_clock = microtime(true);
        self::here('app start');
    }

    static public function getNowTimer() {
        return microtime(true) - self::$_clock;
    }

    static public function getAndStopTimer() {
        return microtime(true) - self::$_clock;
    }
}