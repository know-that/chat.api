<?php

namespace App\Services;

use App\Models\Chat\ChatNoticeModel;
use App\Models\Chat\ChatSingleModel;
use App\Models\Group\GroupChatModel;
use App\Models\Message\MessageFileModel;
use App\Models\Message\MessageTextModel;
use App\Models\User\SystemUserModel;
use App\Models\User\UserModel;
use JetBrains\PhpStorm\ArrayShape;

class ChatSessionService
{
    /**
     * 默认关联关系
     *
     * @param UserModel|null $user
     * @return mixed
     */
    #[ArrayShape([])]
    public function relations(UserModel $user = null): array
    {
        return [
            'source' => function ($query) use ($user) {
                $query->constrain([
                    UserModel::class => function ($query) use ($user) {
                        if ($user) {
                            $query->with('friend:id,user_id,friend_id,alias');
                        }
                        $query->selectRaw('id, nickname, account, avatar, gender');
                    },
                    SystemUserModel::class => function ($query) {
                        $query->selectRaw('id, type, nickname, avatar');
                    },
                    GroupChatModel::class => function ($query) {
                        $query->selectRaw('id, sn, nickname, avatar, creator_id');
                    },
                ]);
            },
            'lastChat' => function ($query) use ($user) {
                $query->with('message', function ($query) {
                    $query->constrain([
                        MessageTextModel::class => function ($query) {
                            $query->selectRaw('id, type, content, is_read, created_at');
                        },
                        MessageFileModel::class => function ($query) {
                            $query->selectRaw('id, file_id, type, is_read, created_at');
                        },
                    ]);
                })
                ->constrain([
                    ChatNoticeModel::class => function ($query) {
                        $query->selectRaw('id, user_id, source_type, source_id, message_type, message_id, is_read, created_at');
                    },
                    ChatSingleModel::class => function ($query) {
                        $query->selectRaw('id, receiver_user_id, sender_user_id, message_type, message_id, is_read, created_at');
                    },
                ]);
            }
        ];
    }
}
