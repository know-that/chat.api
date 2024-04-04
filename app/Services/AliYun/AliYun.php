<?php

namespace App\Services\AliYun;

use Illuminate\Support\Facades\Config;

class AliYun
{
    /**
     * 配置信息
     * @var array|mixed
     */
    public array $config;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->config = Config::get('ali-yun');
    }
}
