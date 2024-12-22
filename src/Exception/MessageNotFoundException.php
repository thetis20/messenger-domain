<?php

namespace Messenger\Domain\Exception;

final class MessageNotFoundException extends \Exception
{
    public const int CODE = 6005;

    public function __construct(string $discussionId)
    {
        parent::__construct('Message not found ' . $discussionId . '.', self::CODE);
    }
}
