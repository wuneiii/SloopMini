<?php

namespace SloopMini\DataAccess\Adapter;


class Mysqli extends BaseDriver {


    private $link;


    public function connect($conf) {
        if (!isset($conf['port'])) {
            $conf['port'] = 3306;
        }
        $conn = mysqli_connect(
            $conf['host'],
            $conf['username'],
            $conf['password'],
            $conf['dbname'],
            $conf['port']
        );
        if (!$conn) {
            $this->logErr('connection fail.' . mysqli_connect_error());
            return false;
        }
        if (!mysqli_select_db($conn, $conf['dbname'])) {
            $this->logErr('select db fail');
            return false;
        }
        if (!mysqli_query($conn, 'SET NAMES UTF8;')) {
            return false;
        }

        $this->link = $conn;
        return true;

    }

    public function close() {
        return mysqli_close($this->link);
    }


    private function execSqlReturnQuery($sql) {
        $start = microtime(true);
        $qry = mysqli_query($this->link, $sql);
        $end = microtime(true);
        $this->logSql($sql, $end - $start, $this->link->info);
        if (!$qry) {
            $this->logErr('mysql query error:' . $sql . '[' . mysqli_error($this->link) . ']');
            return false;
        }

        return $qry;
    }


    private function insertId() {
        return mysqli_insert_id($this->link);
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
        return mysqli_get_server_info($this->link);
    }


    public function insert($sql) {

        $qry = $this->execSqlReturnQuery($sql);

        if ($qry) {
            return $this->insertId();
        }
        return false;

    }

    public function update($sql) {

        if ($this->execSqlReturnQuery($sql)) {
            return mysqli_affected_rows($this->link);
        }
        return false;

    }

    public function delete($sql) {
        if ($this->execSqlReturnQuery($sql)) {
            return mysqli_affected_rows($this->link);
        }
        return false;
    }

    public function getManyRow($sql) {
        $ret = array();
        if (!$qry = $this->execSqlReturnQuery($sql)) {
            return false;
        }
        while ($rs = mysqli_fetch_assoc($qry)) {
            $ret[] = $rs;
        }
        return $ret;
    }

    public function getOneRow($sql) {
        if (!$qry = $this->execSqlReturnQuery($sql)) {
            return false;
        }
        return mysqli_fetch_assoc($qry);
    }

    public function getInt($sql) {
        if (!$qry = $this->execSqlReturnQuery($sql)) {
            return false;
        }
        $res = mysqli_fetch_assoc($qry);
        if (!$res) {
            return false;
        }
        $res = array_values($res);
        return intval($res[0]);
    }

    public function getString($sql) {
        if (!$qry = $this->execSqlReturnQuery($sql)) {
            return false;
        }
        $res = mysqli_fetch_assoc($qry);
        if (!$res) {
            return false;
        }
        $res = array_values($res);
        return strval($res[0]);
    }


    public function txStart() {
        $this->query('START TRANSACTION');
    }

    public function txCommit() {
        $this->query('COMMIT');
    }

    public function txRollback() {
        $this->query('ROLLBACK');
    }
}
