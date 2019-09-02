<?php

use SloopMini\DataAccess\Db;

$host = '';
$user = '';
$passwd = '';
$port = 3306;
$dbname = '';


Db::setConfig($host, $user, $passwd, $dbname, $port);


$config = [
    'property1' => 'value',
    'property2' => 'value',
    'property3' => 'value',
];
return $config;
