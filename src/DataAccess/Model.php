<?php

namespace SloopMini\DataAccess;


abstract class Model extends ModelBase {

    protected $condition = array();


    protected function init($table, $pk, $field) {
        $this->tableName = $table;
        $this->primaryKey = $pk;
        $this->fields = $field;
    }

    public function isInit() {
        if (!$this->tableName || !$this->fields) {
            return false;
        }
        return true;
    }


    /**
     * 查找所有符合条件的.失败返回空model_set
     * 包含所有字段条件，setXX增加的order，where，limit函数都等
     * @param bool $retArray
     * @return array|ModelIterator
     */
    public function findAllMatch($retArray = false) {
        if (!$this->isInit()) {
            die('model not init');
        }
        $condition = $this->condition;
        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName);

        if ($this->values) {
            $where = array();
            foreach ($this->values as $k => $v) {
                if ($v === null) {
                    continue;
                }
                $where[] = array(
                    $k,
                    $v
                );
            }
            if (isset($condition['where'])) {
                $where[] = $condition['where'];
            }
            $sqlFactory->where($where);
        }
        if (isset($condition['orderField'])) {
            $sqlFactory->orderBy($condition['orderField'], $condition['orderSort']);
        }
        if (isset($condition['limitStep'])) {
            $sqlFactory->limit($condition['limitStep'], $condition['limitStart']);
        }

        if ($retArray) {
            return $this->getArrayBySql($sqlFactory->getSql());
        }

