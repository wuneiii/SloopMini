<?php

namespace SloopMini\DataAccess;


class SqlFactory {

    private $type;
    private $table;
    private $selectField;
    private $data;
    private $condition;
    private $orderField;
    private $orderSort;
    private $limitStart;
    private $limitStep;

    CONST SQL_INSERT = 1;
    CONST SQL_UPDATE = 2;
    CONST SQL_SELECT = 3;
    CONST SQL_DELETE = 4;


    public function selectFrom($table, $field = '*') {
        $this->type = self::SQL_SELECT;
        $this->table = $table;
        $this->selectField = $field;
    }

    public function updateSet($table, $data = array()) {
        $this->type = self::SQL_UPDATE;
        $this->table = $table;
        $this->data = $data;
    }

    public function insert($table, $data = array()) {
        $this->type = self::SQL_INSERT;
        $this->table = $table;
        $this->data = $data;
    }

    public function delete($table) {
        $this->type = self::SQL_DELETE;
        $this->table = $table;
    }

    public function where($where) {
        $this->condition = $where;
    }

    public function orderBy($fieldName, $sort = 'DESC') {
        $this->orderField = $fieldName;
        $this->orderSort = $sort;

    }

    public function limit($step, $start = 0) {
        $this->limitStart = $start;
        $this->limitStep = $step;
    }

    public function getSql() {
        switch ($this->type) {
            case self::SQL_INSERT:
                return self::getInsertSql($this->table, $this->data);

            case self::SQL_UPDATE:
                $p = array();
                foreach ($this->data as $k => $v) {
                    $p[] = "`$k` = '$v'";
                }
                $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $p);
                if ($this->condition) {
                    $sql .= ' WHERE ' . $this->getWhere();
                }
                return $sql;

            case self::SQL_DELETE:
                $sql = 'DELETE FROM ' . $this->table;
                if ($this->condition) {
                    $sql .= ' WHERE ' . $this->getWhere();
                }
                return $sql;
            case self::SQL_SELECT:
                $sql = 'SELECT ' . $this->selectField . ' FROM ' . $this->table;
                if ($this->condition) {
                    $sql .= ' WHERE ' . $this->getWhere();
                }
                if ($this->orderField) {
                    $sql .= ' ORDER BY ' . $this->orderField . ' ' . $this->orderSort;
                }
                if ($this->limitStep) {
                    $sql .= ' LIMIT ' . $this->limitStart . ',' . $this->limitStep;
                }
                return $sql;
        }


    }

    private function getWhere() {
        if (!$this->condition) {
            return '';
        }

        if (is_string($this->condition)) {
            return $this->condition;
        }
        if (is_array($this->condition)) {
            $arr = array();
            foreach ($this->condition as $key => $value) {
                if (is_scalar($value)) {

                    if (is_numeric($key)) {
                        /**
                         * array('v=k', 'v=k')
                         */
                        $arr[] = $value;

                    } else {
                        /**
                         * array(k => v, k=>)
                         */
                        $arr[] = sprintf(" %s = '%s' ", $key, $value);
                    }

                } else if (is_array($value) && (count($value) == 2)) {
                    /**
                     * array(
                     *      array(k,v)
                     *      array(k,v)
                     * )
                     */
                    $arr[] = sprintf(" %s = '%s' ", $value[0], $value[1]);
                } else if (is_array($value) && (count($value) == 3)) {
                    /**
                     * array(
                     *      array(k,x,v)
                     *      array(k,x,v)
                     * )
                     */
                    $arr[] = sprintf(" %s %s '%s' ", $value[0], $value[1], $value[2]);
                }
            }
            return implode(' AND ', $arr);
        }
        return '';
    }


    /**
     * 生成update语句
     *
     * @param mixed $table
     * @param mixed $data
     * @param mixed $id
     * @param mixed $pkName
     * @return
     */
    public static function getUpdateSql($table, $data, $id, $pkName = 'id') {
        $p = array();
        foreach ($data as $k => $v) {
            $p[] = "`$k` = '$v'";
        }
        return "UPDATE " . $table . " SET " . implode(', ', $p) . "WHERE " . $pkName . " = '" . $id . "'";
    }

    /**
     * 生成 insert 语句
     *
     * @param mixed $table
     * @param mixed $data
     * @return
     */
    public static function getInsertSql($table, $data) {

        $kp = $vp = array();
        foreach ($data as $k => $v) {
            $kp[] = '`' . $k . '`';
            $vp[] = "'" . $v . "'";
        }

        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(',', $kp), implode(',', $vp));

        return $sql;
    }


    public function getType() {
        return $this->type;
    }


}