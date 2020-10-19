<?php
define("CACHE_PATH", ROOT_PATH . '/runtime/cache');

return [
    'file' => [
        'path' => CACHE_PATH,   # 缓存存储路径
        'prefix' => '',         # 缓存名称前缀
        'expire' => 86400,      # 默认缓存时间
    ],

    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',           # Redis密码
        'select' => 0,          # 分库序号
        'timeout' => 0,         # 超时连接
        'expire' => 0,          # 默认过期时间
        'persistent' => false,  # 是否长连接
        'prefix' => '',         # 缓存名称前缀
        'serialize' => 1,       # 是否序列化存储
    ],

    'memcache' => [],

    'mongo' => [
        'host' => '127.0.0.1',
        'port' => 27017,
        'user' => '',
        'passwd' => '',
        'dbname' => '',
        'collection' => '',
    ]

];