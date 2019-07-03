<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/4
 * Time: 上午1:02
 */

namespace SloopMini\Test;


use SloopMini\ClassLoader;
use SloopMini\DataAccess\DbConfig;
use SloopMini\DataAccess\ErrorCode;

require '../src/loader.php';
ClassLoader::getInstance()->registerNamespace('\SloopMini\\Test\\', __DIR__);

TestModel::main();

class TestModel {


    public static function main() {
        $test = new TestModel();
        $test->test();
    }

    public function test() {
        $default = array(
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'username' => 'root',
            'password' => '123456',
            'dbname'   => 'huiyibao'
        );
        DbConfig::addConfig('ds_abc', $default);


        $modelHuiyi = new ModelHuiyi();

        $res = $modelHuiyi->selectByPk(21);

        var_dump($modelHuiyi->toArray());
        var_dump($res);


    }

}
