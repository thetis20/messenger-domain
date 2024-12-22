<?php

namespace Messenger\Domain\Response;

use Messenger\Domain\Entity\Message;

class DeleteMessageResponse
{
    private Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}
