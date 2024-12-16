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
     * @param array{"discussionMembers.member.email"?: string} $filters
     * @return mixed
     */
    public function countBy(array $filters): int;

    /**
     * @param array{"discussionMembers.member.email"?: string} $filters
     * @param array{limit?: int, page?: int} $options
     * @return mixed
     */
    public function findBy(array $filters, array $options): array;
}
