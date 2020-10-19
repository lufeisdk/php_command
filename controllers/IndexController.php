<?php

namespace app;

use lib\Cache;
use lib\Config;
use lib\Database;
use lib\Log;
use lib\Controller;
use lib\MongoDB;

class IndexController extends Controller
{
    /**
     * @param array $param
     * 命令行执行：php index.php index index name=Lufei age:20 city.Hangzhou
     */
    public function index($param = [])
    {
        $name = $param['name'] ?? 'Lufei';
        $age = $param['age'] ?? 18;
        $city = $param['city'] ?? 'Hangzhou';
        echo "Hello World!" . PHP_EOL;
        echo "I am $name!" . PHP_EOL;
        echo "I am $age and is from $city!" . PHP_EOL;
    }

    public function db($param = [])
    {
        //        $model = Database::getInstance('sqlsrv');
        //        $sql = "select FLD_USERID,FLD_ACCOUNT,FLD_CHARACTER,FLD_MAKEDATE,FLD_SDKID,FLD_SERVERID from TBL_CHARACTER WHERE FLD_ID < ?";
        //        //$bool = $model->check($sql, [10]);
        //        //$bool = $model->fetch_one($sql, [10]);
        //        //$bool = $model->fetch_column($sql, [10]);
        //        //$bool = $model->fetch_column($sql, [10], 'FLD_CHARACTER');
        //        //$bool = $model->fetch_one_cell($sql, [10]);
        //        //$bool = $model->fetch_rows($sql, [10]);
        //        $bool = $model->fetch_list($sql, [10]);
        //        var_dump($bool);
        //        echo $model->get_sql();

        $config = [
            'dbname' => 'fastadmin',
        ];
        $model = Database::getInstance()->init($config, 'default');
        $sql = "show tables like 'fa_admin%'";
        $bool = $model->fetch_rows($sql);
        var_dump($bool);
        $model = Database::getInstance();
        //        $sql = "select * from ios_mafa_order WHERE id < 100 limit 10";
        //        $bool = $model->fetch_rows($sql);
        //        var_dump($bool);
        $sql = "show tables like 'phalapi%'";
        $bool = $model->fetch_rows($sql);
        var_dump($bool);
        //echo $model->get_sql();
        die;

        //        $model = Database::getInstance();
        ////        $sql = "select * from `user` where name=? limit 10";
        //        $sql = "select * from `user` where id > 3 limit 10";
        //        $bool = $model->check($sql);
        //        var_dump($bool);
        //        $sql = $model->get_sql();
        //        echo $sql;
        //        $sql = "select * from `user` where id > ? limit 10";
        //        $bool = $model->check($sql, [3]);
        //        var_dump($bool);
        //        echo '<br>----<br>';
        //        $sql = $model->get_sql();
        //        echo $sql;
        //        $bool = $model->fetch_one($sql, ["世界真'奇妙"]);
        //        var_dump($bool);
        //        echo '<br>----<br>';
        //        $bool = $model->fetch_column($sql, [3]);
        //        var_dump($bool);
        //        echo '<br>----<br>';
        //        $bool = $model->fetch_one_cell($sql, [8]);
        //        pp($bool);
        //        var_dump($bool);
        //        echo '<br>----<br>';
        //        $bool = $model->fetch_rows($sql, [3]);
        //        pp($bool);
        //        echo '<br>----<br>';
        //
        //        $bool = $model->fetch_list($sql, [3]);
        //        pp($bool);
        //        echo '<br>----<br>';
        //
        //        $sql = "insert into `user`(`name`, pwd, gender)values(?,?,?)";
        //        $bool = $model->add($sql, ['aaa924', '123222', 1]);
        //        pp($bool);
        //        echo '<br>----<br>';
        //
        //        $sql = "update `user` set pwd=md5('123456') WHERE id=?";
        //        $bool = $model->save($sql, [9]);
        //        var_dump($bool);
        //        echo '<br>----<br>';
    }

    public function log($param = [])
    {
        $log = Log::getInstance();
        $content = "这是一条日志信息";
        $ret = $log->write($content);
        var_dump($ret);

        $content = "这是一条订单日志信息";
        $ret = $log->setLogFile('order.log')
            ->setFormat("Ymd")
            ->setTag('Error')
            ->write($content);
        var_dump($ret);
    }

