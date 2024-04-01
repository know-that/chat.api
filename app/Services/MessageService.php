<?php

namespace App\Services;

use App\Enums\RelationEnum;
use App\Models\User\UserModel;

class MessageService
{
    /**
     * 单聊消息已读
     *
     * @param UserModel $user
     * @param mixed $items
     * @return bool
     */
    public function chatSingleRead(UserModel $user, mixed $items): bool
    {
        $data = [];
        foreach ($items as $item) {
            if ((int) $item->message->is_read === 0 && $item->sender_user_id === $user->id) {
                $data[$item['message_type']][] = $item['message_id'];
            }
        }
        $maps = RelationEnum::maps();

        // 不执行事务，但抑制错误
        try {
            foreach ($data as $index=>$item) {
                $model = new $maps[$index];
                $model->whereIn('id', $item)->update(['is_read'=>1]);
            }
        } catch (\Throwable $e) {}

        return true;
    }
}
