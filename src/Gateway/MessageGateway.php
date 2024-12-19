<?php

namespace Messenger\Domain\Gateway;

use Messenger\Domain\Entity\Message;

interface MessageGateway
{
    /**
     * Save discussion
     * @param Message $message
     * @return void
     */
    public function insert(Message $message): void;

    /**
     * @param array{"discussion.id"?: string} $filters
     * @return int
     */
    public function countBy(array $filters): int;

    /**
     * @param array{"discussion.id"?: string} $filters
     * @param array{limit?: int, page?: int, offset?: int, orderBy?: array{createdAt?: string}} $options
     * @return Message[]
     */
    public function findBy(array $filters, array $options): array;
}
