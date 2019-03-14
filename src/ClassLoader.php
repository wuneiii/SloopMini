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


        foreach ($this->nsPrefixMap as $nsPrefix => $path) {
            $clsPrefix = substr($className, 0, strlen($nsPrefix));

            if ($clsPrefix == $nsPrefix) {

                if (substr($path, strlen($path) - 1) != '/') {
                    $path .= '/';
                }

                $realClassFile = $path . substr($className, strlen($nsPrefix)) . '.php';
                $realClassFile = str_replace('\\', '/', $realClassFile);

                include_once $realClassFile;


            }
        }
    }
}