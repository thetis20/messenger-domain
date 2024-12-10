<?php

namespace Messenger\Domain\Exception;


use Messenger\Domain\Entity\UserInterface;

final class CreateDiscussionForbiddenException extends \Exception
{
    public const int CODE = 6001;

    public function __construct(UserInterface $user)
    {
        parent::__construct('User ('.$user->getUserIdentifier().') cannot create discussion.', self::CODE);
    }
}
