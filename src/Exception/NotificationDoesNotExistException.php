<?php

namespace Messenger\Domain\Exception;

final class NotificationDoesNotExistException extends \Exception
{
    public const int CODE = 6004;

    public function __construct(string $key)
    {
        parent::__construct('Notification ' . $key . ' does not exists.', self::CODE);
    }
}
