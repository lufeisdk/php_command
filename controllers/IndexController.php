<?php

namespace app;

use lib\Cache;
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