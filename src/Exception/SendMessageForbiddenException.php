<?php

namespace Messenger\Domain\Exception;


use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\UserInterface;

final class SendMessageForbiddenException extends \Exception
{
    public const int CODE = 6002;

    public function __construct(UserInterface $user, Discussion $discussion)
    {
        parent::__construct('User (' . $user->getUserIdentifier() . ') cannot send message in discussion (' . $discussion->getId() . ').', self::CODE);
    }
}
