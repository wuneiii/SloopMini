<?php

namespace SloopMini\DataAccess;

abstract class  ModelBase {

    protected $tableName = null;

    protected $fields = array();

    protected $values = array();

    protected $primaryKey = 'id';

    protected $isEmpty = true;

    public function __get($key) {
        if (in_array($key, $this->fields) && isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    public function __set($key, $value) {
        if (in_array($key, $this->fields)) {
            $this->isEmpty = false;
            $this->values[$key] = $value;
            return true;
        }
        return false;
    }

    public function setPk($id) {
        $this->values[$this->primaryKey] = $id;
    }


    public function getPk() {
        if (!isset($this->values[$this->primaryKey])) {
            return false;
        }
        if (!$this->values[$this->primaryKey]) {
            return false;
        }
        return $this->values[$this->primaryKey];

    }


    public function setTable($tableName) {
        $this->tableName = $tableName;
    }


    public function setEmpty() {
        $this->values = array();
        $this->isEmpty = true;
    }


    public function isEmpty() {
        return $this->isEmpty;
    }


    public function getAllValues() {
        return $this->values;
    }


    public function getField($key) {
        if (!$key) {
            return null;
        }
        if ($this->keyExists($key) && isset($this->values[$key])) {
            return $this->values[$key];
        }
    }


    public function fillByArray($array) {
        if (!is_array($array) || !$array) return;
        foreach ($array as $k => $v) {
            $this->fillByKv($k, $v);
        }
    }

    public function fillByKv($key, $value) {
        if (in_array($key, $this->fields)) {
            $this->values[$key] = $value;
            $this->isEmpty = false;
            return true;
        }
        return false;
    }

    public function unset1($key) {
        if ($this->keyExists($key) && isset($this->values[$key])) {
            unset($this->values[$key]);
        }
        if (!$this->values) {
            $this->isEmpty = true;
        }
    }


    public function keyExists($key) {
        if (in_array($key, $this->fields)) {
            return true;
        } else {
            return false;
        }
    }


    public function dump() {
        var_dump($this->values);
    }
}
