<?php

namespace lib;

class Config
{
    static private $aryConfig = null;

    /**
     * 根据配置文件名和配置项名称获取配置项的值
     * @param $name
     * @param $filename
     */
    static public function get($name, $filename = 'config')
    {
        if (isset(static::$aryConfig[$filename]) && isset(static::$aryConfig[$filename][$name])) {
            return static::$aryConfig[$filename][$name];
        }

        try {
            $file = ROOT_PATH . DS . 'config' . DS . $filename . '.php';
            if (!is_file($file)) {
                throw new Exception("找不到对应的配置文件：" . $file);
            }

            $config = include_once $file;
            if (!isset($config[$name])) {
                throw new Exception("找不到定义的配置项：" . $name);
            }
            static::$aryConfig[$filename] = $config;
            return $config[$name];
        } catch (Exception $e) {
            exit($e->errorMessage());
        }
    }

    /**
     * 获取配置文件的所有配置信息
     * @param $filename
     */
    static public function all($filename)
    {
        if (false !== strpos($filename, '.')) {
            list($file, $name) = explode('.', $filename);
            return self::get($name, $file);
        } else {
            if (isset(static::$aryConfig[$filename])) {
                return static::$aryConfig[$filename];
            }

            try {
                $file = ROOT_PATH . DS . 'config' . DS . $filename . '.php';
                if (!is_file($file)) {
                    throw new Exception("找不到对应的配置文件：" . $file);
                }

                return static::$aryConfig[$filename] = include_once $file;
            } catch (Exception $e) {
                exit($e->errorMessage());
            }
        }
    }
}