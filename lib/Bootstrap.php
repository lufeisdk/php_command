<?php

namespace lib;

class Bootstrap
{
    static private $aryClassFile;

    static public function run()
    {
        $command = Command::getInstance();
        $controller = $command->getController() . 'Controller';
        $action = $command->getAction();
        $params = $command->getParams();

        try {
            $file = APP . DS . $controller . '.php';
            if (!is_file($file)) {
                throw new Exception("找不到对应的控制器类文件：" . $file);
            }

            include_once $file;

            $class = 'app\\' . $controller;
            if (!class_exists($class)) {
                throw new Exception("找不到类方法：" . $class);
            }
            $className = new $class();
            self::init($className, $action, $params);
        } catch (Exception $e) {
            exit($e->errorMessage());
        }
    }

    /**
     * 根据命名空间加载类文件
     * @param $class
     */
    static public function load($class)
    {
        $class = str_replace('\\', '/', $class);
        $file = ROOT_PATH . DS . $class . '.php';

        try {
            if (!is_file($file)) {
                throw new Exception("找不到相应的类文件：" . $file);
            }

            require_once $file;
            self::$aryClassFile[$class] = $file;
        } catch (Exception $e) {
            exit($e->errorMessage());
        }
    }

    public static function init($class, $action, $param)
    {
        $class->init();

        $class->before();

        $class->$action($param);

        $class->after();
    }
}