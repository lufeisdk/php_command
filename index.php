<?php
# 设置时区
ini_set("date.timezone", "PRC");

# 定义根目录
define("ROOT_PATH", __DIR__);

# 定义反斜杠
define('DS', '/');

# 定义控制器目录
define('APP', ROOT_PATH . DS . 'controllers');

# 引入公共函数
require_once ROOT_PATH . DS . 'common' . DS . 'function.php';

# 引入引导文件类
require_once ROOT_PATH . DS . 'lib' . DS . 'Bootstrap.php';

# 根据命名空间自动加载类
spl_autoload_register('\lib\Bootstrap::load');

# 执行命令行
\lib\Bootstrap::run();
