<?php

namespace lib;

class Log
{
    private $handler;

    static private $instance;

    static public function getInstance($driver = 'file')
    {
        if (false == isset(static::$instance[$driver])) {
            $config = require_once ROOT_PATH . '/config/log.php';
            $options = $config[$driver];
            static::$instance[$driver] = new self($driver, $options);
        }
        return static::$instance[$driver];
    }

    private function __construct($driver, array $options = [])
    {
        try {
            $driver = ucfirst($driver);
            $class = 'lib\driver\log\\' . $driver;
            if (!class_exists($class)) {
                throw new Exception("找不到相应的日志驱动类：" . $driver);
            }
            $this->handler = new $class($options);
        } catch (Exception $e) {
            exit($e->errorMessage());
        }
    }

    /**
     * 写日志
     * @param string $data
     * @return bool|int
     */
    public function write($data = '')
    {
        return $this->handler->write($data);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }
}