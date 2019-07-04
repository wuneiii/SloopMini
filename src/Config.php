<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/4
 * Time: 下午8:27
 */

namespace SloopMini;


class Config {

    private static $instance;

    private function __construct() {
    }


    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    private $config = [];

    public function loadConfig($arrConf = array()) {
        if (is_array($arrConf)) {
            foreach ($arrConf as $k => $v) {
                $this->config[$k] = $v;
            }
        }
    }


    public function get($key) {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return null;
    }


}