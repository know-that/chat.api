<?php

namespace App\Services;

use App\Models\Chat\ChatNoticeModel;
use App\Models\Chat\ChatSingleModel;
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
            'source' => function ($query) {
                $query->constrain([
                    UserModel::class => function ($query) {
                        $query->selectRaw('id, nickname, account, avatar, gender');
                    },
                    SystemUserModel::class => function ($query) {
                        $query->selectRaw('id, type, nickname, avatar');
                    },
                ]);
            },
            'lastChat' => function ($query) use ($user) {
                $query->with('message', function ($query) use($user) {
                    $query->constrain([
                        MessageTextModel::class => function ($query) use ($user) {
                            $query->where('receiver_user_id', $user->id)->selectRaw('id, type, content, is_read, created_at');
                        },
                    ]);
                })
                ->constrain([
                    ChatNoticeModel::class => function ($query) {
                        $query->selectRaw('id, user_id, source_type, source_id, message_type, message_id, created_at');
                    },
                    ChatSingleModel::class => function ($query) {
                        $query->selectRaw('id, receiver_user_id, sender_user_id, message_type, message_id, created_at');
                    },
                ]);
            }
        ];
    }
}
