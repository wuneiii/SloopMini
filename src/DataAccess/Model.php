<?php

namespace SloopMini\DataAccess;


abstract class Model extends ModelObject {

    protected $where      = array();
    protected $limitStart = 0;
    protected $limitStep  = 0;
    protected $orderBy    = '';
    protected $orderSort  = ' DESC ';


    private $dbConnection;


    protected function init($table, $pk, $fieldList, $dataSource = 'default') {
        $this->tableName = $table;
        $this->primaryKey = $pk;
        $this->fields = $fieldList;
        $this->dataSourceName = $dataSource;

        $this->dbConnection = DbFactory::getInstance()->getConnection($this->dataSourceName);
        if (!$this->dbConnection) {
            ErrorCode::logError(ErrorCode::DB_CONN_FAIL);
            return false;
        }

    }

    public function isInit() {
        if (!$this->tableName || !$this->fields) {
            $this->logError(ErrorCode::MODEL_NOT_INIT);
            return false;
        }
        return true;
    }

    /**
     * 查询一个数据集
     * @return bool
     */
    public function selectAll() {
        if (!$this->isInit()) {
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName);
        $sqlFactory->where($this->getWhere());
        $sqlFactory->orderBy($this->orderBy, $this->orderSort);

        if ($this->limitStep) {
            $sqlFactory->limit($this->limitStep, $this->limitStart);
        }

        $res = $this->dbConnection->getManyRow($sqlFactory->getSql());

        return $res;
    }


    /**
     * 按照orderField 排序查询，最后一条加载入model。失败返回false
     * @param $orderField
     * @param $orderSort
     * @return bool
     */
    public function selectLastOne($orderField, $orderSort = 'desc') {
        if (!$this->isFieldExists($orderField)) {
            $orderField = $this->primaryKey;
        }

        // 不能清全表
        if (!$this->getWhere()) {
            $this->logError(ErrorCode::CANNOT_SCAN_ALL_TABLE);
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName);
        $sqlFactory->orderBy($orderField, $orderSort);
        $sqlFactory->where($this->where);

        $res = $this->dbConnection->getOneRow($sqlFactory->getSql());
        if ($res) {
            $this->setByArray($res);
            return $res;
        }
        return false;
    }


    /**
     * 根据主键加载一个对象，如果加载失败，返回false
     * @param $pk
     * @return bool|mixed
     */
    public function selectByPk($pk) {
        if (!$pk || !$this->primaryKey) {
            return false;
        }

        return $this->selectByUniqueField($this->primaryKey, $pk);
    }

    /**
     * 根据key value 对来加载数据
     * @param $key
     * @param $value
     * @return bool
     */
    public function selectByUniqueField($key, $value) {

        if (!$this->isFieldExists($key)) {
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName);
        $sqlFactory->where(sprintf("`%s`='%s'", $key, $value));

        $res = $this->dbConnection->getOneRow($sqlFactory->getSql());


        if (!$res) {
            return false;
        }
        $this->setByArray($res);
        return $res;
    }


    /**
     * 根据主键删除. 不接受model内部的参数，只接受函数明确的参数
     * @param string $pk
     * @return bool|resource
     */
    public function deleteByPk($pk) {
        return $this->deleteByUniqueField($this->primaryKey, $pk);
    }


