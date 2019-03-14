<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/2/26
 * Time: 下午10:45
 */

namespace SloopMini;


class App {

    private $config         = [];
    private $strUrlMap      = [];
    private $regUrlMap      = [];
    private $defaultHandler = false;

    public function __construct() {
        @date_default_timezone_set('PRC');

        if (file_exists('config.php')) {
            $this->config = require 'config.php';
        }
    }

    public function getConfig($key) {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return false;
    }

    public function regAppDir($dir) {
        $loader = ClassLoader::getInstance();
        $loader->registerNamespace('\App\\', $dir);
    }

    public function addDefault($callback) {
        $this->defaultHandler = $callback;
    }

    public function add($url, $callback) {
        if (!is_string($url)) {
            return false;
        }

        if (is_string($callback)) {
            if (!class_exists($callback)) {
                var_dump('class not exitst' . $callback);
                return false;
            }
            $callback = [
                new $callback,
                'run'
            ];
        }

        if (!is_callable($callback) && is_object($callback)) {

            if (!class_exists(get_class($callback))) {
                var_dump('class not exitst' . get_class($callback));
                return false;
            }
            $callback = [
                $callback,
                'run'
            ];
        }

        if (preg_match_all('#\{(word|id)\}#', $url, $match)) {
            $url = str_replace([
                '{id}',
                '{word}'
            ], [
                '(\d+)',
                '([a-zA-Z]+)'
            ], $url);

            $this->regUrlMap[$url] = [
                'body'  => $callback,
                'param' => $match[1],
            ];
        } else {
            $this->strUrlMap[$url] = $callback;
        }
    }

    public function run() {
        $path = $_SERVER['REQUEST_URI'];

        if (isset($this->strUrlMap[$path])) {
            call_user_func($this->strUrlMap[$path]);
            return;
        }

        if ($this->regUrlMap) {
            foreach ($this->regUrlMap as $reg => $callback) {
                if (preg_match("#$reg#", $path, $match)) {
                    array_shift($match);
                    $param = [];
                    foreach ($callback['param'] as $k => $p) {
                        $param[$p] = $match[$k];
                    }
                    call_user_func_array($callback['body'], $param);
                    return;
                }
            }
        }
        if ($this->defaultHandler) {
            call_user_func($this->defaultHandler);
            return;
        }
        echo 'no url handler';
    }
}