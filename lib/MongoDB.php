<?php

namespace lib;

use MongoConnectionException;
use MongoDB\Driver\Manager as MongoDBManger;
use MongoDB\Driver\Command as MongoDBCommand;
use MongoDB\Driver\Query as MongoDBQuery;
use MongoDB\Driver\BulkWrite as MongoDBBulkWrite;
use lib\exception\NotFoundException;

class MongoDB
{
    static public $_OPTIONS = [];
    protected $handler = null; # 驱动句柄
    private $_db = null;       # 当前选择的数据库
    private $_collection;      # 当前选择的集合
    static private $instance;  # 单例实例句柄

    // 默认配置
    static private $_CONFIG = [
        'host' => '127.0.0.1',
        'port' => 27017,
        'user' => '',
        'passwd' => '',
        'dbname' => '',         # 数据库名
        'collection' => '',     # 数据集合名称
    ];

    /**
     * 根据驱动获取数据库连接句柄
     * @param string $tagName
     * @return mixed
     */
    static public function getInstance($tagName = 'default')
    {
        if (empty(static::$instance[$tagName])) {
            $options = Config::all('mongodb');
            static::$instance[$tagName] = new self($options);
        }
        return static::$instance[$tagName];
    }

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

    private function __construct(Array $config = [])
    {
        self::$_OPTIONS = array_merge(self::$_CONFIG, $config);

        if (extension_loaded('mongodb')) {
            if ($this->user && $this->passwd) {
                $server = sprintf("mongodb://%s:%s@%s:%s", $this->user, $this->passwd, $this->host, $this->port);
            } else {
                $server = sprintf("mongodb://%s:%s", $this->host, $this->port);
            }

            try {
                $this->handler = new MongoDBManger($server);
                $this->setDB($this->dbname)->setCollection($this->collection);
            } catch (MongoConnectionException $e) {
                throw $e;
            }
        } else {
            throw new NotFoundException('not support: mongodb');
        }
    }

    /**
     * 设置数据库
     * @param $dbname
     * @return $this
     */
    public function setDB($dbname)
    {
        $this->_db = $dbname;
        return $this;
    }

    /**
     * 选择或创建数据库(注意：新创建的数据库如果在关闭连接前没有写入数据将会被自动删除)
     * @param string $dbname 数据库名
     */
    public function getDB()
    {
        return $this->_db;
    }

    /**
     * 设置集合
     * @param $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     * 得到当前集合对象
     * @param string $collection 集合名称
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * 执行MongoDB命令
     * @param $opts
     * @return array
     * # 查询
     * $cmd = [
     *     'find' => 'testdb', // collection表名
     *     'filter' => ['x' => ['$gte' => 1, '$lt' => 3]]
     *    ];
     *
     * # 新增
     * $cmd = [
     *      'insert' => 'testdb', // collection表名
     *      'documents' => [
     *          ['x' => 4, 'name' => 'baidu', 'url' => 'http://baidu.com', 'createat' => '2020-10-13 12:18:44'],
     *          ['x' => 5, 'name' => 'weibo', 'url' => 'http://weibo.com', 'createat' => '2020-10-13 12:18:44'],
     *       ],
     *      'ordered' => true,
     *   ];
     *
     *   // # 更新
     *   $cmd = [
     *      'update' => 'testdb', // collection表名
     *      'updates' => [
     *          ['q' => ['createat' => ['$eq' => "2020-10-14 15:32:46"]], 'u' => ['x' => 1, 'name' => '菜鸟教程', 'url' => 'http://www.runoob.com', 'createat' => '2020-10-13 12:18:44']]
     *      ],
     *   ];
     *
     *   # 删除
     *   $cmd = [
     *      'delete' => 'AuthCode', // collection表名
     *      'deletes' => [
     *          ['q' => ['id' => ['$gte' => 1]], 'limit' => 1] # 删除一条id大于1的记录
     *      ],
     *   ];
     *
     *   $cmd = [
     *      'delete' => 'AuthCode', // collection表名
     *      'deletes' => [
     *          ['q' => ['id' => ['$gte' => 2]], 'limit' => 0] # 删除id大于2的所有记录
     *      ],
     *   ];
     *
     *  $res = $cache->exec($cmd);
     */
    public function exec($opts)
    {
        $cmd = new MongoDBCommand($opts);
        $res = $this->handler->executeCommand($this->_db, $cmd);
        return $res->toArray();
    }

    /**
     * 使用Query方法执行查询
     * @param $filter
     * @param $options
     * # Query查询x大于1且根据x降序排列的数据
     * $filter = ['x' => ['$gt' => 1]];
     * $options = [
     *     'sort' =>  ['x' => -1],
     * ];
     * $ret = $cache->query($filter, $options);
     * @return array
     */
    public function query($filter, $options)
    {
        $query = new MongoDBQuery($filter, $options);
        $cursor = $this->handler->executeQuery($this->_db . '.' . $this->_collection, $query);
        return $cursor->toArray();
    }

    /**
     * 利用BulkWrite实现MongoDB的增删改
     * @param $options
     * $bulk->insert(['_id' => 1, 'x' => 1]);
     * $bulk->update(['x' => 2], ['$set' => ['x' => 1]]);
     * $bulk->delete(['x' => ['$gte' => 11]]);
     * $bulk->delete(['x' => 1], ['limit' => 1]);   // limit 为 1 时，删除第一条匹配数据
     * $bulk->delete(['x' => 2], ['limit' => 0]);   // limit 为 0 时，删除所有匹配数据
     *
     * $data = ['x' => 7, 'name' => 'Taobao', 'url' => 'http://taobao.com', 'createat' => '2020-10-13 12:18:44'];
     * $cache->operate('insert', $data);
     * $cache->operate('delete', ['x' => 9]);
     * $cache->operate('update', [['x' => 9], ['$set' => ['name' => '盒马生鲜1']]]);
     * @return \MongoDB\Driver\WriteResult
     */
    public function operate($do, $option)
    {
        $bulk = new MongoDBBulkWrite(['ordered' => true]);

        if ($do == 'update') {
            $bulk->$do($option[0], $option[1]);
        } else {
            $bulk->$do($option);
        }
        $ret = $this->handler->executeBulkWrite($this->_db . '.' . $this->_collection, $bulk);
        return $ret;
    }

    /**
     * 批量处理操作
     * $options = [
     *  ['type' => 'delete', 'options' => ['x' => ['$gte' => 10]]],
     *  ['type' => 'insert', 'options' => ['x' => 10, 'name' => '菜鸟教程', 'url' => 'http://www.runoob.com', 'createat' => '2020-10-13 12:18:44']],
     *  ['type' => 'insert', 'options' => ['x' => 11, 'name' => '盒马生鲜1', 'url' => 'http://www.hmsx.com', 'createat' => '2020-10-13 12:18:44']],
     *  ['type' => 'update', 'options' => [['x' => 11],['$set' => ['name' => '盒马生鲜']]]],
     * ];
     * @param $options
     * @return bool|\MongoDB\Driver\WriteResult
     */
    public function operates($options)
    {
        if (empty($options)) {
            return false;
        }

        $bulk = new MongoDBBulkWrite(['ordered' => true]);
        foreach ($options as $do => $item) {
            $do = $item['type'];
            $option = $item['options'];
            if ($do == 'update') {
                $bulk->$do($option[0], $option[1]);
            } else {
                $bulk->$do($option);
            }
        }
        $ret = $this->handler->executeBulkWrite($this->_db . '.' . $this->_collection, $bulk);
        return $ret;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }
}