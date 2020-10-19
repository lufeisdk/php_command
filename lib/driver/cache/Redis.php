<?php

namespace lib\driver\cache;

use lib\exception\NotFoundException;

class Redis extends Driver
{
    protected $handler = null; # 驱动句柄

    // 默认配置
    static private $_CONFIG = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',           # Redis密码
        'select' => 0,          # 分库序号
        'timeout' => 0,         # 超时连接
        'expire' => 0,          # 默认过期时间
        'persistent' => false,  # 是否长连接
        'prefix' => '',         # 缓存名称前缀
        'serialize' => 1,       # 是否序列化存储
    ];

    public function __construct(Array $config = [])
    {
        self::$_OPTIONS = array_merge(self::$_CONFIG, $config);

        if (extension_loaded('redis')) {
            $this->handler = new \Redis();

            if ($this->persistent) {
                $this->handler->pconnect($this->host, $this->port, $this->timeout, 'persistent_id_' . $this->select);
            } else {
                $this->handler->connect($this->host, $this->port, $this->timeout);
            }

            if ('' != $this->auth) {
                $this->handler->auth($this->auth);
            }

            if (0 != $this->select) {
                $this->handler->select($this->select);
            }
        } /*elseif (class_exists('\Predis\Client')) {
            $params = [];
            foreach ($this->options as $key => $val) {
                if (in_array($key, ['aggregate', 'cluster', 'connections', 'exceptions', 'prefix', 'profile', 'replication', 'parameters'])) {
                    $params[$key] = $val;
                    unset($this->options[$key]);
                }
            }

            if ('' == $this->auth) {
                unset($this->auth);
            }

            $this->handler = new \Predis\Client($this->options, $params);

            $this->prefix = '';
        } */
        else {
            throw new NotFoundException('not support: redis');
        }
    }

    /**
     * 判断缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return $this->handler->exists($this->getCacheKey($name));
    }

    /**
     * 读取缓存
     * @access public
     * @param  string $name 缓存变量名
     * @param  mixed $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $value = $this->handler->get($this->getCacheKey($name));

        if (is_null($value) || false === $value) {
            return $default;
        }

        if ($this->serialize) {
            return unserialize($value);
        }
        return $value;
    }

    /**
     * 写入缓存
     * @access public
     * @param  string $name 缓存变量名
     * @param  mixed $value 存储数据
     * @param  integer|\DateTime $expire 有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->expire;
        }

        $key = $this->getCacheKey($name);
        $expire = $this->getExpireTime($expire);

        $value = $this->serialize ? serialize($value) : $value;

        if ($expire) {
            $result = $this->handler->setex($key, $expire, $value);
        } else {
            $result = $this->handler->set($key, $value);
        }

        return $result;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param  string $name 缓存变量名
     * @param  int $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        if ($this->serialize) {
            if ($this->has($name)) {
                $value = $this->get($name) + $step;
                $expire = $this->expire;
            } else {
                $value = $step;
                $expire = 0;
            }

            return $this->set($name, $value, $expire) ? $value : false;
        } else {
            $key = $this->getCacheKey($name);
            return $this->handler->incrby($key, $step);
        }
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param  string $name 缓存变量名
     * @param  int $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        if ($this->serialize) {
            if ($this->has($name)) {
                $value = $this->get($name) - $step;
                $expire = $this->expire;
            } else {
                $value = -$step;
                $expire = 0;
            }

            return $this->set($name, $value, $expire) ? $value : false;
        } else {
            $key = $this->getCacheKey($name);
            return $this->handler->decrby($key, $step);
        }
    }

    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        return $this->handler->del($this->getCacheKey($name));
    }

    /**
     * 清空当前redis数据库缓存
     * @return mixed
     */
    public function clear()
    {
        return $this->handler->flushDB();
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }
}