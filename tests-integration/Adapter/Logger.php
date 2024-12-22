<?php

namespace Messenger\Domain\TestsIntegration\Adapter;

use Messenger\Domain\Gateway\LoggerInterface;

class Logger implements LoggerInterface
{
    /** @var string[] */
    private array $logs = [];

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $logs[] = sprintf('[%s] NOTICE %s : %s', date('Y-m-d H:i:s'), $message, json_encode($context));
    }

    /**
     * @return string[]
     */
    public function getLogs(): array
    {
        return $this->logs;
    }
}