<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/3
 * Time: 下午8:08
 */

namespace SloopMini\DataAccess\Adapter;


abstract class BaseDriver {

    private $sqlLog = array();
    private $error  = array();

    public final function logSql($sql, $ts = 0, $msg = '') {
        $this->sqlLog[] = array(
            'sql' => $sql,
            'ts'  => $ts,
            'msg' => $msg
        );
    }

    public final function logErr($msg) {
        $this->error[] = $msg;
    }

    public final function getSqlLog() {
        return $this->sqlLog;
    }

    public final function getError() {
        return $this->error;
    }


    public abstract function connect($config);

    public abstract function close();

    public abstract function insert($sql);

    public abstract function update($sql);

    public abstract function delete($sql);

    public abstract function getManyRow($sql);

    public abstract function getOneRow($sql);

    public abstract function getInt($sql);

    public abstract function getString($sql);

    public abstract function txStart();

    public abstract function txCommit();

    public abstract function txRollback();

}