<?php

namespace lib\driver\cache;

class File extends Driver
{
    // 默认配置
    static private $_CONFIG = [
        'path' => './runtime/cache', # 缓存根目录
        'prefix' => 'Cache',         # 缓存名字前缀
        'expire' => 86400,           # 默认缓存时间1天
    ];

    public function __construct(Array $config = [])
    {
        self::$_OPTIONS = array_merge(self::$_CONFIG, $config);

        $this->createPath($this->path);
    }

    public function has($name)
    {
        $file = $this->getCacheFile($name);
        if (is_file($file)) {
            return true;
        }
        return false;
    }

    public function get($name)
    {
        $filename = $this->getCacheFile($name);

        if (!is_file($filename)) {
            return null;
        }

        $content = file_get_contents($filename);
        $this->expire = null;

        if (false !== $content) {
            $expire = (int)substr($content, 8, 12);
            if (0 != $expire && time() > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                $this->unlink($filename);
                return null;
            }

            $this->expire = $expire;
            $content = substr($content, 32);

            return unserialize($content);
        }
        return null;
    }

    /**
     * 写入缓存
     * @access public
     * @param  string $name 缓存变量名
     * @param  mixed $value 存储数据
     * @param  int|\DateTime $expire 有效时间 0为永久
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->expire;
        }

        $expire = $this->getExpireTime($expire);
        $filename = $this->getCacheFile($name);

        $data = serialize($value);

        $data = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $data;
        $result = file_put_contents($filename, $data);

        if ($result) {
            clearstatcache();
            return true;
        }
        return false;
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
        if ($this->has($name)) {
            $value = $this->get($name) + $step;
            $expire = $this->expire;
        } else {
            $value = $step;
            $expire = 0;
        }

        return $this->set($name, $value, $expire) ? $value : false;
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
        if ($this->has($name)) {
            $value = $this->get($name) - $step;
            $expire = $this->expire;
        } else {
            $value = -$step;
            $expire = 0;
        }

        return $this->set($name, $value, $expire) ? $value : false;
    }

    /**
     * 删除缓存文件
     * @param $name
     * @return bool
     */
    public function rm($name)
    {
        try {
            return $this->unlink($this->getCacheFile($name));
        } catch (Exception $e) {
        }
    }

    /**
     * 清除所有缓存
     */
    public function clear()
    {
        $dir = $this->path . DS;
        if (is_dir($dir)) {
            foreach (glob($dir . '*') as $path) {
                if (is_dir($path)) {
                    $matches = glob($path . DS . '*');
                    if (is_array($matches)) {
                        array_map(function ($v) {
                            $this->unlink($v);
                        }, $matches);
                    }
                    rmdir($path);
                } else {
                    $this->unlink($path);
                }
            }
        }
        return true;
    }

    /**
     * 根据缓存名称获取缓存文件
     * @param $name
     * @return string
     */
    private function getCacheFile($name)
    {
        $file_name = $this->getCacheKey($name);
        return $this->path . DS . $file_name;
    }

    /**
     * 创建缓存目录
     * @param  String $path 缓存目录
     * @return Boolean
     */
    private function createPath($path)
    {
        if (!is_dir($path)) {
            return mkdir($path, 0777, true);
        }
        return true;
    }

    /**
     * 判断文件是否存在后，删除
     * @access private
     * @param  string $path
     * @return boolean
     */
    private function unlink($path)
    {
        return is_file($path) && unlink($path);
    }
}