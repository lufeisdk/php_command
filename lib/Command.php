<?php
/**
 * 命令行解析类
 * 格式：php  [入口文件][控制器类][类方法][参数...]
 *      php index.php index   index id=1 name:ray gender.man
 */

namespace lib;

class Command
{
    static public $instance = null; # 单例句柄
    public $Controller; # 当前调用的控制器类
    public $Action;     # 当前调用的控制器方法
    public $Params = [];# 当前方法携带的参数

    static public function getInstance()
    {
        if (null == static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * 防止使用 new 创建多个实例
     */
    private function __construct()
    {
        $argv = $_SERVER['argv'] ?? [];
        if (!empty($argv)) {
            $this->Controller = $argv[1] ?? Config::get('default_controller');
            $this->Action = $argv[2] ?? Config::get('default_action');
            $params = count($argv) > 3 ? array_slice($argv, 3, count($argv) - 3) : [];
            $this->Params = self::getOption($params);
        }
    }

    /**
     * @return mixed|string
     */
    public function getController()
    {
        return ucfirst($this->Controller);
    }

    /**
     * @return mixed|string
     */
    public function getAction()
    {
        return $this->Action;
    }

    /**
     * @return mixed|array
     */
    public function getParams()
    {
        return $this->Params;
    }

    /**
     * 命令行参数解析函数
     * 命令行参数输入支持三种键值对格式：[=:.]
     * 参考形式：id=1 name:ray gender.man
     * 解析之后形成键值对数组
     * 同时也支持无键名形式参数
     * @return array
     */
    protected function getOption($params)
    {
        $data = [];
        if ($params) {
            foreach ($params as $param) {
                if (preg_match('/[=:\.]/', $param, $matches)) {
                    list($pk, $pv) = explode($matches[0], $param);
                    $data[$pk] = $pv;
                } else {
                    $data[] = $param;
                }
            }
        }
        return $data;
    }

    /**
     * 防止 clone 多个实例
     */
    private function __clone()
    {
    }

    /**
     * 防止反序列化
     */
    private function __wakeup()
    {
    }
}