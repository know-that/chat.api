<?php

namespace App\Models\User;

use App\Enums\Model\UserGenderEnum;
use App\Facades\ToolFacade;
use App\Models\Chat\ChatSingleModel;
use App\Models\Friend\FriendModel;
use App\Traits\Model\BootTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserModel extends AuthUser implements JWTSubject
{
    use HasFactory, BootTrait;

    public $incrementing = false;

    /**
     * 字段黑名单
     * @var array
     */
    protected $guarded = [];

    /**
     * 隐藏字段
     * @var string[]
     */
    protected $hidden = ['password'];

    protected $table = 'user';

    /**
     * 初始化
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        // 设置主键非自增而是雪花编号
        self::snowflakeId();

        // 添加 builder normalPaginate 方法
        self::normalPaginatorMacro();

        // 添加 builder orSearch 方法
        self::orSearchMacro();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * gender_text 获取/访问器
     * @return Attribute
     */
    public function genderText(): Attribute
    {
        return new Attribute(
            get: fn ($value) => UserGenderEnum::tryFrom($this->gender)->text() ?? ''
        );
    }

    /**
     * account 获取/访问器
     * @return Attribute
     */
    public function account(): Attribute
    {
        return new Attribute(
            get: fn ($value) => ToolFacade::strHidden($value)
        );
    }

    /**
     * 未读消息
     * @return HasMany
     */
    public function notReadChats(): HasMany
    {
        return $this->hasMany(ChatSingleModel::class, 'receiver_user_id', 'id')->where('is_read', 0);
    }

    /**
     * 关联好友
     * @return HasOne
     */
    public function friend(): HasOne
    {
        return $this->hasOne(FriendModel::class, 'friend_id', 'id');
    }
}
