<?php

namespace lib\driver\db;

use lib\Exception;
use lib\Log;

class Sqlsrv extends Driver
{
    protected $handler = null; # 驱动句柄

    protected $_sql = '';    # SQL语句

    protected $_param = [];  # SQL语句预编译参数

    private static $_CONFIG = [
        'host' => '127.0.0.1',
        'user' => 'sa',
        'passwd' => '',
        'dbname' => '',
        'charset' => 'utf8'
    ];

    public function __construct(Array $config = [])
    {
        self::$_OPTIONS = array_merge(self::$_CONFIG, $config);

        $pdostr = 'sqlsrv:Server=' . $this->host . ';Database=' . $this->dbname;
        if (isset($this->port)) {
            $pdostr .= ';port=' . $this->port;
        }

        try {
            $this->handler = new \PDO($pdostr, $this->user, $this->passwd);
            # 设置编码
            //$this->handler->exec("SET names " . $this->charset);
            # 设置默认的提取模式
            $this->handler->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('MySQL PDO数据库连接失败~');
        }
    }

    /**
     * 检查记录是否存在
     * @param $sql  sql语句
     * @param array $params 预处理参数
     * @return bool
     */
    public function check($sql, $params = [])
    {
        $rows = $this->getPreResult('fetch', $sql, $params);
        return $rows ? true : false;
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
        $row = $this->getPreResult('fetch', $sql, $params);
        return $row ?: [];
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
        $rows = $this->getPreResult('fetchAll', $sql, $params);
        $column = [];
        if ($rows) {
            if (!$field) {
                $keys = array_keys($rows[0]);
                $field = $keys[0];
            }

            $column = array_column($rows, $field);
        }

        return $column;
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
        $row = $this->getPreResult('fetchColumn', $sql, $params);
        return $row ?: '';
    }

    /**
     * 返回数据库查询结果，作为最大的数组返回
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function fetch_rows($sql, $params = [])
    {
        return $this->getPreResult('fetchAll', $sql, $params);
    }

    /**
     * 返回一个数组，第一列作为key，第二列作为value，其他列有或没有都没有影响
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function fetch_list($sql, $params = [])
    {
        $rows = $this->fetch_rows($sql, $params);
        $list = [];
        if ($rows) {
            $keys = array_keys($rows[0]);
            $list = array_column($rows, $keys[1], $keys[0]);
        }

        return $list;
    }

    /**
     * 执行SQL语句，返回执行记录数
     * @param $sql
     * @param array $params
     * @return Int
     */
    public function query($sql, $params = [])
    {
        return $this->getPreResult('fetch', $sql, $params, true);
    }

    /**
     * 更新数据，返回更新记录数
     * @param $sql
     * @param array $params
     * @return Int
     */
    public function save($sql, $params = [])
    {
        return $this->query($sql, $params);
    }

    /**
     * 新增数据
     * @param $sql
     * @param array $params
     * @return Int或Bool
     */
    public function add($sql, $params = [])
    {
        $this->getPreResult('fetch', $sql, $params);
        return $this->handler->lastInsertId() ?? false;
    }

    /**
     * 打印SQL语句
     * @return mixed
     */
    public function get_sql()
    {
        if ($this->_param) {
            $param = $this->_param;
            $indexed = ($param == array_values($param));
            foreach ($param as $k => $v) {
                if (is_string($v) && false === strpos($v, "'")) { # 若是字符串，且没有单引号则添加单引号
                    $v = "'$v'";
                }
                if ($indexed) {
                    $this->_sql = preg_replace('/\?/', $v, $this->_sql, 1);
                } else {
                    $this->_sql = str_replace(":$k", $v, $this->_sql);
                }
            }
        }
        return $this->_sql;
    }

    /**
     * 获取预处理结果集
     * @param string $func 调用方法，默认fetch，fetchAll，fetchColumn
     * @param $sql      SQL语句
     * @param $params   预处理参数
     * @param bool $rowCount 返回影响的记录数
     * @return int 返回记录数或者结果集
     * @throws Exception
     */
    private function getPreResult($func = 'fetch', $sql, $params, $rowCount = false)
    {
        try {

            $this->_sql = $sql;

            $this->_param = $params;

            $stmt = $this->handler->prepare($sql);

            if ($params) {
                foreach ($params as $k => $param) {
                    $stmt->bindValue($k + 1, $param);
                }
            }

            $stmt->execute();

            return $rowCount ? $stmt->rowCount() : $stmt->$func();
        } catch (Exception $e) {
            $log = Log::getInstance();
            $content = $e->errorMessage();
            $content .= $sql . ' | ' . json_encode($params);
            $log->setLogFile('sql.log')->write($content);
            exit();
        }
    }
}