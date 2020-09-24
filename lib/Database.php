<?php

namespace lib;

class Database
{
    private $handler;

    static private $instance;

    static public function getInstance($driver = 'mysql')
    {
        if (false == isset(static::$instance[$driver])) {
            $config = require_once ROOT_PATH . '/config/database.php';
            $options = $config[$driver];
            static::$instance[$driver] = new self($driver, $options);
        }
        return static::$instance[$driver];
    }

    private function __construct($driver, array $options = [])
    {
        try {
            $driver = ucfirst($driver);
            $class = 'lib\driver\db\\' . $driver;
            if (!class_exists($class)) {
                throw new Exception("找不到相应的数据库驱动类：" . $class);
            }
            $this->handler = new $class($options);
        } catch (Exception $e) {
            exit($e->errorMessage());
        }
    }

    /**
     * 检测数据是否存在
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function check($sql, $params = [])
    {
        return $this->handler->check($sql, $params);
    }

    /**
     * 返回查询结构的一条数据
     * @param $sql
     * @param array $params
     * @param int $num
     * @return mixed
     */
    public function fetch_one($sql, $params = [])
    {
        return $this->handler->fetch_one($sql, $params);
    }

    /**
     * 返回查询结果集中指定列的值，默认是第一列
     * @param $sql
     * @param array $params
     * @param string $field 返回指定列的键名
     * @return mixed
     */
    public function fetch_column($sql, $params = [], $field = '')
    {
        return $this->handler->fetch_column($sql, $params, $field);
    }

    /**
     * 获得查询语句的第一条，第一列的数值
     * 返回查询字段的值
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function fetch_one_cell($sql, $params = [])
    {
        return $this->handler->fetch_one_cell($sql, $params);
    }

    /**
     * 返回数据库查询结果，作为最大的数组返回
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function fetch_rows($sql, $params = [])
    {
        return $this->handler->fetch_rows($sql, $params);
    }

    /**
     * 返回一个数组，第一列作为key，第二列作为value，其他列有或没有都没有影响
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function fetch_list($sql, $params = [])
    {
        return $this->handler->fetch_list($sql, $params);
    }

    /**
     * 执行SQL语句，返回执行记录数
     * @param $sql
     * @param array $params
     * @return Int
     */
    public function query($sql, $params = [])
    {
        return $this->handler->query($sql, $params);
    }

    /**
     * 更新数据，返回更新记录数
     * @param $sql
     * @param array $params
     * @return Int
     */
    public function save($sql, $params = [])
    {
        return $this->handler->save($sql, $params);
    }

    /**
     * 新增数据
     * @param $sql
     * @param array $params
     * @return Int或Bool
     */
    public function add($sql, $params = [])
    {
        return $this->handler->add($sql, $params);
    }

    /**
     * 打印SQL语句
     * @return mixed
     */
    public function get_sql()
    {
        return $this->handler->get_sql();
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }
}