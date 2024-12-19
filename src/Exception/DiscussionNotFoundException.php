<?php

namespace Messenger\Domain\Exception;

final class DiscussionNotFoundException extends \Exception
{
    public const int CODE = 6001;

    public function __construct(string $discussionId)
    {
        parent::__construct('Discussion not found ' . $discussionId . '.', self::CODE);
    }
}
