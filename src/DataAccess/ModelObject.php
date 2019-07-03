<?php
/**
 * Created by PhpStorm.
 * User: xiaolong
 * Date: 2019/7/2
 * Time: 下午5:03
 */

namespace SloopMini\DataAccess;

abstract class  ModelObject {

    protected $dataSourceName = 'default';

    protected $tableName = null;

    protected $fields = array();

    protected $values = array();

    protected $primaryKey = 'id';

    protected $isEmpty = true;


    public function __get($field) {
        return $this->getField($field);
    }

    public function __set($field, $value) {
        return $this->setField($field, $value);
    }

    public function setPk($value) {
        return $this->setField($this->primaryKey, $value);
    }

    public function getPk() {
        return $this->getField($this->primaryKey);
    }


    public function getField($field) {
        if (!$field || !$this->isFieldExists($field)) {
            return null;
        }
        if (!isset($this->values[$field])) {
            return null;
        }
        return $this->values[$field];
    }

    public function setField($field, $value) {
        if (!$value || !$field) {
            return false;
        }
        if (!isset($this->fields[$field])) {
            return false;
        }
        $this->values[$field] = $value;
        return true;
    }


    /**
     * 修改表名
     * @param $tableName
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * 清空
     */
    public function emptyModel() {
        $this->values = array();
        $this->isEmpty = true;
    }

    /**
     * debug 用
     * @return array
     */
    public function getAllValues() {
        return $this->values;
    }


    /**
     * 赋值
     * @param $array
     */
    public function setByArray($array) {
        if ($array || is_array($array)) {
            foreach ($array as $k => $v) {
                $this->setByKv($k, $v);
            }
        }
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function setByKv($key, $value) {
        if (in_array($key, $this->fields)) {
            $this->values[$key] = $value;
            $this->isEmpty = false;
            return true;
        }
        return false;
    }

    /**
     * 清除一个字段的值
     * @param $field
     * @return bool
     */
    public function eraserField($field) {
        if (!$this->isFieldExists($field)) {
            return false;
        }
        if (isset($this->values[$field])) {
            unset($this->values[$field]);
        }
        if (!$this->values) {
            $this->isEmpty = true;
        }
        return true;
    }


    /**
     * 判断model是否为空
     * @return bool
     */
    public function isEmpty() {
        return $this->isEmpty;
    }

    /**
     * 判断 field 是否存在
     * @param $field
     * @return bool
     */
    public function isFieldExists($field) {
        if (in_array($field, $this->fields)) {
            return true;
        } else {
            return false;
        }
    }

}
