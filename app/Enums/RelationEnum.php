<?php

namespace App\Enums;

use App\Models\Chat\ChatGroupModel;
use App\Models\Chat\ChatNoticeModel;
use App\Models\Chat\ChatSingleModel;
use App\Models\Friend\FriendRequestModel;
use App\Models\Group\GroupChatModel;
use App\Models\Message\MessageBusinessCardModel;
use App\Models\Message\MessageFileModel;
use App\Models\Message\MessageTextModel;
use App\Models\User\SystemUserModel;
use App\Models\User\UserModel;

/**
 * 多态关联 Map
 */
enum RelationEnum: string
{
    use CollectTrait;

    case User = UserModel::class;
    case SystemUser = SystemUserModel::class;
    case FriendRequest = FriendRequestModel::class;
    case ChatNotice = ChatNoticeModel::class;
    case ChatSingle = ChatSingleModel::class;
    case ChatGroup = ChatGroupModel::class;
    case MessageText = MessageTextModel::class;
    case MessageFile = MessageFileModel::class;
    case MessageBusinessCard = MessageBusinessCardModel::class;
    case GroupChat = GroupChatModel::class;
}
