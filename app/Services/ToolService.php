<?php

namespace App\Services;

use App\Traits\InstanceTrait;
use App\Traits\Tool\IterableTrait;
use App\Traits\Tool\StringTrait;
use App\Traits\Tool\TreeTrait;

class ToolService
{
    /**
     * 多继承
     */
    use InstanceTrait,  // 单例
        TreeTrait,      // tree
        StringTrait,    // tree
        IterableTrait   // tree
        ;

    /**
     * 字节转可读size
     * @param $bytes
     * @return string
     */
    public function bytesToSize($bytes): string
    {
        $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
        $i = (int) floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $i), 2) . $sizes[$i];
    }
}
