<?php

namespace Messenger\Domain\Exception;

use Messenger\Domain\Entity\Message;
use Messenger\Domain\Entity\UserInterface;

final class DeleteMessageForbiddenException extends \Exception
{

    public function __construct(UserInterface $user, ?Message $message = null)
    {
        parent::__construct(
            $message ? sprintf('User (%s) cannot delete message %s.', $user->getUserIdentifier(), $message->getId()) :
                sprintf('User (%s) cannot delete message.', $user->getUserIdentifier()),
            UseCaseForbiddenException::CODE);
    }
}
