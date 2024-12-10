<?php

namespace Messenger\Domain\Exception;

use Assert\AssertionFailedException;
use Assert\InvalidArgumentException;

final class NotAMemberOfTheDiscussionException extends \Exception
{
    public const int CODE = 6000;

    public function __construct()
    {
        parent::__construct('Not a member of the discussion', self::CODE);
    }
}
