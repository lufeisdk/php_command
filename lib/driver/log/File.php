<?php

namespace lib\driver\log;

class File
{
    // 总配置设定
    static private $_CONFIG = [
        'log_path' => './runtime/log', # 日志根目录
        'log_file' => 'default.log',   # 日志文件名
        'format' => 'Y/m/d',           # 日志自定义目录
        'tag' => '-',                  # 日志标签
    ];

    public function __construct(array $config = [])
    {
        self::$_CONFIG = array_merge(self::$_CONFIG, $config);
    }

    public function __get($name)
    {
        return self::$_CONFIG[$name];
    }

    public function __set($name, $value)
    {
        if (isset(self::$_CONFIG[$name])) {
            self::$_CONFIG[$name] = $value;
        }
    }

    // 当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用。
    public function __isset($name)
    {
        return isset(self::$_CONFIG[$name]);
    }

    /**
     * 写日志
     * @param string $data
     * @return bool|int
     */
    public function write($data = '')
    {
        if (!$data) {
            return false;
        }

        // 获取日志文件
        $log_file = $this->getLogFile();

        // 创建日志目录
        $is_create = $this->createLogPath(dirname($log_file));

        // 创建日期时间对象
        $dt = new \DateTime;

        // 日志内容
        $log_data = sprintf('[%s] %s %s' . PHP_EOL, $dt->format('Y-m-d H:i:s'), $this->tag, $data);

        // 写入日志文件
        if ($is_create) {
            return file_put_contents($log_file, $log_data, FILE_APPEND);
        }
        return false;
    }

    /**
     * 设置日志文件名称
     * @param $name
     * @return string
     */
    public function setLogFile($name)
    {
        $this->log_file = $name;
        return $this;
    }

    /**
     * 设置日志目录格式
     * @param $name
     * @return string
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * 设置日志标签
     * @param $name
     * @return string
     */
    public function setTag($name)
    {
        $this->tag = $name;
        return $this;
    }

    /**
     * 创建日志目录
     * @param  String $log_path 日志目录
     * @return Boolean
     */
    private function createLogPath($log_path)
    {
        if (!is_dir($log_path)) {
            return mkdir($log_path, 0777, true);
        }
        return true;
    }

    /**
     * 获取日志文件名称
     * @return String
     */
    private function getLogFile()
    {
        // 创建日期时间对象
        $dt = new \DateTime;

        // 计算日志目录格式
        return sprintf("%s/%s/%s", $this->log_path, $dt->format($this->format), $this->log_file);
    }
}