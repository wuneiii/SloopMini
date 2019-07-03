<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/3
 * Time: 下午8:18
 */

namespace SloopMini\Test;


use SloopMini\DataAccess\DbConfig;
use SloopMini\DataAccess\DbFactory;
use SloopMini\DataAccess\SqlFactory;

require '../src/loader.php';

TestDbFactory::main();

class TestDbFactory {

    public static function main() {
        $test = new TestDbFactory();
        $test->test();
    }

    private function test() {
        $default = array(
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'username' => 'root',
            'password' => '123456',
            'dbname'   => 'huiyibao'
        );
        DbConfig::addConfig('default', $default);

        $conn = DbFactory::getInstance()->getConnection('default');

        if (!$conn) {
            die('connection fail');
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->insert('huiyi', array('title' => 'title' . time()));

        $id = $conn->insert($sqlFactory->getSql());

        var_dump('insert_id:' . $id);

        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom('huiyi', 'count(id) as count');
        $res = $conn->getInt($sqlFactory->getSql());
        var_dump($res);


        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom('huiyi');
        $res = $conn->getManyRow($sqlFactory->getSql());
        var_dump($res);


        $sqlFactory = new SqlFactory();
        $sqlFactory->updateSet('huiyi', array('strid' => time()));
        $sqlFactory->where('id = ' . $id);

        $res = $conn->update($sqlFactory->getSql());
        var_dump($res);

        $sqlFactory = new SqlFactory();
        $sqlFactory->delete('huiyi');
        $res = $conn->delete($sqlFactory->getSql());

        var_dump($conn->getSqlLog());
        var_dump($conn->getError());


    }

}
