<?php

namespace SloopMini;

class ClassLoader {

    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new ClassLoader();
        }
        return self::$instance;
    }

    private $nsPrefixMap = array();

    private function __construct() {

        spl_autoload_register(array(
            $this,
            'sloopAutoLoader'
        ));
    }

    public function registerNamespace($nsPrefix, $libPath) {
        $this->nsPrefixMap[$nsPrefix] = $libPath;
    }

    public function sloopAutoLoader($className) {


        if (substr($className, 0, 1) != '\\') {
            $className = '\\' . $className;
        }

        $testNs = $className;
        do {
            $pos = strrpos($testNs, '\\');
            $testNs = substr($testNs, 0, $pos);


            if (isset($this->nsPrefixMap[$testNs . '\\'])) {
                $fileName = substr($className, $pos);
                $realClassFile = $this->nsPrefixMap[$testNs . '\\'] . $fileName . '.php';
                $realClassFile = str_replace('\\', '/', $realClassFile);
                break;
            }

            if ($pos === false) {
                var_dump('Class Not Found:' . $className);
                exit;
            }
        } while (true);

        @include_once $realClassFile;
    }
}