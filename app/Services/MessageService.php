<?php

namespace App\Services;

use App\Enums\RelationEnum;
use App\Models\Chat\ChatNoticeModel;
use App\Models\Chat\ChatSingleModel;
use App\Models\Group\GroupChatModel;
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
            if ((int) $item->message->is_read !== 0) {
                continue;
            }

            if ($item instanceof ChatSingleModel && $item->sender_user_id === $user->id) {
                $data[$item['message_type']][] = $item['message_id'];
                continue;
            }

            if ($item instanceof ChatNoticeModel && $item->user_id === $user->id) {
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

    /**
     * 群聊消息已读
     *
     * @param GroupChatModel $groupChat
     * @param mixed          $items
     * @return bool
     */
    public function chatGroupRead(GroupChatModel $groupChat, mixed $items): bool
    {
        return true;
        $data = [];
        foreach ($items as $item) {
            if ((int) $item->message->is_read !== 0) {
                continue;
            }

            if ($item instanceof ChatSingleModel && $item->sender_user_id === $user->id) {
                $data[$item['message_type']][] = $item['message_id'];
                continue;
            }

            if ($item instanceof ChatNoticeModel && $item->user_id === $user->id) {
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
