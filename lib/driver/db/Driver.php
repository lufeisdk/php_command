<?php

namespace lib\driver\db;

abstract class Driver
{
    static public $_OPTIONS = [];

    private $retry = 0; # 重连次数

    public function __get($name)
    {
        return self::$_OPTIONS[$name];
    }

    public function __set($name, $value)
    {
        if (isset(self::$_OPTIONS[$name])) {
            self::$_OPTIONS[$name] = $value;
        }
    }

    /**
     * 检测数据是否存在
     * @param $sql
     * @param array $params
     * @return mixed
     */
    abstract public function check($sql, $params);

    /**
     * 返回查询结构的一条数据
     * @param $sql
     * @param array $params
     * @param int $num
     * @return mixed
     */
    abstract public function fetch_one($sql, $params);

    /**
     * 返回查询结果集中指定列的值，默认是第一列
     * @param $sql
     * @param array $params
     * @param string $field 返回指定列的键名
     * @return mixed
     */
    abstract public function fetch_column($sql, $params, $field);

    /**
     * 获得查询语句的第一条，第一列的数值
     * 返回查询字段的值
     * @param $sql
     * @param array $params
     * @return mixed
     */
    abstract public function fetch_one_cell($sql, $params);

    /**
     * 返回数据库查询结果，作为最大的数组返回
     * @param $sql
     * @param array $params
     * @return mixed
     */
    abstract public function fetch_rows($sql, $params);

    /**
     * 返回一个数组，第一列作为key，第二列作为value，其他列有或没有都没有影响
     * @param $sql
     * @param array $params
     * @return mixed
     */
    abstract public function fetch_list($sql, $params);

    /**
     * 执行SQL语句，返回执行记录数
     * @param $sql
     * @param array $params
     * @return Int
     */
    abstract public function query($sql, $params);

    /**
     * 更新数据，返回更新记录数
     * @param $sql
     * @param array $params
     * @return Int
     */
    abstract public function save($sql, $params);

    /**
     * 新增数据
     * @param $sql
     * @param array $params
     * @return Int或Bool
     */
    abstract public function add($sql, $params);

    /**
     * 打印SQL语句
     * @return mixed
     */
    abstract public function get_sql();
}