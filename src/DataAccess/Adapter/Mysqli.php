<?php

namespace SloopMini\DataAccess\Adapter;


class Mysqli {


    private $currentConn;

    private $lastErrMsg = '';
    private $logMsg     = array();

    public function getLastErrorMsg() {
        return $this->lastErrMsg;
    }

    private function addErr($msg, $csm = 0) {
        $this->lastErrMsg = $msg;
        $this->logMsg[] = array(
            'ts'  => microtime(true),
            'msg' => $msg,
            'csm' => $csm,
        );
    }


    public function connect($arrConfig) {
        $conn = mysqli_connect($arrConfig['host'], $arrConfig['username'], $arrConfig['password']);
        if (!$conn) {
            $this->addErr('connection fail');
            return false;
        }
        if (!mysqli_select_db($conn, $arrConfig['dbname'])) {
            $this->addErr('select db fail');
            return false;
        }
        if (!mysqli_query($conn, 'SET NAMES UTF8;')) {
            return false;
        }

        $this->currentConn = $conn;
        return true;

    }


    public function fetchArray($result, $result_type = MYSQLI_ASSOC) {
        return mysqli_fetch_array($result, $result_type);
    }

    public function query($sql) {
        $start = microtime(true);
        if (!($result = mysqli_query($this->currentConn, $sql))) {
            $this->addErr('mysql query error:' . $sql . '[' . mysqli_error($this->currentConn) . ']');
            var_dump($this->lastErrMsg);
            return false;
        }
        $end = microtime(true);
        $this->addErr($sql, $end - $start);
        return $result;
    }

    public function startTransaction() {
        $this->query('START TRANSACTION');
    }

    public function commitTransaction() {
        $this->query('COMMIT');
    }

    public function rollbackTransaction() {
        $this->query('ROLLBACK');
    }

    public function insertId() {
        return mysqli_insert_id($this->currentConn);
    }

    public function fetchOne($sql) {
        $result = $this->query($sql);
        $record = $this->fetchArray($result);
        return $record;
    }

    public function numRows($query) {
        $query = mysqli_num_rows($query);
        return $query;
    }

    public function numFields($query) {
        return mysqli_num_fields($query);
    }

    public function freeResult($query) {
        $query = mysqli_free_result($query);
        return $query;
    }

    public function version() {
        return mysqli_get_server_info($this->currentConn);
    }

    public function close() {
        return mysqli_close($this->currentConn);
    }

    public function runCountSql($sql) {
        $qry = $this->query($sql);
        $rs = $this->fetchArray($qry, MYSQLI_ASSOC);
        return $rs['count'];
    }
}