    public function cache()
    {
        # MongoDB
        //$cache = Cache::getInstance('mongo');
        $cache = MongoDB::getInstance();
        //var_dump($cache->getDB());
        //var_dump($cache->operate2());die;

        // $options = [
        //     'delete' => ['x' => ['$gte' => 10]],
        //     'insert' => [
        //         ['x' => 10, 'name' => '菜鸟教程', 'url' => 'http://www.runoob.com', 'createat' => '2020-10-13 12:18:44'],
        //         ['x' => 11, 'name' => '盒马生鲜1', 'url' => 'http://www.hmsx.com', 'createat' => '2020-10-13 12:18:44'],
        //     ],
        //     'update' => [['x' => 10],['$set' => ['name' => '盒马生鲜']]],
        // ];

        // $options = [
        //     ['type' => 'delete', 'options' => ['x' => ['$gte' => 10]]],
        //     ['type' => 'insert', 'options' => ['x' => 10, 'name' => '菜鸟教程', 'url' => 'http://www.runoob.com', 'createat' => date("Y-m-d H:i:s")]],
        //     ['type' => 'insert', 'options' => ['x' => 11, 'name' => '盒马生鲜1', 'url' => 'http://www.hmsx.com', 'createat' => date("Y-m-d H:i:s")]],
        //     ['type' => 'update', 'options' => [['x' => 11],['$set' => ['name' => '盒马生鲜']]]],
        // ];
        // $ret = $cache->operates($options);
        // var_dump($ret);
        // die;

        $data = ['x' => 12, 'name' => '盒马生鲜', 'url' => 'http://www.hmsx.com', 'createat' => '2020-10-13 12:18:44'];
        $data = ['x' => 13, 'name' => '盒马生鲜', 'url' => 'http://www.hmsx.com', 'createat' => '2020-10-13 12:18:44'];
        // $ret = $cache->operate('insert', $data); #只支持单条记录插入
        // var_dump($ret);die;
        $ret = $cache->operate('delete', ['x' => 13]);
        var_dump($ret);
        $ret = $cache->operate('update', [['x' => 11], ['$set' => ['name' => '盒马生鲜1']]]);
        var_dump($ret);
        die;

        // $data = ['x' => 6, 'name' => 'aliyun', 'url' => 'http://www.aliyun.com', 'createat' => '2020-10-13 12:18:44'];
        // $data = ['x' => 7, 'name' => 'Taobao', 'url' => 'http://taobao.com', 'createat' => '2020-10-13 12:18:44'];
        // $ret = $cache->operate('insert', $data); #只支持单条记录插入
        // var_dump($ret);die;

        # Query查询x大于1且根据x降序排列的数据
        $filter = ['x' => ['$gt' => 1]];
        $options = [
            'sort' =>  ['x' => -1],
        ];
        $ret = $cache->query($filter, $options);
        var_dump($ret);die;

        # 查询
        $cmd = [
            'find' => 'testdb', // collection表名
            'filter' => ['x' => ['$gte' => 1, '$lt' => 3]]
        ];

        # 新增
        $cmd = [
            'insert' => 'testdb', // collection表名
            'documents' => [
                ['x' => 4, 'name' => 'baidu', 'url' => 'http://baidu.com', 'createat' => '2020-10-13 12:18:44'],
                ['x' => 5, 'name' => 'weibo', 'url' => 'http://weibo.com', 'createat' => '2020-10-13 12:18:44'],
            ],
            'ordered' => true,
        ];

        // # 更新
        $cmd = [
            'update' => 'testdb', // collection表名
            'updates' => [
                ['q' => ['createat' => ['$eq' => "2020-10-14 15:32:46"]], 'u' => ['x' => 1, 'name' => '菜鸟教程', 'url' => 'http://www.runoob.com', 'createat' => '2020-10-13 12:18:44']]
            ],
        ];

        # 删除
        $cmd = [
            'delete' => 'AuthCode', // collection表名
            'deletes' => [
                ['q' => ['id' => ['$gte' => 1]], 'limit' => 1] # 删除一条id大于1的记录
            ],
        ];

        $cmd = [
            'delete' => 'AuthCode', // collection表名
            'deletes' => [
                ['q' => ['id' => ['$gte' => 2]], 'limit' => 0] # 删除id大于2的所有记录
            ],
        ];

        $res = $cache->exec($cmd);
        var_dump($res);

        //        $cache = Cache::getInstance();
        //        $cache = Cache::getInstance('redis');
        //        $ret = $cache->select(1);
        //        var_dump($ret);
        //        var_dump($cache);
        //        $ret = $cache->set('uu1', 1, 100);
        //        var_dump($ret);
        //        $ret = $cache->inc('uu1', 100);
        //        var_dump($ret);
        //        $ret = $cache->dec('uu1', 10);
        //        var_dump($ret);
        //        $ret = $cache->set('uu1', 'test', 100);
        //        var_dump($ret);
        //        $ret = $cache->get('uu1');
        //        var_dump($ret);
        //        $ret = $cache->has('uu1');
        //        var_dump($ret);
        //        $ret = $cache->rm('uu1');
        //        var_dump($ret);
        //        $ret = $cache->clear();
        //        var_dump($ret);
    }

    public function test($param = [])
    {
        $config = Config::all('cache');
        //        pp($config);
        //        $path = array_get($config, 'file.path');
        //        pp($path);
        //        $config = Config::get('file', 'cache');
        //        pp($config);
        //        $config = Config::all('cache.file');
        //        pp($config);
    }
}