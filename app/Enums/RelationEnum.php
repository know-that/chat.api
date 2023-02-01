<?php

namespace App\Enums;

use App\Models\Chat\ChatNotice;
use App\Models\Chat\ChatSingle;
use App\Models\Friend\FriendRequest;
use App\Models\Message\MessageBusinessCard;
use App\Models\Message\MessageFile;
use App\Models\Message\MessageText;
use App\Models\User\SystemUser;
use App\Models\User\User;

/**
 * 多态关联 Map
 */
enum RelationEnum: string
{
    use CollectTrait;

    case User = User::class;
    case SystemUser = SystemUser::class;
    case FriendRequest = FriendRequest::class;
    case ChatNotice = ChatNotice::class;
    case ChatSingle = ChatSingle::class;
    case MessageText = MessageText::class;
    case MessageFile = MessageFile::class;
    case MessageBusinessCard = MessageBusinessCard::class;
}
