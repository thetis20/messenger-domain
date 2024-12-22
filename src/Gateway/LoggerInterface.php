<?php

namespace Messenger\Domain\Gateway;

interface LoggerInterface
{
    /**
     * Normal but significant events.
     *
     * @param mixed[] $context
     */
    public function notice(string|\Stringable $message, array $context = []): void;
}