<?php

namespace Messenger\Domain\Exception;


use Messenger\Domain\Entity\UserInterface;

final class PaginateDiscussionForbiddenException extends \Exception
{
    public const int CODE = 6003;

    public function __construct(UserInterface $user)
    {
        parent::__construct('User ('.$user->getUserIdentifier().') cannot paginate discussion.', self::CODE);
    }
}
