<?php

namespace lib;

use lib\exception\NotFoundException;

class Cache
{
    private $handler;

    static public function getInstance($driver = 'file')
    {
        static $instance; # 单例实例句柄
        if (empty($instance[$driver])) {
            $options = Config::all('cache.' . $driver);
            $instance[$driver] = new self($driver, $options);
        }
        return $instance[$driver];
    }

    private function __construct($driver, array $options = [])
    {
        $driver = ucfirst($driver);
        $class = 'lib\driver\cache\\' . $driver;
        if (!class_exists($class)) {
            throw new NotFoundException("找不到相应的缓存驱动类：" . $class);
        }
        $this->handler = new $class($options);
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