<?php

namespace App\Models;

use App\Enums\Model\FileUploadFromEnum;
use App\Services\ToolService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Config;

class UploadModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $appends = [
        'size_text'
    ];

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
        $self = $this;
        return new Attribute(
            get: static function ($value) use ($self) {
                switch ($self->from) {
                    case FileUploadFromEnum::QiNiu->value:
                        $url = Config::get('qi-niu.koDo.staticUrl')  . '/' . $value;
                        break;

                    case FileUploadFromEnum::AliYun->value:
                        $url = Config::get('ali-yun.OSS.staticUrl')  . '/' . $value;
                        break;

                    default:
                        $isMatched = preg_match('/(https:\/\/|http:\/\/)/', $value);
                        $url = $isMatched ? $value : Config::get('app.asset_url')  . '/' . $value;
                        break;
                }
                return $url;
            }
        );
    }

    /**
     * 大小
     * @return Attribute
     */
    public function sizeText(): Attribute
    {
        $self = $this;
        return new Attribute(
            get: static function () use ($self)  {
                return $self->size ? ToolService::getInstance()->bytesToSize($self->size) : '';
            }
        );
    }
}
