<?php

namespace Messenger\Domain\Exception;


use Messenger\Domain\Entity\UserInterface;

final class PaginateMessageForbiddenException extends \Exception
{
    public const int CODE = 6004;

    public function __construct(UserInterface $user, string $discussionId)
    {
        parent::__construct('User (' . $user->getUserIdentifier() . ') cannot paginate message in discussion ' . $discussionId . '.', self::CODE);
    }
}