    /**
     * 通过匹配条件删除
     * @return bool
     */
    public function deleteByMatch() {

        $w = $this->getWhere();
        // 不能清全表
        if (!$w) {
            $this->logError(ErrorCode::CANNOT_DELETE_ALL_TABLE);
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->delete($this->tableName);
        $sqlFactory->where($w);

        $res = $this->dbConnection->delete($sqlFactory->getSql());

        return $res;
    }


    /**
     * @param $field
     * @param $value
     * @return bool
     */
    public function deleteByUniqueField($field, $value) {
        if (!$this->isFieldExists($field)) {
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->delete($this->tableName);
        $sqlFactory->where(sprintf("%s='%s'", $field, $value));
        $res = $this->dbConnection->delete($sqlFactory->getSql());

        return $res;
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

        return $this->dbConnection->insert($sqlFactory->getSql());
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
        $insertId = false;
        if ($pkValue) {
            // 主键赋值了，先探测是否存在；如果不存在，insert主键，先锁定主键id；
            // update要保证主键已经存在
            // 如果不存在，尝试先插入主键
            if (!$this->isPkExists($pkValue)) {
                if (!$this->lockPk($pkValue)) {
                    return false;
                }
            }
            $sqlFactory->where($this->primaryKey . '= "' . $pkValue . '"');

            $updateData = $this->values;
            unset($updateData[$this->primaryKey]);
            $sqlFactory->updateSet($this->tableName, $updateData);
            $this->dbConnection->update($sqlFactory->getSql());
        } else {
            $sqlFactory->insert($this->tableName, $this->values);
            $insertId = $this->dbConnection->insert($sqlFactory->getSql());
        }


        if ($insertId) {
            if ($sqlFactory->getType() == SqlFactory::SQL_INSERT) {
                $this->setPk($insertId);
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
        $sqlFactory->where($this->getWhere());


        return $this->dbConnection->getInt($sqlFactory->getSql());

    }


    /**
     * @param $field
     * @return mixed
     */
    public function getSum($field) {
        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName, 'SUM(' . $field . ') as sum_value');
        $sqlFactory->where($this->getWhere());

        return $this->dbConnection->getInt($sqlFactory->getSql());
    }


    /**
     * @return array
     */
    private function getWhere() {
        $where = array();
        if ($this->values) {
            foreach ($this->values as $k => $v) {
                if ($v === null) {
                    continue;
                }
                $where[] = " `$k` ='$v' ";
            }
        }
        if ($this->where) {
            foreach ($this->where as $item) {
                $where[] = $item;
            }
        }
        return $where;
    }


    /**
     * @param $value
     * @return bool
     */
    public function isPkExists($value) {
        if (!$value || !$this->primaryKey) {
            return false;
        }
        $sqlFactory = new SqlFactory();
        $sqlFactory->selectFrom($this->tableName, $this->primaryKey);
        $sqlFactory->where(array($this->primaryKey . '=' . $value));

        $ret = $this->dbConnection->getInt($sqlFactory->getSql());

        if (!$ret) {
            return false;
        }
        return true;
    }


    /**
     * @param $field
     * @param int $num
     * @return bool
     */
    public function increase($field, $num = 1) {
        if (!$this->isFieldExists($field)) {
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->updateSet($this->tableName, array($field => "$field + $num "));
        $where = array(
            $this->primaryKey . " = " . $this->getPk()
        );
        $sqlFactory->where($where);

        return $this->dbConnection->update($sqlFactory->getSql());
    }

    /**
     * @param $field
     * @param int $num
     * @return bool
     *
     */
    public function decrease($field, $num = 1) {
        if (!$this->isFieldExists($field)) {
            return false;
        }

        $sqlFactory = new SqlFactory();
        $sqlFactory->updateSet($this->tableName, array($field => "$field - $num "));
        $where = array(
            $this->primaryKey . " = " . $this->getPk()
        );
        $sqlFactory->where($where);

        return $this->dbConnection->update($sqlFactory->getSql());
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


    public function orderBy($field, $sort = 'desc') {
        $this->orderBy = $field;
        $this->orderSort = $sort;
        return $this;
    }


    public function limit($step, $start = 0) {
        $this->limitStep = $step;
        $this->limitStart = $start;
        return $this;
    }

    public function where($where) {
        if (is_array($where)) {
            foreach ($where as $item) {
                $this->where[] = $item;
            }
        } else {
            $this->where[] = $where;
        }
        return $this;
    }

    protected function logError($errorCode) {
        $this->error[] = $errorCode;
    }

    public function getError() {
        return $this->error;
    }

}