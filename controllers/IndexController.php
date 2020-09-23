<?php

namespace app;

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
}