        return $this->getModelSetBySql($sqlFactory->getSql());
    }

    private function getArrayBySql($sql) {
        $retArray = array();
        $db = Db::getConnection();
        $qry = $db->query($sql);
        if ($qry) {
            while ($rs = $db->fetchArray($qry)) {
                $retArray[] = $rs;
            }
        }
        return $retArray;
    }

    /**
     *
     *  * 内部使用，给一个sql，返回一个model_set
     *
     * @note 最好在内部使用，因为内部生成的sql查到的字段，都能用本model来保存。\
     *       如果让外部自由传入sql，可能会查到不包含在本model中的字段，model会丢弃那些数据
     *
     * @param $sql
     * @return ModelIterator
     */
    private function getModelSetBySql($sql) {

        $modelSet = new ModelIterator();
        $db = Db::getConnection();
        $qry = $db->query($sql);
        if ($qry) {
            $modelName = get_class($this);
            while ($rs = $db->fetchArray($qry)) {
                $modelSet->addArray($rs, $modelName);
            }
        }
        return $modelSet;
    }


    /**
     * 按照orderField 排序查询，最后一条加载入model。失败返回false
     * @param $orderField
     * @param $orderSort
     * @return bool
     */
    public function loadLastMatch($orderField, $orderSort = 'desc') {
        if (!$this->keyExists($orderField)) {
            $orderField = $this->primaryKey;
        }


        $where = array();
        if ($this->values) {
            foreach ($this->values as $k => $v) {
                if ($v === null) continue;
                $where[] = " `$k` ='$v' ";
            }
            if (isset($this->condition['where'])) {
                $where[] = $this->condition['where'];
            }
        }
        // 不能清全表
        if (!$where) {
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName);
        $sqlFactory->orderBy($orderField, $orderSort);
        $sqlFactory->where($where);

        $db = Db::getConnection();
        $qry = $db->query($sqlFactory->getSql());
        if ($qry && $rs = $db->fetchArray($qry)) {
            $this->fillByArray($rs);
            return true;
        }
        return false;
    }


    /**
     * 根据主键加载一个对象，如果加载失败，返回false
     * @param $pk
     * @return bool|mixed
     */
    public function loadByPk($pk) {
        if (!$pk || !$this->primaryKey) {
            return false;
        }

        return $this->loadByUniqueKey($this->primaryKey, $pk);
    }

    /**
     * 根据key value 对来加载数据
     * @param $key
     * @param $value
     * @return bool
     */
    public function loadByUniqueKey($key, $value) {

        if (!$this->keyExists($key)) {
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName);
        $sqlFactory->where(sprintf("`%s`='%s'", $key, $value));

        $db = Db::getConnection();
        $qry = $db->query($sqlFactory->getSql());
        if (!$qry) {
            return false;
        }
        if (!$rs = $db->fetchArray($qry)) {
            return false;
        }
        $this->fillByArray($rs);;
        return true;
    }


    /**
     * 根据主键删除. 不接受model内部的参数，只接受函数明确的参数
     * @param string $pk
     * @return bool|resource
     */
    public function deleteByPk($pk) {
        return $this->deleteByUniqueKey($this->primaryKey, $pk);
    }


    /**
     * 通过匹配条件删除
     * @return bool
     */
    public function deleteByMatch() {

        $where = array();
        if ($this->values) {
            foreach ($this->values as $k => $v) {
                if ($v === null) continue;
                $where[] = " `$k` ='$v' ";
            }
            if ($this->condition['where']) {
                $where[] = $this->condition['where'];
            }
        }
        // 不能清全表
        if (!$where) {
            return false;
        }


        $sqlFactory = new SqlFactory();
        $sqlFactory->delete($this->tableName);
        $sqlFactory->where($where);

        $db = Db::getConnection();
        if ($db->query($sqlFactory->getSql())) {
            return true;
        }
        return false;

    }


    /**
     * 按唯一字段删除记录
     * @param $key
     * @param $value
     * @return bool
     */
    public function deleteByUniqueKey($key, $value) {
        if (!$this->keyExists($key)) {
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->delete($this->tableName);
        $sqlFactory->where(sprintf("%s='%s'", $key, $value));

        $db = Db::getConnection();
        if ($db->query($sqlFactory->getSql())) {
            return true;
        }
        return false;
    }

    /**
     * 尝试插入一条指定主键的记录。
     *
     * @param $pk
     * @return bool
     */
    public function lockPk($pk) {
        if (!$pk || !$this->primaryKey) {
            return false;
        }
        $data = array(
            $this->primaryKey => $pk
        );
        $sqlFactory = new SqlFactory();
        $sqlFactory->insert($this->tableName, $data);
        $db = Db::getConnection();

        if ($db->query($sqlFactory->getSql())) {
            return true;
        }
        return false;
    }

    /**
     * 保存一个model到数据库中。如果主键被赋值，则更新记录。如果主键未被赋值，则新建记录。
     * @return bool|resource
     */
    public function saveToDb() {

        // 没有值
        if (!$this->values) {
            return false;
        }

        $pkValue = $this->getPk();
        // 如果主键存在，使用update,不存在用insert


        $sqlFactory = new SqlFactory();
        if ($pkValue) {
            // update
            // 主键赋值了，先探测是否存在；如果不存在，insert主键，先锁定主键id；
            if (!$this->isPkExists($pkValue)) {
                if (!$this->lockPk($pkValue)) {
                    return false;
                }
            }
            $sqlFactory->where($this->primaryKey . '= "' . $pkValue . '"');

            $updateData = $this->values;
            unset($updateData[$this->primaryKey]);
            $sqlFactory->updateSet($this->tableName, $updateData);
            $sql = $sqlFactory->getSql();
        } else {

            $sqlFactory->insert($this->tableName, $this->values);
            $sql = $sqlFactory->getSql();
        }

        $db = Db::getConnection();
        if ($db->query($sql)) {
            if ($sqlFactory->getType() == SqlFactory::SQL_INSERT) {
                $this->setPk($db->insertId());
            }
            return true;
        }
        return false;
    }


    /**
     * 按条件统计总量
     * @return int
     */
    public function getTotalNum() {

        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName, 'COUNT(' . $this->primaryKey . ') as count');

        $where = array();
        if ($this->values) {
            foreach ($this->values as $k => $v) {
                if ($v === null) continue;
                $where[] = " `$k` ='$v' ";
            }
            if (isset($this->condition['where'])) {
                $where[] = $this->condition['where'];
            }
        }
        if ($where) {
            $sqlFactory->where($where);
        }

        $db = Db::getConnection();
        if ($rs = $db->fetchOne($sqlFactory->getSql())) {
            return intval($rs['count']);
        }
        return false;
    }

    /**
     * 判断model是否具有这个字段
     * @param $key
     * @return bool
     */
    public function keyExists($key) {
        if (in_array($key, $this->fields)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 探测某个主键是否已经占用
     * @param $pk
     * @return bool
     */
    public function isPkExists($pk) {
        if (!$pk || !$this->primaryKey) {
            return false;
        }
        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName, $this->primaryKey);
        $sqlFactory->where(array($this->primaryKey => $pk));

        $db = Db::getConnection();
        $qry = $db->query($sqlFactory->getSql());
        if (!$qry || !($rs = $db->fetchArray($qry))) {
            return false;
        }
        return true;
    }


    /**
     * 单字段做加法
     * @param $key
     * @param int $num
     * @return bool|resource
     */
    public function increase($key, $num = 1) {

        if (!$this->keyExists($key)) {
            return false;
        }

        $sql = "";
        $sql .= "UPDATE " . $this->tableName . " SET $key = $key + $num ";
        $sql .= "WHERE " . $this->primaryKey . " = " . $this->getPk();
        $db = Db::getConnection();
        return $db->query($sql);
    }

    /**
     * 单字段做减法
     * @param $key
     * @param $num
     * @return bool|resource
     */
    public function decrease($key, $num = 1) {
        if (!$this->keyExists($key)) {
            return false;
        }

        $sql = "";
        $sql .= "UPDATE " . $this->tableName . " SET $key = $key - $num ";
        $sql .= "WHERE " . $this->primaryKey . " = " . $this->getPk();
        $db = Db::getConnection();
        $db->query($sql);
    }

    /**
     * 把一个model对象变成数组结构
     * @return array
     */
    public function toArray() {
        $array = array();
        if (count($this->values) != 0) {
            foreach ($this->values as $key => $value) {
                $array[$key] = $value;
            }
        }
        return $array;
    }


    public function setOrder($field, $sort = 'desc') {
        $this->condition['orderField'] = $field;
        $this->condition['orderSort'] = $sort;
    }

    /**
     * @param $step
     * @param int $start
     */
    public function setLimit($step, $start = 0) {
        $this->condition['limitStep'] = $step;
        $this->condition['limitStart'] = $start;
    }

    public function setWhere($where) {
        $this->condition['where'] = $where;
    }

}