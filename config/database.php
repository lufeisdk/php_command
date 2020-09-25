<?php
/**
 * 数据库配置文件
 *
 * TagName =>[              # TagName用于标识配置，确保唯一，无重复
 *      'type' => 'mysql',  # 数据库类型字段，必填
 * ]
 */
return [
    # 默认读取default配置
    'default' => [
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'user' => 'root',
        'passwd' => 'root',
        'dbname' => 'test',
        'port' => 3306,
        'charset' => 'utf8'
    ],

    'sqlsrv' => [
        'type' => 'sqlsrv',
        'host' => '127.0.0.1',
        'user' => 'sa',
        'passwd' => '',
        'dbname' => '',
        'charset' => 'utf8'
    ],

    'TagName' => [
        'type' => 'mysql',
        'dbname' => 'fastadmin',
    ]
];

