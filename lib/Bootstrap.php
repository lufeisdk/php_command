<?php

namespace lib;

use lib\exception\NotFoundException;

class Bootstrap
{
    static private $aryClassFile;

    static public function run()
    {
        $command = Command::getInstance();
        $controller = $command->getController() . 'Controller';
        $action = $command->getAction();
        $params = $command->getParams();

        $file = APP . DS . $controller . '.php';
        if (!is_file($file)) {
            throw new NotFoundException("找不到对应的控制器类文件：" . $file);
        }

        include_once $file;

        $class = 'app\\' . $controller;
        if (!class_exists($class)) {
            throw new NotFoundException("找不到类方法：" . $class);
        }
        $className = new $class();
        self::init($className, $action, $params);
    }

    /**
     * 根据命名空间加载类文件
     * @param $class
     */
    static public function load($class)
    {
        $class = str_replace('\\', '/', $class);
        $file = ROOT_PATH . DS . $class . '.php';

        if (!is_file($file)) {
            throw new NotFoundException("找不到相应的类文件：" . $file);
        }

        require_once $file;
        self::$aryClassFile[$class] = $file;
    }

    public static function init($class, $action, $param)
    {
        $class->_params = $param;

        $class->init();

        $class->before();

        $class->$action();

        $class->after();
    }
}