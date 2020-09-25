<?php

namespace app;

use lib\Cache;
use lib\Database;
use lib\Log;
use lib\Controller;

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

    public function cache($param = [])
    {
//        $cache = Cache::getInstance();
        $cache = Cache::getInstance('redis');
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
}