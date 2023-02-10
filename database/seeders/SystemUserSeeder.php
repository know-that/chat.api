<?php

namespace Database\Seeders;

use App\Enums\Model\SystemUserTypeEnum;
use App\Models\User\SystemUserModel;
use Illuminate\Database\Seeder;

/**
 * 系统用户 模拟数据填充
 *
 * @package Database\Seeders
 */
class SystemUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'type'      => SystemUserTypeEnum::Announcement->value,
                'nickname'  => SystemUserTypeEnum::Announcement->text()
            ],
            [
                'type'      => SystemUserTypeEnum::FriendRequest->value,
                'nickname'  => SystemUserTypeEnum::FriendRequest->text()
            ]
        ];
        foreach ($data as $item) {
            SystemUserModel::create($item);
        }
    }
}
