<?php

namespace Messenger\Domain\Exception;

use Messenger\Domain\Entity\UserInterface;

class UseCaseForbiddenException extends \Exception
{
    public const int CODE = 6002;

    public function __construct(UserInterface $user)
    {
        parent::__construct('User (' . $user->getUserIdentifier() . ') cannot ' . self::class . '.', self::CODE);
    }
}
