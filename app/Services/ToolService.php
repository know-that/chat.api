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
}
