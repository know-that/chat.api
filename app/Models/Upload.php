<?php

namespace App\Models;

use App\Enums\Model\FileUploadFromEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Config;

class Upload extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * 字段黑名单
     * @var array
     */
    protected $guarded = [];

    protected $table = 'upload';

    /**
     * from_text 获取/访问器
     * @return Attribute
     */
    public function fromText(): Attribute
    {
        return new Attribute(
            get: fn ($value) => FileUploadFromEnum::tryFrom($value)?->text() ?? ''
        );
    }

    /**
     * url 获取/访问器
     *
     * @return Attribute
     */
    public function url(): Attribute
    {
        return new Attribute(
            get: static function ($value) {
                $isMatched = preg_match('/(https:\/\/|http:\/\/)/', $value);
                return $isMatched ? $value : Config::get('app.asset_url')  . '/' . $value;
            }
        );
    }
}
