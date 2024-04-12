<?php

namespace App\Traits\Tool;

use Exception;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Support\Str;

trait StringTrait
{
    /**
     * 生成不唯一code
     *
     * @param string $type
     * @return string
     * @throws Exception
     */
    public function makeLangCode(string $type = ''): string
    {
        $microTime = explode(' ', microtime());

        // 时间日期
        $date = date('ymdHis', $microTime[1]);

        // 毫秒，后4位
        $micro = substr($microTime[0], 2, 4);

        // 随机数4位
        $rand = match ($type) {
            'number' => random_int(1000, 9999),
            default => Str::random(4),
        };

        return $date . $micro . $rand;
    }

    /**
     * 屏蔽字符串中间
     *
     * @param string $str
     * @param int $length
     * @param string $padString
     * @return string
     */
    public function strHidden(string $str, int $length = 4, string $padString = '*'): string
    {
        $start = mb_substr($str, 0, $length);
        $end = mb_substr($str, -$length, $length);
        $start = str_pad($start, strlen($start) + $length, $padString);

        return $start . $end;
    }

    /**
     * 检测字符串是否为链接
     *
     * @param string $str
     * @return bool
     */
    public function isLink(string $str): bool
    {
        $preg = "/(https?|ftp|file):\/\/[-A-Za-z0-9+&@#\/\%?=~_|!:,.;]+[-A-Za-z0-9+&@#\/\%=~_|]/";
        preg_match_all($preg, $str ,$arr);
        return !empty($arr[0]);
    }

    /**
     * url 参数转数组
     * @param string $url
     * @return array
     */
    public function urlQueryToArray(string $url): array
    {
        $data = explode('&', parse_url($url, PHP_URL_QUERY));
        $arr = [];
        foreach ($data as $item) {

            $itemData = explode('=', $item);
            $arr[$itemData[0]] = $itemData[1];
        }

        return $arr;
    }

    /**
     * 蛇形字符串转驼峰
     *
     * @param string $value
     * @param bool $isFirstLowercase 是否首字母小写
     * @return string
     */
    public function snakeToHump(string $value, bool $isFirstLowercase = false): string
    {
        $value = ucwords(str_replace(['_', '-'], ' ', $value));
        $value = str_replace(' ', '', $value);
        return lcfirst($value);
    }

    /**
     * 驼峰字符串转蛇形
     * @param string $value
     * @return string
     */
    public function humpToSnake(string $value): string
    {
        $value = preg_replace('/\s+/u', '', $value);
        return strtolower(preg_replace('/(.)(?=[A-Z])/u', "$1_", $value));
    }

    /**
     * 雪花ID
     *
     * @param string $prefix
     * @return string
     * @throws Exception
     */
    public function snowId(string $prefix = ''): string
    {
        $snowflake = new Snowflake();
        $id = $snowflake->setStartTimeStamp(strtotime('2023-01-01') * 1000)
            ->setSequenceResolver(new LaravelSequenceResolver(app('cache')->store()))
            ->id();

        return "{$prefix}{$id}";
    }
}
