<?php

use SloopMini\DataAccess\Db;

$host = '182.92.214.249';
$user = 'root';
$passwd = 'Tiri!@#123';
$port = 3306;
$dbname = 'xiaolong_minip_dujing';


Db::setConfig($host, $user, $passwd, $dbname, $port);


$config = [
    'property1' => 'value',
    'property2' => 'value',
    'property3' => 'value',
];
return $config;