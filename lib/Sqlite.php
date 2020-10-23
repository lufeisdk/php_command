<?php

namespace lib;

use SQLite3;
use PDO;
use lib\exception\NotFoundException;

class Sqlite
{
    protected $handler = null;  # 驱动句柄

    static $_isPDO = 0;         # 是否是PDO扩展

    protected $_sql = '';       # SQL语句

    protected $_param = [];     # SQL语句预编译参数

    static private $_CONFIG = [ # 默认配置
        'data_dir' => '/data/sqlite.db',     # sqlite数据库存放地址
    ];

    static public function getInstance()
    {
        static $instance; # 驱动句柄

        if (!$instance) {
            $file = Config::all('sqlite.data_dir');
            if (empty($file)) {
                $file = self::$_CONFIG['data_dir'];
            }
            $instance = new self($file);
        }
        return $instance;
    }

    private function __construct($filename, $flags = null, $encryption_key = null)
    {
        if (extension_loaded('sqlite3')) {

            $this->createPath(dirname($filename));

            $this->createDBFile($filename);

            $this->handler = new SQLite3($filename);


        } elseif (extension_loaded('pdo_sqlite')) {

            $this->createPath(dirname($filename));

            $this->createDBFile($filename);

            $this->handler = new PDO('sqlite:' . $filename, '', '', array(
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));

            self::$_isPDO = 1;
        } else {
            throw new NotFoundException('not support: sqlite3 or pdo_sqlite');
        }
    }

    /**
     * 创建表
     * @param $sql
     * $sql =<<<EOF
     *    CREATE TABLE COMPANY
     *    (ID INT PRIMARY KEY     NOT NULL,
     *    NAME           TEXT    NOT NULL,
     *    AGE            INT     NOT NULL,
     *    ADDRESS        CHAR(50),
     *    SALARY         REAL);
     * EOF;
     * $sql = "create table student(id integer primary key autoincrement, name varchar(50) not null, age int not null, createAt datetime default current_timestamp)";
     * @return bool
     */
    public function create_table($sql)
    {
        return $this->handle($sql);
    }

    /**
     * 检查记录是否存在
     * @param $sql
     * @param array $params
     * $sql = "select * from student where id < 3";
     * @return bool
     */
    public function check($sql)
    {
        if (self::$_isPDO) {
            $result = $this->handler->query($sql);
        } else {
            $result = $this->handler->querySingle($sql);
        }

        return $result ? true : false;
    }

    /**
     * 查询单条数据
     * @param $sql
     * $sql =<<<EOF
     *   SELECT * from COMPANY;
     * EOF;
     * $sql = "select * from student where id < 3";
     * @return bool
     */
    public function fetch_one($sql, $params = [])
    {
        if (self::$_isPDO) {
            $result = $this->getPreResult('fetch', $sql, $params);
        } else {
            $result = $this->handler->querySingle($sql, true);
        }

        return $result ?: '';
    }

    /**
     * 返回查询结果集中指定列的值，默认是第一列
     * @param $sql
     * @param string $field 返回指定列的键名
     * @return array
     */
    public function fetch_column($sql, $params = [], $field = '')
    {
        $column = [];
        if (self::$_isPDO) {
            $rows = $this->getPreResult('fetchAll', $sql, $params);
            if ($rows) {
                $field = $field ?: current(array_keys($rows));
                $column = array_column($rows, $field);
            }
        } else {
            $ret = $this->handler->query($sql);
            if ($ret) {
                while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
                    $field = $field ?: current(array_keys($row));
                    array_push($column, $row[$field]);
                }
            }
        }

        return $column;
    }

    /**
     * 获得查询语句的第一条，第一列的数值
     * 返回查询字段的值
     * @param $sql
     * @return string
     */
    public function fetch_one_cell($sql, $params = [])
    {
        if (self::$_isPDO) {
            $result = $this->getPreResult('fetchColumn', $sql, $params);
        } else {
            $result = $this->handler->querySingle($sql);
        }

        return $result ?: '';
    }

    /**
     * 查询多条记录
     * @param $sql
     * @return array
     */
    public function fetch_rows($sql, $params = [])
    {
        $result = [];
        if (self::$_isPDO) {
            $result = $this->getPreResult('fetchAll', $sql, $params);
        } else {
            $ret = $this->handler->query($sql);
            if ($ret) {
                while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
                    array_push($result, $row);
                }
            }
        }

        return $result;
    }

    /**
     * 新增数据
     * @param $sql
     * $sql =<<<EOF
     *   INSERT INTO COMPANY (ID,NAME,AGE,ADDRESS,SALARY)
     *   VALUES (1, 'Paul', 32, 'California', 20000.00 );
     *
     *   INSERT INTO COMPANY (ID,NAME,AGE,ADDRESS,SALARY)
     *   VALUES (2, 'Allen', 25, 'Texas', 15000.00 );
     * EOF;
     * 单条插入
     * $sql = "insert into student(name,age) values('Jack', 18)";
     * 批量插入
     * $sql = "insert into student(name,age)select 'lily',18 union select 'lucy', 18 union select 'harry', 20";
     * @return bool
     */
    public function add($sql)
    {
        return $this->handle($sql);
    }

    /**
     * 更新数据
     * @param $sql
     * $sql =<<<EOF
     *  UPDATE COMPANY set SALARY = 25000.00 where ID=1;
     * EOF;
     * $sql = "update student set name='Tom', age=19 where id=2";
     * @return bool
     */
    public function save($sql)
    {
        return $this->handle($sql);
    }

    /**
     * 删除数据
     * @param $sql
     * $sql =<<<EOF
     *   DELETE from COMPANY where ID=2;
     * EOF;
     *  $sql = "delete from student where id=3";
     * @return bool
     */
    public function rm($sql)
    {
        return $this->handle($sql);
    }

    /**
     * 执行操作
     * @param $sql
     * @return bool
     */
    protected function handle($sql)
    {
        $ret = $this->handler->exec($sql);
        if (!$ret) {
            return $this->handler->lastErrorMsg();
        }
        return true;
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
        } catch (\PDOException $e) {
            throw new PDOException($e, self::$_CONFIG, $this->_sql());
        } catch (\Throwable $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 创建数据库文件
     * @param $data_file 数据库文件
     * @return bool
     */
    private function createDBFile($data_file)
    {
        if (!is_file($data_file)) {
            return touch($data_file) && chmod($data_file, 0777);
        }
        return true;
    }

    /**
     * 创建SQLite数据库目录
     * @param  String $data_path 数据库目录
     * @return Boolean
     */
    private function createPath($data_path)
    {
        if (!is_dir($data_path)) {
            return mkdir($data_path, 0777, true);
        }
        return true;
    }
}