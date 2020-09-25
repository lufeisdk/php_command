<?php
function pp($var)
{
    echo '<pre>';
    print_r($var);
}

/**
 * array_get方法使用"."号从嵌套数组中获取值
 * @param array $array
 * @param $key
 * @param $default
 * @return mixed|null
 */
function array_get($array, $key, $default = null)
{
    if (is_null($key)) {
        return $array;
    }

    if (isset($array[$key])) {
        return $array[$key];
    }

    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) || !array_key_exists($segment, $array)) {
            return $default;
        }

        $array = $array[$segment];
    }
    return $array;
}