<?php
define('LOG_PATH', ROOT_PATH . '/runtime/log');

return [
    'file' => [
        'log_path' => LOG_PATH,         # 日志根目录
        'log_file' => 'default.log',    # 默认日志文件名
        'format' => 'Y/m/d',            # 日志自定义目录，使用日期时间定义
    ]
];