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
}
