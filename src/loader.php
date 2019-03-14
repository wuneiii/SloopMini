<?php

if (version_compare('5.4', phpversion(), '>')) {
    die("version must > 5.4");
}

define('SLOOP_ROOT', __DIR__);

require SLOOP_ROOT . '/ClassLoader.php';

use SloopMini\ClassLoader as Loader;

Loader::getInstance()
    ->registerNamespace(
        '\SloopMini\\',
        SLOOP_ROOT
    );

