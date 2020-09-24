<?php

namespace lib;

class Cache
{
    private $handler;

    static private $instance;

    static public function getInstance($driver = 'file')
    {
        if (false == isset(static::$instance[$driver])) {
            $config = require_once ROOT_PATH . '/config/cache.php';
            $options = $config[$driver];
            static::$instance[$driver] = new self($driver, $options);
        }
        return static::$instance[$driver];
    }

    private function __construct($driver, array $options = [])
    {
        try {
            $driver = ucfirst($driver);
            $class = 'lib\driver\cache\\' . $driver;
            if (!class_exists($class)) {
                throw new Exception("找不到相应的缓存驱动类：" . $class);
            }
            $this->handler = new $class($options);
        } catch (Exception $e) {
            exit($e->errorMessage());
        }
    }

    public function has($name)
    {
        return $this->handler->has($name);
    }

    public function get($name)
    {
        return $this->handler->get($name);
    }

    public function set($name, $value, $expire_time = null)
    {
        return $this->handler->set($name, $value, $expire_time);
    }

    public function inc($name, $step = 1)
    {
        return $this->handler->inc($name, $step);
    }

    public function dec($name, $step = 1)
    {
        return $this->handler->dec($name, $step);
    }

    public function rm($name)
    {
        return $this->handler->rm($name);
    }

    public function clear()
    {
        return $this->handler->clear();
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }
}