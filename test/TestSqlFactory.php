<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/3
 * Time: 上午11:13
 */

namespace SloopMini\Test;

use SloopMini\DataAccess\SqlFactory;

require '../src/loader.php';

TestSqlFactory::main();

class TestSqlFactory {

    public static function main() {
        $test = new TestSqlFactory();
        $test->test_SqlFactory();
    }


    private function test_SqlFactory() {

        $table = 'table_biz1';
        $field = array(
            'name'       => 'jack',
            'age'        => 25,
            'score'      => 129.9294,
            'is_student' => true
        );

        $sqlFactory = new SqlFactory();
        $sqlFactory->insert($table, $field);
        $this->dumpResult($sqlFactory->getSql());


        $sqlFactory = new SqlFactory();
        $sqlFactory->updateSet($table, $field);
        $sqlFactory->where('id = 1');
        $sqlFactory->where(' age > 31');
        $this->dumpResult($sqlFactory->getSql());

        $sqlFactory = new SqlFactory();
        $sqlFactory->where(' id = 1');
        $sqlFactory->where(' age > 21');
        $sqlFactory->delete($table);
        $this->dumpResult($sqlFactory->getSql());

        $sqlFactory->selectFrom($table, 'name,age');
        $this->dumpResult($sqlFactory->getSql());

        $sqlFactory->orderBy('age', 'asc');
        $this->dumpResult($sqlFactory->getSql());

        $sqlFactory->limit(50, 12);
        $this->dumpResult($sqlFactory->getSql());

        $sqlFactory->selectFrom($table, ' sum(score) as sum1,count(id) as count1');
        $this->dumpResult($sqlFactory->getSql());
    }


    protected function dumpResult($msg) {

        print(str_repeat('-', 100) . "\n");
        printf("%s\n", $msg);
        print(str_repeat('-', 100) . "\n");

    }
}