<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Model\BootTrait;

class BaseModel extends Model
{
    use BootTrait;

    /**
     * 隐藏字段
     * @var string[]
     */
    protected $hidden = ['deleted_at'];

    /**
     * 字段黑名单
     * @var array
     */
    protected $guarded = [];

    /**
     * Bootstrap the model and its traits.
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        // 添加 builder normalPaginator 方法
        self::normalPaginatorMacro();
    }
}
